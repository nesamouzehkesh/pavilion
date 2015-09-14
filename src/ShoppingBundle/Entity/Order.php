<?php

namespace Saman\ShoppingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Saman\Library\Base\BaseEntity;

/**
 * Order
 *
 * @ORM\Table(name="saman_order")
 * @ORM\Entity(repositoryClass="\Saman\ShoppingBundle\Entity\Repository\OrderRepository")
 */
class Order extends BaseEntity
{
    const ITEM_LOGO = 'icon.order';
    
    const STATUS_SUBMITTED = 1;
    const STATUS_APPROVED = 2;
    const STATUS_INPROGRESS = 3;
    const STATUS_STOPED = 4;
    const STATUS_REJECT = 5;
    const STATUS_FINALIZED = 6;
    const STATUS_SHIPPED = 7;
    
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
     * @var integer
     *
     * @ORM\Column(name="type", type="integer", nullable=true)
     */
    private $type;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer", nullable=true)
     */
    private $status;    

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text", nullable=true)
     */
    private $content;
    
    /**
     * @ORM\ManyToOne(targetEntity="\Saman\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=true)
     **/
    private $user;
    
    /**
     * @ORM\OneToMany(targetEntity="OrderProgress", mappedBy="order")
     **/
    private $progresses;    
    
    /**
     * @var string
     * @Saman\Library\Annotation\MediaAnnotation(type="single")
     * @ORM\Column(name="attachments", type="text", nullable=true)
     */
    private $attachments;
    
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
     * @return Repository\OrderRepository
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
     * @return Order
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
     * Set User
     * 
     * @param \Saman\UserBundle\Entity\User $user
     * @return Order
     */
    public function setUser(\Saman\UserBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get User
     *
     * @return \Saman\UserBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Add progresses
     *
     * @param OrderProgress $progresses
     * @return Order
     */
    public function addProgress(OrderProgress $progresses)
    {
        $this->progresses[] = $progresses;

        return $this;
    }

    /**
     * Remove progresses
     *
     * @param OrderProgress $progresses
     */
    public function removeProgress(OrderProgress $progresses)
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