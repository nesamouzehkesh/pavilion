<?php

namespace Saman\AppearanceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Saman\Library\Base\BaseEntity;
use Saman\AppearanceBundle\Entity\Widget;

/**
 * Page
 *
 * @ORM\Table(name="saman_theme")
 * @ORM\Entity(repositoryClass="Saman\AppearanceBundle\Repository\ThemeRepository")
 */
class Theme extends BaseEntity
{
    const ITEM_LOGO = 'icon.theme';
        
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @var string
     *
     * @ORM\Column(name="template", type="string", length=255)
     */
    private $template;
    
    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;
    
    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text", nullable=true)
     */
    private $content;
    
    /**
     * @var array
     *
     * @ORM\Column(name="settings", type="array", nullable=true)
     */
    private $settings;

    /**
     * @var array
     *
     * @ORM\Column(name="structure", type="array", nullable=true)
     */
    private $structure;
    
    /**
     * @var text
     *
     * @ORM\Column(name="rawStructure", type="text", nullable=true)
     */
    private $rawStructure;
    
    /**
     * @ORM\Column(name="is_rawStructure", type="boolean", nullable=true, options={"default"= 0})
     */
    private $isRawStructure;    
    
    /**
     * @var array
     *
     * @ORM\Column(name="widgetsSettings", type="array", nullable=true)
     */
    private $widgetSettings;
    
    /**
     * @var array
     *
     * @ORM\Column(name="staticContent", type="array", nullable=true)
     */
    private $staticContent;
    
    /**
     * @var array
     *
     * @ORM\Column(name="cssContent", type="array", nullable=true)
     */
    private $cssContent;
    
    /**
     * 
     * @ORM\OneToMany(targetEntity="Widget", mappedBy="theme")
     **/
    private $widgets;
    
    /**
     * @ORM\ManyToMany(targetEntity="Saman\LabelBundle\Entity\Label")
     * @ORM\JoinTable(name="saman_theme_label",
     * joinColumns={@ORM\JoinColumn(name="theme_id", referencedColumnName="id")},
     * inverseJoinColumns={@ORM\JoinColumn(name="label_id", referencedColumnName="id")})
     */
    private $labels;
    
    /**
     * @ORM\OneToMany(targetEntity="Saman\CmsBundle\Entity\Page", mappedBy="theme")
     **/
    private $pages;
    
    /**
     *
     * @var type 
     */
    private $defultParameters;
    
    /**
     * 
     */
    public function __construct()
    {
        parent::__construct();
        $this->labels = new ArrayCollection();
        $this->widgets = new ArrayCollection();
        $this->pages = new ArrayCollection();
    }
    
    /**
     * 
     * @return type
     */
    public function __toString()
    {
        return $this->getTitle();
    }
    
    /**
     * 
     * @param type $parameterKey
     * @param type $defaultValue
     * @return type
     */
    public function getDefultParameter($parameterKey, $defaultValue = null)
    {
        if (array_key_exists($parameterKey, $this->defultParameters)) {
            return $this->defultParameters[$parameterKey];
        }
        
        return $defaultValue;
    }
    
    /**
     * 
     * @return type
     */
    public function getDefultParameters()
    {
        return $this->defultParameters;
    }
    
    /**
     * 
     * @return type
     */
    public function setDefultParameters($defultParameters)
    {
        $this->defultParameters = $defultParameters;
        
        return $this;
    }

    /**
     * 
     * @return String
     */
    public function getLogo()
    {
        return self::ITEM_LOGO;
    }    
    
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * Set template
     *
     * @param string $template
     * @return Page
     */
    public function setTemplate($template)
    {
        $this->template = $template;

        return $this;
    }

    /**
     * Get template
     *
     * @return string 
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Page
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Page
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }
    
    /**
     * Set content
     *
     * @param string $content
     * @return Page
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string 
     */
    public function getContent()
    {
        return $this->content;
    }
    
    /**
     * Set settings
     *
     * @param Array $settings
     * @return Config
     */
    public function setSettings($settings)
    {
        $this->settings = $settings;

        return $this;
    }    
    
    /**
     * Get settings
     *
     * @return Array $settings
     */
    public function getSettings()
    {
        return $this->settings;
    }
    
    /**
     * 
     * @param type $param
     * @return type
     */
    public function getParameter($param, $templateSettings = null)
    {
        if (is_array($this->settings) && array_key_exists($param, $this->settings)) {
            return $this->settings[$param];
        }
        
        if (null !== $templateSettings) {
            if (array_key_exists($param, $templateSettings)) {
                return $templateSettings[$param]['default'];
            }
        }
        
        return null;
    }
    
    /**
     * Add label
     *
     * @param \Saman\LabelBundle\Entity\Label $label
     * @return Appraisal
     */
    public function addLabel(\Saman\LabelBundle\Entity\Label $label)
    {
        $this->labels[] = $label;

        return $this;
    }

    /**
     * Remove label
     *
     * @param \Saman\LabelBundle\Entity\Label $label
     */
    public function removeLabel(\Saman\LabelBundle\Entity\Label $label)
    {
        $this->labels->removeElement($label);
    }
    
    /**
     * Get labels
     * 
     * @return type
     */
    public function getLabels()
    {
        return $this->labels;
    }    

    /**
     * Set structure
     *
     * @param array $structure
     * @return Theme
     */
    public function setStructure($structure)
    {
        $this->structure = $structure;

        return $this;
    }

    /**
     * Add pages
     *
     * @param \Saman\CmsBundle\Entity\Page $page
     * @return Theme
     */
    public function addPage(\Saman\CmsBundle\Entity\Page $page)
    {
        $this->pages[] = $page;

        return $this;
    }

    /**
     * Remove pages
     *
     * @param \Saman\CmsBundle\Entity\Page $page
     */
    public function removePage(\Saman\CmsBundle\Entity\Page $page)
    {
        $this->pages->removeElement($page);
    }

    /**
     * Get pages
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPages()
    {
        return $this->pages;
    }    

    /**
     * Get staticContent
     *
     * @return array 
     */
    public function getStaticContent($widgetId = null)
    {
        if (null !== $widgetId) {
            if (!array_key_exists($widgetId, $this->staticContent)) {
                throw new \Exception(sprintf('No static content exist with this widget ID: %d', $widgetId));
            }        
            
            return $this->staticContent[$widgetId];
        }
        
        return $this->staticContent;
    }

    /**
     * Set structure
     *
     * @param array $staticContent
     * @return Theme
     */
    public function setStaticContent($staticContent, $widgetId = null)
    {
        if (null !== $widgetId) {
            $this->staticContent[$widgetId] = $staticContent;
            
        } else {
            $this->staticContent = $staticContent;
        }
                
        return $this;
    }
    
    /**
     * 
     * @param type $staticContents
     * @return \Saman\AppearanceBundle\Entity\Theme
     */
    public function addStaticContents($staticContents)
    {
        foreach ($staticContents as $widgetId => $staticContent) {
            $this->staticContent[$widgetId] = $staticContent;
        }
        
        return $this;
    }

    /**
     * Get cssContent
     *
     * @return array 
     */
    public function getCssContent($widgetId = null)
    {
        if (null !== $widgetId) {
            if (!array_key_exists($widgetId, $this->cssContent)) {
                throw new \Exception(sprintf('No css content exist with this widget ID: %d', $widgetId));
            }        
            
            return $this->cssContent[$widgetId];
        }
        
        return $this->cssContent;
    }
    
    /**
     * Set structure
     *
     * @param array $cssContent
     * @return Theme
     */
    public function setCssContent($cssContent, $widgetId = null)
    {
        if (null !== $widgetId) {
            $this->cssContent[$widgetId] = $cssContent;
        } else {
            $this->cssContent = $cssContent;
        }
                
        return $this;
    }
    
    /**
     * 
     * @param type $cssContents
     * @return \Saman\AppearanceBundle\Entity\Theme
     */
    public function addCssContents($cssContents)
    {
        if (is_array($cssContents)) {
            foreach ($cssContents as $widgetId => $cssContent) {
                $this->cssContent[$widgetId] = $cssContent;
            }
        }
        
        return $this;
    }    
    
    /**
     * Get structure
     *
     * @return array 
     */
    public function getStructure()
    {
        return $this->structure;
    }
    
    /**
     * Set rawStructure
     *
     * @param string $rawStructure
     * @return Theme
     */
    public function setRawStructure($rawStructure)
    {
        $this->rawStructure = $rawStructure;

        return $this;
    }

    /**
     * Get rawStructure
     *
     * @return string 
     */
    public function getRawStructure()
    {
        return $this->rawStructure;
    }

    /**
     * Set isRawStructure
     *
     * @param boolean $isRawStructure
     * @return Theme
     */
    public function setIsRawStructure($isRawStructure)
    {
        $this->isRawStructure = $isRawStructure;

        return $this;
    }

    /**
     * Get isRawStructure
     *
     * @return boolean 
     */
    public function isRawStructure()
    {
        return $this->isRawStructure;
    }
    
    /**
     * Set widgetSettings
     *
     * @param array $widgetSettings
     * @return Theme
     */
    public function setWidgetSettings($widgetSettings)
    {
        $this->widgetSettings = $widgetSettings;

        return $this;
    }
 
    /**
     * 
     * @param type $widgetId
     * @return type
     * @throws \Exception
     */
    public function getWidgetSettings($widgetId = null)
    {
        if (null === $widgetId) {
            return $this->widgetSettings;
        }
        
        $widgetSettings = $this->widgetSettings;
        if (!array_key_exists($widgetId, $widgetSettings)) {
            throw new \Exception(sprintf('No widget settings exist for this ID: %d', $widgetId));
        }
        
        return $widgetSettings[$widgetId];
    }
   
    /**
     * Remove widget
     *
     * @param \Saman\AppearanceBundle\Entity\Widget $widget
     */
    public function removeWidget(\Saman\AppearanceBundle\Entity\Widget $widget)
    {
        $this->widgets->removeElement($widget);
        $rows = $this->getRows();
        $rowId = $widget->getId();
        
        if (array_key_exists($rowId, $rows)) {
            unset($rows[$rowId]);

            $this->setRows($rows);
        }
        
        return $this;
    }

    /**
     * Get widgets
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getWidgets($indexArray = false)
    {
        $widgets = new ArrayCollection();
        $widgetIndex = array();
        foreach ($this->widgets as $widget) {
            if (!$widget->isDeleted()) {
                $widgets->add($widget);
                $widgetIndex[$widget->getId()] = $widget;
            }
        }
        
        return ($indexArray)? $widgetIndex : $widgets;
    }
    
    /**
     * Get widgets based on their type
     * 
     * @param type $type
     * @return type
     */
    public function getWidgetsByType($type)
    {
        $widgets = array();
        foreach ($this->getWidgets() as $widget) {
            if ($type === $widget->getType()) {
                $widgets[] = $widget;
            }
        }
        
        return $widgets;
    }
    
    /**
     * Get all rows
     * 
     * @return array
     */
    public function getRows()
    {
        $rows = $this->getStructure();
        if (!is_array($rows)) {
            $rows = array();
        }
        
        return $rows;
    }
    
    /**
     * Set rows
     * 
     * @param type $rows
     * @return \Saman\AppearanceBundle\Entity\Theme
     */
    public function setRows($rows)
    {
        if (is_array($rows)) {
            $this->setStructure($rows);
        }
        
        return $this;
    }
    
    /**
     * Get one row
     * 
     * @param type $rowId
     * @return array
     */
    public function getRow($rowId)
    {
        $rows = $this->getRows();
        if (array_key_exists($rowId, $rows)) {
            return $rows[$rowId];
        }
        
        return null;
    }
    
    /**
     * 
     * @param type $rowId
     * @return type
     */
    public function getColumns($rowId)
    {
        $row = $this->getRow($rowId);
        if (null === $row) {
            return null;
        }
        
        if ('row' !== $row['type']) {
            throw new \Exception(sprintf('This item is not a row type ID: %d', $rowId));
        }
        
        return $row['columns'];
    }

    /**
     * Update rows sort order
     * 
     * @param type $sortIds
     * @return \Saman\AppearanceBundle\Entity\Theme
     */
    public function updateRowsSortOrder($sortIds, $parentRowId, $parentColumnId)
    {
        $rows = $this->getRows();
        foreach ($sortIds as $sort => $rowId) {
            if (array_key_exists($rowId, $rows)) {
                // Get the row
                $row = $rows[$rowId];
                // Update sort and parent row and column ID
                $row['sort'] = $sort;
                $row['parentRowId'] = $parentRowId;
                $row['parentColumnId'] = $parentColumnId;
                // Set new updated row
                $rows[$rowId] = $row;
            }
        }
        $this->setRows($rows);
        
        return $this;
    }
    
    /**
     * Get one row settings
     * 
     * @param type $rowId
     * @return type
     */
    public function getRowSetting($rowId)
    {
        $row = $this->getRow($rowId);
        if (null === $row) {
            return null;
        }
        
        return $row['settings'];
    }
    
    /**
     * Set one row settings
     * 
     * @param type $rowId
     * @return array
     */
    public function setRowSettings($rowId, $rowSettings = null, $columnsSettings = null)
    {
        $row = $this->getRow($rowId);
        if (null === $row) {
            return $this;
        }
        
        if (null !== $rowSettings) {
            $row['settings'] = $rowSettings;
        }
        
        if (null !== $columnsSettings) {
            foreach ($row['columns'] as $column) {
                if (array_key_exists($column['id'], $columnsSettings)) {
                    $row['columns'][$column['id']]['settings'] = $columnsSettings[$column['id']];
                }
            }
        }
        $this->structure[$rowId] = $row;
        
        return $this;
    }
    
    /**
     * 
     * @param int $parentRowId
     * @param type $columns
     * @return \Saman\AppearanceBundle\Entity\Theme
     */
    public function addRow($numberOfColumns, $parentRowId = '0', $parentColumnId = '0')
    {
        $rows = $this->getRows();
        // Generate row uniq ID
        $rowId = $this->getUniqID($rows, 'r');
        
        // Get last sort order of rows
        $sort = $this->getRowsLastSortOrder(
            $rows, 
            $parentRowId, 
            $parentColumnId
            );
        
        $columns = array();
        $allColumns = $this->getAllColumns();
        // Generate row columns width percentage
        $colMd = round(12/$numberOfColumns);
        for ($i = 1; $i <= $numberOfColumns; $i++) {
            $columnId = $this->getUniqID($allColumns, 'c');
            if ($i === $numberOfColumns) {
                $colMd = $colMd + (12 % $numberOfColumns);
            }
            
            $columnSettings = array(
                'col_md' => $colMd,
                'col_sm' => $colMd,
                'col_xs' => $colMd,
                );
            
            $columns[$columnId] = array(
                'id' => $columnId,
                'settings' => $columnSettings, 
            );
        }
        
        $settings = array(
            'hight' => null,
            );
                
        $rows[$rowId] = array(
            'id' => $rowId,
            'parentRowId' => $parentRowId,
            'parentColumnId' => $parentColumnId,
            'type' => 'row',
            'sort' => $sort,
            'settings' => $settings,
            'columns' => $columns
            );
        
        $this->setRows($rows);
        
        return $this;
    }
    
    /**
     * Add widgets
     * 
     * @param Widget $widget
     * @param type 
     * @param type $parentColumnId
     * @return \Saman\AppearanceBundle\Entity\Theme
     * @throws \Exception
     */
    public function addWidget(Widget $widget)
    {
        $this->widgets->add($widget);
        $rows = $this->getRows();
        
        $parentRowId = $widget->getRowId();
        $parentColumnId = $widget->getColumnId();
        $widgetId = $widget->getId();
        
        if ($parentRowId !== '0' && !array_key_exists($parentRowId, $rows)) {
            throw new \Exception(sprintf('No row exist with this ID: %d', $parentRowId));
        }
        
        if ($parentColumnId !== '0' && !array_key_exists($parentColumnId, $rows[$parentRowId]['columns'])) {
            throw new \Exception(sprintf('No row column exist with this ID: %d', $parentColumnId));
        }
        
        // Get last sort order of rows
        $sort = $this->getRowsLastSortOrder(
            $rows, 
            $parentRowId, 
            $parentColumnId
            );
                
        $rows[$widgetId] = array(
            'id' => $widgetId,
            'parentRowId' => $parentRowId,
            'parentColumnId' => $parentColumnId,
            'type' => 'widget',
            'sort' => $sort,
            'settings' => array(),
            );        
        
        $this->setRows($rows);

        return $this;
    }    
    
    /**
     * Get last sort order of rows (row, widgets) based on $parentRowId and $parentColumnId
     * 
     * @param type $rows
     * @param type $parentRowId
     * @param type $parentColumnId
     * @return int
     */
    private function getRowsLastSortOrder($rows, $parentRowId, $parentColumnId)
    {
        $sort = 1;
        foreach ($rows as $row) {
            if ($parentRowId === $row['parentRowId'] && $parentColumnId === $row['parentColumnId']) {
                $sort++;
            }
        }
        
        return $sort;
    }
    
    /**
     * Count number of row's childrens
     * 
     * @param type $rowId
     * @return type
     */
    public function countRowChildrens($rowId = '0')
    {
        return count($this->getRowChildrens($rowId, false));
    }
    
    /**
     * Get rows childrens (rows and widgets) regardless of wich column they belong
     * 
     * @param type $parentRowId
     * @return type
     */
    public function getRowChildrens($parentRowId = '0', $sort = true)
    {
        $rowChilds = array();
        foreach ($this->getRows() as $item) {
            if ($parentRowId === $item['parentRowId']) {
                $rowChilds[] = $item;
            }
        }
        
        if ($sort) {
            $sortOrder = array();
            foreach ($rowChilds as $key => $rowChild) {
                $sortOrder[$key] = $rowChild['sort'];
            }

            array_multisort($sortOrder, SORT_ASC, $rowChilds);        
        }
        
        return $rowChilds;
    }
    
    /**
     * 
     * @param type $rowItems
     * @param type $columnId
     * @return type
     */
    public function getColumnChildrens($rowItems, $columnId)
    {
        $columnChilds = array();
        foreach ($rowItems as $item) {
            if ($columnId === $item['parentColumnId']) {
                $columnChilds[] = $item;
            }
        }
    
        return $columnChilds;
    }
    
    /**
     * Delete a row (Widget or Row)
     * 
     * @param type $rowId
     * @return \Saman\AppearanceBundle\Entity\Theme
     */
    public function deleteRow($rowId)
    {
        $rows = $this->getRows();
        if (array_key_exists($rowId, $rows)) {
            unset($rows[$rowId]);
            $this->setRows($rows);
        }
        
        return $this;
    }
    
    /**
     * Get all columns (For example this function is used to generate a uniq column ID)
     * 
     * @return type
     */
    public function getAllColumns()
    {
        $allColumns = array();
        $rows = $this->getRows();
        
        foreach ($rows as $row) {
            if (array_key_exists('columns', $row)) {
                foreach ($row['columns'] as $column) {
                    $allColumns[$column['id']] = $column;
                }
            }
        }
        
        return $allColumns;
    }
    
    /**
     * 
     * @param type $themeSettingOptions
     * @param type $templateParameters
     */
    public static function generateThemeSettings($themeSettingOptions, $templateParameters)
    {
        $themeSettings = array();
        $templateSettings = $templateParameters['settings'];
        foreach ($templateSettings as $key => $templateSetting) {
            if (is_array($themeSettingOptions) && array_key_exists($key, $themeSettingOptions)) {
                $themeSettings[$key] = $themeSettingOptions[$key];
            } else {
                $themeSettings[$key] = $templateSetting['default'];
            }
        }
        
        return $themeSettings;
    }
    
    /**
     * 
     * @return array of navigations ID
     */
    public function getNavigationsId()
    {
        $navigationsId = array();
        foreach ($this->getWidgets() as $widget) {
            if ($widget->isNavigationRelated()) {
                $navigationsId[] = (int) $widget->getSetting(Widget::WIDGET_TYPE_MENU_SETTINGS_MENU);
            }
        }
        
        $navigationsId = array_unique($navigationsId);
        if(($key = array_search(0, $navigationsId)) !== false) {
            unset($navigationsId[$key]);
        }        
        
        return $navigationsId;
    }
    
    /**
     * 
     * @param type $navigationId
     * @return type
     */
    public function hasThisNavigation($navigationId)
    {
        return in_array($navigationId, $this->getNavigationsId());
    }
    
    /**
     * 
     * @param type $rows
     * @return type
     */
    private function getUniqID($rows, $prefix)
    {
        do {
            $id = $prefix . rand(1, 10000000);
        } while (array_key_exists($id, $rows));
        
        return $id;
    }
}