<?php

namespace Hafo\DI;

/**
 * Default Hafo DI container implementation with core functionality.
 */
class DefaultContainer implements Container {

    private $factories;

    private $decorators;

    private $services = [];

    /**
     * @param callable[] $factories Array (or \ArrayAccess) of identifier => function(Container $container).
     * @param callable[] $decorators Array (or \ArrayAccess) of identifier => function($service, Container $container).
     */
    function __construct($factories, $decorators = []) {
        $this->factories = $factories;
        $this->decorators = $decorators;
    }

    function get($id) {
        if(!isset($this->services[$id])) {
            if(!$this->has($id)) {
                throw new NotFoundException;
            }
            $this->services[$id] = $this->create($id);
        }

        return $this->services[$id];
    }

    function has($id) {
        return isset($this->factories[$id]);
    }

    function create($id) {
        if(!$this->has($id)) {
            throw new NotFoundException;
        }
        $instance = $this->factories[$id]($this);
        if(!is_object($instance)) {
            return $instance;
        }
        $decorators = array_filter($this->decorators, function($type) use ($instance) {
            if(get_class($instance) === $type || is_subclass_of($instance, $type) || array_key_exists($type, class_implements($instance))) {
                return TRUE;
            }
            return FALSE;
        }, \ARRAY_FILTER_USE_KEY);
        foreach($decorators as $decorator) {
            if(is_array($decorator) && !is_callable($decorator)) {
                foreach($decorator as $actualDecorator) {
                    $actualDecorator($instance, $this);
                }
            } else {
                $decorator($instance, $this);
            }
        }
        return $instance;
    }

}
