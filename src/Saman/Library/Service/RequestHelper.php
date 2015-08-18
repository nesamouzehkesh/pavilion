<?php

namespace Saman\Library\Service;

use Saman\Library\Service\Helper;
use Symfony\Component\HttpFoundation\Request;
use Saman\Library\Base\BaseResult;

class RequestHelper {
    
    /**
     *
     * @var Request $request
     */
    protected $request;
    
    /**
     *
     * @var type 
     */
    protected $helper;

    /**
     *
     * @var BaseResult 
     */
    protected $result;
    
    /**
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Saman\Library\Service\Helper $helper
     */
    public function __construct(
        Request $request,
        Helper $helper
        ) 
    {
        $this->request = $request;
        $this->helper = $helper;
        $this->result = new BaseResult();
    }    
    
    /**
     * 
     * @return type
     */
    public function getRequest()
    {
        return $this->request;
    }
    
    /**
     * 
     * @return type
     */
    public function getHelper()
    {
        return $this->helper;
    }
    
    /**
     * 
     * @return BaseResult
     */
    public function getResult($success = null, $message = null)
    {
        if (null === $success) {
            $this->result->setSuccess($success);
            if (null !== $message) {
                $this->result->addMessage($message);
            }
        }
        
        return $this->result;
    }
}