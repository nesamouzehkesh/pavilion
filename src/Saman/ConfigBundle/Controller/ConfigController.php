<?php

namespace Saman\ConfigBundle\Controller;

use Saman\Library\Base\BaseController;
use Symfony\Component\HttpFoundation\Request;

class ConfigController extends BaseController
{
    public function indexAction(Request $request)
    {
        return $this->getConfigService()->addEditConfigs($request);
    }
    
    /**
     * 
     * @return type
     */
    private function getConfigService()
    {
        return $this
            ->getService('saman_config.config')
            ->setUser($this->getUser());
    }    
}
