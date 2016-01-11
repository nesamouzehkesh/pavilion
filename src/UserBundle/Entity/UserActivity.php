<?php

namespace UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Acme\UserBundle\Entity\User
 *
 * @ORM\Table(name="saman_user_activity")
 * @ORM\Entity(repositoryClass="UserBundle\Entity\Repository\UserActivityRepository")
 */
class UserActivity
{
    const ACTIVITY_LOGIN = 1;
    
    public static $userActivities = array(
        self::ACTIVITY_LOGIN => 'user.log.activity.login'
    );
    
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @ORM\Column(name="type", type="integer")
     */
    protected $type;
    
    /**
     * @ORM\Column(name="time", type="integer")
     */
    protected $time;
    
    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     **/
    private $user;    
    
    /**
     * 
     * @param \Doctrine\ORM\EntityManagerInterface $em
     * @return \UserBundle\Entity\Repository\UserActivityRepository
     */
    public static function getRepository(\Doctrine\ORM\EntityManagerInterface $em)
    {
        return $em->getRepository(__CLASS__);
    }
    
    /**
     * 
     * @param type $type
     * @return type
     */
    public static function getTitle($type)
    {
        if (!array_key_exists($type, self::$userActivities)) {
            return null;
        }
        
        return self::$userActivities[$type];
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
     * @return UserActivity
     */
    public function setType($type)
    {
        if (!array_key_exists($type, self::$userActivities)) {
            throw new \Exception(sprintf('Invalid activity type %s is defined', $type));
        }
        
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
     * Set time
     *
     * @param integer $time
     * @return UserActivity
     */
    public function setTime($time)
    {
        $this->time = $time;

        return $this;
    }

    /**
     * Get time
     *
     * @return integer 
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * Set user
     *
     * @param \UserBundle\Entity\User $user
     * @return UserActivity
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
}
