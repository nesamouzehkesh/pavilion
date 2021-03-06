<?php

namespace ShoppingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Library\Base\BaseEntity;
use Library\Interfaces\ShoppingItemInterface;
use ShoppingBundle\Entity\OrderPayment;
use ShoppingBundle\Library\Serializer\AbstractOrderSerializer;

/**
 * Order
 *
 * @ORM\Table(name="saman_order")
 * @ORM\Entity(repositoryClass="\ShoppingBundle\Entity\Repository\OrderRepository")
 */
class Order extends BaseEntity implements ShoppingItemInterface
{
    const ITEM_LOGO = 'icon.order';
    const DEFAULT_CURRENCY = 'USD';
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
     * @var decimal
     *
     * @ORM\Column(name="totalPrice", type="decimal", nullable=true)
     */
    private $totalPrice;
    
    /**
     * @var decimal
     *
     * @ORM\Column(name="deposit", type="decimal", nullable=true)
     */
    private $deposit;
    
    /**
     * @var string
     *
     * @ORM\Column(name="currency", type="string", length=255)
     */
    private $currency;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="type", type="integer", nullable=true)
     */
    private $type;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="quantity", type="integer", nullable=true)
     */
    private $quantity;
    
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
     * @var integer
     *
     * @ORM\Column(name="progresses", type="integer", nullable=true)
     */
    private $progressesStatus;    
    
    /**
     * @ORM\OneToMany(targetEntity="OrderPayment", mappedBy="order")
     **/
    private $payments;
    
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
     * @ORM\Column(name="is_paid", type="boolean", nullable=true, options={"default"= 0})
     */
    protected $isPaid;    
    
    /**
     *
     * @var type 
     */
    private $loadedContent = null;
    
    /**
     * 
     */
    public function __construct()
    {
        parent::__construct();
        $this->quantity = 1;
        $this->currency = self::DEFAULT_CURRENCY;
        $this->products = new ArrayCollection();
        $this->progresses = new ArrayCollection();
        $this->payments = new ArrayCollection();        
        $this->settings = array();
    }
    
    /**
     * 
     * @param \Doctrine\ORM\EntityManagerInterface $em
     * @return \ShoppingBundle\Entity\Repository\OrderRepository
     */
    public static function getRepository(\Doctrine\ORM\EntityManagerInterface $em)
    {
        return $em->getRepository(__CLASS__);
    }
    
    /**
     * Get order title
     * 
     * @return string
     */
    public function getTitle()
    {
        return 'Order';
    }
    
    /**
     * Get order price
     * 
     * @return type
     */
    public function getPrice()
    {
        return $this->getTotalPrice();
    }
    
    /**
     * Get order SKU
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
     * Get order description
     * 
     * @param type $truncateLength
     */
    public function getDescription($truncateLength = null)
    {
        return 'description';
    }
    
    /**
     * 
     * @param AbstractOrderSerializer $orderSerializer
     * @return type
     */
    public function serialize(AbstractOrderSerializer $orderSerializer)
    {
        return $orderSerializer->serialize($this);
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
     * Set quantity
     *
     * @param integer $quantity
     * @return Order
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity
     *
     * @return integer 
     */
    public function getQuantity()
    {
        if (null === $this->quantity) {
            $this->quantity = 1;
        }
        
        return $this->quantity;
    }
    
    /**
     * 
     * @return type
     */
    public function isCustomOrder()
    {
        return $this->getType() === self::ORDER_TYPE_CUSTOM;
    }
    
    /**
     * 
     * @return type
     */
    public function isProductOrder()
    {
        return $this->getType() === self::ORDER_TYPE_PRODUCT;
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
     * 
     * @param type $loadedContent
     * @return \ShoppingBundle\Entity\Order
     */
    public function setLoadedContent($loadedContent)
    {
        $this->loadedContent = $loadedContent;

        return $this;
    }
    
    /**
     * 
     * @return type
     */
    public function getLoadedContent()
    {
        if (null === $this->loadedContent) {
            throw new \Exception('Items are not loaded in this order');
        }
            
        return $this->loadedContent;
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
        $progresses = new ArrayCollection();
        foreach ($this->progresses as $progress) {
            if (!$progress->isDeleted()) {
                $progresses->add($progress);
            }
        }
        
        return $progresses;        
    }
    
    /**
     * Get active Progress
     * 
     * @return type
     */
    public function getActiveProgress()
    {
        foreach ($this->getProgresses() as $progresses) {
            if ($progresses->getStatus() === OrderProgress::STATUS_INPROGRESS) {
                return $progresses;
            }
        }
        
        return null;
    }
    
    /**
     * @return bool
     */
    public function isSubmitted()
    {
        return $this->getProgressesStatus() === Progress::PROGRESS_SUBMITTED;
    } 
    
    /**
     * @return bool
     */
    public function isOpen()
    {
        return $this->getProgressesStatus() === Progress::PROGRESS_PAID;
    }

    /**
     * @return bool
     */
    public function isInProgress()
    {
        return $this->getProgressesStatus() === Progress::PROGRESS_INPROGRESS;
    }
    
    /**
     * @return bool
     */
    public function isFinalized()
    {
        return $this->getProgressesStatus() === Progress::PROGRESS_FINALIZED;
    }
    
    /**
     * @return bool
     */
    public function isShipped()
    {
        return $this->getProgressesStatus() === Progress::PROGRESS_SHIPPED;
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

    /**
     * Add payments
     *
     * @param \ShoppingBundle\Entity\OrderPayment $payments
     * @return Order
     */
    public function addPayment(\ShoppingBundle\Entity\OrderPayment $payments)
    {
        $this->payments[] = $payments;

        return $this;
    }

    /**
     * Remove payments
     *
     * @param \ShoppingBundle\Entity\OrderPayment $payments
     */
    public function removePayment(\ShoppingBundle\Entity\OrderPayment $payments)
    {
        $this->payments->removeElement($payments);
    }

    /**
     * Get payments
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPayments()
    {
        return $this->payments;
    }
    
    /**
     * Set progressesStatus
     *
     * @param integer $progressesStatus
     * @return Order
     */
    public function setProgressesStatus($progressesStatus)
    {
        $this->progressesStatus = $progressesStatus;

        return $this;
    }

    /**
     * Get progressesStatus
     *
     * @return integer 
     */
    public function getProgressesStatus()
    {
        return $this->progressesStatus;
    }

    /**
     * Set currency
     *
     * @param string $currency
     * @return Order
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * Get currency
     *
     * @return string 
     */
    public function getCurrency()
    {
        if (null === $this->currency or "" === $this->currency) {
            return self::DEFAULT_CURRENCY;
        }
        
        return $this->currency;
    }

    /**
     * Set totalPrice
     *
     * @param string $totalPrice
     * @return Order
     */
    public function setTotalPrice($totalPrice)
    {
        $this->totalPrice = $totalPrice;

        return $this;
    }

    /**
     * Get totalPrice
     *
     * @return string 
     */
    public function getTotalPrice()
    {
        if (!$this->isPaid()) {
            return $this->callTotalPrice();
        }
        
        return $this->totalPrice;
    }

    /**
     * Set deposit
     *
     * @param string $deposit
     * @return Order
     */
    public function setDeposit($deposit)
    {
        $this->deposit = $deposit;

        return $this;
    }

    /**
     * Get deposit
     *
     * @return string 
     */
    public function getDeposit()
    {
        return $this->deposit;
    }

    /**
     * Set isPaid
     *
     * @param boolean $isPaid
     * @return Order
     */
    public function setIsPaid($isPaid)
    {
        $this->isPaid = $isPaid;

        return $this;
    }

    /**
     * Get isPaid
     *
     * @return boolean 
     */
    public function isPaid()
    {
        return $this->isPaid;
    }
    
    /**
     * Calculate totalPrice
     * 
     * @param array $param
     * @return type
     */
    public function callTotalPrice()
    {
        $totalPrice = 0;
        if ($this->isCustomOrder()) {
            $totalPrice = 1;
        } else {
            foreach ($this->getLoadedContent() as $item) {
                $totalPrice = $totalPrice + intval($item['qty']) * $item['product']->getPrice();
            }            
        }
        
        return $totalPrice;
    }    
}