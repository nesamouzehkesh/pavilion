<?php

namespace Saman\AppearanceBundle\Service\Interfaces;

use Saman\AppearanceBundle\Entity\Navigation;

interface NavigationServiceInterface
{
    /**
     * Get Navigation based on its ID. If ID is null then create a new Navigation
     * 
     * @param type $navigationId
     * @return type
     */
    public function getNavigation($navigationId = null);
    
    /**
     * Update Navigation
     * 
     * @param Navigation $navigation
     */
    public function updateNavigation($navigation);
}