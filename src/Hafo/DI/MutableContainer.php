<?php

namespace Hafo\DI;

/**
 * A decorator that makes Hafo DI container mutable.
 * @internal Not intended for use outside of tests.
 */
class MutableContainer implements Container {

    private $container;

    private $services = [];

    private $factories = [];

    function __construct(Container $container) {
        $this->container = $container;
    }

    function get($id) {
        if(!isset($this->services[$id]) && isset($this->factories[$id])) {
            return $this->services[$id] = $this->create($id);
        }
        if(isset($this->services[$id])) {
            return $this->services[$id];
        }
        return $this->container->get($id);
    }

    function create($id, ...$args) {
        if(isset($this->factories[$id])) {
            return $this->factories[$id]($this, ...$args);
        }
        return $this->container->create($id, ...$args);
    }

    function has($id) {
        return isset($this->factories[$id]) || $this->container->has($id);
    }

    /**
     * Replaces (or adds if not found) an instantiated service.
     * 
     * @param string $id Identifier of the entry to replace.
     * @param mixed $service
     * @return $this
     */
    function replace($id, $service) {
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
    function add($id, $factory) {
        $this->factories[$id] = $factory;
        return $this;
    }

}
