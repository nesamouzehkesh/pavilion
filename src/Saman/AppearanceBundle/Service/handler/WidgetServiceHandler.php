<?php

namespace Saman\AppearanceBundle\Service\handler;

use Saman\Library\Service\Helper;
use Saman\AppearanceBundle\Entity\Widget;
use Saman\AppearanceBundle\Service\Interfaces\WidgetServiceHandlerInterface;

class WidgetServiceHandler implements WidgetServiceHandlerInterface
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
     * General get widget static content. 
     * 
     * @return string
     */
    public function getWidgetStaticContent()
    {
        $content = null;
        $type = $this->widget->getType();
        $widgets = $this->helper->getParameter('widgets');
        if (array_key_exists($type, $widgets) && 'static' === $widgets[$type]['type'] && array_key_exists('content', $widgets[$type])) {
            
            $settings = $this->widget->getSettings();
            $search = explode(',', ':' . implode(':,:', array_keys($settings)) . ':');
            
            $content = str_replace(
                $search, 
                $settings, 
                $widgets[$type]['content']
                );
        }
        
        return $content;
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