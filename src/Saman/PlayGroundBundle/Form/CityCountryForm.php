c<?php

namespace Saman\PlayGroundBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class CityCountryForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('country', 'entity', array(
            'class' => 'SamanPlayGroundBundle:Country', 
            'property' => 'name', 
            // 'property_path' => false //Country is not directly related to City
            ));
        
        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) {
                $form = $event->getForm();

                // this would be your entity, i.e. SportMeetup
                $data = $event->getData();

                $country = $data->getCountry();
                $cities = null === $country ? array() : $country->getCities();

                $form->add('position', 'entity', array(
                    'class'       => 'SamanPlayGroundBundle:City',
                    'placeholder' => '',
                    'choices'     => $cities,
                ));
            }
        );        
        
        $builder->add('name');        
        $builder->add('save', 'submit');
    }

    public function getName()
    {
        return 'cityCountry';
    }
}