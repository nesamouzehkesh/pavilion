<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Media
 *
 * @ORM\Table(name="saman_system_config")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\SystemConfigRepository")
 */
class SystemConfig
{
    const PRODUCT_SPECIFICATION_FIELD = 1;
    
    /**
     * @var type 
     */
    public static $configKeys = array(
        self::PRODUCT_SPECIFICATION_FIELD => ''
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
     * @ORM\Column(name="config_key", type="integer")
     */
    private $key;

    /**
     * @var array
     *
     * @ORM\Column(name="options", type="array", nullable=true)
     */
    private $options;
    
    /**
     * 
     * @param type $key
     */
    public function __construct($key = null)
    {
        if (null !== $key) {
            $this->setKey($key);
        }
        
        $this->options = array();
    }

    /**
     * 
     * @param \Doctrine\ORM\EntityManagerInterface $em
     * @return \AppBundle\Entity\Repository\SystemConfigRepository
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
     * Set key
     *
     * @param integer $key
     * @return SystemConfig
     */
    public function setKey($key)
    {
        if (!array_key_exists($key, self::$configKeys)) {
            throw new \Exception('Invalid system config key is set');
        }
        $this->key = $key;

        return $this;
    }

    /**
     * Get key
     *
     * @return integer 
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Set options
     *
     * @param array $options
     * @return SystemConfig
     */
    public function setOptions($options)
    {
        $this->options = $options;

        return $this;
    }
    
    /**
     * Set option
     * 
     * @param type $key
     * @param type $data
     * @return \AppBundle\Entity\SystemConfig
     */
    public function setOption($key, $data)
    {
        $this->options[$key] = $data;
        
        return $this;
    }

    /**
     * Get options
     *
     * @return array 
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Get options
     *
     * @return array 
     */
    public function getOption($fieldKey)
    {
        if (null === $fieldKey) {
            return null;
        }
        
        if (!array_key_exists($fieldKey, $this->options)) {
            throw new \Exception('Config option is not set');
        }
        
        return $this->options[$fieldKey];
    }
    
    /**
     * Unset an option
     * 
     * @param type $fieldKey
     * @return \AppBundle\Entity\SystemConfig
     */
    public function unSetOptions($fieldKey)
    {
        if (null === $fieldKey) {
            return $this;
        }
        
        if (array_key_exists($fieldKey, $this->options)) {
            unset($this->options[$fieldKey]);
        }

        return $this;
    }    
}
