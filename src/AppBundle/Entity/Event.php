<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * User
 *
 * @ORM\Table(name="saman_event")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\EventRepository")
 */
class Event
{
    const TR_ADD_ORDER = 1;
    const TR_EDIT_ORDER = 2;
    const TR_PAYMENT_ORDER = 3;
    
    public static $triggers = array(
        self::TR_ADD_ORDER   => 'Add Order',
        self::TR_EDIT_ORDER  => 'Edit Order',
        self::TR_PAYMENT_ORDER  => 'Payment Order',
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
     * @var integer
     * 
     * @ORM\Column(name="date", type="integer")
     */
    protected $date;    

    /**
     * @var integer
     *
     * @ORM\Column(name="trigger_id", type="integer")
     */
    private $trigger;

    /**
     * @ORM\ManyToOne(targetEntity="\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;
    
    /**
     * @var string
     *
     * @ORM\Column(name="entity_path_name", type="string", length=255)
     */
    private $entityPathName;
    
    /**
     * @var string
     * 
     * @ORM\Column(name="entity_path_id", type="string", length=255)
     */
    protected $entityPathId;
    
    /**
     * 
     */
    public function __construct()
    {
        $date = new \DateTime();
        $this->date = $date->getTimestamp();        
    }
    
    /**
     * 
     * @param \Doctrine\ORM\EntityManagerInterface $em
     * @return \AppBundle\Entity\Repository\EventRepository
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
     * Set trigger
     *
     * @param string $trigger
     * @return Event
     */
    public function setTrigger($trigger)
    {
        $this->trigger = $trigger;

        return $this;
    }

    /**
     * Get trigger
     *
     * @return string 
     */
    public function getTrigger()
    {
        return $this->trigger;
    }
    
    /**
     * Get trigger
     *
     * @return string 
     */
    public function getTriggerLabel()
    {
        return self::$triggers[$this->trigger];
    }

    /**
     * Set entityPathName
     *
     * @param integer $entityPathName
     * @return Event
     */
    public function setEntityPathName($entityPathName)
    {
        $this->entityPathName = $entityPathName;

        return $this;
    }

    /**
     * Get entityPathName
     *
     * @return integer 
     */
    public function getEntityPathName()
    {
        return $this->entityPathName;
    }

    /**
     * Set entityPathId
     *
     * @param boolean $entityPathId
     * @return Event
     */
    public function setEntityPathId($entityPathId)
    {
        $this->entityPathId = $entityPathId;

        return $this;
    }

    /**
     * Get entityPathId
     *
     * @return boolean 
     */
    public function getEntityPathId()
    {
        return $this->entityPathId;
    }

    /**
     * Set user
     *
     * @param \UserBundle\Entity\User $user
     * @return Event
     */
    public function setUser(\UserBundle\Entity\User $user)
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
     * Set date
     *
     * @param integer $date
     * @return Event
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
}
