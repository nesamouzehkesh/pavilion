<?php

namespace ProductBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Library\Base\BaseEntity;
use Library\Interfaces\ShoppingItemInterface;

/**
 * User
 *
 * @ORM\Table(name="saman_product")
 * @ORM\Entity(repositoryClass="ProductBundle\Entity\Repository\ProductRepository")
 */
class Product extends BaseEntity implements ShoppingItemInterface
{
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
     * Product specifications
     * 
     * @var array
     *
     * @ORM\Column(name="specifications", type="array", nullable=true)
     */
    private $specifications;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="price", type="float", nullable=true)
     */
    private $price;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="original_price", type="float", nullable=true)
     */
    private $originalPrice;
    
    /**
     * @ORM\Column(name="available", type="boolean", options={"default"= 1})
     */
    protected $available;
    
    /**
     * @var string
     * @Library\Annotation\MediaAnnotation(type="single")
     * @ORM\Column(name="image", type="text", nullable=true)
     */
    private $image;
    
    /**
     * @var string
     * @Library\Annotation\MediaAnnotation(type="multiple")
     * @ORM\Column(name="images", type="text", nullable=true)
     */
    private $images;
    
    /**
     * @ORM\ManyToMany(targetEntity="\ShoppingBundle\Entity\Order", mappedBy="products")
     **/
    private $orders;
    
    /**
     * @ORM\ManyToMany(targetEntity="Category")
     * @ORM\JoinTable(name="saman_products_productcategory",
     * joinColumns={@ORM\JoinColumn(name="product_id", referencedColumnName="id")},
     * inverseJoinColumns={@ORM\JoinColumn(name="category_id", referencedColumnName="id")})
     */
    protected $categories;    
    
    /**
     * 
     */
    public function __construct()
    {
        parent::__construct();
        $this->orders = new ArrayCollection();
        $this->categories = new ArrayCollection();
        $this->setAvailable(true);
    }
    
    /**
     * 
     * @param \Doctrine\ORM\EntityManagerInterface $em
     * @return \ProductBundle\Entity\Repository\ProductRepository
     */
    public static function getRepository(\Doctrine\ORM\EntityManagerInterface $em)
    {
        return $em->getRepository(__CLASS__);
    }
    
    /**
     * Stock keeping unit
     * 
     * @return type
     */
    public function getSKU()
    {
        return sprintf(
            '%d',
            $this->getId()
            );
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
     * Set specifications
     *
     * @param string $specifications
     * @return Order
     */
    public function setSpecifications($specifications)
    {
        $this->specifications = $specifications;

        return $this;
    }

    /**
     * Get specifications
     *
     * @return string 
     */
    public function getSpecifications()
    {
        return $this->specifications;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Product
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
    public function getDescription($truncateLength = null)
    {
        return $this->truncate($this->description, $truncateLength);
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
     * Set originalPrice
     *
     * @param integer $originalPrice
     * @return Product
     */
    public function setOriginalPrice($originalPrice)
    {
        $this->originalPrice = $originalPrice;

        return $this;
    }

    /**
     * Get originalPrice
     *
     * @return integer 
     */
    public function getOriginalPrice()
    {
        return $this->originalPrice;
    }

    /**
     * Set available
     *
     * @param boolean $available
     * @return Product
     */
    public function setAvailable($available)
    {
        $this->available = $available;

        return $this;
    }

    /**
     * Get available
     *
     * @return boolean 
     */
    public function getAvailable()
    {
        return $this->available;
    }

    /**
     * Set image
     *
     * @param string $image
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
     * @return string 
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set images
     *
     * @param string $images
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
     * @return string 
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * Add orders
     *
     * @param \ShoppingBundle\Entity\Order $orders
     * @return Product
     */
    public function addOrder(\ShoppingBundle\Entity\Order $orders)
    {
        $this->orders[] = $orders;

        return $this;
    }

    /**
     * Remove orders
     *
     * @param \ShoppingBundle\Entity\Order $orders
     */
    public function removeOrder(\ShoppingBundle\Entity\Order $orders)
    {
        $this->orders->removeElement($orders);
    }

    /**
     * Get orders
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getOrders()
    {
        return $this->orders;
    }
    
    /**
     * Add categories
     *
     * @param \ProductBundle\Entity\Category $categories
     * @return Product
     */
    public function addCategory(\ProductBundle\Entity\Category $categories)
    {
        $this->categories[] = $categories;

        return $this;
    }

    /**
     * Remove categories
     *
     * @param \ProductBundle\Entity\Category $categories
     */
    public function removeCategory(\ProductBundle\Entity\Category $categories)
    {
        $this->categories->removeElement($categories);
    }

    /**
     * Get categories
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCategories()
    {
        return $this->categories;
    }
}
