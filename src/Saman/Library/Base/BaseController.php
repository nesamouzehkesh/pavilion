<?php

namespace Saman\Library\Base;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BaseController extends Controller
{
    /**
     * Add Cache-Control Header to responce
     * 
     * @param \Symfony\Component\HttpFoundation\Response $response
     * @param int|600 seconds   $maxAge      Max age for client caching
     * @param int|600 seconds   $sharedAge   Max age for shared (proxy) caching
     * @param type $directive
     * @param type $isPublic
     */
    public function cacheResponse(
        $response, 
        $maxAge = self::CACHE_PRIVATE_MAX_AGE,
        $sharedAge = self::CACHE_SHARED_MAX_AGE,
        $isPublic = false,
        $directives = array() 
        )
    {
        $this->getService('saman.helper')
            ->cacheResponse(
                $response,
                $maxAge,
                $sharedAge,
                $isPublic,
                $directives
                );
    }
    
    /**
     * 
     * @param type $message
     * @return type
     * @throws \Exception
     */
    public function trans($message)
    {
        $messageParam = array();
        $translator = $this->get('translator');
        
        if (is_array($message)) {
            if (array_key_exists(1, $message)) {
                $messageParam = $message[1];
            }
            
            if (array_key_exists(0, $message)) {
                $message = $message[0];
            } else {
                throw new \Exception('Message is used as an array and it is empty!');
            }
        } 
        
        return $translator->trans($message, $messageParam);
    }    
    
    
    /**
     * 
     * @return type
     */
    public function getDispatcher()
    {
        return $this->get('event_dispatcher');
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
     * @param type $formServiceName
     * @param type $entity
     * @param type $options
     * @return type
     */
    public function getForm($formServiceName, $entity, $options = null)
    {
        if (null === $options) {
            $action = $this->getRequest()->getUri();
            
            $options = array(
                'action' => $action,
                'method' => 'post'
                );
        }
        $form = $this->createForm($formServiceName, $entity, $options);
        
        return $form;
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
     * 404 HTTP response
     * 
     * @param type $message
     * @throws NotFoundHttpException
     */
    public function createNotFoundHttpException($message = 'Page not found')
    {
        throw new NotFoundHttpException($message);
    }
    
    /**
     * 500 HTTP response code
     * 
     * @param type $message
     * @throws \Exception
     */
    public function createException($message = 'Something went wrong')
    {
        throw new \Exception('Something went wrong!');
    }
    
    /**
     * 
     * @param type $success
     * @param type $message
     * @param type $content
     * @param type $responseParam
     * @return type
     */
    public function getJsonResponse($success, $message = null, $content = null, $responseParam = null)
    {
        return $this->getService('saman.helper')
            ->getJsonResponse(
                $success, 
                $message, 
                $content, 
                $responseParam
                );        
    }
}