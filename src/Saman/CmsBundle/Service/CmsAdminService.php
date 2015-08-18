<?php

namespace Saman\CmsBundle\Service;

use Symfony\Component\HttpFoundation\Request;
use Saman\Library\Service\Helper;
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
     * @var Helper $helper
     */
    protected $helper;
    
    /**
     * 
     * @param \Saman\Library\Service\Helper $helper
     * @param type $parameters
     */
    public function __construct(
        Helper $helper, 
        $parameters
        ) 
    {
        $this->helper = $helper;
        $this->helper->setParametrs($parameters);
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
     * @param type $itemId
     * @return type
     */
    public function getItem($itemId = null)
    {
        $item = null;
        if (null === $itemId) {
            $item = $this->createNewItem();
        } else {
            $item = $this->helper->getRepository(self::ENTITY_ITEM)
                ->getItem($itemId);
        }        
        
        return $item;
    }
    
    /**
     * Update Item
     * 
     * @param Page $item
     */
    public function updateItem($item)
    {
        $item->setModifiedTime();
        $this->helper->saveEntity($item);
        
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
                return $this->helper->getExceptionResponseNotFound($itemId);
            }

            if ($this->helper->deleteEntity($item)) {
                return $this->helper->getJsonResponse(
                    true,
                    array(
                        'alert.success.itemHasBeenRemoved', 
                        array('%id%' => $itemId))
                    );
            } else {
                return $this->helper->getExceptionResponse(
                    'alert.success.itemHasNotBeenRemoved', 
                    array('%id%' => $itemId)
                    );
            }
        }
    }
    
    /**
     * Add or Edit Item
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param type $itemId
     * @return type
     */
    public function addEditItem(Request $request, $itemId = null)
    {
        /** @var Page */
        $page = $this->getItem($itemId);
        // Check if this $item exist
        if (null === $page) {
            return $this->helper->getExceptionResponseNotFound($itemId);
        }
        
        /** @var PageForm */
        $pageFormType = new PageFormType(
            $this->helper, 
            $page,
            $this->helper->getParameter('page')
            );
        $pageForm = $this->helper->createForm($request, $pageFormType, $page);
        // Handling Form Submissions and validation
        $pageForm->handleRequest($request);
        if ($pageForm->isSubmitted()) {
            if ($pageForm->isValid()) {
                
                $this->updateItem($page);
                
                return $this->helper->getJsonResponse(true);
            }
        }    
        $itemFormView = $this->helper->renderView(
            self::VIEW_ITEM_ADD_EDIT,
            array(
                'form' => $pageForm->createView(),
                'page' => $page
                )
            );
        
        return $this->helper->getJsonResponse(true, null, $itemFormView);        
    }
    
    /**
     * Get Items
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param type $loadJustQuery
     * @return type
     */
    public function getItems(Request $request, $loadJustQuery = true)
    {
        $param = $this->helper->getSearchParam($request);
        $items = $this->helper->getRepository(self::ENTITY_ITEM)
            ->getItems($param, $loadJustQuery);
        
        return $items;
    }
    
    /**
     * Load item
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param int $itemId
     * @return Page
     */
    public function loadItem(Request $request, $itemId)
    {
        $item = $this->helper->getRepository(self::ENTITY_ITEM)
            ->loadItem($itemId);
        
        return $item;
    }
    
    /**
     * Display Items
     * 
     * @param \Saman\CmsBundle\Service\Request $request
     * @return type
     */
    public function displayItems(Request $request)
    {
        $itemsQuery = $this->getItems($request);
        $itemsPagination = $this->helper->paginate($request, $itemsQuery);
        
        $itemsView = $this->helper->renderView(
            self::VIEW_ITEMS_VIEW,
            array('itemsPagination' => $itemsPagination)
            );
        
        if ($request->get('headless')) {
            $response = $this->helper->getJsonResponse(true, null, $itemsView);
        } else {
            $response = $this->helper->render(
                self::VIEW_ITEMS_HOME,
                array(
                    'itemsView' => $itemsView
                    )
                );
        }        
        //$this->helper->cacheResponse($response);
        
        return $response;
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
            return $this->helper->getExceptionResponseNotFound($itemId);
        }
        
        $itemView = $this->helper->renderView(
            self::VIEW_ITEM_VIEW,
            array('item' => $item)
            );
        $response = $this->helper->getJsonResponse(true, null, $itemView);
        //$this->helper->cacheResponse($response);
        
        return $response;
    }
    
    /**
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param type $itemId
     * @return type
     */
    public function displayCacheItem(Request $request, $itemId)
    {
        $item = $this->loadItem($request, $itemId);

        // Check if this $item exist
        if (null === $item) {
            return $this->helper->getExceptionResponseNotFound($itemId);
        }
        
        // create a Response with an ETag and/or a Last-Modified header
        $response = new Response();
        //$response->setETag($article->computeETag());
        $modifyDate = $this->helper->getTime($itemId->getLastModify());
        $response->setLastModified($modifyDate);

        // Set response as public. Otherwise it will be private by default.
        $response->setPublic();

        // Check that the Response is not modified for the given Request
        if ($response->isNotModified($request)) {
            // return the 304 Response immediately
            return $response;
        }
    
        return $this->displayItem($request, $itemId);
    }    
}