<?php

namespace Saman\ShoppingBundle\Service;

use Symfony\Component\HttpFoundation\Request;
use Saman\Library\Service\Helper;
use Saman\Library\Map\EntityMap;
use Saman\AppearanceBundle\Entity\Theme;
use Saman\AppearanceBundle\Entity\Widget;
use Saman\ShoppingBundle\Entity\Order;

class OrderService
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
    public function displayOrders()
    {
        $user = $this->helper->getUser();
        $orders = Order::getRepository($this->helper->getEntityManager())
            ->getUserOrders($user);
        
        $content = $this->helper->renderView(
            'SamanShoppingBundle:Order:index.html.twig',
            array('orders' => $orders)
            );
        
        return $this->displayPage($content);
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
    
    /**
     * Display page
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param type $pageUrl
     * @return type
     */
    private function displayPage($content)
    {
        $id = 2;
        // Get the theme for shopping pages
        $theme = Theme::getRepository($this->helper->getEntityManager())
            ->getThemeContent($id);
        
        // Check if any theme is defined
        $themeStaticContents = array();
        if (null === $theme) {
            $themeContent = Widget::PLACEHOLDER_WIDGET_PAGE;
        } else {
            $themeContent = $theme['themeContent'];
            $themeStaticContents = $theme['themeStaticContent'];
        }

        $searchKeys = array(Widget::PLACEHOLDER_WIDGET_PAGE);
        $replaceValues = array($content);
        if (is_array($themeStaticContents)) {
            foreach ($themeStaticContents as $key => $themeStaticContent) {
                $searchKeys[] = sprintf(':widget_%d:', $key);
                $replaceValues[] = $themeStaticContent;
            }
        }
        
        $finalContent = str_replace($searchKeys, $replaceValues, $themeContent);
        $response = $this->helper->render(
            'SamanCmsBundle:Cms:index.html.twig',
            array('content' => $finalContent)
            );
        
        return $response;
    }    
}