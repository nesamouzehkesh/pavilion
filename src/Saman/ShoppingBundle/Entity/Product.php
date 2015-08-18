<?php

namespace Saman\ShoppingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Saman\Library\Base\BaseEntity;


/**
 * Product
 *
 * @ORM\Table(name="saman_product")
 * @ORM\Entity(repositoryClass="Saman\ShoppingBundle\Repository\ProductRepository")
 */
class Product extends BaseEntity
{
    const ITEM_LOGO = 'icon.product';
    
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
     * @ORM\Column(name="content", type="text", nullable=true)
     */
    private $content;
    
    /**
     * @var string
     * @Saman\Library\Annotation\MediaAnnotation(type="single")
     * @ORM\Column(name="image", type="text", nullable=true)
     */
    private $image;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="price", type="integer", nullable=true)
     */
    private $price;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="viewed", type="integer", nullable=true)
     */
    private $viewed;
    
    /**
     * @ORM\ManyToMany(targetEntity="Saman\LabelBundle\Entity\Label")
     * @ORM\JoinTable(name="saman_product_label",
     * joinColumns={@ORM\JoinColumn(name="product_id", referencedColumnName="id")},
     * inverseJoinColumns={@ORM\JoinColumn(name="label_id", referencedColumnName="id")})
     */
    private $labels;
    
    /**
     *
     * @var type
     */
    private $images;
    
    /**
     * 
     */
    public function __construct() 
    {
        parent::__construct();
        $this->labels = new ArrayCollection();
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
     * @return Product
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
     * Set content
     *
     * @param string $content
     * @return Product
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
     * Set price
     *
     * @param integer $price
     * @return Product
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return integer 
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set image
     *
     * @param String $image
     * @return Product
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return String
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set viewed
     *
     * @param integer $viewed
     * @return Product
     */
    public function setViewed($viewed)
    {
        $this->viewed = $viewed;

        return $this;
    }

    /**
     * Get viewed
     *
     * @return integer 
     */
    public function getViewed()
    {
        return $this->viewed;
    }
    
    /**
     * 
     * @return \Saman\ShoppingBundle\Entity\Product
     */
    public function increaseViewed()
    {
        $this->viewed = $this->viewed + 1;
        
        return $this;
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
     * Set images
     *
     * @param String $image
     * @return Product
     */
    public function setImages($images)
    {
        $this->images = $images;

        return $this;
    }

    /**
     * Get images
     *
     * @return Array
     */
    public function getImages()
    {
        return $this->images;
    }    
}