<?php

namespace Library\Service;

class StaticHelper 
{
    /**
     * 
     * @param type $cssArray
     * @param type $important
     * @return type
     */
    public static function getCss($cssArray, $important = '')
    {
        $css = array();
        foreach ($cssArray as $cssKey => $cssValue) {
            if (null !== $cssValue && '' !== $cssValue) {
                switch ($cssKey) {
                    case 'background-image':
                        $css[] = sprintf('background-image: url(%s)  %s', $cssValue, $important);
                        break;
                    case 'background-color':
                        $css[] = sprintf('%s: %s %s', $cssKey, $cssValue, $important);
                        break;
                    case 'background-attachment':
                    case 'background-repeat':
                        if (array_key_exists('background-image', $cssArray)) {
                            $css[] = sprintf('%s: %s %s', $cssKey, $cssValue, $important);
                        }
                        break;
                    case 'background-position':
                        if (array_key_exists('background-image', $cssArray)) {
                            $cssValue = implode(' ', explode('-', $cssValue));
                            var_dump($cssValue);
                            
                            $css[] = sprintf('%s: %s %s', $cssKey, $cssValue, $important);
                        }
                        break;
                    default :
                        $css[] = sprintf('%s: %s %s', $cssKey, $cssValue, $important);
                }
            }
        }
        
        return count($css) > 0? implode('; ', $css): null;
    } 
    
    /**
     * 
     * @param type $jsonMedia
     * @return type
     */
    public static function getMediaPath($jsonMedia)
    {
        $media = json_decode($jsonMedia, true);
        if (isset($media[0]['path'])) {
            return $media[0]['path'];
        }
        
        if (isset($media['path'])) {
            return $media['path'];
        }
        
        return null;        
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
}