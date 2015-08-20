<?php

namespace Library\Session;

use Symfony\Component\HttpFoundation\Session\Storage\Proxy\SessionHandlerProxy;

class ReadOnlyGuestSessionProxy extends SessionHandlerProxy
{
    private $key;
    private $user;
    
    public function __construct(\SessionHandlerInterface $handler, User $user, $key = null)
    {
        $this->key = (null === $key)? 0 : $key;
        $this->user = $user;
        
        parent::__construct($handler);
    }

    public function read($id)
    {
        $data = parent::read($id);

        return mcrypt_decrypt(\MCRYPT_3DES, $this->key, $data);
    }

    public function write($id, $data)
    {
        if ($this->user->isGuest()) {
            return;
        }
        
        $data = mcrypt_encrypt(\MCRYPT_3DES, $this->key, $data);

        return parent::write($id, $data);
    }    
}