<?php

namespace Saman\AppearanceBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

class LinkController extends NavigationController
{
    /**
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param type $navigationId
     * @return type
     */
    public function addLinkAction(Request $request, $navigationId)
    {
        return $this->getNavigationService()->addEditLink($request, $navigationId, null);
    }
    
    /**
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param type $navigationId
     * @param type $linkId
     * @return type
     */
    public function editLinkAction(Request $request, $navigationId, $linkId)
    {
        return $this->getNavigationService()->addEditLink($request, $navigationId, $linkId);
    }
    
    /**
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param type $linkId
     * @return type
     */
    public function deleteLinkAction(Request $request, $linkId)
    {
        return $this->getNavigationService()->deleteLink($request, $linkId);
    }
    
    /**
     * 
     * @param Request $request
     * @param type $navigationId
     * @return type
     */
    public function sortLinkAction(Request $request, $navigationId)
    {
        return $this->getNavigationService()->sortLinks($request, $navigationId);
    }        
}