<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use ShoppingBundle\Entity\Progress;

/**
 * LoadSystemData:
 * php app/console doctrine:fixtures:load
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class LoadSystemData implements FixtureInterface
{
    /**
     * 
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $this->loadOrderProgress($manager);
    }
    
    /**
     * 
     * @param ObjectManager $manager
     */
    private function loadOrderProgress(ObjectManager $manager)
    {
        
        foreach (Progress::$staticProgresses as $progressId => $progressTitle) {
            $progress = new Progress();
            $progress->setId($progressId);
            $progress->setTitle($progressTitle);
            
            $metadata = $manager->getClassMetaData(get_class($progress));
            $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);            
            $metadata->setIdGenerator(new \Doctrine\ORM\Id\AssignedGenerator());
            
            $manager->persist($progress);
        }
        
        $manager->flush();
    }
}