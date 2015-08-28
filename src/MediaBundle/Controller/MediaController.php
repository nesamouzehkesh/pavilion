<?php

namespace MediaBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Library\Base\BaseController;

class MediaController extends BaseController
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
        try {
            $files = $request->files->get('files');

            return $this->getMediaService()->uploadMedia($files, $isPermanent);
        } catch (\Exception $ex) {
            return $this->getExceptionResponse(
                'alert.error.canNotUploadItem', 
                $ex
                );
        }        
    }
    
    /**
     * Download media
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return type
     */
    public function downloadMediaAction(Request $request)
    {
        try {
            $id = intval($request->get('id'));
            
            return $this->getMediaService()->downloadMedia($id);
        } catch (\Exception $ex) {
            return $this->getExceptionResponse(
                'alert.error.canNotDownloadItem', 
                $ex
                );
        }        
    }
    
    /**
     * Delete media
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return type
     */
    public function deleteMediaAction(Request $request)
    {
        try {
            $id = intval($request->get('id'));
            
            return $this->getMediaService()->deleteMedia($id);
        } catch (\Exception $ex) {
            return $this->getExceptionResponse(
                'alert.error.canNotDeleteItem', 
                $ex
                );
        }        
    }

    /**
     * 
     * @return \MediaBundle\Service\MediaService
     */
    protected function getMediaService()
    {
        return $this->get('saman_media.media')
            ->setUser($this->getUser());
    }    
}