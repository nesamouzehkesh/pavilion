<?php

namespace MediaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Library\Base\BaseEntity;

/**
 * Media
 *
 * @ORM\Table(name="saman_media")
 * @ORM\Entity(repositoryClass="MediaBundle\Entity\Repository\MediaRepository")
 */
class Media extends BaseEntity
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="is_permanent", type="boolean", options={"default" = false})
     */
    private $isPermanent;
    
    /**
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     **/
    private $user;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;
    
    /**
     * @var string
     *
     * @ORM\Column(name="path", type="text")
     */
    private $path;

    /**
     * @var string
     *
     * @ORM\Column(name="entity_class", type="text", nullable=true)
     */
    private $class;
    
    /**
     * @var string
     *
     * @ORM\Column(name="entity_column", type="text", nullable=true)
     */
    private $column;

    /**
     * @var integer
     *
     * @ORM\Column(name="entity_id", type="integer", nullable=true)
     */
    private $entityId;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="size", type="integer", nullable=true)
     */
    private $size;
    
    /**
     *
     * @var string 
     */
    private $error = null;    

    /**
     * 
     */
    public function __construct($isPermanent = null, $visibility = null, $user = null)
    {
        parent::__construct();
        if (null !== $isPermanent) {
            $this->setIsPermanent($isPermanent);
        }
        
        if (null !== $visibility) {
            $this->setVisibility($visibility);
        }
        
        if (null !== $user) {
            $this->setUser($this->user);
        }
    }
    
    /**
     * 
     * @param \Doctrine\ORM\EntityManagerInterface $em
     * @return \MediaBundle\Entity\Repository\MediaRepository
     */
    public static function getRepository(\Doctrine\ORM\EntityManagerInterface $em)
    {
        return $em->getRepository(__CLASS__);
    }    
    
    /**
     * 
     */
    public function __toString()
    {
        return $this->title;
    }
    
    /**
     * 
     * @param type $isPermanent
     * @return \MediaBundle\Entity\Media
     */
    public function setIsPermanent($isPermanent)
    {
        $this->isPermanent = $isPermanent;
        
        return $this;
    }
    
    /**
     * 
     * @return type
     */
    public function isTemporary()
    {
        return !($this->isPermanent);
    }
    
    /**
     * 
     * @return \MediaBundle\Entity\Media
     */
    public function setTemporary()
    {
        $this->isPermanent = false;
        
        return $this;
    }
    
    /**
     * 
     * @return type
     */
    public function isPermanent()
    {
        return $this->isPermanent;
    }

    /**
     * 
     * @return \MediaBundle\Entity\Media
     */
    public function setPermanent()
    {
        $this->isPermanent = true;
        
        return $this;
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
     * @return Media
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
    public function getTitle($returnFileTitleOrFileName = false)
    {
        if ($returnFileTitleOrFileName) {
            if (null === $this->title) {
                return basename($this->getPath());
            } else {
                return $this->title;
            }
        }
        
        return $this->title;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Media
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

    /**
     * Set path
     *
     * @param string $path
     * @return Media
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string 
     */
    public function getPath()
    {
        return $this->path;
    }
    
    /**
     * 
     * @param type $rootDirectory
     * @return type
     */
    public function getFullPath($rootDirectory)
    {
        return $rootDirectory . '/' . $this->getPath();
    }

    /**
     * Set class
     *
     * @param string $class
     * @return Media
     */
    public function setClass($class)
    {
        $this->class = $class;

        return $this;
    }

    /**
     * Get class
     *
     * @return string 
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Set entityId
     *
     * @param integer $entityId
     * @return Media
     */
    public function setEntityId($entityId)
    {
        $this->entityId = $entityId;

        return $this;
    }

    /**
     * Get entityId
     *
     * @return integer 
     */
    public function getEntityId()
    {
        return $this->entityId;
    }

    /**
     * Set size
     *
     * @param integer $size
     * @return Media
     */
    public function setSize($size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Get size
     *
     * @return integer 
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Set user
     *
     * @param \UserBundle\Entity\User $user
     * @return Media
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
     * Set column
     *
     * @param string $column
     * @return Media
     */
    public function setColumn($column)
    {
        $this->column = $column;

        return $this;
    }

    /**
     * Get column
     *
     * @return string 
     */
    public function getColumn()
    {
        return $this->column;
    }
    
    /**
     * 
     * @param type $error
     * @return \MediaBundle\Entity\TempMedia
     */
    public function setError($error)
    {
        $this->error = $error;
        
        return $this;
    }
    
    /**
     * 
     * @return type
     */
    public function getError()
    {
        return $this->error;
    }
    
    /**
     * 
     * @param type $medias
     * @return type
     */
    public static function mediasToArray($medias, $type = null)
    {
        $mediaArray = array();
        // Generate appropriate responce
        foreach ($medias as $media) {
            switch ($type) {
                case null:
                    if (null === $media->getError()) {
                        $mediaArray[] = array(
                            'name' => $media->getTitle(), 
                            'id' => $media->getId(), 
                            'size' => $media->getSize()
                            );
                    } else {
                        $mediaArray[] = array(
                            'name' => $media->getError(), 
                            'id' => null, 
                            'size' => null
                            );
                    }
                break;
            }
        }        
        
        return $mediaArray;
    }
    
    /**
     * 
     * @param type $medias
     * @param type $type
     * @return type
     */
    public static function mediasToString($medias, $type = null)
    {
        $mediaArray = array();
        // Generate appropriate responce
        foreach ($medias as $media) {
            switch ($type) {
                case null:
                    if (null === $media->getError()) {
                        $mediaArray[] = array(
                            'name' => $media->getTitle(), 
                            'id' => $media->getId(), 
                            'path' => $media->getPath()
                            );
                    }
                break;
            }
        }        
        
        return json_encode($mediaArray);
    }
}