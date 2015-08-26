<?php

namespace PlaygroundBundle\Library\Interfaces;

/**
 * Description of DashboardWidgetService
 *
 * @author saman
 */
interface DashboardWidgetInterface
{
    public function setWidget($widget);
    public function getAllWidgets();
    public function findWidget($widgetId);
    public function createWidget($widgetForm);
    public function getWidgetData();
    public function getWidgetView();
}
