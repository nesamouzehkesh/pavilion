<?php

namespace Library\Twig;

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
    const BUTTON_TEMPLATE = '<button %s %s type="button" class="btn btn-%s %s %s %s" %s %s>%s<span class="hidden-sm hidden-xs">%s</span></button>';
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
    
    /**
     * 
     * @param type $navigations
     * @param type $parameters
     * @return type
     * @throws \Exception
     */
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
            
            if (null !== $navigationParameters['action']) {
                $attrUrlContent = '';
                if (null !== $navigationParameters['url']) {
                    $attrUrlContent = sprintf(
                        'data-url="%s"', 
                        $navigationParameters['url']
                        );
                }
                
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
        
        $linkText = (null === $text)? '' : $this->translator->trans($text);
        
        return sprintf(
            self::LINK_TEMPLATE, 
            $parameters['url'],
            $sizeContent,
            $parameters['class'],
            $attrContent,
            $iconContent,
            $linkText
            );
    }
    
    /**
     * 
     * @param type $url
     * @param type $text
     * @param type $clientParameters
     * @return type
     */
    public function button($text, $clientParameters = array())
    {
        $defaultParameters = array(
            'url' => null,
            'icon' => null,
            'size' => null, //('xs', 'sm', 'lg')
            'toggleModal' => null,
            'action' => '',
            'class' => '',
            'type' => 'link',
            'id' => null,
            'attr' => null
        );
        $parameters = array_merge($defaultParameters, $clientParameters);
        
        $urlContent = '';
        if (null !== $parameters['url']) {
            $urlContent = sprintf(
                'data-url="%s"', 
                $parameters['url']
                );
        }
        
        $size = '';
        if (null !== $parameters['size']) {
            $size = sprintf('btn-%s', $parameters['size']);
        }
        
        $toggleModal = '';
        if (null !== $parameters['toggleModal']) {
            $toggleModal = sprintf(
                'data-target="#%s"', 
                // We dont use this any more. We toggle modal in js manually
                //'data-toggle="modal" data-target="#%s"', 
                $parameters['toggleModal']
                );
        }
        
        $icon = '';
        if (null !== $parameters['icon']) {
            $icon = sprintf(
                self::ICON_TEMPLATE . ' ', 
                $this->translator->trans($parameters['icon'])
                );
        }
        
        $attr = '';
        if (null !== $parameters['attr']) {
            foreach ($parameters['attr'] as $key => $value) {
                $attr = $attr . ' ' . $key . '="' . $value . '"';
            }
        }
        
        $id = '';
        if (null !== $parameters['id']) {
            $id = sprintf('id="%s"', $parameters['id']);
        }
        
        return sprintf(
            self::BUTTON_TEMPLATE, 
            $urlContent,
            $id,
            $parameters['type'],
            $size,
            $parameters['class'],
            $parameters['action'],
            $toggleModal,
            $attr,
            $icon,
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