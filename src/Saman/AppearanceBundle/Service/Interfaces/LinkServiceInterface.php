<?php

namespace Saman\AppearanceBundle\Service\Interfaces;

use Symfony\Component\HttpFoundation\Request;
use Saman\AppearanceBundle\Entity\Navigation;
use Saman\AppearanceBundle\Entity\Link;
use Saman\AppearanceBundle\Service\Interfaces\NavigationServiceInterface;

interface LinkServiceInterface
{
    /**
     * 
     * @param NavigationServiceInterface $navigationService
     */
    public function setNavigationService(NavigationServiceInterface $navigationService);
    
    /**
     * 
     * @return type
     * @throws \Exception
     */
    public function getNavigationService();
    
    /**
     * Get Link based on its ID. If ID is null then create a new Link
     * 
     * @param type $linkId
     * @return type
     */
    public function getLink($linkId = null);
    
    /**
     * 
     * @param Navigation $navigation
     */
    public function getLinksView(Navigation $navigation);
    
    /**
     * Update Link
     * 
     * @param Link $link
     */
    public function updateLink(Link $link);
    
    /**
     * Delete an Link
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param type $linkId
     * @return type
     */
    public function deleteLink(Request $request, $linkId);
    
    /**
     * Add Edit link
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param Navigation $navigation
     * @param type $linkId
     * @return type
     */
    public function addEditLink(Request $request, Navigation $navigation, $linkId = null); 
    
    /**
     * 
     * @param type $request
     * @param type $navigation
     */
    public function sortLinks(Request $request, Navigation $navigation);
}