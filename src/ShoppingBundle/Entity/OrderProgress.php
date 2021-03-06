<?php

namespace ShoppingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Library\Base\BaseEntity;

/**
 * OrderProgress
 *
 * @ORM\Table(name="saman_order_progress")
 * @ORM\Entity(repositoryClass="\ShoppingBundle\Entity\Repository\OrderProgressRepository")
 */
class OrderProgress extends BaseEntity
{
    const ITEM_LOGO = 'icon.orderProgress';
    
    const STATUS_INPROGRESS = 1;
    const STATUS_STOPED = 2;
    const STATUS_FINALIZED = 3;
    
    public static $statuses = array(
        self::STATUS_INPROGRESS => array('title' => 'Inprogress', 'class' => 'text-danger'),
        self::STATUS_STOPED =>     array('title' => 'Stoped', 'class' => 'text-warning'),
        self::STATUS_FINALIZED =>  array('title' => 'Finalized', 'class' => 'text-success'),
    );
    
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @ORM\ManyToOne(targetEntity="\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=true)
     **/
    private $user;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer", nullable=true)
     */
    private $status;   
    
    /**
     * @var integer
     *
     * @ORM\Column(name="start_date", type="integer", nullable=true)
     */
    private $startDate; 
    
    /**
     * @var integer
     *
     * @ORM\Column(name="estimated_end_date", type="integer", nullable=true)
     */
    private $estimatedEndDate; 
    
    /**
     * @var integer
     *
     * @ORM\Column(name="actual_end_date", type="integer", nullable=true)
     */
    private $actualEndDate; 
    
    /**
     * @var integer
     *
     * @ORM\Column(name="complete_percentage", type="integer", nullable=true)
     */
    private $completePercentage;     
    
    /**
     * @ORM\ManyToOne(targetEntity="Progress")
     * @ORM\JoinColumn(name="progress_id", referencedColumnName="id")
     **/
    private $progress;
    
    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text", nullable=true)
     */
    private $content;
    
    /**
     * @var string
     *
     * @ORM\Column(name="is_manual", type="boolean", nullable=true, options={"default"= 0})
     */    
    private $isManual;
    
    /**
     * @ORM\ManyToOne(targetEntity="Order", inversedBy="progresses")
     * @ORM\JoinColumn(name="order_id", referencedColumnName="id", nullable=true)
     **/
    private $order;
    
    /**
     * @var string
     * @Library\Annotation\MediaAnnotation(type="single")
     * @ORM\Column(name="attachments", type="text", nullable=true)
     */
    private $attachments;
    
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
    }
    
    /**
     * 
     * @param \Doctrine\ORM\EntityManagerInterface $em
     * @return \ShoppingBundle\Entity\Repository\OrderProgressRepository
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
     * Get status
     *
     * @return integer 
     */
    public function getStatusLabel()
    {
        return self::$statuses[$this->status];
    }       

    /**
     * Set content
     *
     * @param string $content
     * @return OrderProgress
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
     * Set progress
     *
     * @param Progress $progress
     * @return OrderProgress
     */
    public function setProgress(Progress $progress = null)
    {
        $this->progress = $progress;

        return $this;
    }

    /**
     * Get progress
     *
     * @return Progress 
     */
    public function getProgress()
    {
        return $this->progress;
    }
    
    /**
     * Set order
     *
     * @param bool $isManual
     * @return OrderProgress
     */
    public function setIsManual($isManual)
    {
        $this->isManual = $isManual;

        return $this;
    }

    /**
     * Get isManual
     *
     * @return bool
     */
    public function isManual()
    {
        return $this->isManual;
    }

    /**
     * Set order
     *
     * @param Order $order
     * @return OrderProgress
     */
    public function setOrder(Order $order = null)
    {
        $this->order = $order;

        return $this;
    }

    /**
     * Get order
     *
     * @return Order
     */
    public function getOrder()
    {
        return $this->order;
    }
    
    /**
     * Set attachments
     *
     * @param string $attachments
     * @return OrderProgress
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
     * Set startDate
     *
     * @param integer $startDate
     * @return OrderProgress
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * Get startDate
     *
     * @return integer 
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Set estimatedEndDate
     *
     * @param integer $estimatedEndDate
     * @return OrderProgress
     */
    public function setEstimatedEndDate($estimatedEndDate)
    {
        $this->estimatedEndDate = $estimatedEndDate;

        return $this;
    }

    /**
     * Get estimatedEndDate
     *
     * @return integer 
     */
    public function getEstimatedEndDate()
    {
        return $this->estimatedEndDate;
    }

    /**
     * Set actualEndDate
     *
     * @param integer $actualEndDate
     * @return OrderProgress
     */
    public function setActualEndDate($actualEndDate)
    {
        $this->actualEndDate = $actualEndDate;

        return $this;
    }

    /**
     * Get actualEndDate
     *
     * @return integer 
     */
    public function getActualEndDate()
    {
        return $this->actualEndDate;
    }

    /**
     * Set completePercentage
     *
     * @param integer $completePercentage
     * @return OrderProgress
     */
    public function setCompletePercentage($completePercentage)
    {
        $this->completePercentage = $completePercentage;

        return $this;
    }

    /**
     * Get completePercentage
     *
     * @return integer 
     */
    public function getCompletePercentage()
    {
        return $this->completePercentage;
    }
    
    /**
     * Set description
     *
     * @param string $description
     * @return Order
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
}