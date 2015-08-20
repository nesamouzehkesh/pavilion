<?php

namespace Library\Base;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class BaseController extends Controller
{
    /**
     * Set flash bag
     *
     * @param $messageType
     * @param $message
     * @param null $ex
     */
    public function setFlashBag($messageType, $message)
    {
        $flashBag = $this->getService('session')->getFlashBag();
        
        $flashBag->clear();
        $this->addFlashBag($messageType, $message);
    }
    
    /**
     * 
     * @param type $messageType
     * @param type $message
     */
    public function addFlashBag($messageType, $message)
    {
        $flashBag = $this->getService('session')->getFlashBag();
        $transedMessage = $this->transMessage($message);
        
        $flashBag->add($messageType, $transedMessage);
    }
    
    /**
     * 
     * @param type $parameter
     * @return type
     */
    public function getParameter($parameter)
    {
        return $this->container->getParameter($parameter);
    }
    
    /**
     * 
     * @param type $message
     * @param \Exception $previous
     * @param type $code
     * @throws VisibleHttpException
     */
    public function createVisibleHttpException(
        $message = null, 
        \Exception $previous = null, 
        $code = 0
        )
    {
        return $this->getAppService()
            ->createVisibleHttpException($message, $previous, $code);
    }
    
    /**
     * 
     * @param type $success
     * @param type $message
     * @param type $content
     * @param type $responseParam
     * @return type
     */
    public function getJsonResponse(
        $success, 
        $message = null, 
        $content = null, 
        $responseParam = null
        )
    {
        return $this->getAppService()
            ->getJsonResponse($success, $message, $content, $responseParam);
    }
    
    /**
     * 
     * @param type $message
     * @param type $ex
     * @return type
     */
    public function getExceptionResponse($message, $ex = null, $responseParam = null)
    {
        return $this->getAppService()
            ->getExceptionResponse($message, $ex, $responseParam);
    }    
    
    /**
     * 
     * @return \AppBundle\Service\AppService
     */
    public function getAppService()
    {
        return $this->getService('saman.appService');
    }
    
    /**
     * 
     * @return type
     */
    public function getDispatcher()
    {
        return $this->getService('event_dispatcher');
    }
    
    /**
     * 
     * @param type $service
     * @return type
     */
    public function getService($service)
    {
        return $this->get($service);
    } 
    
    /**
     * 
     * @param type $message
     * @return type
     */
    public function transMessage($message)
    {
        return $this->getAppService()->transMessage($message);
    }
}