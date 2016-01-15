<?php

namespace UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Library\Base\BaseEntity;

/**
 * Acme\UserBundle\Entity\User
 *
 * @ORM\Table(name="saman_user")
 * @ORM\Entity(repositoryClass="UserBundle\Entity\Repository\UserRepository")
 */
class User extends BaseEntity implements AdvancedUserInterface, \Serializable
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $username;

    /**
     * @ORM\Column(name="first_name", type="string", length=255, nullable=true)
     */
    private $firstName;
    
    /**
     * @ORM\Column(name="last_name", type="string", length=255, nullable=true)
     */
    private $lastName;
    
    /**
     * @ORM\Column(type="string", length=64, nullable=true)
     */
    private $password;

    /**
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;
    
    /**
     *
     * @ORM\Column(name="salt", type="string")
     */
    private $salt;
    
    /**
     * @ORM\ManyToMany(targetEntity="Role", inversedBy="users")
     * @ORM\JoinTable(name="saman_users_roles")
     */
    private $roles;
    
    /**
     * @ORM\OneToMany(targetEntity="Address", mappedBy="user")
     **/
    private $addresses;
    
    /**
     *
     * @var type 
     */
    private $primaryAddresses = null;
    
    /**
     * 
     */
    public function __construct()
    {
        parent::__construct();
        
        $this->isActive = true;
        $this->salt = md5(uniqid(null, true));
        $this->roles = new ArrayCollection();
        $this->addresses =  new ArrayCollection();
    }
    
    /**
     * 
     * @param \Doctrine\ORM\EntityManagerInterface $em
     * @return \UserBundle\Entity\Repository\UserRepository
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
     * 
     * @return type
     */
    public function getName()
    {
        return $this->getFirstName() . ' ' . $this->getLastName();
    }

    /**
     * @inheritDoc
     */
    public function getUsername()
    {
        return $this->username;
    }
    
    /**
     * 
     * @param type $username
     * @return \UserBundle\Entity\User
     */
    public function setUsername($username)
    {
        $this->username = $username;
        
        return $this;
    }
    
    /**
     * @inheritDoc
     */
    public function getEmail()
    {
        return $this->getUsername();
    }
    
    /**
     * @inheritDoc
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * @inheritDoc
     */
    public function getPassword()
    {
        return $this->password;
    }
    
    /**
     * 
     * @param type $password
     * @return \UserBundle\Entity\User
     */
    public function setPassword($password)
    {
        $this->password = md5($password);
        
        return $this;
    }
    
    /**
     * 
     * @param type $password
     * @return type
     */
    public function checkPassword($password)
    {
        return $this->password === md5($password);
    }

    /**
     * @inheritDoc
     */
    public function getRoles()
    {
        /*
         * Notice that the Role class implements RoleInterface. This is because Symfony's security system requires that the User::getRoles method returns an
         * array of either role strings or objects that implement this interface. If Role didn't implement this interface, then User::getRoles would need to
         * iterate over all the Role objects, call getRole on each, and create an array of strings to return. Both approaches are valid and equivalent.
         */
        $userRoles = array();
        foreach($this->roles as $role){
            $userRoles[] = $role->getRole();
        }
        
        return $userRoles;
        //return $this->roles->toArray();
    }
    
    /**
     * 
     * @param \UserBundle\Entity\Role $role
     * @return \UserBundle\Entity\User
     */
    public function addRole(Role $role)
    {
        $this->roles[] = $role;
        
        return $this;
    }
    
    /**
     * Remove roles
     *
     * @param \UserBundle\Entity\Role $roles
     */
    public function removeRole(Role $roles)
    {
        $this->roles->removeElement($roles);
    }
    
    /**
     * Check if user with admin role
     * @return boolean
     */
    public function hasAdminRole()
    {
        $arr = array_intersect(
            array(
                Role::ROLE_ADMIN,
                ), 
            $this->getRoles()
            );
        
        return !empty($arr);
    }
    
    /**
     * Check if user with Customer role
     * @return boolean
     */
    public function hasCustomerRole()
    {
        $arr = array_intersect(
            array(
                Role::ROLE_USER,
                ), 
            $this->getRoles()
            );
        
        return !empty($arr);
    }   
    
    /**
     * @inheritDoc
     */
    public function eraseCredentials()
    {
    }
    
    /**
     * 
     * @return boolean
     */
    public function isAccountNonExpired()
    {
        return true;
    }

    /**
     * 
     * @return boolean
     */
    public function isAccountNonLocked()
    {
        return true;
    }

    /**
     * 
     * @return boolean
     */
    public function isCredentialsNonExpired()
    {
        return true;
    }

    /**
     * 
     * @return type
     */
    public function isEnabled()
    {
        return $this->isActive;
    }

    /**
     * @see \Serializable::serialize()
     */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->password,
            $this->isActive,
            $this->salt,
        ));
    }

    /**
     * @see \Serializable::unserialize()
     */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username,
            $this->password,
            $this->isActive,
            $this->salt
        ) = unserialize($serialized);
    }

    /**
     * Set firstName
     *
     * @param string $firstName
     * @return User
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string 
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     * @return User
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string 
     */
    public function getLastName()
    {
        return $this->lastName;
    }
    
    /**
     * Add addresses
     *
     * @param \UserBundle\Entity\Address $addresses
     * @return User
     */
    public function addAddress(\UserBundle\Entity\Address $addresses)
    {
        $this->addresses[] = $addresses;

        return $this;
    }

    /**
     * Remove addresses
     *
     * @param \UserBundle\Entity\Address $addresses
     */
    public function removeAddress(\UserBundle\Entity\Address $addresses)
    {
        $this->addresses->removeElement($addresses);
    }

    /**
     * Get addresses
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAddresses()
    {
        return $this->addresses;
    }
    
    /**
     * Get user primary shipping address
     * 
     * @return Address
     */
    public function getPrimaryShippingAddress()
    {
        if (null === $this->primaryAddresses) {
            $this->populatePrimaryAddress();
        }
        
        return $this->primaryAddresses['shipping'];
    }
    
    /**
     * Get user primary billing address
     * 
     * @return Address
     */
    public function getPrimaryBillingAddress()
    {
        if (null === $this->primaryAddresses) {
            $this->populatePrimaryAddress();
        }
        
        return $this->primaryAddresses['billing'];
    }
    
    /**
     * Populate user primary shipping and billing address
     */
    private function populatePrimaryAddress()
    {
        $this->primaryAddresses = array(
            'shipping' => null,
            'billing' => null,
        );
        
        foreach ($this->addresses as $address) {
            if (!$address->isDeleted() and $address->isPrimary()) {
                switch ($address->getAddressType()) {
                    case Address::ADDRESS_TYPE_BILLING:
                        $this->primaryAddresses['billing'] = $address;
                        break;
                    case Address::ADDRESS_TYPE_SHIPPING:
                        $this->primaryAddresses['shipping'] = $address;
                        break;
                    case Address::ADDRESS_TYPE_BILLING_SHIPPING:
                        $this->primaryAddresses['billing'] = $address;
                        $this->primaryAddresses['shipping'] = $address;
                        break;
                }
            }
        }
    }
}
