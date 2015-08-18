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
    public function displayItemsAction(Request $request)
    {
        // Get search parameters from HTTP request
        $searchParam = $this->getBaseService()
            ->getSearchParam($request);

        // Get pages based on $searchParam
        $pagesQuery = $this->getCmsAdminService()
            ->getPages($searchParam);

        // Get pagination
        $pagesPagination = $this->getBaseService()
            ->paginate($request, $pagesQuery);

        // Get the view of pages list
        $pagesView = $this->renderView(
            'SamanCmsBundle:CmsAdmin:element/pages.html.twig',
            array('pagesPagination' => $pagesPagination)
            );

        // If user use the pagination to view other pages then we just return the 
        // $pagesView as a jason response array
        if ($request->get('headless')) {
            return $this->getBaseService()->getJsonResponse(true, null, $pagesView);
        } 

        return $this->render(
            'SamanCmsBundle:CmsAdmin:index.html.twig',
            array(
                'pagesView' => $pagesView
                )
            );
    }
    
    /**
     * 
     * @param type $itemId
     * @return type
     * @throws type
     */
    public function displayItemAction($itemId)
    {
        try {
            // Get Page
            $page = $this->getCmsAdminService()->getPage($itemId);
            
            // Generate the view for this page
            $pageView = $this->renderView(
                'SamanCmsBundle:CmsAdmin:element/page.html.twig',
                array('page' => $page)
                );
            
            // Generate final jason responce
            return $this->getJsonResponse(true, null, $pageView);
        } catch (\Exception $ex) {
            return $this->getExceptionResponse(
                'alert.error.canNotDisplayItem', 
                $ex
                );
        }
    }    
    
    /**
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return type
     */
    public function addItemAction(Request $request)
    {
        try {
            // Get Page
            $page = $this->getCmsAdminService()->getPage(null);
            
            return $this->getCmsAdminService()->addEditItem($request, $page);
        } catch (\Exception $ex) {
            return $this->getExceptionResponse(
                'alert.error.canNotAddItem', 
                $ex
                );
        }        
    }

    /**
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param type $itemId
     * @return type
     */
    public function editItemAction(Request $request, $itemId)
    {
        try {
            return $this->getCmsAdminService()->addEditItem($request, $itemId);
        } catch (\Exception $ex) {
            return $this->getExceptionResponse(
                'alert.error.canNotDeleteItem', 
                $ex
                );
        }        
    }
    
    /**
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param type $itemId
     * @return type
     */
    public function deleteItemAction(Request $request, $itemId)
    {
        try {
            // Get a Page based on its ID ($itemId)
            $page = $this->getCmsAdminService()->getPage($itemId);
            
            // Delete this page
            $this->getBaseService()->deleteEntity($page);
            
            // Add success message in the FlashBag
            $this->addFlashBag('success', 'alert.success.itemRemoved');
            
            return $this->getBaseService()->getJsonResponse(true);
        } catch (\Exception $ex) {
            return $this->getExceptionResponse(
                'alert.error.canNotDeleteItem', 
                $ex
                );
        }   
    }
    
    /**
     * 
     * @return \Saman\CmsBundle\Service\CmsAdminService
     */
    private function getCmsAdminService()
    {
        return $this->getService('saman_cms.cms.admin');
    }
}