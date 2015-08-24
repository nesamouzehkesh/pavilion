<?php

namespace MediaBundle\Service;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Service\AppService;
use MediaBundle\Repository\Select2Repository;

class Select2Service
{
    /**
     *  
     * @var AppService $appService
     */
    protected $appService;

    /**
     * 
     * @param AppService $appService
     * @param type $parameters
     */
    public function __construct(
        AppService $appService,
        $parameters
        ) 
    {
        $this->appService = $appService;
        $this->appService->setParametrs($parameters);
    }
    
    /**
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param type $class
     * @return type
     */
    public function select2Data(Request $request, $class)
    {
        $select2Repository = new Select2Repository(
            $this->appService->getEntityManager(),
            self::decodeClass($class)
            );
        
        $value = $request->request->get('value');
        $page = (int) $request->request->get('page', 1);
        $limit = (($limit = (int) $request->request->get('page_limit', 50)) > 500)? 500 : $limit;
        
        $total = $select2Repository->getCountSelect2Entities($value);
        $data = $select2Repository->getSelect2Entities($value, $page, $limit);
        
        return $this->appService->getJsonResponse(true, null, null, 
            array(
                'total' => $total,
                'data' => $data,
                )
            );
    }
    
    /**
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param type $class
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function select2DataLookup(Request $request, $class)
    {
        $selection = (array) $request->request->get('selection');

        $select2Repository = new Select2Repository(
            $this->appService->getEntityManager(),
            self::decodeClass($class)
            );
        $data = $select2Repository->getSelect2EntitiesByIds($selection);

        // Display view
        return new JsonResponse($data);
    }
    
    /**
     * Decode class name
     * 
     * @param type $class
     * @return type
     */
    public static function decodeClass($class)
    {
        return base64_decode($class);
    }
    
    /**
     * Encode class name
     * 
     * @param type $class
     * @return type
     */
    public static function encodeClass($class)
    {
        return base64_encode($class);
    }
    
    /**
     * Get Media Repository
     * 
     * @return type
     */
    private function getMediaRepository()
    {
        return $this->appService->getRepository('MediaBundle:Media');
    }
}