<?php

namespace CmsBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use CmsBundle\Entity\Page;

/**
 * 
 */
class PageFormType extends AbstractType
{
    /**
     *
     * @var type 
     */
    protected $parameters;
    
    /**
     *
     * @var type 
     */
    protected $page;

    /**
     * 
     * @param type $parameters
     */
    public function __construct(
        Page $page,
        $parameters = array()
        )
    {
        $this->parameters = $parameters;
        $this->page = $page;
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CmsBundle\Entity\Page',
        ));
    }    

    /**
     * 
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title', 'text');
        $builder->add('url', 'text');
        $builder->add('content', 'ckeditor');
        $builder->add('icon', 'saman_icon', array(
            'required' => false,
        ));
        
        $builder->add('labels', 'entity', array(
            'required' => false,
            'attr' => array('class' => 'form-control form-control-select2'),
            'empty_value' => 'Choose a label',
            'multiple'  => true,
            'class' => 'LabelBundle:Label',
            'query_builder' => function(EntityRepository $er) {
                return $er->createQueryBuilder('l')
                    ->where('l.deleted = 0');
            }
        ));
    }

    /**
     * 
     * @return string
     */
    public function getName()
    {
        return 'saman_cms_page_form';
    }
}