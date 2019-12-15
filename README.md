![Inroute](https://raw.githubusercontent.com/inroutephp/inroute/master/res/logo.png "Inroute")

# inroutephp/console

[![Packagist Version](https://img.shields.io/packagist/v/inroutephp/console.svg?style=flat-square)](https://packagist.org/packages/inroutephp/console)
[![Build Status](https://img.shields.io/travis/inroutephp/console/master.svg?style=flat-square)](https://travis-ci.org/inroutephp/console)

Inroute compilation tool for the command line.

## Installation

The inroute console component should be installed as a development dependency.
To execute a compiled router however the inroute runtime must be avaliable. That
leaves us with a two step installation process:

```shell
composer require inroutephp/inroute
composer require --dev inroutephp/console
```

## Build configuration

By default the build configuration is read from a file named `inroute.json` in
the current working directory. A simple configuration file can look like:

```json
{
    "source-dir": "src/Controller",
    "source-prefix": "MyApp\\Controller",
    "target-filename": "src/HttpRouter.php",
    "target-namespace": "MyApp",
    "target-classname": "HttpRouter"
}
```

The following is a list of possible configuration values:

### autoload

Path to project autoloader. Defaults to `vendor/autoload.php`.

### container

The classname of a compile time container. Only needed if compile time objects
have dependencies that needs to be injected.

### bootstrap

Classname of compile time bootstrap script. Should normally not be needed.

### source-dir

Directory to scan for annotated routes. Relative to current working dir.

### source-prefix

psr-4 namespace prefix to use when scanning directory. Found `.php` files are
assumed to contain classes with this namespace prefix.

### source-classes

Array of source classnames, use instead of or togheter with directory scanning.

### route-factory

Classname of route factory, default should normally be fine.

### compiler

Classname of compiler, default should normally be fine.

### core-compiler-passes

Array of core compiler passes, default should normally be fine.

### compiler-passes

Array of custom compiler passes.

### code-generator

The code generator to use, default should normally be fine.

### target-filename

Path to router dump destination. If this file exists it will be overwritten.

### target-namespace

The namespace of the generated router (defaults to no namespace).

### target-classname

The classname of the generated router (defaults to `HttpRouter`).

## Usage

### Building

To build project router simply run

```shell
vendor/bin/inroute build
```

For mor information

```shell
vendor/bin/inroute build -h
```

### Debugging

To view debug information on the generated router run

```shell
vendor/bin/inroute debug
```

For more conprehensive output try

```shell
vendor/bin/inroute debug -v
```
