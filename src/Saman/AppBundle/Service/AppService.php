<?php

namespace Saman\AppBundle\Service;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Bundle\TwigBundle\TwigEngine;
use Symfony\Component\Validator\ValidatorInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\Common\Annotations\AnnotationReader;
use Knp\Component\Pager\Paginator;
use Saman\MediaBundle\Service\MediaService;
use Saman\ConfigBundle\Service\ConfigService;
use Saman\MediaBundle\Form\Type\MultipleType;
use Saman\Library\Exception\VisibleHttpException;

class AppService 
{
    const PARAM_SEARCH_TEXT = 'searchText';
    const PARAM_PAGE = 'page';
    const DEFAULT_LENGTH_RANDOM_STRING = 30;
    
    // Sets the number of seconds after which the response
    // should no longer be considered fresh for private cache
    const CACHE_PRIVATE_MAX_AGE = 600;

    // Sets the number of seconds after which the response
    // should no longer be considered fresh for shared cache
    const CACHE_SHARED_MAX_AGE = 600;
    
    /**
     * Translator services
     * 
     * @var Translator $translator
     */
    protected $translator;  
    
    /**
     *
     * @var SecurityContext $securityContext
     */
    protected $securityContext;    
    
    /**
     *
     * @var FormFactory $formFactory
     */
    protected $formFactory;
    
    /**
     *
     * @var Router $router
     */
    protected $router;
    
    /**
     *
     * @var TwigEngine $templating
     */
    protected $templating;

    /**
     *
     * @var EntityManager $em
     */
    protected $em;
    
    /**
     *
     * @var ValidatorInterface $validator
     */
    protected $validator;

    /**
     *
     * @var type 
     */
    protected $helperParameters = array();    
    
    /**
     *
     * @var type 
     */
    protected $parameters = array();
    
    /**
     *
     * @var Paginator $paginator
     */
    protected $paginator;
    
    /**
     *
     * @var MediaService $mediaService
     */
    protected $mediaService = null;

    /**
     *
     * @var ConfigService $configService
     */
    protected $configService = null;
    
    /**
     * 
     * @param Translator $translator
     * @param SecurityContext $securityContext
     * @param FormFactory $formFactory
     * @param Router $router
     * @param TwigEngine $templating
     * @param EntityManager $em
     * @param Paginator $paginator
     * @param ValidatorInterface $validator
     * @param type $helperParameters
     */
    public function __construct(
        Translator $translator, 
        SecurityContext $securityContext,
        FormFactory $formFactory,
        Router $router,
        TwigEngine $templating,
        EntityManager $em,
        Paginator $paginator,
        ValidatorInterface $validator,    
        $helperParameters
        ) 
    {
        $this->translator = $translator;
        $this->securityContext = $securityContext;
        $this->formFactory = $formFactory;
        $this->router = $router;
        $this->templating = $templating;
        $this->em = $em;
        $this->paginator = $paginator;
        $this->validator = $validator;
        $this->helperParameters = $helperParameters;
    }    
    
    
    /**
     * Get a helper parameter from $helperParameters array
     */
    public function getHelperParameter($parameterKey, $defaultValue = null)
    {
        if (array_key_exists($parameterKey, $this->helperParameters)) {
            return $this->helperParameters[$parameterKey];
        }
        
        return $defaultValue;
    }    
    
    /**
     * 
     * @param \Saman\MediaBundle\Service\MediaService $mediaService
     * @return \Saman\Library\Service\Helper
     */
    public function setMediaService(MediaService $mediaService)
    {
        $this->mediaService = $mediaService;
        
        return $this;
    }
    
    /**
     * 
     * @param \Saman\ConfigBundle\Service\ConfigService $configService
     * @return \Saman\Library\Service\Helper
     */
    public function setConfigService(ConfigService $configService, User $user = null) 
    {
        $user = (null === $user)? $this->getUser() : $user;
        $configService->setUser($user);
        $this->configService = $configService;
        
        return $this;
    }
    
    /**
     * Save media using media service
     * 
     * @param type $entity
     */
    public function saveMedia($entity)
    {
        if (null === $this->mediaService) {
            throw new \Exception("No media service is seet for this helper");
        }
        
        $this->mediaService->saveMedia($entity);
        $this->persistEntity($entity);
        $this->flushEntityManager();
        
        return $this;
    }
    
    /**
     * 
     * @param type $ids
     * @param type $entity
     * @param type $column
     * @return \Saman\Library\Service\Helper
     * @throws \Exception
     */
    public function saveMediaById($ids, $entity, $column = null)
    {
        if (null === $this->mediaService) {
            throw new \Exception("No media service is seet for this helper");
        }
        
        return $this->mediaService->saveMediaById($ids, $entity, $column);
    }
    
    /**
     * Get a config parameter from config service
     * 
     * @param type $configKey
     * @param \Saman\Library\Service\User $user
     * @return type
     * @throws \Exception
     */
    public function getConfig($configKey, User $user = null)
    {
        if (null === $this->configService) {
            throw new \Exception("No config service is seet for this helper");
        }
        
        return $this->configService->getConfig($configKey, $user);
    }
    
    /**
     * Get all config options for this user.
     * 
     * @param \Saman\Library\Service\User $user
     * @return type
     * @throws \Exception
     */
    public function getConfigs(User $user = null)
    {
        if (null === $this->configService) {
            throw new \Exception("No config service is seet for this helper");
        }
        
        return $this->configService->getUserConfigOptions($user);
    }
    
    /**
     * Get a user from the Security Context
     *
     * @return mixed
     * @throws \LogicException If SecurityBundle is not available
     * @see Symfony\Component\Security\Core\Authentication\Token\TokenInterface::getUser()
     */
    public function getUser()
    {
        if (null === $token = $this->securityContext->getToken()) {
            return;
        }

        if (!is_object($user = $token->getUser())) {
            return;
        }

        return $user;
    }    

    /**
     * Set parameters
     * 
     * @param type $parameters
     */
    public function setParametrs($parameters)
    {
        if (null !== $parameters && is_array($parameters)) {
            $this->parameters = array_merge($this->parameters, $parameters);
        }
        
        return $this;
    }
    
    /**
     * Get a parameter from $parameters array
     */
    public function getParameter($parameterKey, $defaultValue = null)
    {
        if (array_key_exists($parameterKey, $this->parameters)) {
            return $this->parameters[$parameterKey];
        }
        
        return $defaultValue;
    }
    
    /**
     * Get all parameters
     */
    public function getParameters()
    {
        return $this->parameters;
    }    
    
    /**
     * 
     * @return Symfony\Component\Form\FormFactory
     */
    public function getFormFactory()
    {
        return $this->formFactory;
    }
    
    /**
     * Creates and returns a Form instance from the type of the form.
     *
     * @param string|FormTypeInterface $type    The built type of the form
     * @param mixed                    $data    The initial data for the form
     * @param array                    $options Options for the form
     *
     * @return Form
     */
    public function createForm(Request $request, $type, $data = null, array $options = array())
    {
        if (count($options) == 0) {
            $action = $request->getUri();
            
            $options = array(
                'action' => $action,
                'method' => 'post'
                );
        }
        
        return $this->formFactory->create($type, $data, $options);
    }  
    
    /**
     * Fetch saman_multiple
     * 
     * @param type $form
     * @param type $fieldName
     * @return null
     */
    public function fetchFormFieldValue($form, $fieldName)
    {
        $value = $form->get($fieldName)->getData();
        if (is_array($value) && array_key_exists(MultipleType::MULTIPLE_FIELD_NAME, $value)) {
            if (!array_key_exists(MultipleType::MULTIPLE_FIELD_NAME, $value)) {
                throw new \Exception('The value is not saman_multiple type');
            }
            $value = $value[MultipleType::MULTIPLE_FIELD_NAME];            
        }
        
        return $value;
    }
    
    /**
     * 
     * @param type $value
     * @param type $fieldType
     * @return type
     */
    public function persistFormValue($value, $field, $entity = null, $column = null)
    {
        $fieldType = $field['type'];
        switch ($fieldType) {
            case 'saman_multiple':
                if (!array_key_exists(MultipleType::MULTIPLE_FIELD_NAME, $value)) {
                    throw new \Exception('The value is not saman_multiple type');
                }
                $value = $value[MultipleType::MULTIPLE_FIELD_NAME];
                foreach ($value as $multipleTypeKey => $multipleTypeData) {
                    foreach ($field['fields'] as $subFieldKey => $subField) {
                        $value[$multipleTypeKey][$subFieldKey] = $this->persistFormValue(
                            $value[$multipleTypeKey][$subFieldKey], 
                            $subField, 
                            $entity
                            );
                    }
                }
                
                break;
            case 'saman_media':
                $mediaIds = $this->saveMediaById(json_decode($value), $entity, $column);
                $value = $mediaIds;
                
                break;
        }
        
        return $value;
    }
    
    /**
     * 
     * @return type
     */
    public function getRouter()
    {
        return $this->router;
    }
    
    /**
     * Generates a URL from the given parameters.
     *
     * @param string         $route         The name of the route
     * @param mixed          $parameters    An array of parameters
     * @param bool|string    $referenceType The type of reference (one of the constants in UrlGeneratorInterface)
     *
     * @return string The generated URL
     *
     * @see UrlGeneratorInterface
     */
    public function generateUrl($route, $parameters = array(), $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH)
    {
        return $this->router->generate($route, $parameters, $referenceType);
    }
    
    /**
     * 
     * @return type
     */
    public function getTemplating()
    {
        return $this->templating;
    }
    
    /**
     * Renders a view and return just content
     *
     * @param string $view       The view name
     * @param array  $parameters An array of parameters to pass to the view
     *
     * @return string The rendered view
     */
    public function renderView($view, array $parameters = array())
    {
        if (null !== $this->configService) {
            $parameters['configs'] = $this->getConfigs();
        }
            
        return $this->templating->render($view, $parameters);
    }    
    
    /**
     * Renders a view and return response
     *
     * @param string   $view       The view name
     * @param array    $parameters An array of parameters to pass to the view
     * @param Response $response   A response instance
     *
     * @return Response A Response instance
     */    
    public function render($view, array $parameters = array(), Response $response = null)
    {
        if (null !== $this->configService) {
            $parameters['configs'] = $this->getConfigs();
        }
        
        return $this->templating->renderResponse($view, $parameters, $response);
    }    
    
    /**
     * 
     * @return type
     */
    public function getEntityManager()
    {
        return $this->em;
    }
    
    /**
     * Get entity repository
     * 
     * @param type $repository
     * @return type
     */
    public function getRepository($repository)
    {
        return $this->em->getRepository($repository);
    }
    
    /**
     * Soft-delete an entity
     * 
     * @param type $entity
     */
    public function deleteEntity($entity)
    {
        $this->deleteEntityAndAssociations($entity);
        $this->em->flush();
    }
    
    /**
     * Hard-delete an entity. An entity will be deleted from database for ever
     * 
     * @param type $entity
     */
    public function removeEntity($entity)
    {
        $this->em->remove($entity);
    }
    
    /**
     * Persist an entity in entity manager
     * 
     * @param type $entity
     */
    public function persistEntity($entity)
    {
        $this->em->persist($entity);
    }
    
    /**
     * Flush entity manager
     */
    public function flushEntityManager()
    {
        $this->em->flush();        
    }
    
    /**
     * Persist an flush entity manager for this entity
     * 
     * @param type $entity
     * @param type $flushEntityManager
     * @return \Saman\Library\Service\Helper
     */
    public function saveEntity($entity, $flushEntityManager = true)
    {
        $this->persistEntity($entity);
        if ($flushEntityManager) {
            $this->flushEntityManager();
        }
        
        return $this;
    }    
    
    /**
     * use Knp\Component\Pager\Pagination\AbstractPagination;
     * 
     * @return type
     */
    public function getPaginator()
    {
        return $this->paginator;
    }
    
    /**
     * 
     * @return type $validator
     */
    public function getValidator()
    {
        return $this->validator;
    }
    
    /**
     * 
     * @param type $entity
     * @return type
     */
    public function validate($entity, $responce = true)
    {
        $errors = $this->getValidator()->validate($entity);
        
        if (count($errors) > 0) {
            if (!$responce) {
                return $errors;
            }
            $errorsString = (string) $errors;

            return $this->getExceptionResponse($errorsString);
        }
        
        return true;
    }
    
    /**
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param type $itemsQuery
     * @return type
     */
    public function paginate(Request $request, $itemsQuery)
    {
        $itemsPagination = $this->getPaginator()->paginate(
            $itemsQuery,
            $request->get(self::PARAM_PAGE, 1),
            $this->getParameter('numberOfItemsPerPage', 10)
        );

        $currentPageNumber = $itemsPagination->getCurrentPageNumber();
        $totalItemCount = $itemsPagination->getTotalItemCount();
        $itemNumberPerPage = $itemsPagination->getItemNumberPerPage();
        
        if (count($itemsPagination) == 0) {
            $currentPageNumber = ceil($totalItemCount / $itemNumberPerPage);
            
            $itemsPagination = $this->getPaginator()->paginate(
                $itemsQuery,
                $currentPageNumber,
                $this->getParameter('numberOfItemsPerPage', 10)
            );
            $itemsPagination->setCurrentPageNumber($currentPageNumber);
        }
        
        return $itemsPagination;
    }

    /**
     * 
     * @return Translator service
     */
    public function getTranslator()
    {
        return $this->translator;
    }
    
    /**
     * Return traslated message
     * 
     * @param type $id
     * @param array $parameters
     * @return type
     */
    public function trans($id, array $parameters = array(), $domain = null, $locale = null)
    {
        return $this->translator->trans($id, $parameters, $domain, $locale);
    }
    
    /**
     * 
     * @return string
     */
    public function toString()
    {
        return 'Helper';
    }
    
    /**
     * Returns the Unix timestamp representing the date. 
     * 
     * @param \DateTime $date
     * @return type
     */
    public function getTimestamp(\DateTime $date = null)
    {
        if (null === $date) {
            $date = new \DateTime();
        }
        
        return $date->getTimestamp();
    }
    
    /**
     * Return Date based on this $timestamp
     * 
     * @param type $timestamp
     * @return \DateTime
     */
    public function getTime($timestamp)
    {
        $date = new \DateTime();
        $date->setTimestamp($timestamp);
        
        return $date;
    }
    
    /**
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param type $param
     */
    public function getSearchParam(Request $request, $param = null)
    {
        if (null === $param) {
            $param = array();
        }
        
        if (null !== ($searchText = $request->get(self::PARAM_SEARCH_TEXT, null))) {
            $param[self::PARAM_SEARCH_TEXT] = $searchText;
        }
        
        return $param;
    }
    
    /**
     * Add Cache-Control Header to responce
     * 
     * @param type $response
     * @param type $maxAge
     * @param type $sharedAge
     * @param type $isPublic
     * @param type $directives
     * @return boolean
     */
    public function cacheResponse(
        $response, 
        $maxAge = self::CACHE_PRIVATE_MAX_AGE,
        $sharedAge = self::CACHE_SHARED_MAX_AGE,
        $isPublic = false,
        $directives = array() 
        )
    {
        return true;
        
        if ($isPublic) {
            $response->setPublic();
        } else {
            $response->setPrivate();
        }
        
        // Sets the number of seconds after which the response
        // should no longer be considered fresh for private cache
        $response->setMaxAge($maxAge);
        
        // Sets the number of seconds after which the response
        // should no longer be considered fresh for shared cache
        $response->setSharedMaxAge($sharedAge);
        
        // set a custom Cache-Control directive
        foreach ($directives as $directiveKey => $directiveValue) {
            $response->headers->addCacheControlDirective($directiveKey, $directiveValue);
        }
        
        return $response;
    }
    
    /**
     * 
     * @param type $message
     * @param \Exception $previous
     * @param type $code
     * @return VisibleHttpException
     */
    public function createVisibleHttpException($message = null, \Exception $previous = null, $code = 0)
    {
        $transedMessage = $this->transMessage($message);
        
        return new VisibleHttpException($transedMessage, $previous, $code);
    }
    
    /**
     * 
     * @param type $message
     * @return type
     * @throws \Exception
     */
    public function transMessage($message)
    {
        // Set jason message content
        if (null === $message) {
            return '';
        }
        
        if (is_array($message)) {
            if (!array_key_exists(0, $message)) {
                throw new \Exception('Array message must have the first index');
            }
            if (array_key_exists(1, $message)) {
                $message = $this->trans($message[0], $message[1]);
            } else {
                $message = $this->trans($message[0]);
            }
        } else {
            $message = $this->trans($message);
        }
        
        return sprintf('%s', $message);
    }     
    
    /**
     * 
     * @param type $message
     * @param type $ex
     * @param type $responseParam
     * @return type
     */
    public function getExceptionResponse($message, $ex = null, $responseParam = null)
    {
        return $this->getJsonResponse(false, $message, null, $responseParam, $ex);
    }
    
    /**
     * 
     * @param type $success
     * @param type $message
     * @param type $content
     * @param type $responseParam
     * @param type $ex
     * @return JsonResponse
     * @throws \Exception
     */
    public function getJsonResponse(
        $success, 
        $message = null, 
        $content = null, 
        $responseParam = null, 
        $ex = null
        )
    {
        // Set jason success status
        $response['success'] = $success;
        $response['message'] = $this->transMessage($message);
        
        if ('' !== $response['message']) {
            $response['message'] = sprintf('%s.', $response['message']);
        }
        
        // Get exception error and add it to responce message
        $exceptionError = $this->getExceptionError($ex);
        if (null !== $exceptionError) {
            $response['message'] = sprintf('%s [%s]', $response['message'], $exceptionError);
        }
        
        // Set jason contet if it is provide
        if (null !== $content) {
            $response['content'] = $content;
        }
        
        // Merge jason responce with some extra user parameters
        if (null !== $responseParam) {
            $response = array_merge($response, $responseParam);
        }
        
        return new JsonResponse($response);
    }
    
    /**
     * 
     * @return ServiceResponse
     */
    public function getServiceResponse($success = true, $messages = null, $entity = null)
    {
        if (null !== $success) {
            $this->serviceResponse->setSuccess($success);
            if (null !== $messages) {
                if (is_array($messages)) {
                    $this->serviceResponse->addMessages($messages);
                } else {
                    $this->serviceResponse->addMessage($messages);
                }
            }
            
            if (null !== $entity) {
                $this->serviceResponse->setEntity($entity);
            }
        }
        
        return $this->serviceResponse;
    }
    
    /**
     * Generate random string with a lenght of $length + (12)
     * 
     * @param type $length
     * @return type
     */
    public function getRandomString($length = null)
    {
        $dateTimeString = date('dym');
        $length = (0 !== intval($length))? $length : self::DEFAULT_LENGTH_RANDOM_STRING;
        $randomString = substr(str_shuffle(md5(time())), 0, ($length - strlen($dateTimeString)));
        $shuffledRandomString = str_shuffle($randomString . $dateTimeString);
        
        return $shuffledRandomString;
    }
    
    /**
     * Get opject properties
     * 
     * @param type $item
     * @param type $allProperties
     * @return type
     */
    public function getObjectProperties($item, $allProperties = false)
    {
        $reflectionObj = new \ReflectionObject($item);
        $reflectionProperties = array();
        
        if ($allProperties) {
            $reflectionProperties = $reflectionObj->getProperties();
        }
        
        foreach ($reflectionObj->getProperties() as $reflectionProperty) {
            if (!in_array($reflectionProperty->getName(), array(
                'id', 
                'labels', 
                'createdTime', 
                'modifiedTime', 
                'deleted', 
                'deletedTime', 
                'visibility'
                ))) {
                $reflectionProperties[] = $reflectionProperty;
            }
        }
        
        return $reflectionProperties;
    }
    
    /**
     * 
     * @param Request $request
     * @return type
     */
    public function getBaseUrl(Request $request)
    {
        return 'http://' . $request->getHttpHost();
    }
    
    /**
     * 
     * @param type $entity
     */
    private function deleteEntityAndAssociations($entity)
    {
        /** @var BaseEntity $entity */
        $entity->setDeleted(true);
        $entity->setDeletedTime();
     
        $annotationReader = new AnnotationReader();
        $reflectionProperties = $this->getObjectProperties($entity);
        foreach ($reflectionProperties as $reflectionProperty) {
            $mediaAnnotation = $annotationReader->getPropertyAnnotation(
                $reflectionProperty, 
                'Saman\\Library\\Annotation\\DeleteAnnotation'
                );

            if ($mediaAnnotation) {
                $column = $reflectionProperty->getName();
                $getter = 'get' . ucfirst($column);
                foreach ($entity->$getter() as $subEntity) {
                    if (!$subEntity->isDeleted()) {
                        $this->deleteEntityAndAssociations($subEntity);
                    }
                }
            }
        }
    }
    
    /**
     * Get exception error
     *
     * @param \Exception $ex
     * @param null|bool $forceToShow
     * @return string $exceptionError
     */
    private function getExceptionError($ex, $forceToShow = false)
    {
        // If the exception is instace of PmsHttpException then we will display
        // its message to the end user. 
        if ($ex instanceof VisibleHttpException) {
            $forceToShow = true;
        }
        
        $exceptionError = null;
        // $showErrorConfig = $this->container->getParameter('elmo_show_exception_error_detail');
        $showErrorConfig = true;
        if (null !== $ex and is_a($ex, '\Exception')) {
            $exceptionError = ($showErrorConfig || $forceToShow)? $ex->getMessage() : '';
        }
        
        return $exceptionError;
    }    
}