<?php

namespace Saman\CmsBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Saman\Library\Base\BaseController;

class CmsAdminController extends BaseController
{
    /**
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return type
     */
    public function indexAction(Request $request)
    {
    }

    /**
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return type
     */
    public function displayItemsAction(Request $request)
    {
        return $this->getCmsAdminService()->displayItems($request);
    }
    
    /**
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param type $itemId
     * @return type
     */
    public function displayItemAction(Request $request, $itemId)
    {
        return $this->getCmsAdminService()->displayItem($request, $itemId);
    }    
    
    /**
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return type
     */
    public function addItemAction(Request $request)
    {
        return $this->getCmsAdminService()->addEditItem($request, null);
    }

    /**
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param type $itemId
     * @return type
     */
    public function editItemAction(Request $request, $itemId)
    {
        return $this->getCmsAdminService()->addEditItem($request, $itemId);
    }
    
    /**
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param type $itemId
     * @return type
     */
    public function deleteItemAction(Request $request, $itemId)
    {
        return $this->getCmsAdminService()->deleteItem($request, $itemId);
    }
    
    /**
     * 
     * @return type
     */
    private function getCmsAdminService()
    {
        return $this->getService('saman_cms.cms.admin');
    }
}