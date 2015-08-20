<?php

namespace AppBundle\Library\Twig;

use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use \Twig_Extension;

/**
 *
 * @author Saman Shafigh - samanshafigh@gmail.com
 */
class TwigFunctionExtension extends Twig_Extension
{
    const ICON_TEMPLATE = '<span class="%s"></span>';
    const LINK_TEMPLATE = '<a href="%s" class="%s %s" %s>%s%s</a>';
    const BUTTON_TEMPLATE = '<button %s type="button" class="btn btn-%s %s %s %s" %s %s>%s%s</button>';
    const BREADCRUMB_TEMPLATE = '<ol class="breadcrumb">%s</ol>';
    const BREADCRUMB_TEXT_ITEM_TEMPLATE = '<li class="active">%s%s</li>';
    const BREADCRUMB_LINK_ITEM_TEMPLATE = '<li><a href="%s" class="%s" %s>%s%s</a></li>';
    const BREADCRUMB_JS_ACTION_ITEM_TEMPLATE = '<li><a href="#" %s class="%s %s" %s>%s%s</a></li>';
    
    /** 
     * 
     * @var Translator  
     */
    protected $translator;
    
    /**
     *
     * @var Twig_Environment $environment
     */
    protected $environment;
    
    /**
     *
     * @var type 
     */
    protected $javascripts = array();

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
     * @param \Twig_Environment $environment
     */
    public function initRuntime(\Twig_Environment $environment)
    {
        $this->environment = $environment;
    }
    
    /**
     * 
     * @return type
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction(
                'getWidth', 
                array($this, 'getWidth')
                ),            
            new \Twig_SimpleFunction(
                'breadcrumb', 
                array($this, 'breadcrumb'),
                array('is_safe' => array('html'))
                ),            
            new \Twig_SimpleFunction(
                'link', 
                array($this, 'link'),
                array('is_safe' => array('html'))
                ),
            new \Twig_SimpleFunction(
                'button', 
                array($this, 'button'),
                array('is_safe' => array('html'))
                ),
            new \Twig_SimpleFunction(
                'jslater', 
                array($this, 'jslater')
                ),
            new \Twig_SimpleFunction(
                'jsnow', 
                array($this, 'jsnow'),
                array('is_safe' => array('html'))
                )
            );
    }

    /**
     * Get the width that is required for displaying this string.
     * 
     * @param mix array or string $mixStrings
     * @return type
     */
    public function getWidth($mixStrings)
    {
        $string = $mixStrings;
        if (is_array($mixStrings)) {
            $maxWidth = 0;
            foreach ($mixStrings as $mixString) {
                if (strlen($mixString) > $maxWidth) {
                    $maxWidth = strlen($mixString);
                    $string = $mixString;
                }
            }
        }
        
        return (strlen($string) * 10) + 20;
    }    
    
    public function breadcrumb($navigations ,$parameters = array())
    {
        $defaultParameters = array(
            'class' => '',
            'attr' => null
        );
        $parameters = array_merge($defaultParameters, $parameters);
        
        $attrContent = '';
        if (null !== $parameters['attr']) {
            foreach ($parameters['attr'] as $key => $value) {
                $attrContent = $attrContent . ' ' . $key . '"' . $value . '"';
            }
        }
        
        if (!is_array($navigations)) {
            throw new \Exception('You should provide an array of navigation items');
        }
        
        $breadcrumbItemsContent = '';
        
        foreach ($navigations as $key => $navigation) {
            $defaultNavigationParameters = array(
                'class' => '',
                'url' => null,
                'text' => 'NULL',
                'attr' => null,
                'action' => null,
                'icon' => null,
                'active' => false
            );
            $navigationParameters = array_merge($defaultNavigationParameters, $navigation);
            
            $itemAttrContent = '';
            if (null !== $navigationParameters['attr']) {
                foreach ($navigationParameters['attr'] as $key => $value) {
                    $itemAttrContent = $itemAttrContent . ' ' . $key . '"' . $value . '"';
                }
            }  
            
            $itemIconContent = '';
            if (null !== $navigationParameters['icon']) {
                $itemIconContent = sprintf(
                    self::ICON_TEMPLATE . ' ', 
                    $this->translator->trans($navigationParameters['icon'])
                    );
            }
            
            $attrUrlContent = '';
            if (null !== $navigationParameters['url']) {
                $attrUrlContent = sprintf(
                    'data-url="%s"', 
                    $this->translator->trans($navigationParameters['url'])
                    );
            }
            
            if (null !== $navigationParameters['action']) {
                $breadcrumbItemsContent = $breadcrumbItemsContent . sprintf(
                    self::BREADCRUMB_JS_ACTION_ITEM_TEMPLATE, 
                    $attrUrlContent,
                    $navigationParameters['class'],
                    $navigationParameters['action'],
                    $itemAttrContent,
                    $itemIconContent,
                    $this->translator->trans($navigationParameters['text'])
                    );
            } elseif ($navigationParameters['active'] or null === $navigationParameters['url']) {
                $breadcrumbItemsContent = $breadcrumbItemsContent . sprintf(
                    self::BREADCRUMB_TEXT_ITEM_TEMPLATE, 
                    $itemIconContent,
                    $this->translator->trans($navigationParameters['text'])
                    );
            } else {
                $breadcrumbItemsContent = $breadcrumbItemsContent . sprintf(
                    self::BREADCRUMB_LINK_ITEM_TEMPLATE, 
                    $navigationParameters['url'],
                    $navigationParameters['class'],
                    $itemAttrContent,
                    $itemIconContent,
                    $this->translator->trans($navigationParameters['text'])
                    );
            }
        }
        
        return sprintf(self::BREADCRUMB_TEMPLATE, $breadcrumbItemsContent);
    }
    
    /**
     * 
     * @param type $url
     * @param type $text
     * @param type $parameters
     * @return type
     */
    public function link($text, $parameters = array())
    {
        $defaultParameters = array(
            'url' => null,
            'icon' => null,
            'size' => 'xs', // lg , no , sm, xs
            'class' => '',
            'attr' => null
        );
        $parameters = array_merge($defaultParameters, $parameters);
        
        $sizeContent = 'btn-' . $parameters['size'];
        if ('no' === $parameters['size']) {
            $sizeContent = '';
        }
        
        $iconContent = '';
        if (null !== $parameters['icon']) {
            $iconContent = sprintf(
                self::ICON_TEMPLATE . ' ', 
                $this->translator->trans($parameters['icon'])
                );
        }
        
        $attrContent = '';
        if (null !== $parameters['attr']) {
            foreach ($parameters['attr'] as $key => $value) {
                $attrContent = $attrContent . ' ' . $key . '="' . $value . '"';
            }
        }
        
        return sprintf(
            self::LINK_TEMPLATE, 
            $parameters['url'],
            $sizeContent,
            $parameters['class'],
            $attrContent,
            $iconContent,
            $this->translator->trans($text)
            );
    }
    
    /**
     * 
     * @param type $url
     * @param type $text
     * @param type $parameters
     * @return type
     */
    public function button($text, $parameters = array())
    {
        $defaultParameters = array(
            'url' => null,
            'icon' => null,
            'size' => '1', //('1' => 'xs', '2' => 'sm', '3' => 'no', '4' => 'lg')
            'toggleModal' => null,
            'action' => '',
            'class' => '',
            'type' => 'link',
            'attr' => null
        );
        $parameters = array_merge($defaultParameters, $parameters);
        
        $urlContent = '';
        if (null !== $parameters['url']) {
            $urlContent = sprintf(
                'data-url="%s"', 
                $parameters['url']
                );
        }
        
        $sizes = array('1' => 'xs', '2' => 'sm', '3' => 'no', '4' => 'lg');
        $size = (array_key_exists($parameters['size'], $sizes))? $sizes[$parameters['size']] : '1';
        
        $sizeContent = 'btn-' . $size;
        if ('no' === $size) {
            $sizeContent = '';
        }
        
        $toggleModalContent = '';
        if (null !== $parameters['toggleModal']) {
            $toggleModalContent = sprintf(
                'data-toggle="modal" data-target="#%s"', 
                $parameters['toggleModal']
                );
        }
        
        $iconContent = '';
        if (null !== $parameters['icon']) {
            $iconContent = sprintf(
                self::ICON_TEMPLATE . ' ', 
                $this->translator->trans($parameters['icon'])
                );
        }
        
        $attrContent = '';
        if (null !== $parameters['attr']) {
            foreach ($parameters['attr'] as $key => $value) {
                $attrContent = $attrContent . ' ' . $key . '="' . $value . '"';
            }
        }
        
        return sprintf(
            self::BUTTON_TEMPLATE, 
            $urlContent,
            $parameters['type'],
            $sizeContent,
            $parameters['class'],
            $parameters['action'],
            $toggleModalContent,
            $attrContent,
            $iconContent,
            $this->translator->trans($text)
            );
    }

    /**
     * 
     * @param type $src
     */
    public function jslater($src)
    {
        $this->javascripts[] = $src;
    }
    
    /**
     * 
     * @return type
     */
    public function jsnow()
    {
        $template = '::twig\javascripts.html.twig';
        
        return $this->environment->render(
            $template, 
            array('scripts' => $this->javascripts)
            );
    }
    
    /**
     * 
     * @return string
     */
    public function getName()
    {
        return 'twig_function_extension';
    }
}