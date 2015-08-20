<?php

namespace CmsBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Library\Base\BaseController;

class CmsAdminController extends BaseController
{
    /**
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return type
     */
    public function displayPagesAction(Request $request)
    {
        // Get search parameters from HTTP request
        $searchParam = $this->getAppService()
            ->getSearchParam($request);

        // Get pages based on $searchParam
        $pagesQuery = $this->getCmsAdminService()
            ->getPages($searchParam);

        // Get pagination
        $pagesPagination = $this->getAppService()
            ->paginate($request, $pagesQuery);

        // Get the view of pages list
        $pagesView = $this->renderView(
            'CmsBundle:CmsAdmin:element/pages.html.twig',
            array('pagesPagination' => $pagesPagination)
            );

        // If user use the pagination to view other pages then we just return the 
        // $pagesView as a jason response array
        if ($request->get('headless')) {
            return $this->getAppService()->getJsonResponse(true, null, $pagesView);
        } 

        return $this->render(
            'CmsBundle:CmsAdmin:index.html.twig',
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
    public function displayPageAction($itemId)
    {
        try {
            // Get Page
            $page = $this->getCmsAdminService()->getPage($itemId);
            
            // Generate the view for this page
            $pageView = $this->renderView(
                'CmsBundle:CmsAdmin:element/page.html.twig',
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
    public function addPageAction(Request $request)
    {
        try {
            // Create new Page
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
    public function editPageAction(Request $request, $itemId)
    {
        try {
            // Get Page
            $page = $this->getCmsAdminService()->getPage($itemId);
            
            return $this->getCmsAdminService()->addEditItem($request, $page);
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
    public function deletePageAction(Request $request, $itemId)
    {
        try {
            // Get a Page based on its ID ($itemId)
            $page = $this->getCmsAdminService()->getPage($itemId);
            
            // Delete this page
            $this->getAppService()->deleteEntity($page);
            
            // Add success message in the FlashBag
            $this->addFlashBag('success', 'alert.success.itemRemoved');
            
            return $this->getAppService()->getJsonResponse(true);
        } catch (\Exception $ex) {
            return $this->getExceptionResponse(
                'alert.error.canNotDeleteItem', 
                $ex
                );
        }   
    }
    
    /**
     * 
     * @return \CmsBundle\Service\CmsAdminService
     */
    private function getCmsAdminService()
    {
        return $this->getService('saman_cms.cms.admin');
    }
}