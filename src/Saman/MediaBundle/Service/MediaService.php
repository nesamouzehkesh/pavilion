<?php

namespace Saman\MediaBundle\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Annotations\AnnotationReader;
use Gaufrette\Filesystem;
use Gaufrette\Adapter\Local;
use Saman\Library\Service\Helper;
use Saman\UserBundle\Entity\User;
use Saman\MediaBundle\Entity\Media;
use Saman\Library\Map\EntityMap;

class MediaService
{
    const FILE_PERMANENT_PATH = "";
    const FILE_TEMPORARY_PATH = "";
    
    /**
     *  
     * @var Helper $helper
     */
    protected $helper;
    
    /**
     *
     * @var type 
     */
    protected $filesystem;
    
    /**
     *
     * @var User $user
     */
    protected $user;

    /**
     * 
     * @param \Saman\Library\Service\Helper $helper
     * @param \Saman\MediaBundle\Service\Filesystem $filesystem
     * @param type $parameters
     */
    public function __construct(
        Helper $helper,
        Filesystem $filesystem,
        $parameters
        ) 
    {
        $this->helper = $helper;
        $this->user = null;
        // Set the default fileSystem based on the service configurations
        $this->filesystem = $filesystem;
        $this->helper->setParametrs($parameters);
    }
    
    /**
     * 
     * @param type $user
     * @return \Saman\MediaBundle\Service\MediaService
     */
    public function setUser($user)
    {
        $this->user = $user;
        
        return $this;
    }

    /**
     * 
     * @param \Gaufrette\Filesystem $filesystem
     */
    public function setFilesystem(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }
    
    /**
     * 
     * @param type $id
     * @return type
     */
    public function getMedias($ids, $readOnly = true)
    {
        return $this->getMediaRepository()->getItemsByID($ids, $readOnly);
    }
    
    /**
     * Get Media from media repository
     * 
     * @param type $id
     * @return type
     */
    public function getMedia($id)
    {
        return $this->getMediaRepository()->getItem($id);
    }
    
    /**
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return type
     */
    public function getMediaFormRequest(Request $request)
    {
        $media = null;
        $id = intval($request->get('id'));
        if (0 !== $id) {
            $media = $this->getMedia($id);
        } 
        
        return $media;
    }
    
    /**
     * Create a response to download a media
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function downloadMedia(Request $request)
    {
        $media = $this->getMediaFormRequest($request);
        if (null !== $media) {
            $response = new Response();
            $rootDirectory = $this->helper->getParameter('rootDirectory');
            $mediaPath = $media->getFullPath($rootDirectory);
            $filename = $media->getTitle(true);
            
            // Set headers
            $response->headers->set('Cache-Control', 'private');
            $response->headers->set('Content-type', mime_content_type($mediaPath));
            $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '";');
            $response->headers->set('Content-length', filesize($mediaPath));

            // Send headers before outputting anything
            $response->sendHeaders();
            $response->setContent(readfile($mediaPath));            
            
            return $response;
        } else {
            throw new \Exception('No media exist to be download');
        }        
    }
    
    /**
     * 
     * @param type $mediaId
     * @return type
     */
    public function getMediaDownloadUrl($mediaId)
    {
        return $this->helper->generateUrl(
            'saman_media_download_media', 
            array('id' => $mediaId)
            );
    }
    
    /**
     * 
     * @param type $ids
     * @return \Saman\MediaBundle\Service\MediaService
     */
    public function saveMediaById($ids, $entity, $column = null)
    {
        $medias = $this->getMedias($ids, false);
        foreach ($medias as $media) {
            // Select thoses temporary medias
            if ($media->isTemporary()) {
                // Set media entity ID
                $media->setEntityId($entity->getId());
                // Set this media to be permanent
                $media->setPermanent();
                // Get media original temporary path
                $originalPath = $media->getPath();
                // Create new permanent path
                $targetPhat = $this->createFilePhat($originalPath, true);
                // Rename file from $sourcePhat to $targetPhat
                $this->renameFile($originalPath, $targetPhat);
                // Update media file location
                $media->setPath($targetPhat);
                // Set media column title
                $media->setColumn($column);
                // Set media class
                $media->setClass(get_class($entity));
                // Persist new changes in media entity
                $this->helper->persistEntity($media);
            }
        }

        return Media::mediasToString($medias);
    }
    
    /**
     * You give an entity to this function and it will save all medias form 
     * template location to permanent one. This function will look at entity 
     * media anotaion and will save its 
     * 
     * @param type $entity
     */
    public function saveMedia($entity)
    {
        $annotationReader = new AnnotationReader();
        $reflectionProperties = $this->helper->getObjectProperties($entity);
        foreach ($reflectionProperties as $reflectionProperty) {
            $mediaAnnotation = $annotationReader->getPropertyAnnotation(
                $reflectionProperty, 
                'Saman\\Library\\Annotation\\MediaAnnotation'
                );

            if ($mediaAnnotation) {
                $column = $reflectionProperty->getName();
                $getter = 'get' . ucfirst($column);
                $setter = 'set' . ucfirst($column);
                $ids = json_decode(stripslashes($entity->$getter()));

                $medias = $this->saveMediaById($ids, $entity, $column);
                $entity->$setter($medias);
            }
        }
        $this->helper->persistEntity($entity);
        $this->helper->flushEntityManager();
        
        return true;
    }    
    
    /**
     * Upload files in request to the temperory or permannet location
     * 
     * @param Request $request
     * @param type $isPermanent
     * @param type $visibility
     * @return type
     */
    public function uploadMedia(Request $request, $isPermanent, $visibility = null)
    {
        $uploadedFiles = $request->files->get('files');
        $medias = $this->createMedia($uploadedFiles, $isPermanent, $visibility);
        $files = Media::mediasToArray($medias);
        
        return $this->helper->getJsonResponse(true, null, null, array('files' => $files));        
    }
    
    /**
     * 
     * @param Request $request
     * @return type
     */
    public function deleteMedia(Request $request)
    {
        $media = $this->getMediaFormRequest($request);
        if (null !== $media) {
            $this->helper->removeEntity($media);
            $this->helper->flushEntityManager();

            return $this->helper->getJsonResponse(
                true,
                array(
                    'alert.success.itemHasBeenRemoved', 
                    array('%id%' => $media->getId()))
                );
        } else {
            return $this->helper->getJsonResponse(
                true,
                array('alert.error.noItemHasBeenFound')
                );
        }        
    }
    
    /**
     * Create a temporary or permanent media, create a file in temporary or 
     * permanent location
     * 
     * @param UploadedFile $file
     * @return Media
     * @throws \InvalidArgumentException
     */
    private function createMedia($uploadedFiles, $isPermanent, $visibility = null)
    {
        $medias = new ArrayCollection();
        if (!is_array($uploadedFiles)) {
            $uploadedFiles = array($uploadedFiles);
        }
        
        foreach ($uploadedFiles as $file) {
            $media = new Media($isPermanent, $visibility, $this->user);
            $validationResult = $this->isValidFile($file);
            if (true === $validationResult) {
                $filePath = $this->createFilePhat(
                    $file->getClientOriginalName(), 
                    $isPermanent
                    );
                
                $addFileResult = $this->createFile($file, $filePath);
                if (true !== $addFileResult) {
                    $media->setError($addFileResult);
                } else {
                    $media->setTitle($file->getClientOriginalName());
                    $media->setPath($filePath);
                    $media->setSize($file->getClientSize());
                    $this->helper->persistEntity($media);
                }
            } else {
                $media->setError($validationResult);                
            }
            
            $medias->add($media);
        }
        $this->helper->flushEntityManager();
        
        return $medias;
    }
    
    /**
     * Validating a file.
     * Check if the file's mime type is in the list of allowed mime types.
     * 
     * @param type $file
     * @return boolean
     */
    private function isValidFile($file)
    {
        $allowedMimeTypes = $this->helper->getParameter('allowedMimeTypes');
        
        if (null === $file) {
            return 'File is null';
        }
        
        if (!is_a($file, '\Symfony\Component\HttpFoundation\File\UploadedFile')) {
            return 'File is not an UploadedFile object';
        }
        
        if ('' === $file->getFilename()) {
            return 'File name cannot be empty';
        }
        
        if (!in_array($file->getClientMimeType(), $allowedMimeTypes)) {
            return sprintf(
                'Files of type %s are not allowed.', 
                $file->getClientMimeType());
        }
        
        return true;
    }
    
    /**
     * 
     * @param UploadedFile $file
     * @param type $filePath
     * @return string|boolean
     */
    private function createFile(UploadedFile $file, $filePath)
    {
        $fileContent = file_get_contents($file->getPathname());
        //$adapter = $this->filesystem->getAdapter();
        //$adapter->setMetadata($filename, array('contentType' => $file->getClientMimeType()));
        //$adapter->write($fileName, $fileContent);
        $this->filesystem->write($filePath, $fileContent);
        
        return true;
    }
    
    /**
     * 
     * @param type $path
     */
    private function renameFile($sourcePhat, $targetPhat)
    {
        return $this->filesystem->rename($sourcePhat, $targetPhat);
    }
    
    /**
     * Returns the original file extension
     *
     * It is extracted from the original file name that was uploaded.
     * Then it should not be considered as a safe value.
     *
     * @return string The extension
     */
    private function getFileExtension($path)
    {
        return pathinfo($path, PATHINFO_EXTENSION);
    }
    
    /**
     * Create file path
     * 
     * @param string $originalPath
     * @param bool $isPermanent
     * @return type
     */
    private function createFilePhat(
        $originalPath, 
        $isPermanent = false
        )
    {
        // Get the enviroment (e.g. dev)
        $environment = $this->helper->getParameter('environment');
        // Define the life cycle path of this file
        $lifeCyclePath = ($isPermanent)? 'perm' : 'temp';
        // Get file extension out of file original path
        $extension = $this->getFileExtension($originalPath);
        // Generate uniq ID for the file title
        $uniqId = $this->helper->getRandomString();
        // Generate flie path
        $filePath = sprintf('%s/%s/%s/%s/%s/%s.%s', 
            $environment,
            $lifeCyclePath,
            date('Y'), 
            date('m'), 
            date('d'), 
            $uniqId, 
            $extension
            );
        
        return $filePath;
    }
    
    /**
     * Get Media Repository
     * 
     * @return type
     */
    private function getMediaRepository()
    {
        return $this->helper->getRepository(EntityMap::MEDIA_MEDIA);
    }
}