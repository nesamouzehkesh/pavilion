<?php

namespace Library\Service;

/**
 * Class Session
 * Overriding some functions of Symfony Session Service
 *
 * @package Library\Services\Session
 */
class Session extends \Symfony\Component\HttpFoundation\Session\Session
{
    /**
     * {@inheritdoc}
     */
    public function set($name, $value)
    {
        parent::set($name, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function get($name, $default = null)
    {
        return parent::get($name, $default);
    }
    
    /**
     * {@inheritdoc}
     */
    public function start()
    {
        parent::start();
    }

    /**
     * {@inheritdoc}
     */
    public function has($name)
    {
        return parent::has($name);
    }
}