<?php

namespace Saman\ShoppingBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Saman\Library\Base\BaseController;

class ShoppingAdminController extends BaseController
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
        return $this->getShoppingService()->displayItems($request);
    }
    
    /**
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param type $itemId
     * @return type
     */
    public function displayItemAction(Request $request, $itemId)
    {
        return $this->getShoppingService()->displayItem($request, $itemId);
    }    
    
    /**
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return type
     */
    public function addItemAction(Request $request)
    {
        return $this->getShoppingService()->addEditItem($request, null);
    }

    /**
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param type $itemId
     * @return type
     */
    public function editItemAction(Request $request, $itemId)
    {
        return $this->getShoppingService()->addEditItem($request, $itemId);
    }
    
    /**
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param type $itemId
     * @return type
     */
    public function deleteItemAction(Request $request, $itemId)
    {
        return $this->getShoppingService()->deleteItem($request, $itemId);
    }
    
    /**
     * 
     * @return type
     */
    private function getShoppingService()
    {
        return $this->getService('saman_shopping.shopping');
    }
}