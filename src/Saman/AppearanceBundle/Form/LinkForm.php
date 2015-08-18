<?php

namespace Saman\AppearanceBundle\Form;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\ORM\EntityRepository;

/**
 * 
 */
class LinkForm extends AbstractType
{
    public function __construct()
    {
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Saman\AppearanceBundle\Entity\Link',
        ));
    }    

    /**
     * 
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title', 'text', array(
            'required' => false,
        ));
        
        $builder->add('page', 'entity', array(
            'required' => false,
            'class' => 'SamanCmsBundle:Page',
            'query_builder' => function(EntityRepository $er) {
                return $er->createQueryBuilder('p')
                    ->where('p.deleted = 0');
                }
        ));
        
        $builder->add('icon', 'saman_icon', array(
            'required' => false,
        ));
        
        $builder->add('hint', 'text', array(
            'required' => false,
        ));
        
        $builder->add('url', 'text', array(
            'required' => false,
        ));
    }

    /**
     * 
     * @return string
     */
    public function getName()
    {
        return 'saman_appearance_link_form';
    }
}