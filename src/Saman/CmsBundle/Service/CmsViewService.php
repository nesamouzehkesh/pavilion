<?php

namespace Saman\CmsBundle\Service;

use Saman\Library\Service\BaseService;
use Saman\CmsBundle\Entity\Page;

class CmsViewService
{
    /**
     *
     * @var BaseService $baseService
     */
    protected $baseService;
    
    /**
     * 
     * @param \Saman\Library\Service\Helper $baseService
     * @param type $parameters
     */
    public function __construct(
        BaseService $baseService, 
        $parameters
        ) 
    {
        $this->baseService = $baseService;
        $this->baseService->setParametrs($parameters);
    }
   
    /**
     * Get page based on its url
     * 
     * @param type $url
     * @return array
     */
    public function getPage($url)
    {
        return Page::getRepository($this->baseService->getEntityManager())
            ->getPageForView($url);
    }
    
    /**
     * Get page based on its url
     * 
     * @return array
     */
    public function getPages()
    {
        return Page::getRepository($this->baseService->getEntityManager())
            ->getPagesListForView();
    }
}