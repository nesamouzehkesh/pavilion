<?php

namespace ShoppingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Library\Base\BaseEntity;

/**
 * Order
 *
 * @ORM\Table(name="saman_order")
 * @ORM\Entity(repositoryClass="\ShoppingBundle\Entity\Repository\OrderRepository")
 */
class Order extends BaseEntity
{
    const ITEM_LOGO = 'icon.order';
    
    const ORDER_TYPE_PRODUCT = 1;
    const ORDER_TYPE_CUSTOM = 2;
    
    const STATUS_SUBMITTED = 1;
    const STATUS_PAID = 2;
    const STATUS_INPROGRESS = 3;
    const STATUS_STOPED = 4;
    const STATUS_FINALIZED = 5;
    const STATUS_SHIPPED = 6;
    
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
     * @ORM\Column(name="type", type="integer", nullable=true)
     */
    private $type;
    
    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text", nullable=true)
     */
    private $content;
    
    /**
     * @ORM\ManyToMany(targetEntity="\ProductBundle\Entity\Product", inversedBy="users")
     * @ORM\JoinTable(name="saman_product_order")
     **/  
    private $products;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer", nullable=true)
     */
    private $status;    
    
    /**
     * @ORM\ManyToOne(targetEntity="\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=true)
     **/
    private $user;
    
    /**
     * @ORM\OneToMany(targetEntity="OrderProgress", mappedBy="order")
     **/
    private $progresses;    
    
    /**
     * @var string
     * @Library\Annotation\MediaAnnotation(type="single")
     * @ORM\Column(name="attachments", type="text", nullable=true)
     */
    private $attachments;
    
    /**
     * 
     */
    public function __construct()
    {
        parent::__construct();
        $this->products = new ArrayCollection();
        $this->settings = array();
    }
    
    /**
     * 
     * @param \Doctrine\ORM\EntityManagerInterface $em
     * @return Repository\OrderRepository
     */
    public static function getRepository(\Doctrine\ORM\EntityManagerInterface $em)
    {
        return $em->getRepository(__CLASS__);
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
     * Set type
     *
     * @param integer $type
     * @return Order
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return integer 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set content
     *
     * @param string $content
     * @return Order
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
     * Set status
     *
     * @param integer $status
     * @return Order
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set attachments
     *
     * @param string $attachments
     * @return Order
     */
    public function setAttachments($attachments)
    {
        $this->attachments = $attachments;

        return $this;
    }

    /**
     * Get attachments
     *
     * @return string 
     */
    public function getAttachments()
    {
        return $this->attachments;
    }

    /**
     * Add products
     *
     * @param \ProductBundle\Entity\Product $products
     * @return Order
     */
    public function addProduct(\ProductBundle\Entity\Product $products)
    {
        $this->products[] = $products;

        return $this;
    }

    /**
     * Remove products
     *
     * @param \ProductBundle\Entity\Product $products
     */
    public function removeProduct(\ProductBundle\Entity\Product $products)
    {
        $this->products->removeElement($products);
    }

    /**
     * Get products
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * Set user
     *
     * @param \UserBundle\Entity\User $user
     * @return Order
     */
    public function setUser(\UserBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \UserBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Add progresses
     *
     * @param \ShoppingBundle\Entity\OrderProgress $progresses
     * @return Order
     */
    public function addProgress(\ShoppingBundle\Entity\OrderProgress $progresses)
    {
        $this->progresses[] = $progresses;

        return $this;
    }

    /**
     * Remove progresses
     *
     * @param \ShoppingBundle\Entity\OrderProgress $progresses
     */
    public function removeProgress(\ShoppingBundle\Entity\OrderProgress $progresses)
    {
        $this->progresses->removeElement($progresses);
    }

    /**
     * Get progresses
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getProgresses()
    {
        return $this->progresses;
    }
}
