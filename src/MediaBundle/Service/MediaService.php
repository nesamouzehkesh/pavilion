<?php

namespace MediaBundle\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Annotations\AnnotationReader;
use Gaufrette\Filesystem;
use AppBundle\Service\AppService;
use UserBundle\Entity\User;
use MediaBundle\Entity\Media;
use Library\Exception\VisibleHttpException;

class MediaService
{
    const FILE_PERMANENT_PATH = "";
    const FILE_TEMPORARY_PATH = "";
    
    /**
     *  
     * @var AppService $appService
     */
    protected $appService;
    
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
     * @param AppService $appService
     * @param Filesystem $filesystem
     * @param type $parameters
     */
    public function __construct(
        AppService $appService,
        Filesystem $filesystem,
        $liipImagineController,
        $liipImagineCacheManagerm,
        $parameters
        ) 
    {
        $this->appService = $appService;
        $this->user = null;
        // Set the default fileSystem based on the service configurations
        $this->filesystem = $filesystem;
        $this->appService->setParametrs($parameters);
    }
    
    /**
     * 
     * @param type $user
     * @return \MediaBundle\Service\MediaService
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
        return Media::getRepository($this->appService->getEntityManager())
            ->getItemsByID($ids, $readOnly);
    }
    
    /**
     * Get Media from media repository
     * 
     * @param type $id
     * @return Media|array
     */
    public function getMedia($id, $loadAsArray = false)
    {
        return Media::getRepository($this->appService->getEntityManager())
            ->getItem($id, $loadAsArray);
    }
    
    /**
     * Create a response to download a media
     * 
     * @param type $id
     * @return Response
     * @throws \Exception
     */
    public function downloadMedia($id)
    {
        // Get media as an array
        $media = $this->getMedia($id, true);
        if (null === $media) {
            throw $this->appService->createVisibleHttpException('alert.error.noItemFound');
        }
        
        // Check permission to access this file
        if (!$this->hasAccess('download', $media)) {
            throw $this->appService->createVisibleHttpException('alert.error.insufficientPermission');
        }
        
        // Generate appropriate response for this file
        $response = new Response();
        $rootDirectory = $this->appService->getParameter('rootDirectory');
        $mediaPath = $rootDirectory . '/' . $media['path'];
        $filename = $media['title'];
        if (null === $filename) {
            $filename = basename($media['path']);
        }

        // Set headers
        $response->headers->set('Cache-Control', 'private');
        $response->headers->set('Content-type', mime_content_type($mediaPath));
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '";');
        $response->headers->set('Content-length', filesize($mediaPath));

        // Send headers before outputting anything
        $response->sendHeaders();
        $response->setContent(readfile($mediaPath));            

        return $response;
    }
    
    /**
     * 
     * @param type $mediaId
     * @return type
     */
    public function getMediaDownloadUrl($mediaId)
    {
        return $this->appService->generateUrl(
            'saman_media_download_media', 
            array('id' => $mediaId)
            );
    }
    
    /**
     * 
     * @param type $ids
     * @return \MediaBundle\Service\MediaService
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
                $this->appService->persistEntity($media);
                
                
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
        $reflectionProperties = $this->appService->getObjectProperties($entity);
        foreach ($reflectionProperties as $reflectionProperty) {
            $mediaAnnotation = $annotationReader->getPropertyAnnotation(
                $reflectionProperty, 
                'Library\\Annotation\\MediaAnnotation'
                );

            if ($mediaAnnotation) {
                $column = $reflectionProperty->getName();
                $getter = 'get' . ucfirst($column);
                $setter = 'set' . ucfirst($column);
                $mediasData = json_decode(stripslashes($entity->$getter()), true);
                if (is_array($mediasData)) {
                    $mediasId = array_map(function ($it) {return $it['id'];}, $mediasData);
                    $medias = $this->saveMediaById($mediasId, $entity, $column);
                    $entity->$setter($medias);
                }
            }
        }
        $this->appService->persistEntity($entity);
        $this->appService->flushEntityManager();
        
        return true;
    }    
        
    /**
     * Upload files in request to the temperory or permannet location
     * 
     * @param File $files
     * @param type $isPermanent
     * @param type $visibility
     * @return type
     */
    public function uploadMedia($files, $isPermanent, $visibility = null)
    {
        $medias = $this->createMedia($files, $isPermanent, $visibility);
        $filesArray = Media::mediasToArray($medias);
        
        return $this->appService->getJsonResponse(true, null, null, array('files' => $filesArray));        
    }
    
    /**
     * 
     * @param type $id
     * @return type
     * @throws \Exception
     */
    public function deleteMedia($id)
    {
        // Get media as an array
        $media = $this->getMedia($id, true);
        if (null === $media) {
            throw $this->appService->createVisibleHttpException('alert.error.noItemFound');
        }
        
        // Check permission to access this file
        if (!$this->hasAccess('delete', $media)) {
            throw $this->appService->createVisibleHttpException('alert.error.insufficientPermission');
        }
        
        $mediaR = $this->appService->getEntityManager()
            ->getReference('MediaBundle:Media', $media['id']);
        $this->appService->removeEntity($mediaR);
        $this->appService->flushEntityManager();

        return $this->appService->getJsonResponse(
            true,
            array(
                'alert.success.itemHasBeenRemoved', 
                array('%id%' => $media['id']))
            );
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
                    $this->appService->persistEntity($media);
                }
            } else {
                $media->setError($validationResult);                
            }
            
            $medias->add($media);
        }
        $this->appService->flushEntityManager();
        
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
        $allowedMimeTypes = $this->appService->getParameter('allowedMimeTypes');
        
        if (null === $file) {
            throw new VisibleHttpException('File is null');
        }
        
        if (!is_a($file, '\Symfony\Component\HttpFoundation\File\UploadedFile')) {
            throw new VisibleHttpException('File is not an UploadedFile object');
        }
        
        if ('' === $file->getFilename()) {
            throw new VisibleHttpException('File name cannot be empty');
        }
        
        if (!in_array($file->getClientMimeType(), $allowedMimeTypes)) {
            throw new VisibleHttpException(sprintf(
                'Files of type %s are not allowed.', 
                $file->getClientMimeType())
                );
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
        $environment = $this->appService->getParameter('environment');
        // Define the life cycle path of this file
        $lifeCyclePath = ($isPermanent)? 'perm' : 'temp';
        // Get file extension out of file original path
        $extension = $this->getFileExtension($originalPath);
        // Generate uniq ID for the file title
        $uniqId = $this->appService->getRandomString();
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
     * 
     * @param type $action
     * @param type $media
     * @return boolean
     */
    private function hasAccess($action, $media)
    {
        $user = $this->appService->getUser();
        if ($user instanceof User and $user->hasAnyAdminRole()) {
            return true;
        }
        
        switch ($action) {
            case 'download':
                if (Media::VISIBILITY_PUBLIC === $media['visibility']) {
                    return true;
                }
                
                if ($user instanceof User and $user->getId() === $media['user']) {
                    return true;
                }
                break;
            case 'delete':
                if ('0' === $media['isPermanent']) {
                    if ($user instanceof User and $user->getId() === $media['user']) {
                        return true;
                    }
                }
                break;                
        }
        
        return false;
    }
}