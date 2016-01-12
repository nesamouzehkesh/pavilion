<?php

namespace Library\Base;

class BaseApi
{
    /**
     * Some param required by each particuler action
     * 
     * @var type 
     */
    private $param = array();
    
    /**
     * 
     * @param type $param
     * @return \Library\Base\BaseApi
     */
    public function setParam($param)
    {
        $this->param = $param;
        
        return $this;
    }
    
    /**
     * 
     * @param string $key
     * @param array $externalParam
     * @return mix
     * @throws \Exception
     */
    public function getParam($key, $externalParam = null)
    {
        $param = (null === $externalParam)? $this->param : $externalParam;
        if (!array_key_exists($key, $param)) {
            throw new \Exception(sprintf('Parameter [%s] is not set', $key));
        }
        
        return $param[$key];
    }    
}