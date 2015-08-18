<?php

namespace Saman\AppearanceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Saman\Library\Base\BaseEntity;

/**
 * Page
 *
 * @ORM\Table(name="saman_widget")
 * @ORM\Entity(repositoryClass="Saman\AppearanceBundle\Repository\WidgetRepository")
 */
class Widget extends BaseEntity
{
    const ITEM_LOGO = 'icon.widget';
    
    const WIDGET_TYPE_MENU = 'menu';
    const WIDGET_TYPE_ICON = 'icon';
    const WIDGET_TYPE_PAGE = 'page';
    const WIDGET_TYPE_CAROUSEL = 'carousel';
    
    const WIDGET_TYPE_MENU_SETTINGS_MENU = 'menu';
    const PLACEHOLDER_WIDGET_PAGE = '::widgetPage::';
        
    public static $navigationRelatedWidgets = array(
        self::WIDGET_TYPE_MENU
    );
    
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
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     */
    private $title;
    
    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255)
     */
    private $type;
    
    /**
     * @var int
     *
     * @ORM\Column(name="theme_row_id", type="string", length=255)
     */
    private $rowId;    
    
    /**
     * @var int
     *
     * @ORM\Column(name="theme_column_id", type="string", length=255)
     */
    private $columnId;    
    
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
     * @var string
     *
     * @ORM\Column(name="cssContent", type="text", nullable=true)
     */
    private $cssContent;
    
    /**
     * @var array
     *
     * @ORM\Column(name="settings", type="array", nullable=true)
     */
    private $settings;

    /**
     * @ORM\ManyToOne(targetEntity="Theme", inversedBy="widgets")
     * @ORM\JoinColumn(name="theme_id", referencedColumnName="id")
     **/
    private $theme;
    
    /**
     *
     * @var type 
     */
    private $widgetStructure;
    
    public function __construct()
    {
        parent::__construct();
        $this->widgetStructure = null;
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
     * Set cssContent
     *
     * @param string $cssContent
     * @return Page
     */
    public function setCssContent($cssContent)
    {
        $this->cssContent = $cssContent;

        return $this;
    }

    /**
     * Get cssContent
     *
     * @return string 
     */
    public function getCssContent()
    {
        return $this->cssContent;
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
     * @param type $key
     * @return type
     */
    public function getSetting($key)
    {
        if (array_key_exists($key, $this->settings)) {
            return $this->settings[$key];
        }
        
        return null;
    }    
    
    /**
     * Set theme
     *
     * @param \Saman\AppearanceBundle\Entity\Theme $theme
     * @return Navigation
     */
    public function setTheme(\Saman\AppearanceBundle\Entity\Theme $theme)
    {
        $this->theme = $theme;

        return $this;
    }

    /**
     * Get theme
     *
     * @return \Saman\AppearanceBundle\Entity\Theme $theme
     */
    public function getTheme()
    {
        return $this->theme;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return Widget
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }
    
    /**
     * 
     * @return type
     */
    public function isNavigationRelated()
    {
        return in_array($this->type, self::$navigationRelatedWidgets);
    }

    /**
     * Set row
     *
     * @param integer $rowId
     * @return Widget
     */
    public function setRowId($rowId)
    {
        $this->rowId = $rowId;

        return $this;
    }

    /**
     * Get row
     *
     * @return integer 
     */
    public function getRowId()
    {
        return $this->rowId;
    }

    /**
     * Set column
     *
     * @param integer $column
     * @return Widget
     */
    public function setColumnId($columnId)
    {
        $this->columnId = $columnId;

        return $this;
    }

    /**
     * Get column
     *
     * @return integer 
     */
    public function getColumnId()
    {
        return $this->columnId;
    }
    
    /**
     * 
     * @param type $widgetStructure
     */
    public function setWidgetStructure($widgetStructure)
    {
        $this->widgetStructure = $widgetStructure;
    }
    
    /**
     * 
     * @return type
     */
    public function getWidgetStructure()
    {
        return $this->widgetStructure;
    }
    
    /**
     * 
     * @param type $widgetsStructure
     */
    public static function getWidgetChoiceFieldValues($widgetsStructure)
    {
        $choices = array();
        foreach ($widgetsStructure as $key => $widgetStructure) {
            $choices[$key] = $widgetStructure['title'];
        }
        
        return $choices;
    }
}