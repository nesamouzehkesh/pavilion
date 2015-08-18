<?php

namespace Saman\AppearanceBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Saman\AppearanceBundle\Entity\Theme;
use Saman\Library\Service\Helper;

class ThemeSettingForm extends AbstractType
{
    /**
     *
     * @var Helper $helper
     */
    private $helper;
    
    /**
     *
     * @var Theme $theme
     */
    private $theme;
    
    /**
     *
     * @var array $templateParameters
     */
    private $templateParameters;    

    /**
     * 
     * @param type $userConfig
     */
    public function __construct(Helper $helper, Theme $theme, $templateParameters)
    {
        $this->helper = $helper;
        $this->theme = $theme;
        $this->templateParameters = $templateParameters;
    }
        
    /**
     * 
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $templateParameters = $this->templateParameters['settings'];
      
        foreach ($templateParameters as $key => $parameter) {
            $type = $parameter['type'];
            $attr = array(
                'description' => array_key_exists('description', $parameter)? $parameter['description'] : NULL,
                'possibleValues' => array_key_exists('possibleValues', $parameter)? $parameter['possibleValues'] : NULL,
                'icon' => array_key_exists('icon', $parameter)? $parameter['icon'] : NULL,
                'grid' => array_key_exists('grid', $parameter)? $parameter['grid'] : NULL
                );
            
            if (isset($parameter['attr'])) {
                $attr = array_merge($attr, $parameter['attr']);
            }
            
            $fieldOptions = array(
                'mapped' => false,
                'label' => $parameter['label'],
                'required' => $parameter['required'],
                'data' => $this->theme->getParameter($key, $templateParameters),
                'attr' => $attr
                );

            switch ($type) {
                case 'saman_select2':
                    $fieldOptions['choices'] = $parameter['choices'];
                    break;
                case 'choice':
                    $fieldOptions['choices'] = $parameter['choices'];
                    break;
            }            

            $builder->add($key, $type, $fieldOptions);
        }
    }

    /**
     * 
     * @return string
     */
    public function getName()
    {
        return 'saman_appearance_theme_setting_form';
    }
}