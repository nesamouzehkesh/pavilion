<?php

namespace Saman\AppearanceBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Saman\Library\Base\BaseController;

class WidgetController extends BaseController
{
    /**
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return type
     */
    public function indexAction(Request $request)
    {
    }
    
    /**
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param type $themeId
     * @return type
     */
    public function addThemeWidgetAction(
        Request $request, $themeId, $parentRowId, $parentColumnId)
    {
        return $this->getWidgetService()
            ->addThemeWidget($request, $themeId, $parentRowId, $parentColumnId);
    }
    
    /**
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param type $themeId
     * @return type
     */
    public function editThemeWidgetAction(Request $request, $themeId)
    {
        return $this->getWidgetService()->editThemeWidget($request, $themeId);
    }
    
    /**
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param type $themeId
     * @param type $widgetId
     * @return type
     */
    public function deleteThemeWidgetAction(
        Request $request, $themeId, $widgetId)
    {
        return $this->getWidgetService()
            ->deleteThemeWidget($request, $themeId, $widgetId);
    }
    
    /**
     * 
     * @return type
     */
    private function getWidgetService()
    {
        $defaultThemeService = $this->getService('saman_appearance.theme');
        
        return $this->getService('saman_appearance.widget')
            ->setThemeService($defaultThemeService);
    }
}