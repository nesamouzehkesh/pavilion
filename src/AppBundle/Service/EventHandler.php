<?php

namespace AppBundle\Service;

use Library\Base\BaseEntity;
use AppBundle\Service\AppService;
use AppBundle\Entity\Event;

class EventHandler
{
    /**
     *
     * @var AppService $appService
     */
    protected $appService;
    
    /**
     * 
     * @param \AppBundle\Service\AppService $appService
     * @param type $parameters
     */
    public function __construct(
        AppService $appService, 
        $parameters = array()
        ) 
    {
        $this->appService = $appService;
        $this->appService->setParametrs($parameters);
    }
    
    /**
     * 
     * @param BaseEntity $entity
     * @param type $trigger
     */
    public function handleEvent(BaseEntity $entity, $trigger)
    {
        if (!isset(Event::$triggers[$trigger])) {
            throw new \Exception('Invalid trigger type is defined');
        }
        
        $event = new Event();
        $event->setUser($this->appService->getUser());
        $event->setTrigger($trigger);
        $event->setEntityPathId($entity->getId());
        $event->setEntityPathName(get_class($entity));
        $this->appService->saveEntity($event);
        
        return $event;
    }
}