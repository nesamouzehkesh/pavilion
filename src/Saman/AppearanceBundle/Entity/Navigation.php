<?php

namespace Saman\AppearanceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Saman\Library\Base\BaseEntity;

/**
 * Page
 *
 * @ORM\Table(name="saman_navigation")
 * @ORM\Entity(repositoryClass="Saman\AppearanceBundle\Repository\NavigationRepository")
 */
class Navigation extends BaseEntity
{
    const ITEM_LOGO = 'icon.navigation';
    
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
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;
    
    /**
     * @Saman\Library\Annotation\DeleteAnnotation(type="single")
     * @ORM\OneToMany(targetEntity="Link", mappedBy="navigation")
     * @ORM\OrderBy({"sort" = "ASC"})
     **/
    private $links;    

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
    
    public function __construct()
    {
        parent::__construct();
        $this->themes = new ArrayCollection();
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
     * @param string $content
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
     * Add links
     *
     * @param \Saman\AppearanceBundle\Entity\Link $links
     * @return Navigation
     */
    public function addLink(\Saman\AppearanceBundle\Entity\Link $links)
    {
        $this->links[] = $links;

        return $this;
    }

    /**
     * Remove links
     *
     * @param \Saman\AppearanceBundle\Entity\Link $links
     */
    public function removeLink(\Saman\AppearanceBundle\Entity\Link $links)
    {
        $this->links->removeElement($links);
    }

    /**
     * Get links
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getLinks()
    {
        return $this->links;
    }
    
    /**
     * 
     * @return type
     */
    public function getLastLinkSort()
    {
        $lastSort = 0;
        foreach ($this->links as $link) {
            if ($lastSort < $link->getSort()) {
                $lastSort = $link->getSort();
            }
        }
        
        return $lastSort;
    }
}