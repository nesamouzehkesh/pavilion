<?php

namespace UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Library\Base\BaseEntity;

/**
 * Acme\UserBundle\Entity\User
 *
 * @ORM\Table(name="saman_users")
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
     * @ORM\Column(type="string", length=25, unique=true)
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
     * @ORM\Column(type="string", length=64)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=60, unique=true)
     */
    private $email;

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
     * 
     */
    public function __construct()
    {
        parent::__construct();
        
        $this->isActive = true;
        $this->salt = md5(uniqid(null, true));
        $this->roles = new ArrayCollection();
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
     * @inheritDoc
     */
    public function getEmail()
    {
        return $this->email;
    }
    
    /**
     * 
     * @param type $email
     * @return \UserBundle\Entity\User
     */
    public function setEmail($email)
    {
        $this->email = $email;
        
        return $this;
    }     

    /**
     * @inheritDoc
     */
    public function getRoles()
    {
        //return array('ROLE_ADMIN');
        return $this->roles->toArray();
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
}