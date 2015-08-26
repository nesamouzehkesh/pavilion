<?php

namespace PlaygroundBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\Document(repositoryClass="PlaygroundBundle\Document\Repository\ProductRepository")
 */
class Product
{
    /**
     * @MongoDB\Id
     */
    protected $id;

    /**
     * @MongoDB\String
     */
    protected $name;

    /**
     * @MongoDB\Float
     */
    protected $price;
    
    /** @MongoDB\ReferenceMany(targetDocument="Feature", cascade={"all"}) */
    protected $features;

    /**
     * Get id
     *
     * @return id $id
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * 
     * @param \Doctrine\ORM\EntityManagerInterface $em
     * @return \PlaygroundBundle\Document\Repository\ProductRepository
     */
    public static function getRepository($em)
    {
        return $em->getRepository(__CLASS__);
    }    

    /**
     * Set name
     *
     * @param string $name
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;
        
        return $this;
    }

    /**
     * Get name
     *
     * @return string $name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set price
     *
     * @param float $price
     * @return self
     */
    public function setPrice($price)
    {
        $this->price = $price;
        
        return $this;
    }

    /**
     * Get price
     *
     * @return float $price
     */
    public function getPrice()
    {
        return $this->price;
    }
    
    /**
     * 
     * @return type
     */
    public function getFeatures()
    { 
        return $this->features;
    }
    
    /**
     * 
     * @param \Saman\MongoBundle\Document\Feature $feature
     */
    public function addFeature(Feature $feature)
    { 
        $this->features[] = $feature; 
    }
}
