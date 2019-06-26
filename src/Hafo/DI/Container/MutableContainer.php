<?php
declare(strict_types=1);

namespace Hafo\DI\Container;
use Hafo\DI\Container;

/**
 * A decorator that makes Hafo DI container mutable.
 * @internal Not intended for use outside of tests.
 */
final class MutableContainer implements Container
{
    /** @var Container */
    private $container;

    private $services = [];

    private $factories = [];

    public function __construct(Container $container) {
        $this->container = $container;
    }

    public function get($id) {
        if(!isset($this->services[$id]) && isset($this->factories[$id])) {
            return $this->services[$id] = $this->create($id);
        }

        if(isset($this->services[$id])) {
            return $this->services[$id];
        }

        return $this->container->get($id);
    }

    public function create($id, ...$args) {
        if(isset($this->factories[$id])) {
            return $this->factories[$id]($this, ...$args);
        }

        return $this->container->create($id, ...$args);
    }

    public function has($id) {
        return isset($this->factories[$id]) || $this->container->has($id);
    }

    /**
     * Replaces (or adds if not found) an instantiated service.
     * 
     * @param string $id Identifier of the entry to replace.
     * @param mixed $service
     * @return $this
     */
    public function replace($id, $service) {
        $this->services[$id] = $service;

        return $this;
    }

    /**
     * Adds (or replaces if found) a service factory.
     *
     * @param string $id Identifier of the entry to add.
     * @param callable $factory function(Container $container)
     * @return $this
     */
    public function add($id, $factory) {
        $this->factories[$id] = $factory;

        return $this;
    }
}
