<?php

namespace MediaBundle\Twig;

use \Twig_Extension;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use MediaBundle\Form\Type\MultipleType;
use Library\Components\MediaHandler;

/**
 *
 * @author Saman Shafigh - samanshafigh@gmail.com
 */
class MediaTwigFilterExtension extends Twig_Extension
{
    const DOWNLOAD_FILE_TEMPLATE = '<tr class="template-download" style="height: 26px;"><td colspan="2"><span class="glyphicon glyphicon-paperclip" aria-hidden="true"></span><span style="margin-left: 2px;" class="name">%s</span></td><td style="width: 12px"><button type="button" data-id="%d" class="btn btn-link delete action-delete-file" style="padding: 0px 5px 0px 5px;"><span class="glyphicon glyphicon-remove" disabled></span></button></td></tr>';
    
    /**
     *
     * @var Router $router
     */
    protected $router;
    
    /**
     *
     * @var CacheManager $cacheManager 
     */
    protected $cacheManager;

    /**
     *  
     * @var array $parameters
     */
    protected $parameters;
    
    /**
     * 
     * @param type $parameters
     */
    public function __construct(
        Router $router,
        CacheManager $cacheManager,
        $parameters
        ) 
    {
        $this->router = $router;
        $this->cacheManager = $cacheManager;
        $this->parameters = $parameters;
    }
    
    /**
     * 
     * @return type
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter(
                'image', 
                array($this, 'image'), 
                array('is_safe' => array('html'))
                ),            
            new \Twig_SimpleFilter(
                'filterMultiplePrototype', 
                array($this, 'filterMultiplePrototype'), 
                array('is_safe' => array('html'))
                ),
            new \Twig_SimpleFilter(
                'getMedia', 
                array($this, 'getMedia')
                ),
            new \Twig_SimpleFilter(
                'getMedias', 
                array($this, 'getMedias')
                ),
            new \Twig_SimpleFilter(
                'toMediaData', 
                array($this, 'toMediaData')
                ),
            new \Twig_SimpleFilter(
                'toMediaList', 
                array($this, 'toMediaList'), 
                array('is_safe' => array('html'))
                ),
            );
    }
    
    /**
     * 
     * @param type $prototype
     * @return type
     */
    public function filterMultiplePrototype($prototype)
    {
        $prototype = str_replace(
            array(sprintf('[%s]', MultipleType::MULTIPLE_FIELD_NAME)),
            array(''),
            $prototype
            );
        
        return $prototype;
    }
    
    /**
     * 
     * @param type $jsonMedia
     * @return type
     * @throws \Exception
     */
    public function getMedia($jsonMedia)
    {
        return MediaHandler::getMedia($jsonMedia, true);
    }
    
    /**
     * 
     * @param type $jsonMedias
     * @return type
     * @throws \Exception
     */
    public function getMedias($jsonMedias)
    {
        return MediaHandler::getMedias($jsonMedias, true);
    }
    
    /**
     * Gets the browser path for the image and filter to apply.
     *
     * @param string $jsonMedia
     * @param string $filter
     * @param array $runtimeConfig
     *
     * @return \Twig_Markup
     */
    public function image($jsonMedia, $filter, array $runtimeConfig = array())
    {
        return MediaHandler::getMediaUrl($jsonMedia, $this->cacheManager, $filter, $runtimeConfig);
    }
    
    /**
     * Image Filter
     * @param $instance
     * @param $type
     * @param int $width
     * @param int $height
     * @return string
     */
    public function toMediaData($jsonMedias)
    {
        $medias = json_decode($jsonMedias, true);
        $result = '';
        if (is_array($medias)) {
            $mediasId = array();
            foreach ($medias as $media) {
                $mediasId[] = array('name' => $media['name'], 'id' => $media['id']);
            }

            $result = json_encode($mediasId);
        }
        
        return $result;
    }
    
    /**
     * 
     * @param type $jsonMedias
     * @return type
     */
    public function toMediaList($jsonMedias)
    {
        $medias = json_decode($jsonMedias, true);
        $result = '';
        if (is_array($medias)) {
            foreach ($medias as $media) {
                // Generate file download url
                $fileDownloadUrl = $this->router->generate(
                    'saman_media_download_media', 
                    array('id' => $media['id']));
                
                // Generate file download html link
                $fileDownloadLink = sprintf(
                    '<a href="%s">%s</a>', 
                    $fileDownloadUrl, 
                    $media['name']
                    );
                
                // Generate the final list                 
                $result = $result . sprintf(
                    self::DOWNLOAD_FILE_TEMPLATE,
                    $fileDownloadLink,
                    $media['id']
                    );
            }
        }
        
        return $result;
    }
    
    /**
     * 
     * @return string
     */
    public function getName()
    {
        return 'media_twig_function_extension';
    }
    
    /**
     * Get a parameter from $parameters array
     */
    private function getParameter($parameterKey, $defaultValue = null)
    {
        if (array_key_exists($parameterKey, $this->parameters)) {
            return $this->parameters[$parameterKey];
        }
        
        return $defaultValue;
    }    
}