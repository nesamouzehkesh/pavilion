<?php

namespace Saman\AppearanceBundle\Service\handler;

use Saman\Library\Map\ViewMap;
use Saman\Library\Service\Helper;
use Saman\AppearanceBundle\Entity\Widget;
use Saman\AppearanceBundle\Service\Interfaces\WidgetServiceHandlerInterface;

class WidgetServiceHandlerIcon implements WidgetServiceHandlerInterface
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
     * Widget ICON css array
     * 
     * @var type 
     */
    private static $cssArray = array(
            'xxs' => array('size' => 55,  'border' => 3, 
                'text' => array('line_height' => 50, 'font_size' => 12), 
                'icon' => array('line_height' => 55, 'font_size' => 25)),
            'xs' =>  array('size' => 70,  'border' => 3, 
                'text' => array('line_height' => 70, 'font_size' => 15), 
                'icon' => array('line_height' => 74, 'font_size' => 34)),
            's' =>   array('size' => 85,  'border' => 4, 
                'text' => array('line_height' => 79, 'font_size' => 19), 
                'icon' => array('line_height' => 87, 'font_size' => 40)),
            'm' =>   array('size' => 100, 'border' => 4, 
                'text' => array('line_height' => 90, 'font_size' => 23), 
                'icon' => array('line_height' => 105,'font_size' => 43)),
            'l' =>   array('size' => 115, 'border' => 4, 
                'text' => array('line_height' => 108, 'font_size' => 28), 
                'icon' => array('line_height' => 119, 'font_size' => 60)),
            'xl' =>  array('size' => 130, 'border' => 4, 
                'text' => array('line_height' => 120, 'font_size' => 32), 
                'icon' => array('line_height' => 140, 'font_size' => 70)),
            'xxl' => array('size' => 145, 'border' => 4, 
                'text' => array('line_height' => 140, 'font_size' => 35), 
                'icon' => array('line_height' => 155, 'font_size' => 75))                
        );
    
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
            ViewMap::APPEARANCE_ADMIN_WIDGET_ICON_WEB_VIEW,
            array(
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
        $settings = $this->widget->getSettings();
        
        $size = (isset($settings['size']))? $settings['size'] : 'm';
        $type = (isset($settings['type']))? $settings['type'] : 'icon';
        $borderColor = (isset($settings['borderColor']))? $settings['borderColor'] : '#ccc';
        $color = (isset($settings['color']))? $settings['color'] : '#666';
        $background = (isset($settings['background']))? $settings['background'] : '#ddd';
        $hoverBorderColor = (isset($settings['hoverBorderColor']))? $settings['hoverBorderColor'] : '#ccc';
        $hoverColor = (isset($settings['hoverColor']))? $settings['hoverColor'] : '#666';
        $hoverBackground = (isset($settings['hoverBackground']))? $settings['hoverBackground'] : '#ddd';
        $css = self::$cssArray[$size];
        
        $cssContent = sprintf('.css-widget-%d{display:block;width:%dpx;height:%dpx;line-height:%dpx;border-radius:50%%;border:%dpx solid %s;font-size:%dpx;color:%s;text-align:center;text-decoration:none;background:%s}',
            $this->widget->getId(),
            $css['size'],
            $css['size'],
            $css[$type]['line_height'],
            $css['border'],
            $borderColor,
            $css[$type]['font_size'],
            $color,
            $background
            );
        
        $cssContent .= sprintf('.css-widget-%d:hover{border:%dpx solid %s;color:%s;text-decoration:none;background:%s}',
            $this->widget->getId(),
            $css['border'],
            $hoverBorderColor,
            $hoverColor,
            $hoverBackground
            );
        
        
        return $cssContent;
    }
}