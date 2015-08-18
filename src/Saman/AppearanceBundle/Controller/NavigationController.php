<?php

namespace Saman\AppearanceBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Saman\Library\Base\BaseController;

class NavigationController extends BaseController
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
     * @return type
     */
    public function displayNavigationsAction(Request $request)
    {
        return $this->getNavigationService()->displayNavigations($request);
    }
 
    /**
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return type
     */
    public function addNavigationAction(Request $request)
    {
        return $this->getNavigationService()->addNavigation($request);
    }

    /**
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param type $navigationId
     * @return type
     */
    public function editNavigationAction(Request $request, $navigationId)
    {
        return $this->getNavigationService()->editNavigation($request, $navigationId);
    }
    
    /**
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param type $navigationId
     * @return type
     */
    public function deleteNavigationAction(Request $request, $navigationId)
    {
        return $this->getNavigationService()->deleteNavigation($request, $navigationId);
    }
    
    /**
     * 
     * @param Request $request
     * @param type $navigationId
     * @return type
     */
    public function publishNavigationAction(Request $request, $navigationId)
    {
        $defaultThemeService = $this->getService('saman_appearance.theme');
        
        return $this->getService('saman_appearance.navigation')
            ->setThemeService($defaultThemeService)
            ->publishNavigation($request, $navigationId);
    }

    /**
     * 
     * @return type
     */
    public function getNavigationService()
    {
        $defaultLinkService = $this->getService('saman_appearance.link');
        
        return $this->getService('saman_appearance.navigation')
            ->setLinkService($defaultLinkService);
    }
}