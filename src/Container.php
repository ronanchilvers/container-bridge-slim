<?php

namespace Ronanchilvers\Container\Slim;

use Ronanchilvers\Container\Container as BaseContainer;
use Ronanchilvers\Container\Slim\CallableResolver;
use Slim\DefaultServicesProvider;
use Slim\Handlers\Error;
use Slim\Handlers\NotAllowed;
use Slim\Handlers\NotFound;
use Slim\Handlers\PhpError;
use Slim\Handlers\Strategies\RequestResponse;
use Slim\Http\Environment;
use Slim\Http\Headers;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Router;

/**
 * Container subclass to ease working with Slim3
 *
 * @author Ronan Chilvers <ronan@d3r.com>
 */
class Container extends BaseContainer
{
    /**
     * @var array
     */
    protected $defaultSettings = [
        'httpVersion' => '1.1',
        'responseChunkSize' => 4096,
        'outputBuffering' => 'append',
        'determineRouteBeforeAppMiddleware' => false,
        'displayErrorDetails' => false,
        'addContentLengthHeader' => true,
        'routerCacheFile' => false,
    ];

    /**
     * {@inheritdoc}
     *
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function __construct(array $items = [])
    {
        if (isset($items['settings'])) {
            $items['settings'] = array_merge(
                $this->defaultSettings,
                $items['settings']
            );
        }
        parent::__construct($items);
        $this->registerSlimServices();
    }

    /**
     * Register the base services for Slim
     *
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    private function registerSlimServices()
    {
        if (!$this->has('environment')) {
            $this->share('environment', function () {
                return new Environment($_SERVER);
            });
        }

        if (!$this->has('request')) {
            $this->share('request', function ($container) {
                return Request::createFromEnvironment($container->get('environment'));
            });
        }

        if (!$this->has('response')) {
            $this->share('response', function ($container) {
                $headers = new Headers(['Content-Type' => 'text/html; charset=UTF-8']);
                $response = new Response(200, $headers);

                return $response->withProtocolVersion($container->get('settings')['httpVersion']);
            });
        }

        if (!$this->has('router')) {
            $this->share('router', function ($container) {
                $routerCacheFile = false;
                if (isset($container->get('settings')['routerCacheFile'])) {
                    $routerCacheFile = $container->get('settings')['routerCacheFile'];
                }


                $router = (new Router)->setCacheFile($routerCacheFile);
                if (method_exists($router, 'setContainer')) {
                    $router->setContainer($container);
                }

                return $router;
            });
        }

        if (!$this->has('foundHandler')) {
            $this->share('foundHandler', function () {
                return new RequestResponse;
            });
        }

        if (!$this->has('phpErrorHandler')) {
            $this->share('phpErrorHandler', function ($container) {
                return new PhpError($container->get('settings')['displayErrorDetails']);
            });
        }

        if (!$this->has('errorHandler')) {
            $this->share('errorHandler', function ($container) {
                return new Error(
                    $container->get('settings')['displayErrorDetails']
                );
            });
        }

        if (!$this->has('notFoundHandler')) {
            $this->share('notFoundHandler', function () {
                return new NotFound;
            });
        }

        if (!$this->has('notAllowedHandler')) {
            $this->share('notAllowedHandler', function () {
                return new NotAllowed;
            });
        }

        if (!$this->has('callableResolver')) {
            $this->share('callableResolver', function ($container) {
                return new CallableResolver($this);
            });
        }
    }
}
