<?php

namespace Saman\MediaBundle\Service;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Saman\Library\Service\Helper;
use Saman\Library\Map\EntityMap;
use Saman\MediaBundle\Repository\Select2Repository;

class Select2Service
{
    /**
     *  
     * @var Helper $helper
     */
    protected $helper;

    /**
     * 
     * @param \Saman\Library\Service\Helper $helper
     * @param \Saman\MediaBundle\Service\Filesystem $filesystem
     * @param type $parameters
     */
    public function __construct(
        Helper $helper,
        $parameters
        ) 
    {
        $this->helper = $helper;
        $this->helper->setParametrs($parameters);
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
            $this->helper->getEntityManager(),
            self::decodeClass($class)
            );
        
        $value = $request->request->get('value');
        $page = (int) $request->request->get('page', 1);
        $limit = (($limit = (int) $request->request->get('page_limit', 50)) > 500)? 500 : $limit;
        
        $total = $select2Repository->getCountSelect2Entities($value);
        $data = $select2Repository->getSelect2Entities($value, $page, $limit);
        
        return $this->helper->getJsonResponse(true, null, null, 
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
            $this->helper->getEntityManager(),
            self::decodeClass($class)
            );
        $data = $select2Repository->getSelect2EntitiesByIds($selection);

        // Display view
        return new JsonResponse($data);
    }
    
    /**
     * Decode class name
     * 
     * @param type $classId
     * @return type
     */
    public static function decodeClass($class)
    {
        return $class = (base64_decode($class));
    }
    
    /**
     * Encode class name
     * 
     * @param type $classId
     * @return type
     */
    public static function encodeClass($class)
    {
        return $class = (base64_encode($class));
    }
    
    /**
     * Get Media Repository
     * 
     * @return type
     */
    private function getMediaRepository()
    {
        return $this->helper->getRepository(EntityMap::MEDIA_MEDIA);
    }
}