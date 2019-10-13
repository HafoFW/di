<?php
declare(strict_types=1);

namespace Hafo\DI\Container;

use Hafo\DI\Autowiring;
use Hafo\DI\Container;
use Hafo\DI\Exception\ContainerException;
use Hafo\DI\Exception\NotFoundException;

final class DefaultContainer implements Container
{
    /** @var callable[] */
    private $factories;

    /** @var callable[][] */
    private $decorators;

    /** @var Autowiring */
    private $autowiring;

    /** @var mixed[] */
    private $services = [];

    /** @var string[][] */
    private $decoratorsUsed = [];

    /**
     * @param iterable|callable[] $factories Array of identifier => function(Container $container)
     * @param iterable|callable[][] $decorators Array of identifier => [function($service, Container $container)]
     * @param Autowiring $autowiring
     */
    public function __construct(iterable $factories = [], iterable $decorators = [], Autowiring $autowiring = null)
    {
        $this->factories = $factories;
        $this->decorators = $decorators;
        $this->autowiring = $autowiring === null ? new Autowiring\NoAutowiring() : $autowiring;
    }

    public function get($id)
    {
        if (!array_key_exists($id, $this->services)) {
            if (!$this->has($id)) {
                throw new NotFoundException("Entry with id {$id} not found.");
            }
            $this->services[$id] = $this->create($id);
        }

        return $this->services[$id];
    }

    public function has($id)
    {
        if (!array_key_exists($id, $this->factories)) {
            $factory = $this->autowiring->createFactory($id);
            if (!$factory) {
                return false;
            }

            $this->factories[$id] = $factory;

            return true;
        }

        return true;
    }

    public function create($id, ...$args)
    {
        if (!$this->has($id)) {
            throw new NotFoundException("Entry with id {$id} not found.");
        }

        try {
            $instance = $this->factories[$id]($this, ...$args);
            if (!is_object($instance)) {
                return $instance;
            }

            return $this->decorate($instance);
        } catch (\Throwable $e) {
            throw new ContainerException("Error while retrieving entry with id {$id}.", 0, $e);
        }
    }

    private function decorate($instance)
    {
        $objectHash = spl_object_hash($instance);
        if (!array_key_exists($objectHash, $this->decoratorsUsed)) {
            $this->decoratorsUsed[$objectHash] = [];
        }

        $decoratorGroup = array_filter($this->decorators, function ($type) use ($instance) {
            return is_a($instance, $type);
        }, \ARRAY_FILTER_USE_KEY);

        foreach ($decoratorGroup as $name => $decorators) {
            if (array_key_exists($name, $this->decoratorsUsed[$objectHash])) {
                continue;
            }

            if (is_callable($decorators)) {
                $decorators = [$decorators];
            }

            foreach ($decorators as $decorator) {
                $ret = $decorator($instance, $this);
                if ($ret !== null) {
                    $instance = $ret;
                }
            }

            $this->decoratorsUsed[$objectHash][$name] = true;
        }

        return $instance;
    }
}
