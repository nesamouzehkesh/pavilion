<?php

/*
 * This file is part of the Symfony Best Practices package.
 *
 * (c) Saman Shafigh <samanshafigh@gmail.com>
 */

namespace Library\Components;

/**
 * ServiceBag is a container for key/value pairs. This code is influnesed by 
 * ParameterBag class implemented by Fabien Potencier <fabien@symfony.com>
 *
 * @author Saman Shafigh <samanshafigh@gmail.com>
 *
 * @api
 */
class ServiceBag implements \IteratorAggregate, \Countable
{
    /**
     * Service storage.
     *
     * @var array
     */
    protected $services;

    /**
     * Constructor.
     *
     * @param array $services An array of services
     *
     * @api
     */
    public function __construct(array $services = array())
    {
        $this->services = $services;
    }

    /**
     * Returns the services.
     *
     * @return array An array of services
     *
     * @api
     */
    public function all()
    {
        return $this->services;
    }

    /**
     * Returns the parameter keys.
     *
     * @return array An array of parameter keys
     *
     * @api
     */
    public function keys()
    {
        return array_keys($this->services);
    }

    /**
     * Replaces the current services by a new set.
     *
     * @param array $services An array of services
     *
     * @api
     */
    public function replace(array $services = array())
    {
        $this->services = $services;
    }

    /**
     * Adds services.
     *
     * @param array $services An array of services
     *
     * @api
     */
    public function add(array $services = array())
    {
        $this->services = array_replace($this->services, $services);
    }
    
    /**
     * Returns a service by service ID.
     * 
     * @param type $serviceId
     * @param type $defaultServiceId
     * @return type
     * @throws \InvalidArgumentException
     * 
     * @api
     */
    public function get($serviceId, $defaultServiceId = null)
    {
        if (!array_key_exists($serviceId, $this->services)) {
            if (null !== $defaultServiceId) {
                return $this->get($defaultServiceId);
            }
            
            throw new \InvalidArgumentException(sprintf('No service is available with this ID %s.', $serviceId));
        }

        return $this->services[$serviceId];
    }

    /**
     * Sets a parameter by name.
     *
     * @param string $key   The key
     * @param mixed  $value The value
     *
     * @api
     */
    public function set($key, $value)
    {
        $this->services[$key] = $value;
    }

    /**
     * Returns true if the parameter is defined.
     *
     * @param string $key The key
     *
     * @return bool true if the parameter exists, false otherwise
     *
     * @api
     */
    public function has($key)
    {
        return array_key_exists($key, $this->services);
    }

    /**
     * Removes a parameter.
     *
     * @param string $key The key
     *
     * @api
     */
    public function remove($key)
    {
        unset($this->services[$key]);
    }

    /**
     * Returns an iterator for services.
     *
     * @return \ArrayIterator An \ArrayIterator instance
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->services);
    }

    /**
     * Returns the number of services.
     *
     * @return int The number of services
     */
    public function count()
    {
        return count($this->services);
    }
}
