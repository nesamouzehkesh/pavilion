<?php

namespace Saman\CmsBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Saman\Library\Base\BaseController;

class CmsController extends BaseController
{
    /**
     * 
     * @param type $pageUrl
     * @return type
     */
    public function indexAction(Request $request, $pageUrl)
    {
        return $this->getCmsService()->displayPage($request, $pageUrl);
    }
    
    /**
     * 
     * @return type
     */
    private function getCmsService()
    {
        return $this->getService('saman_cms.cms');
    }    
}