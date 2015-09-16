<?php

namespace ShoppingBundle\Service;

use AppBundle\Service\AppService;

class ShoppingService
{

    /**
     *
     * @var AppService $appService
     */
    protected $appService;
    
    /**
     * 
     * @param \AppBundle\Service\AppService $appService
     * @param type $parameters
     */
    public function __construct(
        AppService $appService, 
        $parameters = array()
        ) 
    {
        $this->appService = $appService;
        $this->appService->setParametrs($parameters);
    }
}