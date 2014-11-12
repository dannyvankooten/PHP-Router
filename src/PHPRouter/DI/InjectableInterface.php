<?php

namespace PHPRouter\DI;

/**
 * Interface InjectableInterface
 * @package PHPRouter\DI
 */
interface InjectableInterface
{
    /**
     * Set the service container
     * @return mixed
     */
    public function setServiceContainer();

}