<?php

namespace Library\Session;

use Symfony\Component\HttpFoundation\Session\Storage\Proxy\SessionHandlerProxy;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\PdoSessionHandler;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\NativeFileSessionHandler;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\NativeSessionHandler;
use UserBundle\Entity\User;

class BaseSession
{
    protected $user;

    public function __construct(User $user = null)
    {
        $this->user = $user;
    }

    public function read($key)
    {
        $session = $this->getSession();
        $sessionKey = $this->getSessionKey($key);
        
        return $session->get($sessionKey);
    }
    
    public function write($key, $data)
    {
        $session = $this->getSession();
        $sessionKey = $this->getSessionKey($key);
        $session->set($sessionKey, $data);
        
        return $this;
    }
    
    public function getSession()
    {
        //$sessionProxy = $this->getProxy();
        $sessionProxy = null;
        $sessionStorage = new NativeSessionStorage(array(), $sessionProxy);
        $session = new Session($sessionStorage);
        
        return $session;
    }
    
    /**
     * 
     * @return \Library\Session\EncryptedSessionProxy
     */
    private function getProxy()
    {
        $userId = (null !== $this->user)? $this->user->getId() : null;
        $proxy = new EncryptedSessionProxy(new NativeSessionHandler(), $userId);       
        //$proxy = new EncryptedSessionProxy(new NativeFileSessionHandler(), $userId);
        
        return $proxy;
    }
    
    /**
     * 
     * @return type
     * @throws \Exception
     */
    private function getSessionKey($key)
    {
        $userId = (null !== $this->user)? $this->user->getId() : 0;
        
        return sha1(sprintf('saman_user_%s_%d', $key, $userId));
    }    
}