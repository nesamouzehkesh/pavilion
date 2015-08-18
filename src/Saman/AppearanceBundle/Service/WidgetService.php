<?php

namespace Saman\AppearanceBundle\Service;

use Symfony\Component\HttpFoundation\Request;
use Saman\Library\Service\Helper;
use Saman\Library\Map\EntityMap;
use Saman\AppearanceBundle\Entity\Widget;
use Saman\AppearanceBundle\Entity\Theme;
use Saman\Library\Map\ViewMap;
use Saman\MediaBundle\Service\MediaService;
use Saman\AppearanceBundle\Form\ThemeSettingForm;
use Saman\AppearanceBundle\Form\WidgetSettingForm;
use Saman\AppearanceBundle\Form\WidgetForm;

class WidgetService
{
    /**
     *
     * @var Helper $helper
     */
    protected $helper;
    
    /**
     *
     * @var ThemeService $themeService
     */
    protected $themeService;
    
    /**
     *
     * @var type 
     */
    protected $handlerDependencies = array();

    /**
     * 
     * @param Helper $helper
     * @param \Saman\AppearanceBundle\Service\MediaService $mediaService
     * @param type $parameters
     */
    public function __construct(
        Helper $helper, 
        MediaService $mediaService, 
        $parameters
        ) 
    {
        $this->helper = $helper;
        $this->helper->setMediaService($mediaService);
        $this->helper->setParametrs($parameters);
    }
    
    /**
     * Service sub handelers Dependencies
     * 
     * @param type $handlerDependencies
     */
    public function setHandlerDependencies($handlerDependencies)
    {
        $this->handlerDependencies = $handlerDependencies;
        
        return $this;
    }
    
    /**
     * 
     * @param \Saman\AppearanceBundle\Service\ThemeService $themeService
     * @return \Saman\AppearanceBundle\Service\WidgetService
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
            throw new \Exception('No theme Service is defined for this service');
        }
        
        return $this->themeService;
    }    
    
    /**
     * Create a new Widget
     */
    public function createNewWidget()
    {
        $widget = new Widget();
        
        return $widget; 
    }
    
    /**
     * Get Widget based on its ID. If ID is null then create a new Widget
     * 
     * @param int $widgetId
     * @return Widget
     */
    public function getWidget($widgetId = null)
    {
        $widget = null;
        if (null === $widgetId) {
            $widget = $this->createNewWidget();
        } else {
            $widget = $this->getWidgetRepository()->getWidget($widgetId);
            $widgetStructure = $this->getWidgetStructure($widget->getType());
            $widget->setWidgetStructure($widgetStructure);
        }        
        
        return $widget;
    }
    
    /**
     * Update $widget
     * 
     * @param Widget $widget
     */
    public function updateWidget($widget)
    {
        $widget->setModifiedTime();
        $this->helper->saveEntity($widget);
        
        return true;
    }    
    
    /**
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param type $themeId
     * @return type
     */
    public function addThemeWidget(Request $request, $themeId, $parentRowId, $parentColumnId)
    {
        /** @var Theme $theme */
        $theme = $this->getThemeService()->getTheme($themeId);
        if (null === $theme) {
            return $this->helper->getExceptionResponseNotFound($themeId);
        }
        
        $widget = $this->getWidget();
        /** @var ThemeSettingForm $themeSettingForm */
        $widgetFormType = new WidgetForm($this->getWidgetStructure());
        
        /** @var ThemeForm */
        $widgetForm = $this->helper->createForm($request, $widgetFormType, $widget);
        // Handling Form Submissions and validation
        $widgetForm->handleRequest($request);
        if ($widgetForm->isSubmitted()) {
            if ($widgetForm->isValid()) {
                // Get and then set widget default settings
                $settings = $this->getWidgetDefaultSettings($widget->getType());
                $widget->setSettings($settings);
                
                // Set widget parent row and column ID
                $widget->setRowId($parentRowId);
                $widget->setColumnId($parentColumnId);
                $widget->setTheme($theme);
                $this->updateWidget($widget);

                // TODO: Validating the wedgit
                $theme->addWidget($widget);
                $this->getThemeService()->updateTheme($theme);
                
                return $this->helper->getJsonResponse(true);
            }
        }        
        
        $themeEditView = $this->helper->renderView(
            ViewMap::APPEARANCE_ADMIN_WIDGET_ADD,
            array(
                'form' => $widgetForm->createView(),
                )
            );
        
        return $this->helper->getJsonResponse(true, null, $themeEditView); 
    }
    
    /**
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param type $themeId
     * @param type $widgetId
     * @return type
     */
    public function deleteThemeWidget(Request $request, $themeId, $widgetId)
    {
        if ($request->isXmlHttpRequest() && $request->isMethod('POST')) {
            /** @var Theme $theme */
            $theme = $this->getThemeService()->getTheme($themeId);
            if (null === $theme) {
                return $this->helper->getExceptionResponseNotFound($themeId);
            }

            /** @var Widget $widget */
            $widget = $this->getWidget($widgetId);
            if (null === $widget) {
                return $this->helper->getExceptionResponseNotFound($widgetId);
            }

            $rowId = $widget->getRowId();
            if ($rowId !== '0' && null === $theme->getRow($rowId)) {
                return $this->helper->getExceptionResponseNotFound($rowId);
            }

            if ($this->helper->deleteEntity($widget)) {
                $theme->removeWidget($widget);
                $this->getThemeService()->updateTheme($theme);
                
                return $this->helper->getJsonResponse(
                    true,
                    array(
                        'alert.success.itemHasBeenRemoved', 
                        array('%id%' => $themeId))
                    );
            } else {
                return $this->helper->getExceptionResponse(
                    'alert.success.itemHasNotBeenRemoved', 
                    array('%id%' => $widgetId)
                    );
            }
        }        
    }
        
    /**
     * Edit Theme Structure Widget
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param type $themeId
     * @return type
     */
    public function editThemeWidget(Request $request, $themeId)
    {
        $widgetId = $request->get('widgetId');
        
        /** @var Theme */
        $widget = $this->getWidget($widgetId);
        // Check if this $theme exist
        if (null === $widget) {
            return $this->helper->getExceptionResponseNotFound($widgetId);
        }
        
        /** @var ThemeSettingForm $themeSettingForm */
        $widgetSettingFormType = new WidgetSettingForm(
            $widget, 
            $this->helper->getEntityManager()
            );
        
        /** @var ThemeForm */
        $widgetSettingForm = $this->helper->createForm($request, $widgetSettingFormType);
        // Handling Form Submissions and validation
        $widgetSettingForm->handleRequest($request);
        if ($widgetSettingForm->isSubmitted()) {
            if ($widgetSettingForm->isValid()) {
                // Get user config form values from POST request
                $setting = $this->getWidgetSettingFromRequest(
                    $request,
                    $widget,
                    $widgetSettingForm
                    );
                
                $widget->setSettings($setting);
                $this->updateWidget($widget);
                
                return $this->helper->getJsonResponse(true);
            }
        }    
        
        $themeEditView = $this->helper->renderView(
            ViewMap::APPEARANCE_ADMIN_WIDGET_EDIT,
            array(
                'form' => $widgetSettingForm->createView(),
                'widgetStructure' => $widget->getWidgetStructure()
                )
            );
        
        return $this->helper->getJsonResponse(true, null, $themeEditView);        
    }
    
    /**
     * Return static content of this widget
     * 
     * @param type $widget
     * @param type $dependencies
     * @return type
     */
    public function getWidgetStaticContent($widget)
    {
        return $this->getWidgetServiceHandler($widget)
            ->getWidgetStaticContent();
    }
    
    /**
     * Return css content of this widget
     * 
     * @param type $widget
     * @param type $dependencies
     * @return type
     */
    public function getWidgetCssContent($widget)
    {
        return $this->getWidgetServiceHandler($widget)
            ->getWidgetCssContent();
    }    
        
    /**
     * 
     * @param Widget $widget
     * @return \Saman\AppearanceBundle\Service\handler\widgetHandler
     * @throws \Symfony\Component\Debug\Exception\ClassNotFoundException
     */
    private function getWidgetServiceHandler(Widget $widget)
    {
        $widgetHandler = sprintf(
            '\Saman\AppearanceBundle\Service\handler\WidgetServiceHandler%s', 
            ucfirst($widget->getType())
            );
        
        if (!class_exists($widgetHandler)) {
            $widgetHandler = '\Saman\AppearanceBundle\Service\handler\WidgetServiceHandler';
        }
        
        if (!isset($this->handlerDependencies['helper'])) {
            $this->handlerDependencies['helper'] = $this->helper;
        }
        
        return new $widgetHandler($widget, $this->handlerDependencies);
    }
    
    /**
     * Get Theme Repository
     * 
     * @return type
     */
    private function getWidgetRepository()
    {
        return $this->helper->getRepository(EntityMap::APPEARANCE_WIDGET);
    }
    
    /**
     * Get widget structure
     * 
     * @param type $widgetType
     * @return type
     * @throws \Exception
     */
    private function getWidgetStructure($widgetType = null)
    {
        if (null === $widgetType) {
            return $this->helper->getParameter('widgets');
        }
        
        $widgetStructures = $this->helper->getParameter('widgets');
        if (!array_key_exists($widgetType, $widgetStructures)) {
            throw new \Exception(sprintf('No widget structur exist with this Type: %d', $widgetType));
        }
        
        return $widgetStructures[$widgetType];
    }
    
    /**
     * Get widget default settings
     * 
     * @param type $widgetType
     * @return array
     */
    private function getWidgetDefaultSettings($widgetType)
    {
        $widgetStructure = $this->getWidgetStructure($widgetType);
        $settings = array();
        
        if (!array_key_exists('settings', $widgetStructure)) {
            return $settings;
        }
        
        foreach ($widgetStructure['settings'] as $key => $setting) {
            if (array_key_exists('default', $setting)) {
                $settings[$key] = $setting['default'];
            }
        }
        
        return $settings;
    }
    
    /**
     * 
     * @param type $requestParam
     * @param type $widgetStructure
     */
    private function getWidgetSettingFromRequest(Request $request, Widget $widget, $form)
    {
        $widgetStructure = $widget->getWidgetStructure();
        if (!array_key_exists('settings', $widgetStructure)) {
            return array();
        }
        
        $settings = array();
        $rawSettingValues = $request->request->get($form->getName());
        foreach ($widgetStructure['settings'] as $key => $setting) {
            if (array_key_exists($key, $rawSettingValues)) {
                $settings[$key] = $this->helper->persistFormValue(
                    $rawSettingValues[$key], 
                    $setting,
                    $widget
                    );
            } else {
                if (array_key_exists('default', $setting)) {
                    $settings[$key] = $setting['default'];
                }
            }
        }
        
        return $settings;
    }
}