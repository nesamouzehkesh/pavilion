<?php

namespace Saman\AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Saman\Library\Helpers\LoremIpsumGenerator;
use Saman\CmsBundle\Entity\Page;
use Saman\LabelBundle\Entity\Label;
use Saman\UserBundle\Entity\User;
use Saman\UserBundle\Entity\Role;

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
        'roles' => array(
            array('name' => 'User', 'role' => Role::ROLE_USER),
            array('name' => 'Admin', 'role' => Role::ROLE_ADMIN),
        ),
        'users' => array(
            array(
                'username' => 'admin@admin.com',
                'password' => 'admin',
                'email' => 'admin@admin.com',
                'role' => Role::ROLE_ADMIN
            ),
            array(
                'username' => 'user@user.com',
                'password' => 'user',
                'email' => 'user@user.com',
                'role' => Role::ROLE_USER
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
        foreach ($this->data['users'] as $userData) {
            $user = new User();
            $user->setUsername($userData['username']);
            $user->setPassword(md5($userData['password']));
            $user->setEmail($userData['email']);
            $user->addRole($this->roles[$userData['role']]);
            
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
}
