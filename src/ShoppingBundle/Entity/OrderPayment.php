<?php

namespace ShoppingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Library\Base\BaseEntity;

/**
 * Order
 *
 * @ORM\Table(name="saman_order_payment")
 * @ORM\Entity(repositoryClass="\ShoppingBundle\Entity\Repository\PaymentRepository")
 */
class OrderPayment extends BaseEntity
{
    const ITEM_LOGO = 'icon.order';
    
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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;
    
    /**
     * @var integer
     * 
     * @ORM\Column(name="date", type="integer")
     */
    protected $date;
    
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
     * 
     */
    public function __construct()
    {
        parent::__construct();
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
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
    
    
    /**
     * Set name
     *
     * @param integer $name
     * @return Payment
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get date
     *
     * @return integer 
     */
    public function getName()
    {
        return $this->name;
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
}
