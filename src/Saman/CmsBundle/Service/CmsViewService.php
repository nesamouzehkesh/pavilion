<?php

namespace Saman\CmsBundle\Service;

use Saman\AppBundle\Service\AppService;
use Saman\CmsBundle\Entity\Page;

class CmsViewService
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
     * Get page based on its url
     * 
     * @param type $url
     * @return array
     */
    public function getPage($url)
    {
        return Page::getRepository($this->appService->getEntityManager())
            ->getPageForView($url);
    }
    
    /**
     * Get page based on its url
     * 
     * @return array
     */
    public function getPages()
    {
        return Page::getRepository($this->appService->getEntityManager())
            ->getPagesListForView();
    }
}