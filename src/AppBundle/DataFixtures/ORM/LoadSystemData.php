<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Library\Helpers\LoremIpsumGenerator;
use CmsBundle\Entity\Page;
use LabelBundle\Entity\Label;
use UserBundle\Entity\User;
use UserBundle\Entity\Role;
use ProductBundle\Entity\Product;

/**
 * User
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class LoadSystemData implements FixtureInterface
{
    private $data = array(
        'pages' => 20,
        'products' => 20,
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
            ),
            array(
                'username' => 'user@user.com',
                'password' => 'user',
                'email' => 'user@user.com',
                'role' => Role::ROLE_USER,
                'firstName' => 'firstName',
                'lastName' => 'lastName',
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
    private function loadUserRoles($manager)
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
    private function loadUsers($manager)
    {
        $generator = new LoremIpsumGenerator();
        $usersData = $this->data['users'];
        for ($i = 0; $i < 20; $i++) {
            $email = $generator->getEmail();
            
            $usersData[] = array(
                'username' => $email,
                'password' => 'abcd',
                'email' => $email,
                'role' => Role::ROLE_USER,
                'firstName' => $generator->getName(),
                'lastName' => $generator->getName(),
            );
        }
        
        foreach ($usersData as $userData) {
            $user = new User();
            $user->setUsername($userData['username']);
            $user->setPassword(md5($userData['password']));
            $user->setEmail($userData['email']);
            $user->addRole($this->roles[$userData['role']]);
            
            if (isset($userData['firstName'])) {
                $user->setFirstName($userData['firstName']);
            }
            if (isset($userData['lastName'])) {
                $user->setLastName($userData['lastName']);
            }
            
            $manager->persist($user);
        }
        
        $manager->flush();
    }
    
    /**
     * 
     * @param type $manager
     */
    private function loadLabels($manager)
    {
        $generator = new LoremIpsumGenerator();
        foreach ($this->data['labels'] as $labelTitle) {
            $label = new Label();
            $label->setTitle($labelTitle);
            $label->setDescription($generator->getDescription());
            $this->labels[] = $label;
            
            $manager->persist($label);
        }
        
        $manager->flush();
    }
    
    /**
     * 
     * @param type $manager
     */
    private function loadPages($manager)
    {
        $generator = new LoremIpsumGenerator();
        
        $page = new Page();
        $page->setTitle('Home Page');
        $page->setContent($generator->getDescription());
        $page->setUrl('');
        $page->addLabel($this->labels[0]);
        $page->addLabel($this->labels[1]);
        $manager->persist($page);
            
        for ($i = 0; $i < $this->data['pages']; $i++) {
            $page = new Page();
            $page->setTitle($generator->getTitle());
            $page->setContent($generator->getDescription());
            $page->setUrl($generator->getUrl(true));
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
    private function loadProducts($manager)
    {
        $generator = new LoremIpsumGenerator();
        for ($i = 0; $i < $this->data['products']; $i++) {
            $product = new Product();
            $product->setTitle($generator->getTitle());
            $product->setDescription($generator->getDescription());
            $product->setPrice(rand(10, 10000));
            
            $manager->persist($product);
        }
        
        $manager->flush();
    }    
}