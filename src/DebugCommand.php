<?php

declare(strict_types = 1);

namespace inroutephp\console;

use inroutephp\inroute\Compiler\CompilerFacade;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;

class DebugCommand extends BuildCommand
{
    protected function configure()
    {
        parent::configure();
        $this
            ->setName('debug')
            ->setDescription('Debug inroute project')
            ->setHelp("Examine all routes found during compilation\n\nHint: use -v for more comprehensive output")
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        (new CompilerFacade)->compileProject($this->getSettings(), $routes);

        $table = new Table($output);

        $headers = ['Name', 'Method', 'Path', 'Service'];

        if ($output->isVerbose()) {
            $headers[] = 'Middlewares';
            $headers[] = 'Attributes';
        }

        $table->setHeaders($headers);

        foreach ($routes->getRoutes() as $route) {
            $row = [
                $route->getName(),
                implode(',', $route->getHttpMethods()),
                $route->getPath(),
                $route->getServiceId() . ':' . $route->getServiceMethod()
            ];

            if ($output->isVerbose()) {
                $row[] = implode(', ', $route->getMiddlewareServiceIds());
                $row[] = json_encode($route->getAttributes());
            }

            $table->addRow($row);
        }

        $table->render();

        return 0;
    }
}
