<?php

namespace PlaygroundBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Library\Helpers\LoremIpsumGenerator;
use PlaygroundBundle\Entity\LinkWidget;
use PlaygroundBundle\Entity\WelcomeWidget;

/**
 * User
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class LoadWidgetData implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $generator = new LoremIpsumGenerator();
        for ($i = 0; $i < 10; $i++) {
            $linkWidget = new LinkWidget();
            $linkWidget->setTitle($generator->getTitle());
            $linkWidget->setUrl($generator->getUrl());
            
            $welcomeWidget = new WelcomeWidget();
            $welcomeWidget->setTitle($generator->getTitle());
            $welcomeWidget->setContent($generator->getDescription());
            
            $manager->persist($linkWidget);
            $manager->persist($welcomeWidget);
        }
        
        $manager->flush();
    }
}