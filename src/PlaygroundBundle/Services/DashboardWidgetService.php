<?php

namespace PlaygroundBundle\Services;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use Library\Components\ServiceBag;
use PlaygroundBundle\Library\Interfaces\DashboardWidgetInterface;
use Library\Interfaces\ServiceAwareEntityInterface;

/**
 * Description of DashboardWidgetService
 *
 * @author saman
 */
class DashboardWidgetService
{
    /** @var ServiceBag|DashboardWidgetInterface[] */
    protected $widgets;
 
    /**
     * Defaults
     */
    public function __construct()
    {
        $this->widgets = new ServiceBag();
    }
    
    /**
     * Register a Widget with the service
     * 
     * @param \PlaygroundBundle\Library\Interfaces\DashboardWidgetInterface $widget
     * @param type $widgetType
     */
    public function addWidgetService(DashboardWidgetInterface $widget, $widgetType)
    {
        $this->widgets->set($widgetType, $widget);
    }
 
    /**
     * Fetch a widget by ID
     * @param string $widgetType
     * @return DashboardWidgetInterface
     */
    public function getWidgetService($widgetType)
    {
        return $this->widgets->get($widgetType);
    }
    
    /**
     * 
     * @param type $widgetType
     * @param type $widgetForm
     * @return type
     */
    public function createWidget($widgetType, $widgetForm)
    {
        return $this->getWidgetService($widgetType)
            ->createWidget($widgetForm);
    }
    
    /**
     * 
     * @param type $widgetType
     * @return type
     */
    public function getAllWidgets($widgetType)
    {
        return $this->getWidgetService($widgetType)
            ->getAllWidgets();
    }

    /**
     * 
     * @param type $widgetType
     * @param type $widgetId
     * @return type
     */
    public function findWidget($widgetType, $widgetId)
    {
        return $this->getWidgetService($widgetType)
            ->findWidget($widgetId);
    }
    
    /**
     * 
     * @param \PlaygroundBundle\Library\Interfaces\ServiceAwareEntityInterface $widget
     * @return type
     */
    public function getWidgetData(ServiceAwareEntityInterface $widget)
    {
        return $this->getWidgetService($widget->getServiceId())
            ->setWidget($widget)
            ->getWidgetData();
    }
    
    /**
     * 
     * @param \PlaygroundBundle\Library\Interfaces\ServiceAwareEntityInterface $widget
     * @return type
     */
    public function getWidgetView($widgetType, $widgetId)
    {
        $widget = $this->findWidget($widgetType, $widgetId);
        
        return $this->getWidgetService($widget->getServiceId())
            ->setWidget($widget)
            ->getWidgetView();
    }
}