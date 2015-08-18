<?php

namespace Saman\CmsBundle\Service;

use Symfony\Component\HttpFoundation\Request;
use Saman\Library\Service\Helper;
use Saman\Library\Map\EntityMap;
use Saman\CmsBundle\Entity\Page;
use Saman\Library\Map\ViewMap;
use Saman\AppearanceBundle\Entity\Widget;

class CmsService
{
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
     * Display page
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param type $pageUrl
     * @return type
     */
    public function displayPage(Request $request, $pageUrl)
    {
        $page = $this->loadPage($pageUrl, $request);
        // Check if this $item exist
        if (null === $page) {
            throw new \Exception('Page does not exist');
        }
        
        if (null === $page['themeContent']) {
            $themeContent = '::widgetPage::';
            $themeStaticContents = array();
        } else {
            $themeContent = $page['themeContent'];
            $themeStaticContents = $page['themeStaticContent'];
        }

        $searchKeys = array(Widget::PLACEHOLDER_WIDGET_PAGE);
        $replaceValues = array($page['content']);
        foreach ($themeStaticContents as $key => $themeStaticContent) {
            $searchKeys[] = sprintf(':widget_%d:', $key);
            $replaceValues[] = $themeStaticContent;
        }
        
        $content = str_replace($searchKeys, $replaceValues, $themeContent);
       
        $response = $this->helper->render(
            ViewMap::CMS_INDEX,
            array('content' => $content)
            );
        
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
        $item = $this->loadPage($pageUrl, $request);

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
    
    /**
     * Get Item based on its ID. If ID is null then create a new Item
     * 
     * @param type $pageUrl
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return type
     */
    private function loadPage($pageUrl, Request $request = null)
    {
        $item = $this->helper->getRepository(EntityMap::CMS_PAGE)
            ->loadPage($pageUrl, $request);
        
        return $item;
    }    
}