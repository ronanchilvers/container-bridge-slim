<?php

namespace Ronanchilvers\Container\Slim;

use Ronanchilvers\Container\Container as BaseContainer;
use Ronanchilvers\Container\Slim\CallableResolver;
use Slim\DefaultServicesProvider;

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
        $this->set('callableResolver', function ($container) {
            return new CallableResolver($this);
        });
        (new DefaultServicesProvider())->register($this);
    }
}
