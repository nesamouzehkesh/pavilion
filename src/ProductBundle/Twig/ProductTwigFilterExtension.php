<?php

namespace ProductBundle\Twig;

use \Twig_Extension;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;

/**
 *
 * @author Saman Shafigh - samanshafigh@gmail.com
 */
class ProductTwigFilterExtension extends Twig_Extension
{
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
                'transSpecifications', 
                array($this, 'transSpecifications')
                ),
            );
    }
    
    /**
     * Trans product specifications based on $specificationFields
     * 
     * @param type $specifications
     * @param type $specificationFields
     * @return type
     */
    public function transSpecifications($specifications, $specificationFields)
    {
        $params = array();
        if (!is_array($specifications)) {
            return $params;
        }
        
        foreach ($specificationFields as $key => $field) {
            $value = null;
            if (array_key_exists($key, $specifications)) {
                if ('choice' === $field['type']) {
                    if (array_key_exists($specifications[$key], $field['choices'])) {
                        $value = $field['choices'][$specifications[$key]];
                    }
                } else {
                    $value = $specifications[$key];
                }
            }

            if (null !== $value) {
                $params[$field['label']] = $value;
            }
        }

        return $params;
    }

    /**
     * 
     * @return string
     */
    public function getName()
    {
        return 'twig_product_filter_extension';
    }    
}