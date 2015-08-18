<?php

namespace Saman\Library\Base;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\MappedSuperclass
 */
class BaseEntity
{
    const ITEM_LOGO = 'icon.page';
    const VISIBILITY_PUBLIC = 0;
    const VISIBILITY_PRIVATE = 1;
    const VISIBILITY_HIDDEN = 2;
    
    private static $visibilityArray = array(
        self::VISIBILITY_PUBLIC => array('logo' => 'icon.visibility.public'),
        self::VISIBILITY_PRIVATE => array('logo' => 'icon.visibility.private'),
        self::VISIBILITY_HIDDEN => array('logo' => 'icon.visibility.hidden')
    );
    
    /**
     * @ORM\Column(name="created_time", type="integer", nullable=true)
     */
    protected $createdTime;
    
    /**
     * @ORM\Column(name="modified_time", type="integer", nullable=true)
     */
    protected $modifiedTime;
    
    /**
     * @ORM\Column(name="is_deleted", type="boolean", nullable=true, options={"default"= 0})
     */
    protected $deleted;

    /**
     * @ORM\Column(name="deleted_time", type="bigint", nullable=true)
     */
    protected $deletedTime;
    
    /**
     * @ORM\Column(name="visibility", type="integer", options={"default"= 0})
     */
    protected $visibility;
    
    /**
     *
     * @var ArrayCollection $medias
     */
    protected $medias;
    
    /**
     * 
     */
    public function __construct()
    {
        $date = new \DateTime();
        $this->deleted = 0;
        $this->createdTime = $date->getTimestamp();
        $this->modifiedTime = $date->getTimestamp();
        $this->medias = new ArrayCollection();
        $this->setVisibility();
    }
    
    /**
     * 
     * @return type
     */
    public function isNew()
    {
        return null === $this->getId();
    }
    
    /**
     * Get modified time
     *
     * @return integer 
     */
    public function getCreatedTime()
    {
        return $this->createdTime;
    }       
    /**
     * Set modified time
     *
     * @param integer $modifiedTime
     * @return Page
     */
    public function setModifiedTime($modifiedTime = null)
    {
        if (null === $modifiedTime) {
            $date = new \DateTime();
            $modifiedTime = $date->getTimestamp();
        }
        $this->modifiedTime = $modifiedTime;

        return $this;
    }

    /**
     * Get modified time
     *
     * @return integer 
     */
    public function getModifiedTime()
    {
        return $this->modifiedTime;
    }    

    /**
     * Set deleted
     *
     * @param boolean $deleted
     * @return Rating
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;
    
        return $this;
    }

    /**
     * Get deleted
     *
     * @return boolean 
     */
    public function isDeleted()
    {
    	return $this->deleted;
    }

    /**
     * Get deleted
     *
     * @return boolean 
     */
    public function getDeleted()
    {
        return $this->deleted;
    }
    
    /**
     * Set deleted time as current time
     * 
     * @return \Library\BaseEntity
     */
    public function setDeletedTime()
    {
        $this->deletedTime = time();
        
        return $this;
    }
    
    /**
     * Get deletedTime
     * @return integer
     */
    public function getDeletedTime()
    {
        return $this->deletedTime;
    }
    
    /**
     * 
     * @param type $visibility
     */
    public function setVisibility($visibility = null)
    {
        $visibility = (null === $visibility)? self::VISIBILITY_PUBLIC : $visibility;
        if (!array_key_exists($visibility, BaseEntity::$visibilityArray)) {
            throw new \InvalidArgumentException('Visibility value is not correct');
        }
        $this->visibility = $visibility;
        
        return $this;        
    }
    
    /**
     * 
     * @return type
     */
    public function getVisibility()
    {
        return $this->visibility;
    }
    
    /**
     * 
     * @return type
     */
    public function getVisibilityLogo()
    {
        $visibilityArray = BaseEntity::$visibilityArray;
        
        return $visibilityArray[$this->visibility]['logo'];
    }    

    /**
     * 
     * @return type
     */
    public function setPublic()
    {
        return $this->setVisibility(self::VISIBILITY_PUBLIC);
    }    
    
    /**
     * 
     * @return type
     */
    public function isPublic()
    {
        return (self::VISIBILITY_PUBLIC === $this->visibility);
    }

    /**
     * 
     * @return type
     */
    public function setPrivate()
    {
        return $this->setVisibility(self::VISIBILITY_PRIVATE);
    }
    
    /**
     * 
     * @return type
     */
    public function isPrivate()
    {
        return (self::VISIBILITY_PRIVATE === $this->visibility);
    }

    /**
     * 
     * @return type
     */
    public function setHidden()
    {
        return $this->setVisibility(self::VISIBILITY_HIDDEN);
    }
    
    /**
     * 
     * @return type
     */
    public function isHidden()
    {
        return (self::VISIBILITY_HIDDEN === $this->visibility);
    }
    
    /**
     * 
     * @return type
     */
    public function getLogo()
    {
        return self::ITEM_LOGO;
    }
    
    /**
     * 
     * @param type $medias
     */
    public function setMedias($medias)
    {
        $this->medias = $medias;
    }
    
    /**
     * 
     * @return type
     */
    public function getMedias()
    {
        return $this->medias;
    }
}