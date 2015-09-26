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
        'pages' => 0,
        'pagesData' => array(
            array(
                'title'   => 'Order',
                'url'     => '/order',
                'content' => 'We handmade your carpet based on your design, whether its an abstract painting, 
                        a family picture a company logo or a pattern of your choice from our gallery. 
                        First you select your design, the size, colour and specific manner in which 
                        your unique, high quality Persian carpet will be made. Our intelligent 
                        software turns your design into a recipe for a handmade carpet using the 
                        best matching colours and patterns and you made your order.                         
                        Then we choose the best organic materials which are hand-carded and hand-dyed, 
                        producing a rich, harmonious effect for your carpet. Your carpet will be 
                        woven by skilful hands of our craftsmen. The result is an eye-catching masterpiece 
                        which is unique in both its built and design.
                        We bear with you throughout the entire design and production process so that you 
                        will be able to make trivial or substantive alteration as your carpet is being 
                        made in our workshop. When your Carpet is ready we will ship it free of any charge
                        to your address.
                '
            ),
            array(
                'title'   => 'Gallery',
                'url'     => '/gallery',
                'content' => 'We believe our custom handmade Persian carpet collection offers perfect choices 
                    that would creatively match your style and individuality. Each knot and pieces 
                    is made by hand and passion. Depending on your design, your carpet can 
                    become a painting frame on your wall, a family picture in your room, a wedding 
                    present to your friend, or your company logo to carry your passion for quality to 
                    your business partners. If you still prefer traditional Iranian designs, you can
                    browse our extensive collection of royalty-free designs, ready to be ordered.'
            ),
            array(
                'title'   => 'Aboutus',
                'url'     => '/aboutus',
                'content' => 'We are a family of designers and craftsmen working together to revive 
                    the most iconic product of the Iranian art and culture, the Persian carpet. You 
                    can mix the authenticity of the handmade Persian carpets with any design of your 
                    own. Your carpet will be woven by skilful hands of our craftsmen, with knots of 
                    silk and wool on strings of love and passion. The product is a masterpiece with 
                    quality of ancient persian carpets and the unique design of your own taste.'
            ),
            array(
                'title'   => 'Contact',
                'url'     => '/contact',
                'content' => 'We are located in Tabriz, IRAN, one of the world’s greatest hubs for Persian handmade carpet and rug industry. 
                    <address>
                        <strong>Address</strong><br>
                            Unit 2, Tech Hub Tabriz Islamic Art University<br>
                            Azadi Street, Tabriz - Iran<br>
                            Postcode: 5164736931<br>
                        <a href="mailto:info@zibaf.com">info@zibaf.com</a>
                    </address>'
            ),
        ),
        'products' => 10,
        'roles' => array(
            array('name' => 'User', 'role' => Role::ROLE_USER),
            array('name' => 'Admin', 'role' => Role::ROLE_ADMIN),
        ),
        'users' => 5,
        'usersData' => array(
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
        $usersData = $this->data['usersData'];
        for ($i = 0; $i < $this->data['users']; $i++) {
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

        foreach ($this->data['pagesData'] as $pageData) {
            $page = new Page();
            $page->setTitle($pageData['title']);
            $page->setContent($pageData['content']);
            $page->setUrl($pageData['url']);
            $page->addLabel($this->labels[1]);
            
            $manager->persist($page);
        }
        
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