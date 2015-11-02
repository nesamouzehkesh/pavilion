<?php

namespace ShoppingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Library\Base\BaseEntity;
use Library\Api\Payment\PaymentEntityInterface;
use ShoppingBundle\Library\Serializer\AbstractOrderSerializer;
use ShoppingBundle\Library\Serializer\PayPalOrderSerializer;

/**
 * Order
 *
 * @ORM\Table(name="saman_order_payment")
 * @ORM\Entity(repositoryClass="\ShoppingBundle\Entity\Repository\PaymentRepository")
 */
class OrderPayment extends BaseEntity implements PaymentEntityInterface
{
    const ITEM_LOGO = 'icon.orderPayment';
    
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
     * @ORM\Column(name="date", type="integer")
     */
    protected $date;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer")
     */
    private $status;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="type", type="integer")
     */
    private $type;
    
    /**
     * @var string
     *
     * @ORM\Column(name="content", type="array", nullable=true)
     */
    private $content;
    
    /**
     * @var array
     *
     * @ORM\Column(name="item_list", type="array", nullable=true)
     */
    private $itemList;
    
    /**
     * @var string
     *
     * @ORM\Column(name="currency", type="string", length=255)
     */
    private $currency;    
    
    /**
     * @var integer
     *
     * @ORM\Column(name="value", type="decimal")
     */
    private $value;
    
    /**
     * @ORM\ManyToOne(targetEntity="\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     **/
    private $user;
    
    /**
     * @ORM\ManyToOne(targetEntity="Order", inversedBy="payments")
     * @ORM\JoinColumn(name="order_id", referencedColumnName="id", nullable=true)
     **/
    private $order;
    
    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;    
    
    /**
     * 
     */
    public function __construct()
    {
        parent::__construct();
        
        $date = new \DateTime();       
        $this->setDate($date->getTimestamp());
    }
    
    /**
     * 
     * @param \Doctrine\ORM\EntityManagerInterface $em
     * @return \ShoppingBundle\Entity\Repository\PaymentRepository
     */
    public static function getRepository(\Doctrine\ORM\EntityManagerInterface $em)
    {
        return $em->getRepository(__CLASS__);
    }
    
    /**
     * 
     * @return AbstractOrderSerializer
     * @throws \Exception
     */
    public function getPaymentSerializer()
    {
        if (null === $this->getType()) {
            throw new \Exception('No payment type is defined');
        }
        
        switch ($this->getType()) {
            case OrderPayment::TYPE_PAY_PAL:
                return new PayPalOrderSerializer();
        }
        
        throw new \Exception('No payment serializer is defined for this payment type');
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
     * Set date
     *
     * @param integer $date
     * @return Payment
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return integer 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set type
     *
     * @param integer $type
     * @return Payment
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
     * @param array $content
     * @return Payment
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return array 
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set value
     *
     * @param array $value
     * @return Payment
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return array 
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set user
     *
     * @param \UserBundle\Entity\User $user
     * @return Payment
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
     * Set order
     *
     * @param \ShoppingBundle\Entity\Order $order
     * @return OrderPayment
     */
    public function setOrder(\ShoppingBundle\Entity\Order $order = null)
    {
        $this->order = $order;

        return $this;
    }

    /**
     * Get order
     *
     * @return \ShoppingBundle\Entity\Order 
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return OrderPayment
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
     * Set currency
     *
     * @param string $currency
     * @return OrderPayment
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
        return $this->currency;
    }

    /**
     * Set itemList
     *
     * @param array $itemList
     * @return OrderPayment
     */
    public function setItemList($itemList)
    {
        $this->itemList = $itemList;

        return $this;
    }

    /**
     * Get itemList
     *
     * @return array 
     */
    public function getItemList()
    {
        return $this->itemList;
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
}
