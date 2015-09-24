<?php

namespace ShoppingBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use ShoppingBundle\Entity\OrderProgress;

/**
 * 
 */
class OrderProgressType extends AbstractType
{
    /**
     * 
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $statusesOptions = array_map(function ($it) {
                return $it['title'];
            }, OrderProgress::$statuses);
        
        $builder
            ->add('status', 'choice', array(
                'choices' => $statusesOptions, 
                'attr' => array('class' => 'form-control form-control-select2')
                ))
            ->add('startDate', 'text', array('required' => false))
            ->add('estimatedEndDate', 'text', array('required' => false))
            ->add('actualEndDate', 'text', array('required' => false))
            ->add('completePercentage', 'integer', array('required' => false))
            ->add('description', 'textarea', array('required' => false))
            ->add('attachments', 'saman_media');
        
        $builder->add('progress', 'entity', array(
            'attr' => array('class' => 'form-control form-control-select2'),
            'empty_value' => 'Choose a Progress',
            'multiple'  => false,
            'class' => 'ShoppingBundle:Progress',
            'query_builder' => function(EntityRepository $er) {
                return $er->createQueryBuilder('p')
                    ->where('p.deleted = 0');
            }
        ));        
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ShoppingBundle\Entity\OrderProgress',
        ));
    }
    
    /**
     * 
     * @return string
     */
    public function getName()
    {
        return 'saman_order_progress_form';
    }
}