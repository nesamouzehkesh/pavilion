<?php

namespace PlaygroundBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Library\Base\BaseEntity;
use Library\Interfaces\ServiceAwareEntityInterface;

/**
 * LinkWidget
 *
 * @ORM\Table(name="saman_widget_link")
 * @ORM\Entity(repositoryClass="PlaygroundBundle\Entity\Repository\LinkWidgetRepository")
 */
class LinkWidget extends BaseEntity implements ServiceAwareEntityInterface
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
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=255)
     */
    private $url;
    
    /**
     * 
     */
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * 
     * @return string
     */
    public function getServiceId()
    {
        return 'linking-widget';
    }
    
    /**
     * 
     * @param \Doctrine\ORM\EntityManagerInterface $em
     * @return \PlaygroundBundle\Entity\Repository\LinkWidgetRepository
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
     * Set title
     *
     * @param string $title
     * @return LinkWidget
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
     * Set url
     *
     * @param string $url
     * @return LinkWidget
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string 
     */
    public function getUrl()
    {
        return $this->url;
    }
}
