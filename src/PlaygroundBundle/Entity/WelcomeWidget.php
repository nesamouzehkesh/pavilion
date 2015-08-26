<?php

namespace PlaygroundBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Library\Base\BaseEntity;
use Library\Interfaces\ServiceAwareEntityInterface;

/**
 * WelcomeWidget
 *
 * @ORM\Table(name="saman_widget_welcome")
 * @ORM\Entity(repositoryClass="PlaygroundBundle\Entity\Repository\WelcomeWidgetRepository")
 */
class WelcomeWidget extends BaseEntity implements ServiceAwareEntityInterface
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
     * @ORM\Column(name="content", type="text")
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
     * @return string
     */
    public function getServiceId()
    {
        return 'welcome-widget';
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
     * @return WelcomeWidget
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
     * @return WelcomeWidget
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
