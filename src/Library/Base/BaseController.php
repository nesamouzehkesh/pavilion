<?php

namespace Library\Base;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpKernel\Exception\GoneHttpException;

class BaseController extends Controller
{
    /**
     * Generate Csrf Token and put it in a session parameter with this name $tokenName
     * 
     * @param String $intention The intention used when generating the CSRF token
     * @param String $tokenName The name of session parameter 
     * @return String
     */
    public function generateAccessToken($intention = 'form', $tokenName = 'access_token') 
    {
        // Generate a CSRF token
        $token = $this->get('form.csrf_provider')->generateCsrfToken($intention);

        $this->getSession()->set($tokenName, $token);
        
        return $token;
    }
    
    /**
     * Set flash bag
     *
     * @param $messageType
     * @param $message
     * @param null $ex
     */
    public function setFlashBag($messageType, $message)
    {
        $flashBag = $this->getSession()->getFlashBag();
        
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
        $flashBag = $this->getSession()->getFlashBag();
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
     * @param bool $success
     * @param array $responseParam
     * @param string|array $message
     * @return type
     */
    public function getApiResponse(
        $success, 
        $responseParam = array(),
        $message = null
        )
    {
        $response = array();
        $response['success'] = $success;
        if (null !== $message) {
            $response['message'] = $this->transMessage($message);
        }
        
        return array_merge($response, $responseParam);
    }
    
    /**
     * 
     * @param type $message
     * @param type $ex
     * @param type $responseParam
     * @return type
     */
    public function getApiExceptionResponse(
        $message = null,
        $ex = null,
        $responseParam = array()
        )
    {
        $response = array();
        $response['success'] = false;
        if (null !== $message) {
            $response['message'] = $this->transMessage($message);
        }
        if (null !== $ex) {
            $response['exception'] = $this->getAppService()->getExceptionError($ex);
        }
        
        return array_merge($response, $responseParam);
    }
    
    /**
     * 
     * @param type $message
     * @param type $ex
     * @return type
     */
    public function getException($message, $ex = null)
    {
        $exceptionError = $this->getAppService()
            ->getExceptionError($ex);
        
        $transMessage = $this->transMessage($message);
        if ('' !== $exceptionError) {
            $transMessage = sprintf('%s [%s]', $transMessage, $exceptionError);
        }
        
        return new GoneHttpException($transMessage);
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
     * @return \AppBundle\Service\AppService
     */
    public function getAppService()
    {
        return $this->getService('saman.appService');
    }
    
    /**
     * 
     * @return \AppBundle\Service\EventHandler
     */
    public function getEventHandler()
    {
        return $this->getService('saman.eventHandler');
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
     * @return \Library\Service\Session
     */
    public function getSession()
    {
        return $this->getService('saman.session');
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

    /**
     * 
     * @param type $id
     * @param array $parameters
     * @return type
     */
    public function trans($id, array $parameters = array())
    {
        return $this->get('translator')->trans($id, $parameters);
    }
    
    public function addFormError($form, $message)
    {
        $form->addError(new FormError($this->transMessage($message)));
    }
    
}