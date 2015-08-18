<?php

namespace Saman\AppearanceBundle\Service;

use Symfony\Component\HttpFoundation\Request;
use Saman\Library\Service\Helper;
use Saman\Library\Map\EntityMap;
use Saman\AppearanceBundle\Library\Theme;
use Saman\Library\Map\ViewMap;
use Saman\AppearanceBundle\Form\ThemeSettingForm;
use Saman\AppearanceBundle\Form\ThemeRawStructureForm;
use Saman\AppearanceBundle\Form\RowSettingForm;
use Saman\AppearanceBundle\Service\NavigationService;
use Saman\AppearanceBundle\Service\WidgetService;
/*
 * tarife ye exception jadid ke harmoghe etefagh oftad mostaghim mohtaviyatesha 
 * nemayesh midam be karbar. sayere exception ha bayad message defult nemayesh 
 * dade beshe. Message defult hamon message tahe har functione service hast.
 * 
 * har functione service ke mostaghim ba user intrface nahaye saroar dare bayad 
 * cach execption kone. Design paterne estefade shode baraye widget ha ke be sorate
 * widget resolverha ijad shode bayad be sorate library widget ha tarif beshe. 
 * khode library/Widget moghe ijad ye resolvere khase khodesha ijad mikone ke 
 * bar asase 
 * 
 * 
 * 
 */
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
     * Get Theme based on its ID. If ID is null then create a new Theme
     * 
     * @param type $themeId
     * @return type
     */
    private function getTheme($themeId = null, $loadTheme = false)
    {
        $themeEntity = null;
        if (null !== $themeId) {
            // Load theme entity from DB
            $themeEntity = $this->getThemeRepository()
                ->getTheme((int) $themeId, $loadTheme);
        }
        // Create theme
        $theme = new Theme($themeEntity);
        
        return $theme;
    }
    
    /**
     * Load theme
     * 
     * @param int $themeId
     * @return Theme
     */
    private function loadTheme($themeId)
    {
        return $this->getTheme($themeId, true);
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
        $theme->getTheme()->setWidgetSettings($widgetSettings);
        
        $content = $this->getThemeWebContent($request, $theme);
        $theme->getTheme()->setContent($content);
        
        $staticContents = $this->getThemeStaticContents($theme);
        $theme->getTheme()->addStaticContents($staticContents);
        
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
        $theme = $this->getTheme($themeId);
        // Check if this $theme exist
        if (null === $theme) {
            return $this->helper->getExceptionResponseNotFound($themeId);
        }
        
        $theme->getTheme()->setIsRawStructure(false);
        if ('true' === $isRawStructure) {
            $theme->getTheme()->setIsRawStructure(true);
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
        $widgets = $theme->getTheme()->getWidgets(true);
        $rowStructures = '';
        foreach ($theme->getTheme()->getRowChildrens() as $row) {
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
                'theme' => $theme->getTheme(),
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
        
        $theme->getTheme()->addRow($columns, $parentRowId, $parentColumnId);
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
        
        $row = $theme->getTheme()->getRow($rowId);
        if (null === $row) {
            return $this->helper->getExceptionResponseNotFound($rowId);
        }
        
        if (0 !== $theme->getTheme()->countRowChildrens($rowId)) {
            return $this->helper->getExceptionResponse('alert.error.hasChildrenAndCannotBeRemoved');
        }
        
        $theme->getTheme()->deleteRow($rowId);
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
        
        $row = $theme->getTheme()->getRow($rowId);
        if (null === $row) {
            return $this->helper->getExceptionResponseNotFound($rowId);
        }
        
        /** @var RowSettingForm $rowSettingFormType */
        $rowSettingFormType = new RowSettingForm($theme->getTheme(), $rowId);
        
        /** @var ThemeForm */
        $rowSettingForm = $this->helper->createForm($request, $rowSettingFormType);
        // Handling Form Submissions and validation
        $rowSettingForm->handleRequest($request);
        if ($rowSettingForm->isSubmitted()) {
            if ($rowSettingForm->isValid()) {
                // Get user config form values from POST request
                $setting = $this->getRowSettingsFromRequest(
                    $request,
                    $rowSettingFormType->getName(),
                    $theme->getTheme(),
                    $rowId
                    );
                
                $theme->getTheme()->setRowSettings($rowId, null, $setting);
                $this->updateTheme($theme->getTheme());
                
                return $this->helper->getJsonResponse(true);
            }
        }    
        
        $themeEditView = $this->helper->renderView(
            ViewMap::APPEARANCE_ADMIN_THEME_EDIT_ROW,
            array(
                'form' => $rowSettingForm->createView(),
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
            $theme->getTheme()->updateRowsSortOrder($sortIds, $parentRowId, $parentColumnId);
            $this->updateTheme($theme->getTheme());
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
        $this->helper->saveEntity($theme->getTheme(), $flushEntityManager);
        
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

            if ($this->helper->deleteEntity($theme->getTheme())) {
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
        $themeForm = $this->helper->createForm($request, 'saman_appearance_theme_form', $theme->getTheme());
        // Handling Form Submissions and validation
        $themeForm->handleRequest($request);
        if ($themeForm->isSubmitted()) {
            if ($themeForm->isValid()) {
                $this->updateTheme($theme->getTheme());
                
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
        $templateParams = $this->getTemplateParameters($theme->getTheme()->getTemplate());
        // Check if this $templateParameters exist
        if (null === $templateParams) {
            return $this->helper->getExceptionResponse('alert.error.noItemHasBeenFound');
        }
        
        // Display the theme setting form
        /** @var ThemeSettingFormType $themeSettingForm */
        $themeSettingFormType = new ThemeSettingForm($this->helper, $theme->getTheme(), $templateParams);
        $themeSettingForm = $this->helper->createForm($request, $themeSettingFormType, $theme->getTheme());
        $themeSettingForm->handleRequest($request);
        if ($themeSettingForm->isSubmitted() && $themeSettingForm->isValid()) {
            // Get user config form values from POST request
            $themeSettingOptions = $request->request->get($themeSettingFormType->getName());
            $this->updateThemeSettings($theme->getTheme(), $themeSettingOptions, $templateParams);

            return $this->helper->getJsonResponse(true);
        }    
        
        // Display the theme raw structure content form
        /** @var ThemeRawStructureForm $themeRawStructureFormType */
        $themeRawStructureFormType = new ThemeRawStructureForm();
        $themeRawStructureForm = $this->helper->createForm($request, $themeRawStructureFormType, $theme->getTheme());
        $themeRawStructureForm->handleRequest($request);
        if ($themeRawStructureForm->isSubmitted() && $themeRawStructureForm->isValid()) {
            // Get user config form values from POST request
            $this->updateTheme($theme->getTheme());

            return $this->helper->getJsonResponse(true);
        }    
        
        $themeEditView = $this->helper->renderView(
            ViewMap::APPEARANCE_ADMIN_THEME_EDIT,
            array(
                'themeSettingForm' => $themeSettingForm->createView(),
                'themeRawStructureForm' => $themeRawStructureForm->createView(),
                'template' => $templateParams,
                'theme' => $theme->getTheme(),
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
            array('theme' => $theme->getTheme())
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
            return null;
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
            $theme->getTheme(),
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
    private function getRowSettingsFromRequest(
        Request $request, $requestParamName, $theme, $rowId)
    {
        $settings = array();
        $rawSetting = $request->request->get($requestParamName);
        
        $columns = $theme->getTheme()->getColumns($rowId);
        foreach ($columns as $columnId => $column) {
            $settins = $column['settings'];
            foreach ($settins as $settinKey => $settin) {
                $key = $columnId.'_'.$settinKey;
                if (array_key_exists($key, $rawSetting)) {
                    // TODO: Validate the setting parameter
                    $settings[$columnId][$settinKey] = $rawSetting[$key];
                }
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
        foreach ($theme->getTheme()->getWidgets() as $widget) {
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
        if ($theme->getTheme()->isRawStructure()) {
            return $theme->getTheme()->getRawStructure();
        }
        
        $baseUrl = $this->helper->getBaseUrl($request);
        $template = $this->getTemplateParameters($theme->getTheme()->getTemplate());
        $themeSettings = $theme->getTheme()->getSettings();
        if (!is_array($themeSettings)) {
            $themeSettings = array();
        }
        
        // Set settings
        $templateSettings = $template['settings'];
        $templateContent = $template['content'];
        foreach ($templateSettings as $key => $templateSetting) {
            $settingValue = $templateSetting['default'];
            if (array_key_exists($key, $themeSettings)) {
                $settingValue = $themeSettings[$key];
            }
            
            if ('' !== preg_replace('/\s+/', '', $settingValue)) {
                $settingContent = sprintf($templateSetting['container'], $settingValue);
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
        $templateStyles = $template['data']['style'];
        if (null !== $templateStyles && is_array($templateStyles)) {
            foreach ($templateStyles as $templateStyle) {
                $templateStylesContent = $templateStylesContent . 
                    sprintf($templateStyle, $baseUrl);
            }
        }
        
        // Set Scripts
        $templateScriptContent = '';
        $templateScripts = $template['data']['script'];
        if (null !== $templateScripts && is_array($templateScripts)) {
            foreach ($templateScripts as $templateScript) {
                $templateScriptContent = $templateScriptContent . 
                    sprintf($templateScript, $baseUrl);
            }
        }
        
        // Set meta data
        $templateMetaDataContent = '';
        $templateMetaDatas = $template['data']['metaData'];
        if (null !== $templateMetaDatas && is_array($templateMetaDatas)) {
            foreach ($templateMetaDatas as $templateMetaData) {
                $templateMetaDataContent = $templateMetaDataContent . 
                    $templateMetaData;
            }
        }
        
        $widgetStructures = $this->helper->getParameter('widgets');
        $widgets = $theme->getTheme()->getWidgets(true);
        // Set page main content structure
        $pageMainContent = '';
        foreach ($theme->getTheme()->getRowChildrens() as $row) {
            $this->generateThemeWebStructure(
                $widgetStructures, 
                $widgets, 
                $theme->getTheme(),    
                $row, 
                $pageMainContent
                );
        }        
        
        // Return the final content
        $finalContent = str_replace(
            array(
                '::pageMainMetaData::', 
                '::pageMainScript::', 
                '::pageMainStyle::', 
                '::pageMainContent::'),
            array(
                $templateMetaDataContent, 
                $templateScriptContent, 
                $templateStylesContent, 
                $pageMainContent),
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
            'theme' => $theme->getTheme(),
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
                $rowItems = $theme->getTheme()->getRowChildrens($row['id']);
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

                    foreach ($theme->getTheme()->getColumnChildrens($rowItems, $columnId) as $childItem) {
                        $this->generateThemeWebStructure(
                            $widgetStructures, 
                            $widgets, 
                            $theme->getTheme(), 
                            $childItem, 
                            $structure
                            );
                    }
                    $structure .= '</div>'; // End of column
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
            'theme' => $theme->getTheme(),
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
                $rowItems = $theme->getTheme()->getRowChildrens($row['id']);
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

                    foreach ($theme->getTheme()->getColumnChildrens($rowItems, $columnId) as $childItem) {
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
    private function getThemeStaticContents(Theme $theme)
    {
        $staticContents = array();
        foreach ($theme->getTheme()->getWidgets() as $widget) {
            $staticContent = $this->getWidgetService()
                ->getWidgetStaticContent(
                    $widget, 
                    array(
                        'helper' => $this->helper,
                        'navigationService' => $this->getNavigationService()
                        )
                    );
            
            if (null !== $staticContent) {
                $staticContents[$widget->getId()] = $staticContent;
            }
        }
        
        return $staticContents;
    }     
}