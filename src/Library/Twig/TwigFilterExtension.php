<?php

namespace Library\Twig;

use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use \Twig_Extension;
use Library\Service\StaticHelper;

/**
 *
 * @author Saman Shafigh - samanshafigh@gmail.com
 */
class TwigFilterExtension extends Twig_Extension
{
    const ICON_TEMPLATE = '<span class="%s %s"></span>';
    const ALERT_TEMPLATE = '<div class="alert alert-%s alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>%s</div>';
    
    /** 
     * 
     * @var Translator  
     */
    protected $translator;
    
    /**
     * Resolve dependencies
     * 
     * @param Translator $translator
     */
    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }  
    
    /**
     * 
     * @return type
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter(
                'getParam', 
                array($this, 'getParam')
                ),
            new \Twig_SimpleFilter(
                'mapTo', 
                array($this, 'mapTo')
                ),
            new \Twig_SimpleFilter(
                'getBaseUri', 
                array($this, 'getBaseUri')
                ),
            new \Twig_SimpleFilter(
                'style', 
                array($this, 'style'), 
                array('is_safe' => array('html'))
                ),
            new \Twig_SimpleFilter(
                'css', 
                array($this, 'css'), 
                array('is_safe' => array('html'))
                ),
            new \Twig_SimpleFilter(
                'clearable', 
                array($this, 'clearable'), 
                array('is_safe' => array('html'))
                ),
            new \Twig_SimpleFilter(
                'icon', 
                array($this, 'icon'), 
                array('is_safe' => array('html'))
                ),
            new \Twig_SimpleFilter(
                'showAlert', 
                array($this, 'showAlert'), 
                array('is_safe' => array('html'))
                ),            
            new \Twig_SimpleFilter(
                'warningAlert', 
                array($this, 'warningAlert'), 
                array('is_safe' => array('html'))
                ),
            new \Twig_SimpleFilter(
                'successAlert', 
                array($this, 'successAlert'), 
                array('is_safe' => array('html'))
                ),
            new \Twig_SimpleFilter(
                'errorAlert', 
                array($this, 'errorAlert'), 
                array('is_safe' => array('html'))
                ),
            );
    }
    
    /**
     * 
     * @param type $params
     * @param type $key
     * @param type $default
     * @return type
     */
    public function getParam($params, $key, $default = null)
    {
        if (is_array($params) AND array_key_exists($key, $params)) {
            return $params[$key];
        }
        
        return $default;
    }
    
    /**
     * 
     * @param type $uri
     * @return type
     */
    public function mapTo($index, array $from)
    {
        $total = count($from);
        if ($total > 0) {
            $key = ($index % $total) - 1;
            if (array_key_exists($key, $from)) {
                return $from[$key];
            }
        }
        
        return null;
    }
    
    /**
     * 
     * @param type $uri
     * @return type
     */
    public function getBaseUri($uri)
    {
        $uriArray = explode("/", $uri);
        
        return isset($uriArray[1])? $uriArray[1] : null;
    }
    
    /**
     * 
     * @param type $cssArray
     * @return type
     */
    public function style($cssArray)
    {
        $css = $this->css($cssArray);
        
        return (null !== $css)? sprintf('style="%s"', $css) : null;
    }
    
    /**
     * 
     * @param type $cssArray
     * @return type
     */
    public function css($cssArray, $important = '')
    {
        return StaticHelper::getCss($cssArray, $important); 
    }    
    
    /**
     * Add class clearable to the text box
     * 
     * @param type $formElement
     */
    public function clearable($formElement)
    {
        return (null === $formElement)? 'form-control clearable' : 'form-control x clearable';
    }
    
    /**
     * Create Bootstrap icon
     * 
     * @param type $icon
     * @return type
     */
    public function icon($icon, $extraClass = '')
    {
        return sprintf(
            self::ICON_TEMPLATE, 
            $this->translator->trans($icon), 
            $extraClass
            );
    }
    
    /**
     * Show an Alert
     * 
     * @param type $alert
     * @param type $alertType
     * @return type
     */
    public function showAlert($alert, $alertType)
    {
        return $this->getAlert($alertType, $alert);
    }
    
    /**
     * Create Bootstrap alert 
     * 
     * @param type $warningAlert
     * @return type
     */
    public function warningAlert($warningAlert)
    {
        return $this->getAlert('warning', $warningAlert);
    }
    
    /**
     * Create Bootstrap alert 
     * 
     * @param type $successAlert
     * @return type
     */
    public function successAlert($successAlert)
    {
        return $this->getAlert('success', $successAlert);
    }
    
    /**
     * Create Bootstrap alert 
     * 
     * @param type $errorAlert
     * @return type
     */
    public function errorAlert($errorAlert)
    {
        return $this->getAlert('danger', $errorAlert);
    }
    
    /**
     * 
     * @param type $alert
     * @param type $alertType
     * @return type
     */
    private function getAlert($alertType, $alert)
    {
        $alert = $this->translator->trans($alert);
        
        return sprintf(self::ALERT_TEMPLATE, $alertType, $alert);
    }

    /**
     * 
     * @return string
     */
    public function getName()
    {
        return 'twig_filter_extension';
    }    
}