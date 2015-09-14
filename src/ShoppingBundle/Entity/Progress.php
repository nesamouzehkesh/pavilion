<?php

namespace Saman\ShoppingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Saman\Library\Base\BaseEntity;

/**
 * Progress
 *
 * @ORM\Table(name="saman_progress")
 * @ORM\Entity(repositoryClass="\Saman\ShoppingBundle\Entity\Repository\ProgressRepository")
 */
class Progress extends BaseEntity
{
    const ITEM_LOGO = 'icon.progress';
    
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
}