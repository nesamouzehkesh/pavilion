<?php

namespace Saman\AppearanceBundle\Service\Interfaces;

interface WidgetServiceHandlerInterface
{
    /**
     * Return static content of this widget
     */
    public function getWidgetStaticContent();
    
    /**
     * Return css content of this widget
     */
    public function getWidgetCssContent();
}