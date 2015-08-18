<?php

namespace Saman\AppearanceBundle\Service;

use Symfony\Component\HttpFoundation\Request;
use Saman\Library\Service\Helper;
use Saman\Library\Map\EntityMap;
use Saman\Library\Map\ViewMap;
use Saman\AppearanceBundle\Entity\Navigation;
use Saman\AppearanceBundle\Service\ThemeService;
use Saman\AppearanceBundle\Service\Interfaces\LinkServiceInterface;
use Saman\AppearanceBundle\Service\Interfaces\NavigationServiceInterface;
use Saman\AppearanceBundle\Entity\Widget;

class NavigationService implements NavigationServiceInterface
{
    /**
     *
     * @var Helper $helper
     */
    protected $helper;
    
    /**
     *
     * @var LinkServiceInterface $linkService 
     */
    protected $linkService;
    
    /**
     *
     * @var LinkServiceInterface $linkService 
     */
    protected $themeService;
    
    /**
     *
     * @var WidgetService $widgetService 
     */
    protected $widgetService;    
    
    /**
     * 
     * @param \Saman\Library\Service\Helper $helper
     * @param type $parameters
     */
    public function __construct(
        Helper $helper,
        $parameters
        ) 
    {
        $this->helper = $helper;
        $this->helper->setParametrs($parameters);
    }
    
    /**
     * 
     * @param LinkServiceInterface $linkService
     * @return \Saman\AppearanceBundle\Service\NavigationService
     */
    public function setLinkService(LinkServiceInterface $linkService)
    {
        $this->linkService = $linkService;
        $this->linkService->setNavigationService($this);
        
        return $this;
    }
    
    /**
     * 
     * @return type
     * @throws \Exception
     */
    public function getLinkService()
    {
        if (null === $this->linkService) {
            throw new \Exception('No Link Service is defined for this service');
        }
        
        return $this->linkService;
    }
    
    /**
     * 
     * @param ThemeService $themeService
     * @return \Saman\AppearanceBundle\Service\NavigationService
     */
    public function setThemeService(ThemeService $themeService)
    {
        $this->themeService = $themeService;
        
        return $this;
    }
    
    /**
     * 
     * @return type
     * @throws \Exception
     */
    public function getThemeService()
    {
        if (null === $this->themeService) {
            throw new \Exception('No Theme Service is defined for this service');
        }
        
        return $this->themeService;
    }     
    
    /**
     * Create a new Navigation
     */
    public function createNewNavigation()
    {
        $navigation = new Navigation();
        
        return $navigation; 
    }
    
    /**
     * Get Navigation based on its ID. If ID is null then create a new Navigation
     * 
     * @param type $navigationId
     * @return type
     */
    public function getNavigation($navigationId = null)
    {
        $navigation = null;
        if (null === $navigationId) {
            $navigation = $this->createNewNavigation();
        } else {
            $navigation = $this->getNavigationRepository()->getNavigation(intval($navigationId));
        }        
        
        return $navigation;
    }
    
    /**
     * Update Navigation
     * 
     * @param Navigation $navigation
     */
    public function updateNavigation($navigation)
    {
        $navigation->setModifiedTime();
        $this->helper->saveEntity($navigation);
        
        return true;
    }
    
    /**
     * 
     * @param type $request
     * @param type $navigationId
     */
    public function publishNavigation(Request $request, $navigationId)
    {
        /** @var Navigation $navigation */
        $navigation = $this->getNavigation($navigationId);
        // Check if this $navigation exist
        if (null === $navigation) {
            return $this->helper->getExceptionResponseNotFound($navigationId);
        }
        
        $flushIsRequired = false;
        $themes = $this->getThemeService()->loadAllThemes();
        $dependencies = array(
            'helper' => $this->helper,
            'navigationService' => $this
            );
        
        // For each theme update its widget static content related to menus
        foreach ($themes as $theme) {
            // Get all menu type widgets of this theme
            $widgets = $theme->getWidgetsByType(Widget::WIDGET_TYPE_MENU);
            $updateIsRequired = false;
            // For each widget update its static content based on this navigation
            foreach ($widgets as $widget) {
                $widgetMenuId = (int) $widget->getSetting(Widget::WIDGET_TYPE_MENU_SETTINGS_MENU);
                if ($navigationId === $widgetMenuId) {
                    $updateIsRequired = true;
                    $flushIsRequired = true;
                    $staticContent = $this->getWidgetService()
                        ->getWidgetStaticContent($widget, $dependencies);
                    
                    $theme->setStaticContent($staticContent, $widget->getId());
                }
            }
            if ($updateIsRequired) {
                $this->updateTheme($theme, false);
            }            
        }
        
        if ($flushIsRequired) {
            $this->helper->flushEntityManager();
        }
        
        return $this->helper->getJsonResponse(true, 'module.appearance.alert.success.navigationIsPublished', null);
    }
    
    /**
     * Delete an Navigation
     * 
     * @param int $navigationId
     * @return type
     */
    public function deleteNavigation($request, $navigationId)
    {
        if (!$request->isXmlHttpRequest() || !$request->isMethod('POST')) {
            return;
        }
        /** @var Navigation $navigation */
        $navigation = $this->getNavigation($navigationId);

        // Check if this $navigation exist
        if (null === $navigation) {
            return $this->helper->getExceptionResponseNotFound($navigationId);
        }

        if ($this->helper->deleteEntity($navigation)) {
            return $this->helper->getJsonResponse(
                true,
                array(
                    'alert.success.itemHasBeenRemoved', 
                    array('%id%' => $navigationId))
                );
        } else {
            return $this->helper->getExceptionResponse(
                'alert.success.itemHasNotBeenRemoved', 
                array('%id%' => $navigationId)
                );
        }
    }
    
    /**
     * Add or Edit Navigation
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param type $navigationId
     * @return type
     */
    public function addNavigation(Request $request)
    {
        /** @var Navigation */
        $navigation = $this->getNavigation(null);
        
        /** @var NavigationForm */
        $navigationForm = $this->helper->createForm(
            $request, 
            'saman_appearance_navigation_form', 
            $navigation
            );
        
        // Handling Form Submissions and validation
        $navigationForm->handleRequest($request);
        if ($navigationForm->isSubmitted()) {
            if ($navigationForm->isValid()) {
                $this->updateNavigation($navigation);
                
                return $this->helper->getJsonResponse(true);
            }
        }    
        $navigationFormView = $this->helper->renderView(
            ViewMap::APPEARANCE_ADMIN_NAVIGATION_ADD,
            array('form' => $navigationForm->createView())
            );
        
        return $this->helper->getJsonResponse(true, null, $navigationFormView);        
    }
    
    /**
     * 
     * @param Request $request
     * @param type $navigationId
     * @return type
     */
    public function editNavigation(Request $request, $navigationId)
    {
        /** @var Navigation */
        $navigation = $this->loadNavigation($navigationId);
        // Check if this $navigation exist
        if (null === $navigation) {
            return $this->helper->getExceptionResponseNotFound($navigationId);
        }
        
        /** @var NavigationForm */
        $navigationForm = $this->helper->createForm(
            $request, 
            'saman_appearance_navigation_form', 
            $navigation);
        
        // Handling Form Submissions and validation
        $navigationForm->handleRequest($request);
        if ($navigationForm->isSubmitted()) {
            if ($navigationForm->isValid()) {
                $this->updateNavigation($navigation);
                
                return $this->helper->getJsonResponse(true);
            }
        }
        
        $navigationEditView = $this->helper->renderView(
            ViewMap::APPEARANCE_ADMIN_NAVIGATION_EDIT,
            array(
                'form' => $navigationForm->createView(),
                'navigation' => $navigation,
                'linksView' => $this->getLinkService()->getLinksView($navigation)
                )
            );
        
        return $this->helper->getJsonResponse(true, null, $navigationEditView);        
    }
    
    /**
     * Get Navigations
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param type $loadJustQuery
     * @return type
     */
    public function getNavigations(Request $request, $loadJustQuery = true)
    {
        $param = $this->helper->getSearchParam($request);
        $navigations = $this->getNavigationRepository()->getNavigations($param, $loadJustQuery);
        
        return $navigations;
    }
    
    /**
     * 
     * @param type $navigationsId
     * @return type
     */
    public function getNavigationsById($navigationsId)
    {
        $navigations = $this->getNavigationRepository()->getNavigationsById($navigationsId);
        
        return $navigations;
    }
    
    /**
     * Load navigation
     * 
     * @param int $navigationId
     * @return Navigation
     */
    public function loadNavigation($navigationId)
    {
        $navigation = $this->getNavigationRepository()->loadNavigation(intval($navigationId));
        
        return $navigation;
    }
    
    /**
     * Display Navigations
     * 
     * @param \Saman\CmsBundle\Service\Request $request
     * @return type
     */
    public function displayNavigations(Request $request)
    {
        $navigationsQuery = $this->getNavigations($request);
        $navigationsPagination = $this->helper->paginate($request, $navigationsQuery);
        
        $navigationsView = $this->helper->renderView(
            ViewMap::APPEARANCE_ADMIN_NAVIGATIONS_VIEW,
            array('navigationsPagination' => $navigationsPagination)
            );
        
        if ($request->get('headless')) {
            $response = $this->helper->getJsonResponse(true, null, $navigationsView);
        } else {
            $response = $this->helper->render(
                ViewMap::APPEARANCE_ADMIN_NAVIGATIONS_HOME,
                array(
                    'navigationsView' => $navigationsView
                    )
                );
        }        
        
        return $response;
    }
    
    /**
     * Display Navigation
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param type $navigationId
     * @return type
     */
    public function displayNavigation(Request $request, $navigationId)
    {
        $navigation = $this->loadNavigation($request, $navigationId);

        // Check if this $navigation exist
        if (null === $navigation) {
            return $this->helper->getExceptionResponseNotFound($navigationId);
        }
        
        $navigationView = $this->helper->renderView(
            ViewMap::APPEARANCE_ADMIN_NAVIGATION_VIEW,
            array('navigation' => $navigation)
            );
        $response = $this->helper->getJsonResponse(true, null, $navigationView);
        
        return $response;
    }
    
    /**
     * Delete an Link
     * 
     * @param int $linkId
     * @return type
     */
    public function deleteLink(Request $request, $linkId)
    {
        return $this->getLinkService()->deleteLink($request, $linkId);
    }
    
    /**
     * Add Edit link
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param type $navigationId
     * @param type $linkId
     * @return type
     */
    public function addEditLink(Request $request, $navigationId, $linkId = null)
    {
        $navigation = $this->getNavigation($navigationId);
        
        return $this->getLinkService()->addEditLink($request, $navigation, $linkId);
    }    
    
    /**
     * 
     * @param Request $request
     * @param type $navigationId
     * @return type
     */
    public function sortLinks(Request $request, $navigationId)
    {
        $navigation = $this->getNavigation($navigationId);
        
        return $this->getLinkService()->sortLinks($request, $navigation);
    }
       
    /**
     * Get Navigation Repository
     * 
     * @return type
     */
    protected function getNavigationRepository()
    {
        return $this->helper->getRepository(EntityMap::APPEARANCE_NAVIGATION);
    }
}