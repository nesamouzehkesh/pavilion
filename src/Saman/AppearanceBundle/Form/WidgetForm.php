<?php

namespace Saman\AppearanceBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Saman\AppearanceBundle\Entity\Widget;

class WidgetForm extends AbstractType
{
    /**
     *
     * @var type 
     */
    protected $widgetsStructure;
    
    /**
     * 
     * @param type $userConfig
     */
    public function __construct($widgetsStructure)
    {
        $this->widgetsStructure = $widgetsStructure;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title', 'text', array(
            'required' => false
        ));
        
        $builder->add('type', 'choice', array(
            'choices' => Widget::getWidgetChoiceFieldValues($this->widgetsStructure),
        ));
    }

    public function getName()
    {
        return 'saman_appearance_theme_widget_form';
    }
}