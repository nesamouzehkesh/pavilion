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
}