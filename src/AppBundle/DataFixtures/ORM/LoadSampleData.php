<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Library\Helpers\LoremIpsumGenerator;
use CmsBundle\Entity\Page;
use LabelBundle\Entity\Label;
use UserBundle\Entity\Address;
use UserBundle\Entity\User;
use UserBundle\Entity\Role;
use ProductBundle\Entity\Product;

/**
 * LoadSystemData:
 * php app/console doctrine:fixtures:load
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class LoadSampleData implements FixtureInterface
{
    private $data = array(
        'pages' => 20,
        'products' => 10,
        'roles' => array(
            array('name' => 'User', 'role' => Role::ROLE_USER),
            array('name' => 'Admin', 'role' => Role::ROLE_ADMIN),
        ),
        'users' => array(
            array(
                'username' => 'admin@admin.com',
                'password' => 'admin',
                'email' => 'admin@admin.com',
                'role' => Role::ROLE_ADMIN,
                'firstName' => 'firstName',
                'lastName' => 'lastName',
                'addresses' => array(
                    array(
                        'type' => Address::ADDRESS_TYPE_BILLING,
                        'city' => 'Sydney',
                        'country' => 'AU',
                        'fullName' => 'Admin User',
                        'state' => 'NSW',
                        'firstAddressLine' => '43/6 Jon Street, Wellington',
                        'phoneNumber' => '23489234',
                        'postCode' => '2345',
                    ),
                    array(
                        'type' => Address::ADDRESS_TYPE_SHIPPING,
                        'city' => 'Sydney',
                        'country' => 'AU',
                        'fullName' => 'Admin User',
                        'state' => 'NSW',
                        'firstAddressLine' => '43/2 Timberland Street, Wellington',
                        'phoneNumber' => '3454359',
                        'postCode' => '2345',
                    ),
                )
            ),
            array(
                'username' => 'user@user.com',
                'password' => 'user',
                'email' => 'user@user.com',
                'role' => Role::ROLE_USER,
                'firstName' => 'firstName',
                'lastName' => 'lastName',
                'addresses' => array(
                    array(
                        'type' => Address::ADDRESS_TYPE_BILLING_SHIPPING,
                        'city' => 'Sydney',
                        'country' => 'AU',
                        'fullName' => 'User',
                        'state' => 'NSW',
                        'firstAddressLine' => '12/1 A32 Street, Rhod',
                        'phoneNumber' => '09378265',
                        'postCode' => '1675',
                    ),
                )
            ),
        ),
        'labels' => array(
            'A', 
            'B', 
            'C', 
            'D', 
            'E'
        ),
    );
    
    /** @var Label[] */
    private $labels;
    
    /** @var Role[] */
    private $roles;
    
    /**
     * 
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $this->loadUserRoles($manager);
        $this->loadUsers($manager);
        $this->loadLabels($manager);
        $this->loadPages($manager);
        $this->loadProducts($manager);
    }
    
    /**
     * 
     * @param type $manager
     */
    private function loadUserRoles(ObjectManager $manager)
    {
        foreach ($this->data['roles'] as $roleData) {
            $role = new Role();
            $role->setName($roleData['name']);
            $role->setRole($roleData['role']);
            $this->roles[$roleData['role']] = $role;
            
            $manager->persist($role);
        }
        
        $manager->flush();
    }    
    
    /**
     * 
     * @param type $manager
     */
    private function loadUsers(ObjectManager $manager)
    {
        $loremIpsum = new LoremIpsumGenerator();
        $usersData = $this->data['users'];
        for ($i = 0; $i < 20; $i++) {
            $email = $loremIpsum->getEmail();
            
            $usersData[] = array(
                'username' => $email,
                'password' => 'abcd',
                'email' => $email,
                'role' => Role::ROLE_USER,
                'firstName' => $loremIpsum->getName(),
                'lastName' => $loremIpsum->getName(),
            );
        }
        
        foreach ($usersData as $userData) {
            $user = new User();
            $user->setUsername($userData['username']);
            $user->setPassword($userData['password']);
            $user->setEmail($userData['email']);
            $user->addRole($this->roles[$userData['role']]);
            
            if (isset($userData['firstName'])) {
                $user->setFirstName($userData['firstName']);
            }
            if (isset($userData['lastName'])) {
                $user->setLastName($userData['lastName']);
            }
            if (isset($userData['addresses'])) {
                foreach ($userData['addresses'] as $addressData) {
                    $address = new Address();
                    $address->setAddressType($addressData['type']);
                    $address->setCity($addressData['city']);
                    $address->setCountry($addressData['country']);
                    $address->setFullName($addressData['fullName']);
                    $address->setState($addressData['state']);
                    $address->setFirstAddressLine($addressData['firstAddressLine']);
                    $address->setPhoneNumber($addressData['phoneNumber']);
                    $address->setPostCode($addressData['postCode']);
                    $address->setType(Address::TYPE_PRIMARY);
                    $address->setUser($user);
                    $user->addAddress($address);
                        
                    $manager->persist($address);
                }
                
            }
            
            $manager->persist($user);
        }
        
        $manager->flush();
    }
    
    /**
     * 
     * @param type $manager
     */
    private function loadLabels(ObjectManager $manager)
    {
        $loremIpsum = new LoremIpsumGenerator();
        foreach ($this->data['labels'] as $labelTitle) {
            $label = new Label();
            $label->setTitle($labelTitle);
            $label->setDescription($loremIpsum->getDescription());
            $this->labels[] = $label;
            
            $manager->persist($label);
        }
        
        $manager->flush();
    }
    
    /**
     * 
     * @param type $manager
     */
    private function loadPages(ObjectManager $manager)
    {
        $loremIpsum = new LoremIpsumGenerator();
        
        $page = new Page();
        $page->setTitle('Home Page');
        $page->setContent($loremIpsum->getDescription());
        $page->setUrl('');
        $page->addLabel($this->labels[0]);
        $page->addLabel($this->labels[1]);
        $manager->persist($page);
            
        for ($i = 0; $i < $this->data['pages']; $i++) {
            $page = new Page();
            $page->setTitle($loremIpsum->getTitle());
            $page->setContent($loremIpsum->getDescription());
            $page->setUrl($loremIpsum->getUrl(true));
            for ($j = 0; $j < rand(1 , count($this->labels)); $j++) {
                $page->addLabel($this->labels[$j]);
            }
            
            $manager->persist($page);
        }
        
        $manager->flush();
    }
        
    /**
     * 
     * @param type $manager
     */
    private function loadProducts(ObjectManager $manager)
    {
        $loremIpsum = new LoremIpsumGenerator();
        for ($i = 0; $i < $this->data['products']; $i++) {
            $product = new Product();
            $product->setTitle($loremIpsum->getTitle());
            $product->setDescription($loremIpsum->getDescription());
            $product->setPrice(rand(10, 10000));
            
            $manager->persist($product);
        }
        
        $manager->flush();
    }    
}