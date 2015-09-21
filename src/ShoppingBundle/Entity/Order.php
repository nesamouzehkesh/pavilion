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
     * @ORM\Column(name="content", type="array", nullable=true)
     */
    private $content;
    
    /**
     * @ORM\ManyToMany(targetEntity="\ProductBundle\Entity\Product", inversedBy="users")
     * @ORM\JoinTable(name="saman_product_order")
     **/  
    private $products;
    
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
     * @Library\Annotation\MediaAnnotation(type="multiple")
     * @ORM\Column(name="attachments", type="text", nullable=true)
     */
    private $attachments;
    
    /**
     * @ORM\ManyToOne(targetEntity="\UserBundle\Entity\Address")
     * @ORM\JoinColumn(name="billing_address_id", referencedColumnName="id")
     **/
    private $billingAddress;
    
    /**
     * @ORM\ManyToOne(targetEntity="\UserBundle\Entity\Address")
     * @ORM\JoinColumn(name="shipping_address_id", referencedColumnName="id")
     **/
    private $shippingAddress;    
    
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
    
    /**
     * Get active Progress
     * 
     * @return type
     */
    public function getActiveProgress()
    {
        foreach($this->getProgresses() as $progresses) {
            if ($progresses->getStatus() === OrderProgress::STATUS_INPROGRESS) {
                return $progresses;
            }
        }
        
        return null;
    }

    /**
     * Set billingAddress
     *
     * @param \UserBundle\Entity\Address $billingAddress
     * @return Order
     */
    public function setBillingAddress(\UserBundle\Entity\Address $billingAddress = null)
    {
        $this->billingAddress = $billingAddress;

        return $this;
    }

    /**
     * Get billingAddress
     *
     * @return \UserBundle\Entity\Address 
     */
    public function getBillingAddress()
    {
        return $this->billingAddress;
    }

    /**
     * Set shippingAddress
     *
     * @param \UserBundle\Entity\Address $shippingAddress
     * @return Order
     */
    public function setShippingAddress(\UserBundle\Entity\Address $shippingAddress = null)
    {
        $this->shippingAddress = $shippingAddress;

        return $this;
    }

    /**
     * Get shippingAddress
     *
     * @return \UserBundle\Entity\Address 
     */
    public function getShippingAddress()
    {
        return $this->shippingAddress;
    }
}
