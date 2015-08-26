<?php

namespace PlaygroundBundle\Library\Components;

use AppBundle\Service\AppService;
use PlaygroundBundle\Library\Interfaces\DashboardWidgetInterface;
use PlaygroundBundle\Entity\LinkWidget;

/**
 * Description of LinkingDashboardWidget
 *
 * @author saman
 */
class LinkingDashboardWidget implements DashboardWidgetInterface
{
    /** @var BaseService $appService */
    protected $appService;
    
    /** @var LinkWidget $widget*/
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
     * @return \PlaygroundBundle\Entity\LinkWidget
     * @throws \InvalidArgumentException
     */
    public function findWidget($widgetId)
    {
        $widget = $this->getRepository()->find($widgetId);
        if (!$widget instanceof LinkWidget) {
            throw new \InvalidArgumentException(sprintf('No link widget is found for this ID: %d', $widgetId));
        }
        
        return $widget;
    }
    
    public function createWidget($widgetForm)
    {
        
    }
    
    /**
     * 
     * @param type $widget
     * @return \PlaygroundBundle\Library\Components\LinkingDashboardWidget
     */
    public function setWidget($widget)
    {
        $this->widget = $widget;
        
        return $this;
    }
    
    /**
     * 
     * @return \PlaygroundBundle\Entity\LinkWidget
     * @throws \InvalidArgumentException
     */
    public function getWidget()
    {
        if (!$this->widget instanceof LinkWidget) {
            throw new \InvalidArgumentException('No link widget is set');
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
            'url' => $widget->getUrl(),
            'title' => $widget->getTitle()
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
            '::Playground/Components/linkWidget.html.twig', 
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
        return $this->appService->getRepository('PlaygroundBundle:LinkWidget');
    }
}