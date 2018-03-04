<?php

namespace Ronanchilvers\Container\Slim;

use Psr\Container\ContainerInterface;
use Ronanchilvers\Container\NotFoundException;
use RuntimeException;
use Slim\Interfaces\CallableResolverInterface;

/**
 * Callable resolver that allows autowiring from the container
 *
 * Much of the code is copied directly from the default slim CallableResolver
 * which unfortunately is a final class.
 *
 * @author Ronan Chilvers <ronan@d3r.com>
 */
class CallableResolver implements CallableResolverInterface
{

    const CALLABLE_PATTERN = '!^([^\:]+)\:([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)$!';

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Resolve toResolve into a closure so that the router can dispatch.
     *
     * If toResolve is of the format 'class:method', then try to extract 'class'
     * from the container otherwise instantiate it and then dispatch 'method'.
     *
     * NB: This method is copied verbatim from the Slim codebase
     *
     * @param mixed $toResolve
     *
     * @return callable
     *
     * @throws RuntimeException if the callable does not exist
     * @throws RuntimeException if the callable is not resolvable
     */
    public function resolve($toResolve)
    {
        if (is_callable($toResolve)) {
            return $toResolve;
        }

        if (!is_string($toResolve)) {
            $this->assertCallable($toResolve);
        }

        // check for slim callable as "class:method"
        if (preg_match(self::CALLABLE_PATTERN, $toResolve, $matches)) {
            $resolved = $this->resolveCallable($matches[1], $matches[2]);
            $this->assertCallable($resolved);

            return $resolved;
        }

        $resolved = $this->resolveCallable($toResolve);
        $this->assertCallable($resolved);

        return $resolved;
    }

    /**
     * {@inheritdoc}
     *
     * Altered to allow the container to autowire
     *
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    protected function resolveCallable($class, $method = '__invoke')
    {
        try {
            return [$this->container->get($class), $method];
        } catch (NotFoundException $ex) {
            throw new RuntimeException(sprintf('Callable %s does not exist', $class));
        }
    }

    /**
     * NB: This method is copied verbatim from the Slim codebase
     *
     * @param Callable $callable
     *
     * @throws \RuntimeException if the callable is not resolvable
     */
    protected function assertCallable($callable)
    {
        if (!is_callable($callable)) {
            throw new RuntimeException(sprintf(
                '%s is not resolvable',
                is_array($callable) || is_object($callable) ? json_encode($callable) : $callable
            ));
        }
    }
}
