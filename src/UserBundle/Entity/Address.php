<?php

namespace UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Library\Base\BaseEntity;

/**
 * Acme\UserBundle\Entity\User
 *
 * @ORM\Table(name="saman_user_address")
 * @ORM\Entity(repositoryClass="UserBundle\Entity\Repository\AddressRepository")
 */
class Address extends BaseEntity
{
    const ADDRESS_TYPE_SHIPPING = 1;
    const ADDRESS_TYPE_BILLING = 2;
    const ADDRESS_TYPE_BILLING_SHIPPING = 3;
    
    const TYPE_PRIMARY = 1;
    const TYPE_SECONDARY = 2;
    
    const LOCATION_TYPE_RESIDENTIAL = 1;
    const LOCATION_TYPE_BUSINESS = 2;
    const LOCATION_TYPE_MAILBOX = 3;
    
    public static $locationTypes = array(
        self::LOCATION_TYPE_RESIDENTIAL => 'address.locationTypes.residential',
        self::LOCATION_TYPE_BUSINESS => 'address.locationTypes.business',
        self::LOCATION_TYPE_MAILBOX => 'address.locationTypes.mailbox'
    );

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="full_name", type="string", length=255)
     */
    private $fullName;
    
    /**
     * @var string
     *
     * @ORM\Column(name="first_address_line", type="text")
     */
    private $firstAddressLine;
    
    /**
     * @var string
     *
     * @ORM\Column(name="second_address_line", type="text", nullable=true)
     */
    private $secondAddressLine;
    
    /**
     * @ORM\Column(name="city", type="string", length=255)
     */
    private $city;
    
    /**
     * @ORM\Column(name="state", type="string", length=255)
     */
    private $state;
    
    /**
     * @ORM\Column(name="post_code", type="string", length=255)
     */
    private $postCode;
    
    /**
     * @ORM\Column(name="country", type="string", length=20)
     */
    private $country;
    
    /**
     * @ORM\Column(name="phone_number", type="string", length=255)
     */
    private $phoneNumber;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="address_type", type="integer")
     */
    private $addressType;        
    
    /**
     * @var integer
     *
     * @ORM\Column(name="type", type="integer")
     */
    private $type;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="location_type", type="integer")
     */
    private $locationType;
    
    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="addresses")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     **/
    private $user;
    
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
     * @return \UserBundle\Entity\Repository\AddressRepository
     */
    public static function getRepository(\Doctrine\ORM\EntityManagerInterface $em)
    {
        return $em->getRepository(__CLASS__);
    }    
    
    /**
     * 
     * @return type
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * Set fullName
     *
     * @param string $fullName
     * @return Address
     */
    public function setFullName($fullName)
    {
        $this->fullName = $fullName;

        return $this;
    }

    /**
     * Get fullName
     *
     * @return string 
     */
    public function getFullName()
    {
        return $this->fullName;
    }

    /**
     * Set firstAddressLine
     *
     * @param string $firstAddressLine
     * @return Address
     */
    public function setFirstAddressLine($firstAddressLine)
    {
        $this->firstAddressLine = $firstAddressLine;

        return $this;
    }

    /**
     * Get firstAddressLine
     * 
     * @param type $forceToString
     * @return string
     */
    public function getFirstAddressLine($forceToString = false)
    {
        if ($forceToString and null === $this->firstAddressLine) {
            return '';
        }
        
        return $this->firstAddressLine;
    }

    /**
     * Set secondAddressLine
     *
     * @param string $secondAddressLine
     * @return Address
     */
    public function setSecondAddressLine($secondAddressLine)
    {
        $this->secondAddressLine = $secondAddressLine;

        return $this;
    }

    /**
     * Get secondAddressLine
     *
     * @param type $forceToString
     * @return string 
     */
    public function getSecondAddressLine($forceToString = false)
    {
        if ($forceToString and null === $this->secondAddressLine) {
            return '';
        }
        
        return $this->secondAddressLine;
    }

    /**
     * Set city
     *
     * @param string $city
     * @return Address
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return string 
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set state
     *
     * @param string $state
     * @return Address
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Get state
     *
     * @return string 
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set postCode
     *
     * @param string $postCode
     * @return Address
     */
    public function setPostCode($postCode)
    {
        $this->postCode = $postCode;

        return $this;
    }

    /**
     * Get postCode
     *
     * @return string 
     */
    public function getPostCode()
    {
        return $this->postCode;
    }

    /**
     * Set country
     *
     * @param string $country
     * @return Address
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return string 
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set phoneNumber
     *
     * @param string $phoneNumber
     * @return Address
     */
    public function setPhoneNumber($phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    /**
     * Get phoneNumber
     *
     * @return string 
     */
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    /**
     * Set addressType
     *
     * @param integer $addressType
     * @return Address
     */
    public function setAddressType($addressType)
    {
        $this->addressType = $addressType;

        return $this;
    }

    /**
     * Get addressType
     *
     * @return integer 
     */
    public function getAddressType()
    {
        return $this->addressType;
    }
    
    /**
     * Set type
     *
     * @param integer $type
     * @return Address
     */
    public function setType($type)
    {
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
     * 
     * @return type
     */
    public function isPrimary()
    {
        return $this->type === self::TYPE_PRIMARY;
    }

    /**
     * 
     * @return type
     */
    public function isSecondary()
    {
        return $this->type === self::TYPE_SECONDARY;
    }
    
    /**
     * 
     * @return type
     */
    public function isBillingAddress()
    {
        return $this->addressType === self::ADDRESS_TYPE_BILLING;
    }
    
    /**
     * 
     * @return type
     */
    public function isShippingAddress()
    {
        return $this->addressType === self::ADDRESS_TYPE_SHIPPING;
    }
    
    /**
     * Set user
     *
     * @param \UserBundle\Entity\User $user
     * @return Address
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
     * Set locationType
     *
     * @param integer $locationType
     * @return Address
     */
    public function setLocationType($locationType)
    {
        if (!isset(self::$locationTypes[$locationType])) {
            throw new \Exception('Invalid location type is set to this address');
        }
        
        $this->locationType = $locationType;

        return $this;
    }

    /**
     * Get locationType
     *
     * @return integer 
     */
    public function getLocationType()
    {
        return $this->locationType;
    }
    
    /**
     * Get locationType label. If $labelArray is provided it returns the label 
     * based on this $labelArray array, otherwise it will use self::$locationTypes
     * 
     * @param array $labelArray
     * @return string|array
     */
    public function getLocationTypeLabel(array $labelArray = array())
    {
        if (count($labelArray)) {
            if (!isset($labelArray[$this->locationType])) {
                return '';
            }

            return $labelArray[$this->locationType];
        } else {
            return self::$locationTypes[$this->locationType];
        }
    }    
}