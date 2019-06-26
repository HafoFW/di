<?php
declare(strict_types=1);

namespace Hafo\DI;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;


/**
 * PSR dependency injection container with a factory method.
 */
interface Container extends ContainerInterface {

    /**
     * Creates a new instance by identifier and returns it.
     *
     * @param string $id Identifier
     * @param array $args Constructor arguments
     *
     * @throws NotFoundExceptionInterface No entry was found for this identifier.
     * @throws ContainerExceptionInterface Error while creating the entry.
     *
     * @return mixed Entry.
     */
    public function create($id, ...$args);

}
