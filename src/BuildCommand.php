<?php

declare(strict_types = 1);

namespace inroutephp\console;

use inroutephp\inroute\Compiler\CompilerFacade;
use inroutephp\inroute\Compiler\Settings\ArraySettings;
use inroutephp\inroute\Compiler\Settings\ManagedSettings;
use inroutephp\inroute\Compiler\Settings\SettingsInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class BuildCommand extends Command
{
    private const DEFAULT_SETTINGS = [
        'autoload' => 'vendor/autoload.php',
    ];

    private const SETTING_OPTION_MAP = [
        'autoload' => [
            'autoload',
            null,
            InputOption::VALUE_REQUIRED,
            'Path to project autoloader'
        ],
        'code-generator' => [
            'code-generator',
            null,
            InputOption::VALUE_REQUIRED,
            'Classname of code generator to use'
        ],
        'compiler-passes' => [
            'compiler-pass',
            null,
            InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
            'Classname of compiler pass to add to build configuration'
        ],
        'container' => [
            'container',
            null,
            InputOption::VALUE_REQUIRED,
            'Classname of a compile time container'
        ],
        'source-classes' => [
            'source-class',
            null,
            InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
            'Name of class to scan for routes'
        ],
        'source-dir' => [
            'source-dir',
            null,
            InputOption::VALUE_REQUIRED,
            'Directory to scan for annotated routes'
        ],
        'source-prefix' => [
            'source-prefix',
            null,
            InputOption::VALUE_REQUIRED,
            'psr-4 namespace prefix to use when scanning directory'
        ],
        'target-classname' => [
            'target-classname',
            null,
            InputOption::VALUE_REQUIRED,
            'The classname of the generated router'
        ],
        'target-filename' => [
            'target-filename',
            null,
            InputOption::VALUE_REQUIRED,
            'Target output filename'
        ],
        'target-namespace' => [
            'target-namespace',
            null,
            InputOption::VALUE_REQUIRED,
            'The namespace of the generated router'
        ],
    ];

    /** @var SettingsInterface */
    private $settings;

    protected function getSettings(): SettingsInterface
    {
        return $this->settings;
    }

    protected function configure()
    {
        $this
            ->setName('build')
            ->setDescription('Build inroute project')
            ->setHelp('Generate and dump inroute http routing middleware')
            ->addOption(
                'configuration',
                'c',
                InputOption::VALUE_REQUIRED,
                'Path to build configuration file',
                'inroute.json'
            )
            ->addOption(
                'no-configuration',
                null,
                InputOption::VALUE_NONE,
                'Do not attempt to load a configuration file'
            )
        ;

        foreach (self::SETTING_OPTION_MAP as $option) {
            $this->addOption(...$option);
        }
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->settings = new ArraySettings(self::DEFAULT_SETTINGS);

        if (!$input->getOption('no-configuration')) {
            $settingsFile = $input->getOption('configuration');

            $output->writeln(
                "Using configuration file '$settingsFile'",
                OutputInterface::VERBOSITY_VERBOSE
            );

            if (!is_file($settingsFile) || !is_readable($settingsFile)) {
                throw new \RuntimeException("Unable to read '$settingsFile'");
            }

            $rawSettings = json_decode((string)file_get_contents($settingsFile));

            if (is_null($rawSettings)) {
                throw new \RuntimeException("Invalid content in '$settingsFile'");
            }

            $this->settings = new ManagedSettings(new ArraySettings((array)$rawSettings), $this->settings);
        }

        $cliSettings = [];

        foreach (self::SETTING_OPTION_MAP as $setting => list($option)) {
            if ($value = $input->getOption($option)) {
                $cliSettings[$setting] = $value;
            }
        }

        $this->settings = new ManagedSettings(new ArraySettings($cliSettings), $this->settings);

        $autoloader = $this->settings->getSetting('autoload');

        $output->writeln(
            "Using autoloader '$autoloader'",
            OutputInterface::VERBOSITY_VERBOSE
        );

        if (!is_file($autoloader) || !is_readable($autoloader)) {
            throw new \RuntimeException("Unable to read '$autoloader'");
        }

        require_once $autoloader;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $code = (new CompilerFacade)->compileProject($this->settings);

        $output->writeln("Dumping router to '{$this->settings->getSetting('target-filename')}'");

        file_put_contents($this->settings->getSetting('target-filename'), "<?php $code");

        return 0;
    }
}
