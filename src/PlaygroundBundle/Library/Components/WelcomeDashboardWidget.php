<?php

namespace PlaygroundBundle\Library\Components;

use AppBundle\Service\AppService;
use PlaygroundBundle\Library\Interfaces\DashboardWidgetInterface;
use PlaygroundBundle\Entity\WelcomeWidget;

/**
 * Description of LinkingDashboardWidget
 *
 * @author saman
 */
class WelcomeDashboardWidget implements DashboardWidgetInterface
{
    /** @var BaseService $appService */
    protected $appService;
    
    /** @var WelcomeWidget $widget */
    protected $widget;
    
    /**
     * Dependency Injection
     * @param \Library\Bases\BaseService $appService
     */
    public function __construct(AppService $appService)
    {
        $this->appService = $appService;
    }
    
    /**
     * 
     * @return type
     */
    public function getAllWidgets()
    {
        return $this->getRepository()->findAll();    
    }
    
    /**
     * 
     * @param type $widgetId
     * @return \PlaygroundBundle\Entity\WelcomeWidget
     * @throws \InvalidArgumentException
     */
    public function findWidget($widgetId)
    {
        $widget = $this->getRepository()->find($widgetId);
        if (!$widget instanceof WelcomeWidget) {
            throw new \InvalidArgumentException(sprintf('No welcome widget is found for this ID: %d', $widgetId));
        }
        
        return $widget;
    }
    
    public function createWidget($widgetForm)
    {
        
    }
 
    /**
     * 
     * @param type $widget
     * @return \PlaygroundBundle\Library\Components\WelcomeDashboardWidget
     */
    public function setWidget($widget)
    {
        $this->widget = $widget;
        
        return $this;
    }
    
    /**
     * 
     * @return \PlaygroundBundle\Entity\WelcomeWidget
     * @throws \InvalidArgumentException
     */
    public function getWidget()
    {
        if (!$this->widget instanceof WelcomeWidget) {
            throw new \InvalidArgumentException('No welcome widget is set');
        }
        
        return $this->widget;
    }
    
    /**
     * 
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getWidgetData()
    {
        $widget = $this->getWidget();
        $data = array(
            'title' => $widget->getTitle(),
            'content' => $widget->getContent()
            );
        
        return $data;
    }
    
    /**
     * 
     * @return type
     */
    public function getWidgetView()
    {
        $widget = $this->getWidget();
        
        return $this->appService->renderView(
            '::Playground/Components/welcomeWidget.html.twig', 
            array(
                'widget' => $widget
                )
            );
    }
    
    /**
     * 
     * @return type
     */
    private function getRepository()
    {
        return $this->appService->getRepository('PlaygroundBundle:WelcomeWidget');
    }    
}