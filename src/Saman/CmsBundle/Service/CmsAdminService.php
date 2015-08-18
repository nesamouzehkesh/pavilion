<?php

namespace Saman\CmsBundle\Service;

use Symfony\Component\HttpFoundation\Request;
use Saman\Library\Service\BaseService;
use Saman\Library\Map\EntityMap;
use Saman\CmsBundle\Entity\Page;
use Saman\Library\Map\ViewMap;
use Saman\CmsBundle\Form\PageFormType;

class CmsAdminService
{
    /**
     * Template view paramaters
     */
    const VIEW_ITEMS_HOME = ViewMap::CMS_ADMIN_PAGE_ITEMS_HOME;
    const VIEW_ITEM_ADD_EDIT = ViewMap::CMS_ADMIN_PAGE_ITEM_ADD_EDIT;
    const VIEW_ITEMS_VIEW = ViewMap::CMS_ADMIN_PAGE_ITEMS_VIEW;
    const VIEW_ITEM_VIEW = ViewMap::CMS_ADMIN_PAGE_ITEM_VIEW;
    /**
     * Main Entity
     */
    const ENTITY_ITEM = EntityMap::CMS_PAGE;
    
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
     * Create a new Item
     */
    public function createNewItem()
    {
        $item = new Page();
        
        return $item; 
    }
    
    /**
     * Get Item based on its ID. If ID is null then create a new Item
     * 
     * @param int $pageId
     * @return type
     */
    public function getPage($pageId = null)
    {
        if (null === $pageId) {
            return $this->createNewItem();
        }
        
        // Get Page form repository
        $page = Page::getRepository($this->baseService->getEntityManager())
            ->getPage($pageId);

        // Check if page is found
        if (!$page instanceof Page) {
            throw $this->baseService
                ->createVisibleHttpException('alert.error.noItemFound');
        }

        return $page;
    }
    
    /**
     * 
     * @param type $param
     * @param type $loadJustQuery
     * @return type
     */
    public function getPages($param = null, $loadJustQuery = true)
    {
        return Page::getRepository($this->baseService->getEntityManager())
            ->getPages($param, $loadJustQuery);
    }
    
    /**
     * Update Item
     * 
     * @param Page $item
     */
    public function updateItem($item)
    {
        $item->setModifiedTime();
        $this->baseService->saveEntity($item);
        
        return true;
    }
    
    /**
     * Delete an Item
     * 
     * @param int $itemId
     * @return type
     */
    public function deleteItem($request, $itemId)
    {
        if ($request->isXmlHttpRequest() && $request->isMethod('POST')) {
            /** @var Page $item */
            $item = $this->getItem($itemId);

            // Check if this $item exist
            if (null === $item) {
                return $this->baseService->getExceptionResponseNotFound($itemId);
            }

            if ($this->baseService->deleteEntity($item)) {
                return $this->baseService->getJsonResponse(
                    true,
                    array(
                        'alert.success.itemHasBeenRemoved', 
                        array('%id%' => $itemId))
                    );
            } else {
                return $this->baseService->getExceptionResponse(
                    'alert.success.itemHasNotBeenRemoved', 
                    array('%id%' => $itemId)
                    );
            }
        }
    }
    
    /**
     * Add or Edit Item
     * 
     * @param Request $request
     * @param Page $page
     * @return type
     */
    public function addEditItem(Request $request, Page $page)
    {
        /** @var PageForm */
        $pageFormType = new PageFormType(
            $this->baseService, 
            $page,
            $this->baseService->getParameter('page')
            );
        $pageForm = $this->baseService->createForm($request, $pageFormType, $page);
        // Handling Form Submissions and validation
        $pageForm->handleRequest($request);
        if ($pageForm->isSubmitted()) {
            if ($pageForm->isValid()) {
                
                $this->updateItem($page);
                
                return $this->baseService->getJsonResponse(true);
            }
        }    
        $itemFormView = $this->baseService->renderView(
            self::VIEW_ITEM_ADD_EDIT,
            array(
                'form' => $pageForm->createView(),
                'page' => $page
                )
            );
        
        return $this->baseService->getJsonResponse(true, null, $itemFormView);        
    }
    
    /**
     * Display Item
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param type $itemId
     * @return type
     */
    public function displayItem(Request $request, $itemId)
    {
        $item = $this->loadItem($request, $itemId);

        // Check if this $item exist
        if (null === $item) {
            return $this->baseService->getExceptionResponseNotFound($itemId);
        }
        
        $itemView = $this->baseService->renderView(
            self::VIEW_ITEM_VIEW,
            array('item' => $item)
            );
        $response = $this->baseService->getJsonResponse(true, null, $itemView);
        //$this->baseService->cacheResponse($response);
        
        return $response;
    }
}