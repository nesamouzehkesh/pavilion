<?php

namespace Saman\AppearanceBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\ORM\EntityRepository;
use Saman\AppearanceBundle\Entity\Widget;

class WidgetSettingForm extends AbstractType
{
    /**
     *
     * @var Widget $widget
     */
    protected $widget;
    
    /**
     *
     * @var type 
     */
    protected $em;

    /**
     * 
     * @param type $userConfig
     */
    public function __construct(Widget $widget, $em)
    {
        $this->widget = $widget;
        $this->em = $em;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $widgetStructure = $this->widget->getWidgetStructure();
        // If no settings is defined for this widget just return null
        if (!is_array($widgetStructure) or !array_key_exists('settings', $widgetStructure)) {
            return;
        }
        
        // If the content of settings is empty or is not array return null
        $defaultSettings = $widgetStructure['settings'];
        if (!is_array($defaultSettings) or 0 === count($defaultSettings)) {
            return;
        }
        
        $settings = $this->widget->getSettings();
        foreach ($defaultSettings as $key => $defaultSetting) {
            $type = $defaultSetting['type'];
            $data = (array_key_exists('default', $defaultSetting))? $defaultSetting['default'] : null;
            $required = (array_key_exists('required', $defaultSetting))? $defaultSetting['required'] : false;
            $description = (array_key_exists('description', $defaultSetting))? $defaultSetting['description'] : null;
            $grid = (array_key_exists('grid', $defaultSetting))? $defaultSetting['grid'] : null;
            
            if (array_key_exists($key, $settings)) {
                $data = $settings[$key];
            }

            $fieldOptions = array(
                'mapped' => false,
                'label' => $defaultSetting['label'],
                'data' => $data,
                'required' => $required,
                'attr' => array(
                    'description' => $description,
                    'grid' => $grid
                    )
                );
            
            switch ($type) {
                case 'saman_multiple':
                    $allowAdd = (array_key_exists('allow_add', $defaultSetting))? $defaultSetting['allow_add'] : null;
                    $fieldOptions['fields'] = $defaultSetting['fields'];
                    $fieldOptions['data'] = $data;
                    $fieldOptions['allow_add'] = $allowAdd;
                    break;
                case 'choice':
                    $fieldOptions['choices'] = $defaultSetting['choices'];
                    break;
                case 'entity':
                    $entity = null;
                    if (intval($data)) {
                        $entity = $this->em
                            ->getReference($defaultSetting['class'], intval($data));
                    }
                    $fieldOptions['data'] = $entity;
                    $fieldOptions['class'] = $defaultSetting['class'];
                    $fieldOptions['query_builder'] = function(EntityRepository $er) {
                        return $er->createQueryBuilder('l')
                            ->where('l.deleted = 0');
                        };

                    break;
            }

            $builder->add($key, $type, $fieldOptions);
        }
    }

    public function getName()
    {
        return 'saman_appearance_theme_widget_setting_form';
    }
}