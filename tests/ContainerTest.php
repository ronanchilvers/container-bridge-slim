<?php

namespace Ronanchilvers\Container\Slim\Test;

use PHPUnit\Framework\TestCase;
use Ronanchilvers\Container\Slim\Container;
use Slim\CallableResolver;
use Slim\Handlers\Error;
use Slim\Handlers\NotAllowed;
use Slim\Handlers\NotFound;
use Slim\Handlers\PhpError;
use Slim\Handlers\Strategies\RequestResponse;
use Slim\Http\Environment;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Router;

/**
 * Base test case for slim container
 *
 * @author Ronan Chilvers <ronan@d3r.com>
 */
class ContainerTest extends TestCase
{
    protected $slimDefaults = [
        'httpVersion' => '1.1',
        'responseChunkSize' => 4096,
        'outputBuffering' => 'append',
        'determineRouteBeforeAppMiddleware' => false,
        'displayErrorDetails' => false,
        'addContentLengthHeader' => true,
        'routerCacheFile' => false,
    ];

    /**
     * Provider for slim defaults
     *
     * @return array
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function slimDefaultsProvider()
    {
        $a = [];
        foreach ($this->slimDefaults as $key => $value) {
            $a[] = [$key, $value];
        }

        return $a;
    }

    /**
     * Test that the slim default settings are correct
     *
     * @dataProvider slimDefaultsProvider
     * @test
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function testSlimDefaultsAreCorrect($key, $value)
    {
        $container = new Container;
        $settings = $container->get('settings');

        $this->assertEquals($value, $settings[$key]);
    }

    /**
     * Data provider for default slim services
     *
     * @return array
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function slimServicesProvider()
    {
        return [
            ['environment',         Environment::class],
            ['request',             Request::class],
            ['response',            Response::class],
            ['router',              Router::class],
            ['foundHandler',        RequestResponse::class],
            ['phpErrorHandler',     PhpError::class],
            ['errorHandler',        Error::class],
            ['notFoundHandler',     NotFound::class],
            ['notAllowedHandler',   NotAllowed::class],
            ['callableResolver',    CallableResolver::class],
        ];
    }

    /**
     * Test that the default slimt services are correct
     *
     * @dataProvider slimServicesProvider
     * @test
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function testDefaultSlimServicesAreCorrect($key, $class)
    {
        $container = new Container;

        $this->assertInstanceOf($class, $container->get($key));
    }
}
