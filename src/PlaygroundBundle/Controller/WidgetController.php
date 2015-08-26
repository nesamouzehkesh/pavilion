<?php

namespace PlaygroundBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class WidgetController extends Controller
{
    public function listWidgetTypesAction()
    {
        $widgetTypes = array('linking-widget', 'welcome-widget');
        
        return $this->render(
            '::Playground/Widget/types.html.twig', 
            array(
                'widgetTypes' => $widgetTypes,
                )
            );
    }
    
    public function listWidgetsAction($widgetType)
    {
        $widgets = $this->getWidgetService()->getAllWidgets($widgetType);
        
        return $this->render(
            '::Playground/Widget/widgets.html.twig', 
            array(
                'widgets' => $widgets,
                'widgetType' => $widgetType
                )
            );
    }
    
    public function showWidgetAction($widgetType, $widgetId)
    {
        $widgetView = $this->getWidgetService()->getWidgetView($widgetType, $widgetId);
        
        return $this->render(
            '::Playground/Widget/widget.html.twig', 
            array(
                'widgetView' => $widgetView,
                'widgetType' => $widgetType
                )
            );
    }
    
    /**
     * 
     * @return \PlaygroundBundle\Services\DashboardWidgetService
     */
    private function getWidgetService()
    {
        return $this->get('saman_dashboard.widget_service');
    }
}
