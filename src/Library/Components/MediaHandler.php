<?php

namespace Library\Components;

use Library\Components\Media;

class MediaHandler
{
    /**
     * @var type 
     */
    protected $cacheManager = null;
    
    /**
     * @var array config options
     */
    protected $options = array(
        'filter' => 'o',
        'tFilters' => array('t')
    );

    public function __construct($cacheManager = null, array $options = array())
    {
        $this->options = array_merge($this->options, $options);
        
        if (null !== $cacheManager) {
            $this->setCacheManager($cacheManager);
        }
    }

    /**
     * 
     * @param type $cacheManager
     */
    public function setCacheManager($cacheManager)
    {
        $this->cacheManager = $cacheManager;
        
        return $this;
    }
    
    /**
     * 
     * @return type
     * @throws \Exception
     */
    protected function getCacheManager()
    {
        if (null === $this->cacheManager) {
            throw new \Exception('Cache manager should be set to this media handler');
        }
        
        return $this->cacheManager;
    }

    /**
     * 
     * @param Media|String|Array $media
     * @param string $filter
     * @param array $tFilters
     * @return type
     */
    public function getBrowserMediaArray($media, $filter = null, $tFilters = array())
    {
        $mediaInfo = $this->getMediaInfo($media);
        if (null === $mediaInfo) {
            return null;
        }
        
        $filter = (null === $filter)? $this->options['filter'] : $filter;
        $tFilters = (0 === count($tFilters))? $this->options['tFilters'] : $tFilters;
        
        $browserPath = $this->getCacheManager()
            ->getBrowserPath($mediaInfo['path'], $filter);

        $thumbnailBrowserPaths = array();
        foreach ($tFilters as $tFilter) {
            $thumbnailBrowserPaths[$tFilter] = $this->getCacheManager()
                ->getBrowserPath($mediaInfo['path'], $tFilter);
        }

        return array(
            'name' => $mediaInfo['name'],
            'path' => $browserPath, 
            'thumbnails' => $thumbnailBrowserPaths
            );            
    }
    
    /**
     * 
     * @param Media|String|Array $media
     * @param string $filter
     * @param array $tFilters
     * @return type
     */
    public function getBrowserMedia($media, $filter = null, $tFilters = array())
    {
        return self::makeMedia($this->getBrowserMediaArray($media, $filter, $tFilters));
    }
    
    /**
     * 
     * @param Media[]|String|Array $medias
     * @param string $filter
     * @param array $tFilters
     * @return type
     */
    public function getBrowserMediasArray($medias, $filter = null, $tFilters = array())
    {
        if (!is_array($medias)) {
            $medias = json_decode($medias, true);
        }
        
        $browserMedias = array();
        if (is_array($medias)) {
            foreach ($medias as $media) {
                $browserMedia = $this->getBrowserMediaArray($media, $filter, $tFilters);
                if (null !== $browserMedia) {
                    $browserMedias[] = $browserMedia;
                }
            }
        }
        
        return $browserMedias;
    }
    
    /**
     * 
     * @param Media[]|String|Array $medias
     * @param string $filter
     * @param array $tFilters
     * @return type
     */
    public function getBrowserMedias($medias, $filter = null, $tFilters = array())
    {
        $browserMediasArray = $this->getBrowserMediasArray($medias, $filter, $tFilters);
        
        $browserMedias = array();
        foreach ($browserMediasArray as $browserMediaArray) {
            $browserMedia = self::makeMedia($browserMediaArray);
            if (null !== $browserMedia) {
                $browserMedias[] = $browserMedia;
            }
        }
        
        return $browserMedias;
    }
    
    /**
     * 
     * @param type $jsonMedia
     * @param type $convert
     * @return type
     */
    public static function getMedia($jsonMedia, $convert = false)
    {
        if ($convert) {
            return self::makeMedia(json_decode($jsonMedia, true));
        }
        
        return $jsonMedia;
    }
    
    /**
     * 
     * @param type $jsonMedias
     * @param type $convert
     * @return type
     */
    public static function getMedias($jsonMedias, $convert = false)
    {
        if ($convert) {
            $medias = array();
            $mediasArray = json_decode($jsonMedias, true);
            if (!is_array($mediasArray)) {
                return array();
            }
            
            foreach ($mediasArray as $data) {
                $media = self::makeMedia($data);
                if (null !== $media) {
                    $medias[] = $media;
                }
            }

            return $medias;              
        }
        
        return $jsonMedias;
    }
    
    /**
     * 
     * @param type $jsonMedia
     * @param type $cacheManager
     * @param type $filter
     * @param array $runtimeConfig
     * @return \Twig_Markup
     */
    public static function getMediaUrl($jsonMedia, $cacheManager = null, $filter = 'origin', array $runtimeConfig = array())
    {
        $path = self::getMediaPath($jsonMedia);
        
        if (null !== $path) {
            if (null !== $cacheManager && null !== $filter) {
                return new \Twig_Markup(
                    $cacheManager->getBrowserPath($path, $filter, $runtimeConfig),
                    'utf8'
                );
            } else {
                //TODO: this part should be modified to just return image url without caching and filterning
                return '../app/media/' . $path;
            }
        }
        
        return null;
    }
   
    /**
     * 
     * @param type $mediaArray
     * @throws \Exception
     */
    private static function makeMedia($mediaArray)
    {
        $mediaData = null;
        if (isset($mediaArray['path'])) {
            $mediaData = $mediaArray;
        } else if (isset($mediaArray[0]['path'])) {
            $mediaData = $mediaArray[0];
        }

        $media = null;
        if (null !== $mediaData) {
            $media = new Media();
            $media->setPath($mediaData['path']);
            if (isset($mediaData['name'])) {
                $media->setName($mediaData['name']);
            }
            if (isset($mediaData['size'])) {
                $media->setSize($mediaData['size']);
            }
        }

        return $media;        
    }
    
    /**
     * 
     * @param type $media
     * @return type
     */
    private function getMediaInfo($media)
    {
        if (null === $media) {
            return null;
        }
        
        if ($media instanceof Media) {
            return array(
                'name' => $media->getName(), 
                'path' => $media->getPath()
                );
        }
        
        if (is_array($media)) {
            if (is_array($media[0])) {
                $media = $media[0];
            }
        } else {
            $media = json_decode($media, true);
            if (is_array($media) and isset($media[0])) {
                $media = $media[0];
            }
        }

        if (is_array($media) and isset($media['path'])) {
            $path = $media['path'];
            $name = $media['name'];
            
            return array(
                'name' => $name, 
                'path' => $path
                );
        }
        
        return null; 
    }
}