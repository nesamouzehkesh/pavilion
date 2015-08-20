<?php

namespace AppBundle\Controller;

use Library\Base\BaseController;

class AppApiController extends BaseController
{
    /**
     * Get flash bag message view
     * 
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function displayFlashBagAction()
    {
        // Get flash Bag
        $flashBag = $this->getService('session')->getFlashBag();
        
        // Get message groups in this $flashBag
        $messageGroups = $flashBag->all();
        
        // Generate the view for these $messageGroups
        $view = $this->renderView(
            'AppBundle:AppApi:displayFlashBag.html.twig', 
            array(
                'messageGroups' => $messageGroups
            ));
        
        // Return a jason response
        return $this->getJsonResponse(true, null, $view);
    }
}