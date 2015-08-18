<?php

namespace Saman\AppearanceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Saman\Library\Base\BaseEntity;

/**
 * Page
 *
 * @ORM\Table(name="saman_link")
 * @ORM\Entity(repositoryClass="Saman\AppearanceBundle\Repository\LinkRepository")
 */
class Link extends BaseEntity
{
    const ITEM_LOGO = 'icon.link';
    
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="sort", type="integer", nullable=true)
     */
    private $sort;    
    
    /**
     * @ORM\ManyToOne(targetEntity="Navigation", inversedBy="links")
     * @ORM\JoinColumn(name="navigation_id", referencedColumnName="id")
     **/
    private $navigation;
    
    /**
     * @ORM\OneToMany(targetEntity="Link", mappedBy="parent")
     **/
    private $children;

    /**
     * @ORM\ManyToOne(targetEntity="Link", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     **/
    private $parent;    
    
    /**
     * @ORM\ManyToOne(targetEntity="Saman\CmsBundle\Entity\Page")
     * @ORM\JoinColumn(name="page_id", referencedColumnName="id", nullable=true)
     **/
    private $page;
    
    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;
    
    /**
     * @var string
     *
     * @ORM\Column(name="hint", type="text", nullable=true)
     */
    private $hint;

    /**
     * @var string
     *
     * @ORM\Column(name="icon", type="string", length=255, nullable=true)
     */
    private $icon;
    
    /**
     * @var string
     *
     * @ORM\Column(name="url", type="text", nullable=true)
     */
    private $url;
    
    /**
     * @var array
     *
     * @ORM\Column(name="settings", type="array", nullable=true)
     */
    private $settings;
    
    public function __construct()
    {
        parent::__construct();
        $this->children = new ArrayCollection();
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
     * Set sort
     *
     * @return integer 
     */
    public function setSort($sort)
    {
        $this->sort = $sort;
        
        return $sort;
    }
    
    /**
     * Get sort
     *
     * @return integer 
     */
    public function getSort()
    {
        return $this->sort;
    }    

    /**
     * Set title
     *
     * @param string $title
     * @return Link
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
     * Set hint
     *
     * @param string $hint
     * @return Link
     */
    public function setHint($hint)
    {
        $this->hint = $hint;

        return $this;
    }

    /**
     * Get hint
     *
     * @return string 
     */
    public function getHint()
    {
        return $this->hint;
    }

    /**
     * Set url
     *
     * @param string $url
     * @return Link
     */
    public function setUrl($url)
    {
        $this->url = $url;
        $this->page = null;

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
     * Set settings
     *
     * @param array $settings
     * @return Link
     */
    public function setSettings($settings)
    {
        $this->settings = $settings;

        return $this;
    }

    /**
     * Get settings
     *
     * @return array 
     */
    public function getSettings()
    {
        return $this->settings;
    }

    /**
     * Set navigation
     *
     * @param \Saman\AppearanceBundle\Entity\Navigation $navigation
     * @return Link
     */
    public function setNavigation(\Saman\AppearanceBundle\Entity\Navigation $navigation = null)
    {
        $this->navigation = $navigation;

        return $this;
    }

    /**
     * Get navigation
     *
     * @return \Saman\AppearanceBundle\Entity\Navigation 
     */
    public function getNavigation()
    {
        return $this->navigation;
    }

    /**
     * Add children
     *
     * @param \Saman\AppearanceBundle\Entity\Link $children
     * @return Link
     */
    public function addChild(\Saman\AppearanceBundle\Entity\Link $children)
    {
        $this->children[] = $children;

        return $this;
    }

    /**
     * Remove children
     *
     * @param \Saman\AppearanceBundle\Entity\Link $children
     */
    public function removeChild(\Saman\AppearanceBundle\Entity\Link $children)
    {
        $this->children->removeElement($children);
    }

    /**
     * Get children
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Set parent
     *
     * @param \Saman\AppearanceBundle\Entity\Link $parent
     * @return Link
     */
    public function setParent(\Saman\AppearanceBundle\Entity\Link $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \Saman\AppearanceBundle\Entity\Link 
     */
    public function getParent()
    {
        return $this->parent;
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
     * Set page
     *
     * @param \Saman\CmsBundle\Entity\Page $page
     * @return Link
     */
    public function setPage(\Saman\CmsBundle\Entity\Page $page = null)
    {
        $this->page = $page;
        if (null === $this->title) {
            $this->setTitle($page->getTitle());
        }
        $this->url = null;

        return $this;
    }

    /**
     * Get page
     *
     * @return \Saman\CmsBundle\Entity\Page 
     */
    public function getPage()
    {
        return $this->page;
    }
}