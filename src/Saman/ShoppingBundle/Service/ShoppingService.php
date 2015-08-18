<?php

namespace Saman\ShoppingBundle\Service;

use Symfony\Component\HttpFoundation\Request;
use Saman\Library\Service\Helper;
use Saman\Library\Map\EntityMap;
use Saman\ShoppingBundle\Entity\Product;
use Saman\Library\Map\ViewMap;
use Saman\MediaBundle\Service\MediaService;
use Saman\ConfigBundle\Service\ConfigService;

class ShoppingService
{
    /**
     * Template view paramaters
     */
    const VIEW_ITEMS_HOME = ViewMap::SHOPPING_ADMIN_PAGE_ITEMS_HOME;
    const VIEW_ITEM_ADD_EDIT = ViewMap::SHOPPING_ADMIN_PAGE_ITEM_ADD_EDIT;
    const VIEW_ITEMS_VIEW = ViewMap::SHOPPING_ADMIN_PAGE_ITEMS_VIEW;
    const VIEW_ITEM_VIEW = ViewMap::SHOPPING_ADMIN_PAGE_ITEM_VIEW;
    /**
     * Main Entity
     */
    const ENTITY_ITEM = EntityMap::SHOPPING_PRODUCT;
    
    /**
     *
     * @var Helper $helper
     */
    protected $helper;
    
    /**
     * 
     * @param Helper $helper
     * @param MediaService $mediaService
     * @param ConfigService $configService
     * @param type $parameters
     */
    public function __construct(
        Helper $helper,
        MediaService $mediaService, 
        ConfigService $configService,
        $parameters
        ) 
    {
        $this->helper = $helper;
        $this->helper->setMediaService($mediaService);
        //$this->helper->setConfigService($configService);
        $this->helper->setParametrs($parameters);
    }
    
    /**
     * Create a new Item
     */
    public function createNewItem()
    {
        $item = new Product();
        
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
     * @param Product $item
     */
    public function updateItem($item)
    {
        $item->setModifiedTime();
        $this->helper->saveEntity($item);
        $this->helper->saveMedia($item);
        
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
            /** @var Product $item */
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
        /** @var Product */
        $item = $this->getItem($itemId);
        // Check if this $item exist
        if (null === $item) {
            return $this->helper->getExceptionResponseNotFound($itemId);
        }
        
        /** @var ProductForm */
        $itemForm = $this->helper->createForm($request, 'saman_shopping_product_form', $item);
        
        // Handling Form Submissions and validation
        $itemForm->handleRequest($request);
        if ($itemForm->isSubmitted()) {
            if ($itemForm->isValid()) {
                $this->updateItem($item);
                
                return $this->helper->getJsonResponse(true);
            }
        }    
        $itemFormView = $this->helper->renderView(
            self::VIEW_ITEM_ADD_EDIT,
            array('form' => $itemForm->createView())
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
     * @return Product
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