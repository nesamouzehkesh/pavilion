<?php

namespace CmsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Library\Base\BaseEntity;

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
        $this->url = $url;

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
}