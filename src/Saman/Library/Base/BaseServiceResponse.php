<?php

namespace Saman\Library\Base;

use Symfony\Component\HttpFoundation\JsonResponse;

class BaseServiceResponse
{
    /**
     * @var boll
     */
    protected $success;
    
    /**
     *
     * @var Array
     */
    protected $messages;
   
    /**
     *
     * @var Object
     */
    protected $entity;

    /**
     *
     * @var string
     */
    protected $content;
    
    /**
     * 
     */
    public function __construct()
    {
        $this->messages = array();
        $this->content = null;
    }

    /**
     * 
     * @return \Saman\Library\Base\BaseResult
     */
    public function setSuccess($success)
    {
        $this->success = $success;
        
        return $this;
    }
    
    /**
     * @return bool
     */
    public function isSuccess()
    {
        return $this->success;
    }
    
    /**
     * 
     * @param type $message
     * @return \Saman\Library\Base\BaseResult
     */
    public function addMessage($message)
    {
        $this->messages[] = $message;
        
        return $this;
    }
    
    /**
     * 
     * @param type $messages
     * @return \Saman\Library\Base\BaseResult
     */
    public function addMessages($messages)
    {
        if (is_array($messages)) {
            foreach ($messages as $message) {
                $this->messages[] = $message;
            }
        }
        
        return $this;
    }
        
    /**
     * 
     * @return Message
     */
    public function getMessages($returnAsString = false)
    {
        if ($returnAsString) {
            return implode('. ', $this->messages);
        } else {
            return $this->messages;
        }
    }
    
    /**
     * 
     * @return JsonResponse
     */
    public function getJsonResponse($parametersSuccess = null, $parametersError = null)
    {
        $jasonResponse = new JsonResponse();
        
        $responseArray = array(
            'success' => $this->isSuccess(), 
            'message' => $this->getMessages(true),
            'content' => $this->getContent()
            );
        
        // Merge user extera Success parameters
        if ($this->isSuccess() and is_array($parametersSuccess)) {
            $responseArray = array_merge($responseArray, $parametersSuccess);
        } else if (is_array($parametersError)) {
            $responseArray = array_merge($responseArray, $parametersError);
        }        
        $jasonResponse->setContent(json_encode($responseArray));
        
        return $jasonResponse;
    }
    
    /**
     * 
     * @param type $entity
     * @return \Saman\Library\Base\BaseResult
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;
        
        return $this;
    }
    
    /**
     * 
     * @param type $content
     * @return \Saman\Library\Base\BaseResponse
     */
    public function setContent($content)
    {
        $this->content = $content;
        
        return $this;
    }
    
    /**
     * 
     * @return type
     */
    public function getContent()
    {
        return $this->content;
    }
}