<?php

namespace Library\Base;

abstract class BaseHelper
{
    /**
     * Filter URL 
     * 
     * If $final is set to true we do some extra filtering. This filtering
     * will normally used when we want to use twig URL or PATH function to 
     * generate a url for example create a menue.
     * 
     * @param type $url
     * @param type $final
     * @return type
     */
    public static function filterUrl($url, $final = false)
    {
        // Remove repeted '/' for example '///' will be replace by just one '/'
        $url = preg_replace('/(\/+)/', '/', $url);
        
        // If final is set to true we do some extra filtering.
        if ($final) {
            $url = preg_replace('/^([^a-zA-Z0-9])*/', '', $url);
        }
        
        return $url;
    }
}