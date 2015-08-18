<?php

namespace Saman\Library\Session;

use Symfony\Component\HttpFoundation\Session\Storage\Proxy\SessionHandlerProxy;

class EncryptedSessionProxy extends SessionHandlerProxy
{
    private $key;
    
    public function __construct(\SessionHandlerInterface $handler, $key = null)
    {
        $this->key = (null === $key)? 0 : $key;

        parent::__construct($handler);
    }
    
    /**
     * 
     * @param type $id
     * @return type
     */
    public function read($id)
    {
        $data = parent::read($id);

        return mcrypt_decrypt(\MCRYPT_3DES, $this->key, $data);
    }
    
    /**
     * 
     * @param type $id
     * @param type $data
     * @return type
     */
    public function write($id, $data)
    {
        $data = mcrypt_encrypt(\MCRYPT_3DES, $this->key, $data);

        return parent::write($id, $data);
    }    
}