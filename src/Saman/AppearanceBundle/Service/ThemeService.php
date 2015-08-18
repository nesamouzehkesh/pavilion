<?php

namespace Saman\AppearanceBundle\Service;

use Symfony\Component\HttpFoundation\Request;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Saman\Library\Service\StaticHelper;
use Saman\Library\Service\Helper;
use Saman\Library\Map\EntityMap;
use Saman\AppearanceBundle\Entity\Theme;
use Saman\Library\Map\ViewMap;
use Saman\MediaBundle\Service\MediaService;
use Saman\AppearanceBundle\Form\ThemeSettingForm;
use Saman\AppearanceBundle\Form\ThemeRawStructureForm;
use Saman\AppearanceBundle\Form\RowSettingForm;
use Saman\AppearanceBundle\Service\NavigationService;
use Saman\AppearanceBundle\Service\WidgetService;

class ThemeService
{
    /**
     *
     * @var Helper $helper
     */
    protected $helper;
    
    /**
     *
     * @var NavigationService $navigationService 
     */
    protected $navigationService;
    
    /**
     *
     * @var WidgetService $widgetService 
     */
    protected $widgetService;
    
    /**
     *
     * @var CacheManager $cacheManagerService
     */
    protected $cacheManagerService;

    /**
     * 
     * @param \Saman\Library\Service\Helper $helper
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
     * 
     * @param NavigationService $navigationService
     * @return \Saman\AppearanceBundle\Service\ThemeService
     */
    public function setNavigationService(NavigationService $navigationService)
    {
        $this->navigationService = $navigationService;
        
        return $this;
    }
    
    /**
     * 
     * @return type
     * @throws \Exception
     */
    public function getNavigationService()
    {
        if (null === $this->navigationService) {
            throw new \Exception('No Navigation Service is defined for this service');
        }
        
        return $this->navigationService;
    }
    
    /**
     * 
     * @param \Saman\AppearanceBundle\Service\WidgetService $widgetService
     * @return \Saman\AppearanceBundle\Service\ThemeService
     */
    public function setWidgetService(WidgetService $widgetService)
    {
        $this->widgetService = $widgetService;
        
        return $this;
    }
    
    /**
     * 
     * @return WidgetService
     * @throws \Exception
     */
    public function getWidgetService()
    {
        if (null === $this->widgetService) {
            throw new \Exception('No Widget Service is defined for this service');
        }
        
        return $this->widgetService;
    }
    
    
    /**
     * 
     * @param NavigationService $cacheManagerService
     * @return \Saman\AppearanceBundle\Service\ThemeService
     */
    public function setCacheManagerService(CacheManager $cacheManagerService)
    {
        $this->cacheManagerService = $cacheManagerService;
        
        return $this;
    }
    
    /**
     * 
     * @return type
     * @throws \Exception
     */
    public function getCacheManagerService()
    {
        if (null === $this->cacheManagerService) {
            throw new \Exception('No Cache Manager Service is defined for this service');
        }
        
        return $this->cacheManagerService;
    }    
    
    /**
     * Create a new Theme
     */
    public function createNewTheme()
    {
        $theme = new Theme();
        
        return $theme; 
    }
    
    /**
     * Get Theme based on its ID. If ID is null then create a new Theme
     * 
     * @param type $themeId
     * @return type
     */
    public function getTheme($themeId = null)
    {
        $theme = null;
        if (null === $themeId) {
            $theme = $this->createNewTheme();
        } else {
            $theme = $this->getThemeRepository()->getTheme(intval($themeId));
        }        
        
        $defultParameters = $this->helper->getParameters();
        $theme->setDefultParameters($defultParameters);
        
        return $theme;
    }
    
    /**
     * Publish theme content
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param type $themeId
     * @return type
     */
    public function publishTheme(Request $request, $themeId)
    {
        /** @var Theme $theme */
        $theme = $this->loadTheme($themeId);
        if (null === $theme) {
            return $this->helper->getExceptionResponseNotFound($themeId);
        }
        
        $widgetSettings = $this->getCompressWidgetSettings($theme);
        $theme->setWidgetSettings($widgetSettings);
        
        $this->setThemeStaticContents($theme);
        $content = $this->getThemeWebContent($request, $theme);
        $theme->setContent($content);
        
        //$navigationsId = $theme->getNavigationsId();
        //$this->publishThemeNavigation(array($theme), $navigationsId);
        
        $this->updateTheme($theme);
        
        return $this->helper->getJsonResponse(
            true, 
            'module.appearance.alert.success.themeIsPublished', 
            null
            );
    }
    
    /**
     * 
     * @param Request $request
     * @return type
     */
    public function setStructureType(Request $request)
    {
        $themeId = $request->get('themeId');
        $isRawStructure = $request->get('isRawStructure');
        /** @var Theme $theme */
        $theme = $this->loadTheme($themeId);
        // Check if this $theme exist
        if (null === $theme) {
            return $this->helper->getExceptionResponseNotFound($themeId);
        }
        
        $theme->setIsRawStructure(false);
        if ('true' === $isRawStructure) {
            $theme->setIsRawStructure(true);
        }
        $this->updateTheme($theme);
        
        return $this->helper->getJsonResponse(true, null);        
    }

    /**
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return type
     */
    public function displayThemeStructure(Request $request)
    {
        $themeId = $request->get('themeId');
        /** @var Theme $theme */
        $theme = $this->loadTheme($themeId);
        // Check if this $theme exist
        if (null === $theme) {
            return $this->helper->getExceptionResponseNotFound($themeId);
        }
        
        $widgetStructures = $this->helper->getParameter('widgets');
        $widgets = $theme->getWidgets(true);
        $rowStructures = '';
        foreach ($theme->getRowChildrens() as $row) {
            $this->generateThemeStructure(
                $widgetStructures, 
                $widgets, 
                $theme,    
                $row, 
                $rowStructures
                );
        }
        
        $themeStructureView = $this->helper->renderView(
            ViewMap::APPEARANCE_ADMIN_THEME_ROW_STRUCTURE_VIEW,
            array(
                'type' => 'body',
                'theme' => $theme,
                'rowStructures' => $rowStructures,
                )
            );                
        
        return $this->helper->getJsonResponse(true, null, $themeStructureView);           
    }

    /**
     * Add new row in theme structure
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param type $themeId
     * @param type $rowId
     */
    public function addThemeRow(Request $request, $themeId)
    {
        $columns = intval($request->get('columns'));
        $parentRowId = $request->get('parentRowId');
        $parentColumnId = $request->get('parentColumnId');
        
        /** @var Theme $theme */
        $theme = $this->getTheme($themeId);
        if (null === $theme) {
            return $this->helper->getExceptionResponseNotFound($themeId);
        }
        
        $theme->addRow($columns, $parentRowId, $parentColumnId);
        $this->updateTheme($theme);
        
        return $this->helper->getJsonResponse(true, null);        
    }
    
    /**
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param type $themeId
     * @param type $rowId
     * @return type
     */
    public function deleteThemeRow(Request $request, $themeId, $rowId)
    {
        /** @var Theme $theme */
        $theme = $this->getTheme($themeId);
        if (null === $theme) {
            return $this->helper->getExceptionResponseNotFound($themeId);
        }
        
        $row = $theme->getRow($rowId);
        if (null === $row) {
            return $this->helper->getExceptionResponseNotFound($rowId);
        }
        
        if (0 !== $theme->countRowChildrens($rowId)) {
            return $this->helper->getExceptionResponse('alert.error.hasChildrenAndCannotBeRemoved');
        }
        
        $theme->deleteRow($rowId);
        $this->updateTheme($theme);
        
        return $this->helper->getJsonResponse(true, null);        
    }
    
    /**
     * 
     * @param type $request
     * @param type $themeId
     * @param type $rowId
     */
    public function editThemeRow($request, $themeId, $rowId)
    {
        /** @var Theme */
        /** @var Theme $theme */
        $theme = $this->getTheme($themeId);
        if (null === $theme) {
            return $this->helper->getExceptionResponseNotFound($themeId);
        }
        
        $row = $theme->getRow($rowId);
        if (null === $row) {
            return $this->helper->getExceptionResponseNotFound($rowId);
        }
        
        /** @var RowSettingForm $rowSettingFormType */
        $rowSettingFormType = new RowSettingForm($theme, $rowId);
        
        /** @var ThemeForm */
        $rowSettingForm = $this->helper->createForm($request, $rowSettingFormType);
        // Handling Form Submissions and validation
        $rowSettingForm->handleRequest($request);
        if ($rowSettingForm->isSubmitted()) {
            if ($rowSettingForm->isValid()) {
                $requestParameters = $request->request->get($rowSettingFormType->getName());
                // Get column setting paramaters
                $columnSetting = $this->getColumnSettingsFromRequest(
                    $requestParameters,
                    $theme,
                    $rowId
                    );
                // Get row setting paramaters
                $rowSetting = $requestParameters['rowSettings'];
                $theme->setRowSettings($rowId, $rowSetting, $columnSetting);
                $this->updateTheme($theme);
                
                return $this->helper->getJsonResponse(true);
            }
        }    
        
        $themeEditView = $this->helper->renderView(
            ViewMap::APPEARANCE_ADMIN_THEME_EDIT_ROW,
            array(
                'form' => $rowSettingForm->createView(),
                'theme' => $theme,
                'row' => $row
                )
            );
        
        return $this->helper->getJsonResponse(true, null, $themeEditView);        
    }
    
    /**
     * 
     * @param Request $request
     * @param type $themeId
     * @param type $parentRowId
     * @param type $parentColumnId
     * @return type
     */
    public function sortRows(Request $request, $themeId, $parentRowId, $parentColumnId)
    {
        /** @var Theme $theme */
        $theme = $this->getTheme($themeId);
        if (null === $theme) {
            return $this->helper->getExceptionResponseNotFound($themeId);
        }
        
        $sortIds = explode(",", $request->get('sortIds', ''));
        if (is_array($sortIds)) {
            $theme->updateRowsSortOrder($sortIds, $parentRowId, $parentColumnId);
            $this->updateTheme($theme);
        }
                
        return $this->helper->getJsonResponse(true);
    }    
    
    /**
     * Update Theme
     * 
     * @param type $theme
     * @param type $flushEntityManager
     * @return boolean
     */
    public function updateTheme($theme, $flushEntityManager = true)
    {
        $theme->setModifiedTime();
        $this->helper->saveEntity($theme, $flushEntityManager);
        
        return true;
    }
    
    /**
     * Delete an Theme
     * 
     * @param int $themeId
     * @return type
     */
    public function deleteTheme($request, $themeId)
    {
        if ($request->isXmlHttpRequest() && $request->isMethod('POST')) {
            /** @var Theme $theme */
            $theme = $this->getTheme($themeId);

            // Check if this $theme exist
            if (null === $theme) {
                return $this->helper->getExceptionResponseNotFound($themeId);
            }

            if ($this->helper->deleteEntity($theme)) {
                return $this->helper->getJsonResponse(
                    true,
                    array(
                        'alert.success.itemHasBeenRemoved', 
                        array('%id%' => $themeId))
                    );
            } else {
                return $this->helper->getExceptionResponse(
                    'alert.success.itemHasNotBeenRemoved', 
                    array('%id%' => $themeId)
                    );
            }
        }
    }
    
    /**
     * Add or Edit Theme
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param type $themeId
     * @return type
     */
    public function addTheme(Request $request)
    {
        /** @var Theme */
        $theme = $this->getTheme(null);
        
        /** @var ThemeForm */
        $themeForm = $this->helper->createForm($request, 'saman_appearance_theme_form', $theme);
        // Handling Form Submissions and validation
        $themeForm->handleRequest($request);
        if ($themeForm->isSubmitted()) {
            if ($themeForm->isValid()) {
                $this->updateTheme($theme);
                
                return $this->helper->getJsonResponse(true);
            }
        }    
        $themeFormView = $this->helper->renderView(
            ViewMap::APPEARANCE_ADMIN_THEME_ADD,
            array('form' => $themeForm->createView())
            );
        
        return $this->helper->getJsonResponse(true, null, $themeFormView);        
    }
    
    /**
     * 
     * @param Request $request
     * @param type $themeId
     * @return type
     */
    public function editTheme(Request $request, $themeId)
    {
        /** @var Theme */
        $theme = $this->getTheme($themeId);
        // Check if this $theme exist
        if (null === $theme) {
            return $this->helper->getExceptionResponseNotFound($themeId);
        }
        
        /** @var Array $templateParameters */
        $templateParams= $this->getTemplateParameters($theme->getTemplate());
        // Display the theme setting form
        /** @var ThemeSettingFormType $themeSettingForm */
        $themeSettingFormType = new ThemeSettingForm(
            $this->helper, 
            $theme, 
            $templateParams
            );
        $themeSettingForm = $this->helper->createForm(
            $request, 
            $themeSettingFormType, 
            $theme
            );
        
        $themeSettingForm->handleRequest($request);
        if ($themeSettingForm->isSubmitted() && $themeSettingForm->isValid()) {
            // Get user config form values from POST request
            $themeSettingOptions = $this->getThemeSettingFromRequest(
                $request, 
                $theme, 
                $themeSettingForm
                );
            
            $this->updateThemeSettings(
                $theme, 
                $themeSettingOptions, 
                $templateParams
                );

            return $this->helper->getJsonResponse(true);
        }    
        
        // Display the theme raw structure content form
        /** @var ThemeRawStructureForm $themeRawStructureFormType */
        $themeRawStructureFormType = new ThemeRawStructureForm();
        $themeRawStructureForm = $this->helper->createForm(
            $request, 
            $themeRawStructureFormType, 
            $theme
            );
        $themeRawStructureForm->handleRequest($request);
        if ($themeRawStructureForm->isSubmitted() && $themeRawStructureForm->isValid()) {
            // Get user config form values from POST request
            $this->updateTheme($theme);

            return $this->helper->getJsonResponse(true);
        }    
        
        $themeEditView = $this->helper->renderView(
            ViewMap::APPEARANCE_ADMIN_THEME_EDIT,
            array(
                'themeSettingForm' => $themeSettingForm->createView(),
                'themeRawStructureForm' => $themeRawStructureForm->createView(),
                'template' => $templateParams,
                'theme' => $theme,
                'defaultWidgets' => $this->helper->getParameter('widgets')
                )
            );
        
        return $this->helper->getJsonResponse(true, null, $themeEditView);        
    }
    
    /**
     * Get Themes
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param type $loadJustQuery
     * @return type
     */
    public function getThemes(Request $request, $loadJustQuery = true)
    {
        $param = $this->helper->getSearchParam($request);
        $themes = $this->getThemeRepository()->getThemes($param, $loadJustQuery);
        
        return $themes;
    }
    
    /**
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param type $loadJustQuery
     * @return type
     */
    public function loadAllThemes()
    {
        $themes = $this->getThemeRepository()->loadAllThemes();
        
        return $themes;
    }    
    
    /**
     * Load theme
     * 
     * @param int $themeId
     * @return Theme
     */
    public function loadTheme($themeId)
    {
        $theme = $this->getThemeRepository()->getTheme((int) $themeId, true);
        
        return $theme;
    }
    
    /**
     * Display Themes
     * 
     * @param \Saman\CmsBundle\Service\Request $request
     * @return type
     */
    public function displayThemes(Request $request)
    {
        $themesQuery = $this->getThemes($request);
        $themesPagination = $this->helper->paginate($request, $themesQuery);
        
        $themesView = $this->helper->renderView(
            ViewMap::APPEARANCE_ADMIN_THEMES_VIEW,
            array('themesPagination' => $themesPagination)
            );
        
        if ($request->get('headless')) {
            $response = $this->helper->getJsonResponse(true, null, $themesView);
        } else {
            $response = $this->helper->render(
                ViewMap::APPEARANCE_ADMIN_THEMES_HOME,
                array(
                    'themesView' => $themesView
                    )
                );
        }        
        
        return $response;
    }
    
    /**
     * Display Theme
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param type $themeId
     * @return type
     */
    public function displayTheme(Request $request, $themeId)
    {
        $theme = $this->loadTheme($request, $themeId);

        // Check if this $theme exist
        if (null === $theme) {
            return $this->helper->getExceptionResponseNotFound($themeId);
        }
        
        $themeView = $this->helper->renderView(
            ViewMap::APPEARANCE_ADMIN_THEME_VIEW,
            array('theme' => $theme)
            );
        $response = $this->helper->getJsonResponse(true, null, $themeView);
        
        return $response;
    }
    
    /**
     * Get Theme Repository
     * 
     * @return type
     */
    private function getThemeRepository()
    {
        return $this->helper->getRepository(EntityMap::APPEARANCE_THEME);
    }    
    
    /**
     * 
     * @return type
     */
    private function getTemplateParameters($templateName)
    {
        $templates = $this->helper->getParameter('templates');
        if (!array_key_exists($templateName, $templates)) {
            return $this->helper->getExceptionResponse('alert.error.noItemHasBeenFound');
        }
        
        return $templates[$templateName];
    }
    
    /**
     * 
     * @param type $theme
     * @param type $themeSettingOptions
     */
    private function updateThemeSettings($theme, $themeSettingOptions, $templateParameters)
    {
        return $this->getThemeRepository()->updateThemeSettings(
            $theme,
            $themeSettingOptions,
            $templateParameters
            );        
    }
    
    /**
     * 
     * @param Request $request
     * @param type $requestParamName
     * @return type
     */
    private function getColumnSettingsFromRequest($requestParameters, $theme, $rowId)
    {
        $settings = array();
        $columnsDefultParameter = $theme->getDefultParameter('columns');
        $columns = $theme->getColumns($rowId);
        
        foreach ($columns as $column) {
            foreach ($columnsDefultParameter['settings'] as $settingKey => $setting) {
                $settings[$column['id']][$settingKey] = $this->helper->persistFormValue(
                    $requestParameters[$column['id']][$settingKey], 
                    $setting, 
                    $theme
                    );
            }        
        }
        
        return $settings;
    }
    
    /**
     * 
     * @param type $theme
     * @return array
     */
    private function getCompressWidgetSettings(Theme $theme)
    {
        $widgetSettings = array();
        foreach ($theme->getWidgets() as $widget) {
            $widgetSettings[$widget->getId()] = $widget->getSettings();
        }
        
        return $widgetSettings;
    }
    
    /**
     * 
     * @param Request $request
     * @param Theme $theme
     * @return type
     */
    private function getThemeWebContent(Request $request, Theme $theme)
    {
        // If theme use just raw structure return its content.
        if ($theme->isRawStructure()) {
            return $theme->getRawStructure();
        }
        
        $baseUrl = $this->helper->getBaseUrl($request);
        /** @var Array $templateParameters */
        $templateParams = $this->getTemplateParameters($theme->getTemplate());
        $themeSettings = $theme->getSettings();
        if (!is_array($themeSettings)) {
            $themeSettings = array();
        }
        
        // Set settings
        $templateSettings = $templateParams['settings'];
        $templateContent = $templateParams['content'];
        foreach ($templateSettings as $key => $templateSetting) {
            $settingValue = $templateSetting['default'];
            if (array_key_exists($key, $themeSettings)) {
                $settingValue = $themeSettings[$key];
            }
            
            if ('' !== preg_replace('/\s+/', '', $settingValue)) {
                $settingContent = $settingValue;
                if (isset($templateSetting['container'])) {
                    $settingContent = sprintf(
                        $templateSetting['container'], 
                        $settingValue
                        );
                }
            } else {
                $settingContent = '';
            }
            
            $templateContent = str_replace(
                sprintf(':%s:', $key), 
                $settingContent, 
                $templateContent
                );
        }
        
        // Set styles
        $templateStylesContent = '';
        $templateStyles = $templateParams['data']['style'];
        if (null !== $templateStyles && is_array($templateStyles)) {
            foreach ($templateStyles as $templateStyle) {
                $templateStylesContent = $templateStylesContent . 
                    sprintf($templateStyle, $baseUrl);
            }
        }
        
        // Set Scripts
        $templateScriptContent = '';
        $templateScripts = $templateParams['data']['script'];
        if (null !== $templateScripts && is_array($templateScripts)) {
            foreach ($templateScripts as $templateScript) {
                $templateScriptContent = $templateScriptContent . 
                    sprintf($templateScript, $baseUrl);
            }
        }
        
        // Set meta data
        $templateMetaDataContent = '';
        $templateMetaDatas = $templateParams['data']['metaData'];
        if (null !== $templateMetaDatas && is_array($templateMetaDatas)) {
            foreach ($templateMetaDatas as $templateMetaData) {
                $templateMetaDataContent = $templateMetaDataContent . 
                    $templateMetaData;
            }
        }
        
        $widgetStructures = $this->helper->getParameter('widgets');
        $widgets = $theme->getWidgets(true);
        // Set page main content structure
        $pageMainContent = '';
        foreach ($theme->getRowChildrens() as $row) {
            $this->generateThemeWebStructure(
                $widgetStructures, 
                $widgets, 
                $theme,    
                $row, 
                $pageMainContent
                );
        }        
        
        // Get widgets CSS content
        $templateCssContent = $this->themeCssContentToString($theme);
                
        // Return the final content
        $finalContent = str_replace(
            array(
                '::pageMainMetaData::', 
                '::pageMainScript::', 
                '::pageMainStyle::', 
                '::pageMainContent::',
                '::pageMainCss::'
                ),
            array(
                $templateMetaDataContent, 
                $templateScriptContent, 
                $templateStylesContent, 
                $pageMainContent,
                $templateCssContent
                ),
            $templateContent
            );
        
        return $finalContent;
    }
    
    /**
     * 
     * @param type $widgetStructures
     * @param type $widgets
     * @param Theme $theme
     * @param type $row
     * @param type $structure
     * @return type
     */
    private function generateThemeWebStructure(
        $widgetStructures, $widgets, Theme $theme, $row, &$structure)
    {
        $templateParameters = array(
            'row' => $row,
            'theme' => $theme,
            'widgets' => $widgets,
            'widgetStructures' => $widgetStructures
            );

        switch ($row['type']) {
            case 'widget':
                $templateParameters['type'] = 'widget';
                $structure .= $this->helper->renderView(
                    ViewMap::APPEARANCE_ADMIN_THEME_ROW_STRUCTURE_WEB_VIEW,
                    $templateParameters
                    );             
                break;
            case 'row':
                $rowItems = $theme->getRowChildrens($row['id']);
                // Start of the row
                $templateParameters['type'] = 'row';
                $structure .= $this->helper->renderView(
                    ViewMap::APPEARANCE_ADMIN_THEME_ROW_STRUCTURE_WEB_VIEW,
                    $templateParameters
                    );

                foreach ($row['columns'] as $columnId => $column) {
                    // Start of the column
                    $templateParameters['type'] = 'column';
                    $templateParameters['column'] = $column;
                    $structure .= $this->helper->renderView(
                        ViewMap::APPEARANCE_ADMIN_THEME_ROW_STRUCTURE_WEB_VIEW,
                        $templateParameters
                        );

                    foreach ($theme->getColumnChildrens($rowItems, $columnId) as $childItem) {
                        $this->generateThemeWebStructure(
                            $widgetStructures, 
                            $widgets, 
                            $theme, 
                            $childItem, 
                            $structure
                            );
                    }
                    $structure .= '</div></div>'; // End of column
                }
                $structure .= '</div>'; // End of row
                
                break;
        }
        
        return;
    }
    
    /**
     * Recursive function to generate theme row structures
     * 
     * @param type $widgetStructures
     * @param type $widgets
     * @param \Saman\AppearanceBundle\Entity\Theme $theme
     * @param type $row
     * @param type $view
     * @return type
     */
    private function generateThemeStructure($widgetStructures, $widgets, Theme $theme, $row, &$structure)
    {
        $templateParameters = array(
            'row' => $row,
            'theme' => $theme,
            'widgets' => $widgets,
            'widgetStructures' => $widgetStructures
            );

        switch ($row['type']) {
            case 'widget':
                $templateParameters['type'] = 'widget';
                $structure .= $this->helper->renderView(
                    ViewMap::APPEARANCE_ADMIN_THEME_ROW_STRUCTURE_VIEW,
                    $templateParameters
                    );             
                break;
            case 'row':
                $rowItems = $theme->getRowChildrens($row['id']);
                // Start of the row
                $templateParameters['type'] = 'row';
                $structure .= $this->helper->renderView(
                    ViewMap::APPEARANCE_ADMIN_THEME_ROW_STRUCTURE_VIEW,
                    $templateParameters
                    );

                foreach ($row['columns'] as $columnId => $column) {
                    // Start of the column
                    $templateParameters['type'] = 'column';
                    $templateParameters['column'] = $column;
                    $structure .= $this->helper->renderView(
                        ViewMap::APPEARANCE_ADMIN_THEME_ROW_STRUCTURE_VIEW,
                        $templateParameters
                        );

                    foreach ($theme->getColumnChildrens($rowItems, $columnId) as $childItem) {
                        $this->generateThemeStructure(
                            $widgetStructures, 
                            $widgets, 
                            $theme, 
                            $childItem, 
                            $structure
                            );
                    }
                    $structure .= '</u></div>'; // End of column
                }
                $structure .= '</div></li>'; // End of row
                
                break;
        }
        
        return;
    }
    
    /**
     * Get theme static content to be published
     * 
     * @param type $theme
     */
    private function setThemeStaticContents(Theme $theme)
    {
        $staticContents = array();
        $cssContents = array();
        
        $widgetService = $this->getWidgetService()->setHandlerDependencies(
            array(
                'navigationService' => $this->getNavigationService()
                )
            );
        
        foreach ($theme->getWidgets() as $widget) {
            $staticContent = $widgetService->getWidgetStaticContent($widget);
            $cssContent = $widgetService->getWidgetCssContent($widget);
            
            if (null !== $staticContent) {
                $staticContents[$widget->getId()] = $staticContent;
            }
            
            if (null !== $cssContent) {
                $cssContents[$widget->getId()] = $cssContent;
            }            
        }
        
        $theme->addStaticContents($staticContents);
        $theme->addCssContents($cssContents);
        
        return $theme;
    }     
    
    /**
     * Get theme settings from request and persist them
     * 
     * @param Request $request
     * @param Theme $theme
     * @param type $form
     * @return type
     */
    private function getThemeSettingFromRequest(Request $request, Theme $theme, $form)
    {
        /** @var Array $templateParameters */
        $templateParams = $this->getTemplateParameters($theme->getTemplate());
        
        $settings = array();
        $rawSettingValues = $request->request->get($form->getName());
        foreach ($templateParams['settings'] as $key => $setting) {
            if (array_key_exists($key, $rawSettingValues)) {
                $settings[$key] = $this->helper->persistFormValue(
                    $rawSettingValues[$key], 
                    $setting,
                    $theme
                    );
            } else {
                if (array_key_exists('default', $setting)) {
                    $settings[$key] = $setting['default'];
                }
            }
        }
        
        return $settings;
    }    
    
    /**
     * 
     * @param Theme $theme
     * @return type
     */
    private function themeCssContentToString(Theme $theme)
    {
        $settings = $theme->getSettings();
        $bodyCssArray = array(
            'background-image' => isset($settings['backgroundImage'])? StaticHelper::getMediaUrl($settings['backgroundImage'], $this->getCacheManagerService()) : null,
            'background-color' => isset($settings['backgroundColor'])? $settings['backgroundColor'] : null,
            'background-repeat' => isset($settings['backgroundRepeat'])? $settings['backgroundRepeat'] : null,
            'background-position' => isset($settings['backgroundPosition'])? $settings['backgroundPosition'] : null,
            'background-attachment' => isset($settings['backgroundAttachment'])? $settings['backgroundAttachment'] : null,
        );
        
        // Get theme body css content
        $cssContent = sprintf('body{%s}', StaticHelper::getCss($bodyCssArray));
        
        // Get widget css content
        if (count($theme->getCssContent()) > 0) {
            $cssContent .= implode(' ', $theme->getCssContent());
        }    
        
        return sprintf('<style>%s</style>', $cssContent);
    }    
}