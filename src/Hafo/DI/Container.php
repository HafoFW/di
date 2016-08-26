<?php

namespace Hafo\DI;

use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;

/**
 * The interface of an Interop dependency injection container with a factory method.
 */
interface Container extends ContainerInterface {

    /**
     * Creates a new instance by identifier and returns it.
     *
     * @param string $id Identifier
     *
     * @throws NotFoundException No entry was found for this identifier.
     * @throws ContainerException Error while creating the entry.
     *
     * @return mixed Entry.
     */
    function create($id);

}
