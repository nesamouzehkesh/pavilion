<?php

namespace CmsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Library\Base\BaseEntity;
use Library\Base\BaseHelper;

/**
 * Page
 *
 * @ORM\Table(name="saman_page")
 * @ORM\Entity(repositoryClass="CmsBundle\Entity\Repository\PageRepository")
 */
class Page extends BaseEntity
{
    const ITEM_LOGO = 'icon.page';
    
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
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;
    
    /**
     * @var string
     *
     * @ORM\Column(name="icon", type="string", length=255, nullable=true)
     */
    private $icon;    

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text", nullable=true)
     */
    private $content;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=255)
     */
    private $url;
    
    /**
     * @ORM\ManyToMany(targetEntity="LabelBundle\Entity\Label")
     * @ORM\JoinTable(name="saman_page_label",
     * joinColumns={@ORM\JoinColumn(name="page_id", referencedColumnName="id")},
     * inverseJoinColumns={@ORM\JoinColumn(name="label_id", referencedColumnName="id")})
     */
    protected $labels;    
    
    /**
     * @var array
     *
     * @ORM\Column(name="settings", type="array", nullable=true)
     */
    private $settings;
    
    /**
     * @var array
     *
     * @ORM\Column(name="staticContent", type="array", nullable=true)
     */
    private $staticContent;    
    
    /**
     * 
     */
    public function __construct()
    {
        parent::__construct();
        $this->labels = new ArrayCollection();
        $this->settings = array();
    }
        
    /**
     * 
     * @param \Doctrine\ORM\EntityManagerInterface $em
     * @return \CmsBundle\Entity\Repository\PageRepository
     */
    public static function getRepository(\Doctrine\ORM\EntityManagerInterface $em)
    {
        return $em->getRepository(__CLASS__);
    }    
    
    /**
     * 
     * @return type
     */
    public function __toString() {
        return $this->getTitle();
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
     * Set icon
     *
     * @param string $icon
     * @return Link
     */
    public function setIcon($icon)
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * Get icon
     *
     * @return string 
     */
    public function getIcon()
    {
        return $this->icon;
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
     * Set url
     *
     * @param string $url
     * @return Page
     */
    public function setUrl($url)
    {
        $this->url = BaseHelper::filterUrl($url);

        return $this;
    }

    /**
     * Get url
     *
     * @return string 
     */
    public function getUrl()
    {
        return $this->url;
    }
    
    /**
     * Add label
     *
     * @param \LabelBundle\Entity\Label $label
     * @return Appraisal
     */
    public function addLabel(\LabelBundle\Entity\Label $label)
    {
        $this->labels[] = $label;

        return $this;
    }

    /**
     * Remove label
     *
     * @param \LabelBundle\Entity\Label $label
     */
    public function removeLabel(\LabelBundle\Entity\Label $label)
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
        if (!is_array($this->settings)) {
            $this->settings = array();
        }
        
        return $this->settings;
    }
    
    /**
     * Get settings
     *
     * @return Array $settings
     */
    public function getSetting($setting)
    {
        if (is_array($this->settings) && array_key_exists($setting, $this->settings)) {
            return $this->settings[$setting];
        }
        
        return null;
    }
    
    /**
     * Get staticContent
     *
     * @return array 
     */
    public function getStaticContent($staticContentId = null)
    {
        if (null !== $staticContentId) {
            if (!array_key_exists($staticContentId, $this->staticContent)) {
                //throw new \Exception(sprintf('No static content exist with this ID: %d', $staticContentId));
                return null;
            }
            
            return $this->staticContent[$staticContentId];
        }

        return $this->staticContent;
    }

    /**
     * Set structure
     *
     * @param array $staticContent
     * @return Theme
     */
    public function setStaticContent($staticContent, $staticContentId = null)
    {
        if (null !== $staticContentId) {
            $this->staticContent[$staticContentId] = $staticContent;
        } else {
            $this->staticContent = $staticContent;
        }
        
        return $this;
    }
    
    /**
     * 
     * @param type $staticContents
     * @return \AppearanceBundle\Entity\Theme
     */
    public function addStaticContents($staticContents)
    {
        foreach ($staticContents as $staticContentId => $staticContent) {
            $this->staticContent[$staticContentId] = $staticContent;
        }
        
        return $this;
    }    
}