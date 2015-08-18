<?php

namespace Saman\AppearanceBundle\Service\handler;

use Saman\Library\Map\ViewMap;
use Saman\Library\Service\Helper;
use Saman\AppearanceBundle\Entity\Widget;
use Saman\AppearanceBundle\Service\Interfaces\WidgetServiceHandlerInterface;
use Saman\AppearanceBundle\Service\NavigationService;

class WidgetServiceHandlerMenu implements WidgetServiceHandlerInterface
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
     * @var NavigationService $navigationService 
     */
    protected $navigationService;
    
    /**
     * 
     * @param Widget $widget
     * @param type $dependencies
     */
    public function __construct(Widget $widget, $dependencies)
    {
        $this->helper = $dependencies['helper'];
        $this->navigationService = $dependencies['navigationService'];
        $this->widget = $widget;
    }
    
    /**
     * 
     * @return string
     */
    public function getWidgetStaticContent()
    {
        $navigationId = (int) $this->widget->getSetting(Widget::WIDGET_TYPE_MENU_SETTINGS_MENU);
        // Load navigation from DB and put it in the cache
        $navigation = $this->navigationService->loadNavigation($navigationId);
        
        // Get appropriate template based on menuType (e.g. Simple menu)
        switch ($this->widget->getSetting('menuType')) {
            case 'simple':
                $template = ViewMap::APPEARANCE_ADMIN_WIDGET_MENU_WEB_SIMPLE_VIEW;
                break;
            default :
                $template = ViewMap::APPEARANCE_ADMIN_WIDGET_MENU_WEB_BOOTSTRAP_VIEW;
                break;
        }
                
        // Set page main content structure
        $staticContent = $this->helper->renderView(
            $template,
            array(
                'theme' => $this->widget->getTheme(),
                'widget' => $this->widget,
                'navigation' => $navigation
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
        $cssContent = null;
        $settings = $this->widget->getSettings();
        switch ($this->widget->getSetting('menuType')) {
            case 'simple':
                $color = (isset($settings['textColor']))? $settings['textColor'] : '#000';
                $hoverColor = (isset($settings['textHoverColor']))? $settings['textHoverColor'] : '#000';

                $cssContent = sprintf('.css-widget-%d{color:%s;text-decoration:none;}',
                    $this->widget->getId(),
                    $color
                    );

                $cssContent .= sprintf('.css-widget-%d:hover{color:%s;text-decoration:none;}',
                    $this->widget->getId(),
                    $hoverColor
                    );
                
                break;
        }
        
        return $cssContent;
    }
}