<?php

namespace Saman\MediaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class MediaController extends Controller
{
    /**
     * Upload a file in a temporary location and returns an JSON array of uploaded 
     * files info, including their uploaded phat, original name.
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return type
     */
    public function uploadMediaAction(Request $request, $isPermanent)
    {
        return $this->getMediaService()->uploadMedia($request, $isPermanent);
    }
    
    /**
     * Download media
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return type
     */
    public function downloadMediaAction(Request $request)
    {
        return $this->getMediaService()->downloadMedia($request);
    }
    
    /**
     * Delete media
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return type
     */
    public function deleteMediaAction(Request $request)
    {
        return $this->getMediaService()->deleteMedia($request);
    }

    /**
     * 
     * @return type
     */
    protected function getMediaService()
    {
        return $this->get('saman_media.media')
            ->setUser($this->getUser());
    }    
}