<?php

namespace ShoppingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Library\Base\BaseEntity;

/**
 * Progress
 *
 * @ORM\Table(name="saman_progress")
 * @ORM\Entity(repositoryClass="\ShoppingBundle\Entity\Repository\ProgressRepository")
 */
class Progress extends BaseEntity
{
    const ITEM_LOGO = 'icon.progress';
    
    const PROGRESS_TYPE_STATIC = 1;
    const PROGRESS_TYPE_DYNAMIC = 1;
    
    const PROGRESS_SUBMITTED = 1;
    const PROGRESS_PAID = 2;
    const PROGRESS_INPROGRESS = 3;
    const PROGRESS_STOPED = 4;
    const PROGRESS_FINALIZED = 5;
    const PROGRESS_SHIPPED = 6;
    const PROGRESS_REFUND = 7;
    
    public static $staticProgress = array(
        self::PROGRESS_SUBMITTED => 'SUBMITTED',
        self::PROGRESS_PAID => 'PAID',
        self::PROGRESS_INPROGRESS => 'INPROGRESS',
        self::PROGRESS_STOPED => 'STOPED',
        self::PROGRESS_FINALIZED => 'FINALIZED',
        self::PROGRESS_SHIPPED => 'SHIPPED',
        self::PROGRESS_REFUND => 'REFUND'
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
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;    
    
    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text", nullable=true)
     */
    private $content;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="type", type="integer", nullable=true)
     */
    private $type;    
    
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
     * @return Repository\ProductRepository
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
    public function setId($id)
    {
        $this->id = $id;
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
     * @return Progress
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
     * Set content
     *
     * @param string $content
     * @return Progress
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
}
