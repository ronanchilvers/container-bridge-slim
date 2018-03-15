# container-bridge-slim

[![Build Status](https://travis-ci.org/ronanchilvers/container-bridge-slim.svg?branch=master)](https://travis-ci.org/ronanchilvers/container-bridge-slim)
[![codecov](https://codecov.io/gh/ronanchilvers/container-bridge-slim/branch/master/graph/badge.svg)](https://codecov.io/gh/ronanchilvers/container-bridge-slim)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)

A bridge for using [ronanchilvers/container](https://github.com/ronanchilvers/container) with Slim3. This package allows you to replace the default slim3 container (Pimple) with ronanchilvers/container.

## Installation

The easiest way to install is via composer:

```
composer install ronanchilvers/container-bridge-slim
```

## Usage

To replace the default slim container with ronanchilvers/container all you need to do is pass an instance of the container into the constructor of the slim app object. Here's an example:

```php
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Ronanchilvers\Container\Slim\Container;
use Slim\App;

// Initialise a new container instance
$container = new Container();

// Pass the container instance into the app object's constructor
$app = new App($container);

$app->get('/', function (ServerRequestInterface $request, ResponseInterface $response) {
    return $response->write('hallo');
});
$app->run();
```

Controlling Slim settings is similar to the usual Slim mechanism:

```php
use Ronanchilvers\Container\Slim\Container;
use Slim\App;

$container = new Container([
    'settings' => [
        'displayErrorDetails' => true,
    ]
]);

// Create the App object
$app = new App($container);
```

## Testing

This is quite simple a simple bridge and has 100% test coverage. You can run the tests by doing:

```
./vendor/bin/phpunit
```

The default phpunit.xml.dist file creates coverage information in a build/coverage subdirectory.

## Contributing

If anyone has any patches they want to contribute I'd be more than happy to review them. Please raise a PR. You should:

* Follow PSR2
* Maintain 100% test coverage or give the reasons why you aren't
* Follow a one feature per pull request rule

## License

This software is licensed under the MIT license. Please see the [License File](LICENSE.md) for more information.
