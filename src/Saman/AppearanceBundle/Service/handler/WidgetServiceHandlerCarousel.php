<?php

namespace Saman\AppearanceBundle\Service\handler;

use Saman\Library\Map\ViewMap;
use Saman\Library\Service\Helper;
use Saman\AppearanceBundle\Entity\Widget;
use Saman\AppearanceBundle\Service\Interfaces\WidgetServiceHandlerInterface;

class WidgetServiceHandlerCarousel implements WidgetServiceHandlerInterface
{
    /**
     *
     * @var Helper $helper 
     */
    protected $helper;
    
    /**
     *
     * @var Widget $widget
     */
    protected $widget;
    
    /**
     * 
     * @param Widget $widget
     * @param type $dependencies
     */
    public function __construct(Widget $widget, $dependencies)
    {
        $this->helper = $dependencies['helper'];
        $this->widget = $widget;
    }
    
    /**
     * 
     * @return string
     */
    public function getWidgetStaticContent()
    {
        // Set page main content structure
        $staticContent = $this->helper->renderView(
            ViewMap::APPEARANCE_ADMIN_WIDGET_CAROUSEL_WEB_VIEW,
            array(
                'theme' => $this->widget->getTheme(),
                'widget' => $this->widget,
                )
            );
            
        return $staticContent;        
    }
    
    /**
     * Return css content of this widget
     * 
     * @return null
     */
    public function getWidgetCssContent()
    {
        return null;
    }
}