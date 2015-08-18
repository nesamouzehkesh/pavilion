<?php

namespace Saman\ConfigBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Saman\Library\Base\BaseEntity;

/**
 * Media
 *
 * @ORM\Table(name="saman_config")
 * @ORM\Entity(repositoryClass="Saman\ConfigBundle\Repository\ConfigRepository")
 */
class Config extends BaseEntity
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
     * @ORM\ManyToOne(targetEntity="Saman\UserBundle\Entity\User")
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
     * @var array
     *
     * @ORM\Column(name="options", type="array", nullable=true)
     */
    private $options;

    /**
     * 
     */
    public function __construct($visibility = null, $user = null)
    {
        parent::__construct();
        if (null !== $visibility) {
            $this->setVisibility($visibility);
        }
        
        if (null !== $user) {
            $this->setUser($this->user);
        }
    }
    
    /**
     * 
     */
    public function __toString()
    {
        return $this->title;
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
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set options
     *
     * @param Array $options
     * @return Config
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
     * @param type $value
     * @return \Saman\MediaBundle\Entity\Config
     */
    public function setOption($key, $value)
    {
        if (array_key_exists($key, $this->options)) {
            $this->options[$key] = $value;
        }

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
     * Get config
     *
     * @param Array $options
     * @return Option
     */
    public function getOption($key, $deafuleValue)
    {
        if (!array_key_exists($key, $this->options)) {
           return $deafuleValue;
        }
        
        return $this->options[$key];
    }

    /**
     * Set user
     *
     * @param \Saman\UserBundle\Entity\User $user
     * @return Media
     */
    public function setUser(\Saman\UserBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Saman\UserBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }
    
    /**
     * 
     * @param type $configDefultParameters
     * @param array $status
     * @param type $configInputParameters
     * @return type
     */
    public static function generateConfigOptions(
        $configDefultParameters, 
        $statuses = array('public'), 
        $configInputParameters = array()
        )
    {
        $finalResultConfigOptions = array();
        foreach ($statuses as $status) {
            $configCategories = array();
            foreach ($configDefultParameters['configs'] as $configCategoryKey => $configs) {
                if (strtoupper($status) === strtoupper($configs['type'])) {
                    $configCategories[$configCategoryKey] = $configs['options'];
                }
            }

            $resultConfigOptions = array();
            foreach ($configCategories as $configCategoryKey => $configOptions) {
                foreach ($configOptions as $configOptionKey => $option) {
                    $fullConfigKey = $configCategoryKey . '_' . $configOptionKey;

                    $configValue = $option['default'];
                    if (array_key_exists($fullConfigKey, $configInputParameters)) {
                        $configValue = $configInputParameters[$fullConfigKey];
                    }
                    $resultConfigOptions[$fullConfigKey] = $configValue;
                }
            }
            $finalResultConfigOptions = array_merge(
                $finalResultConfigOptions, 
                $resultConfigOptions
                );
        }
        
        return $finalResultConfigOptions;
    }
}