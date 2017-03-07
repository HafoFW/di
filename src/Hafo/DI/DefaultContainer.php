<?php

namespace Hafo\DI;
use Nette\Caching\Cache;

/**
 * Default Hafo DI container implementation with core functionality.
 */
class DefaultContainer implements Container {

    private $factories;

    private $decorators;

    private $services = [];

    private $autowiringCache;

    /**
     * @param callable[] $factories Array (or \ArrayAccess) of identifier => function(Container $container).
     * @param callable[] $decorators Array (or \ArrayAccess) of identifier => function($service, Container $container).
     * @param Cache|NULL $autowiringCache
     */
    function __construct($factories, $decorators = [], Cache $autowiringCache = NULL) {
        $this->factories = $factories;
        $this->decorators = $decorators;
        $this->autowiringCache = $autowiringCache;
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
        $has = isset($this->factories[$id]);
        if(!$has && $this->autowiringCache) {
            $factory = $this->tryCreateFactory($id);
            if(!$factory) {
                return FALSE;
            }
            $this->factories[$id] = $factory;
            return TRUE;
        }
        return $has;
    }

    function create($id) {
        if(!$this->has($id)) {
            throw new NotFoundException;
        }
        $instance = $this->factories[$id]($this);
        if(!is_object($instance)) {
            return $instance;
        }
        return $this->decorate($instance);
    }

    private function decorate($instance) {
        $decorators = array_filter($this->decorators, function($type) use ($instance) {
            if(get_class($instance) === $type || is_subclass_of($instance, $type) || array_key_exists($type, class_implements($instance))) {
                return TRUE;
            }
            return FALSE;
        }, \ARRAY_FILTER_USE_KEY);
        foreach($decorators as $decorator) {
            $actualDecorators = $decorator;
            if(!is_array($decorator) && is_callable($decorator)) {
                $actualDecorators = [$decorator];
            }
            foreach($actualDecorators as $actualDecorator) {
                $ret = $actualDecorator($instance, $this);
                if($ret) {
                    $instance = $ret;
                }
            }
        }
        return $instance;
    }

    private function tryCreateFactory($id) {
        $args = $this->autowiringCache->load($id, function() use ($id) {
            return $this->tryResolve($id);
        });
        if(!is_array($args)) {
            return FALSE;
        }
        return function(Container $c) use ($id, $args) {
            return (new \ReflectionClass($id))->newInstanceArgs(array_map(function($arg) use ($c) {
                return class_exists($arg) || interface_exists($arg) ? $c->get($arg) : $arg;
            }, $args));
        };
    }

    private function tryResolve($id) {
        if(class_exists($id)) {
            $resolved = [];

            $ctor = (new \ReflectionClass($id))->getConstructor();
            foreach($ctor->getParameters() as $param) {
                $paramClass = $param->getClass();
                if($paramClass === NULL && !$param->isDefaultValueAvailable()) {
                    return FALSE;
                }
                $resolved[$param->getPosition()] = $paramClass === NULL ? $param->getDefaultValue() : $paramClass->getName();
            }
            return $resolved;
        }
        return FALSE;
    }

}
