<?php

namespace Saman\AppearanceBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Saman\Library\Base\BaseController;

class ThemeController extends BaseController
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
     * @param Request $request
     * @param type $themeId
     */
    public function publishThemeAction(Request $request, $themeId)
    {
        $defaultWidgetService = $this->getService('saman_appearance.widget');
        $defaultNavigationService = $this->getService('saman_appearance.navigation');
        $cacheManagerService = $this->getService('liip_imagine.cache.manager');
        
        return $this->getThemeService()
            ->setNavigationService($defaultNavigationService)    
            ->setWidgetService($defaultWidgetService)
            ->setCacheManagerService($cacheManagerService)
            ->publishTheme($request, $themeId);
    }

    /**
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return type
     */
    public function displayThemesAction(Request $request)
    {
        return $this->getThemeService()->displayThemes($request);
    }
    
    /**
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param type $themeId
     * @return type
     */
    public function displayThemeAction(Request $request, $themeId)
    {
        return $this->getThemeService()->displayTheme($request, $themeId);
    }    
    
    /**
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return type
     */
    public function addThemeAction(Request $request)
    {
        return $this->getThemeService()->addTheme($request);
    }

    /**
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param type $themeId
     * @return type
     */
    public function editThemeAction(Request $request, $themeId)
    {
        return $this->getThemeService()->editTheme($request, $themeId);
    }
    
    /**
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param type $themeId
     * @return type
     */
    public function deleteThemeAction(Request $request, $themeId)
    {
        return $this->getThemeService()->deleteTheme($request, $themeId);
    }

    /**
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param type $themeId
     * @return type
     */
    public function displayThemeStructureAction(Request $request)
    {
        return $this->getThemeService()->displayThemeStructure($request);
    }
    
    /**
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param type $themeId
     * @return type
     */
    public function addThemeRowAction(Request $request, $themeId)
    {
        return $this->getThemeService()->addThemeRow($request, $themeId);
    }
    
    /**
     * 
     * @param Request $request
     * @param type $themeId
     * @param type $rowId
     * @return type
     */
    public function editThemeRowAction(Request $request, $themeId, $rowId)
    {
        return $this->getThemeService()->editThemeRow($request, $themeId, $rowId);
    }
    
    /**
     * 
     * @param Request $request
     * @param type $themeId
     * @param type $rowId
     * @return type
     */
    public function deleteThemeRowAction(Request $request, $themeId, $rowId)
    {
        return $this->getThemeService()->deleteThemeRow($request, $themeId, $rowId);
    }
    
    /**
     * 
     * @param Request $request
     * @param type $themeId
     * @param type $parentRowId
     * @param type $parentColumnId
     * @return type
     */
    public function sortRowsAction(Request $request, $themeId, $parentRowId, $parentColumnId)
    {
        return $this->getThemeService()->sortRows($request, $themeId, $parentRowId, $parentColumnId);
    }
    
    /**
     * 
     * @param Request $request
     */
    public function setStructureTypeAction(Request $request)
    {
        return $this->getThemeService()->setStructureType($request);
    }
    
    /**
     * 
     * @return type
     */
    private function getThemeService()
    {
        return $this->getService('saman_appearance.theme');
    }
}