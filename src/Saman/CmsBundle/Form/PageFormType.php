<?php

namespace Saman\CmsBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Saman\Library\Service\Helper;
use Saman\CmsBundle\Entity\Page;

/**
 * 
 */
class PageFormType extends AbstractType
{
    /**
     *
     * @var Helper $helper
     */
    private $helper;
    
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
        Helper $helper, 
        Page $page,
        $parameters = array())
    {
        $this->helper = $helper;
        $this->parameters = $parameters;
        $this->page = $page;
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Saman\CmsBundle\Entity\Page',
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
        $builder->add('content', 'ckeditor');//ckeditor
        $builder->add('icon', 'saman_icon', array(
            'required' => false,
        ));
        
        $builder->add('theme', 'entity', array(
            'required' => false,
            'attr' => array('class' => 'form-control form-control-select2'),
            'empty_value' => 'Choose a theme',
            'class' => 'SamanAppearanceBundle:Theme',
            'query_builder' => function(EntityRepository $er) {
                return $er->createQueryBuilder('t')
                    ->where('t.deleted = 0');
            }
        ));
        
        $builder->add('labels', 'entity', array(
            'required' => false,
            'attr' => array('class' => 'form-control form-control-select2'),
            'empty_value' => 'Choose a label',
            'multiple'  => true,
            'class' => 'SamanLabelBundle:Label',
            'query_builder' => function(EntityRepository $er) {
                return $er->createQueryBuilder('l')
                    ->where('l.deleted = 0');
            }
        ));
    
        $builder->add('pages', 'saman_select2', array(
            'required' => false,
            'mapped' => false,
            'class' => 'SamanCmsBundle:Page',
            'data' => array(1, 2),
            'minimumInputLength' => 0,
            'multiple' => false,
            'placeholder' => 'ss',
            'allowClear' => true,
            ));
        
        $builder->add('settings', 'saman_collection', array(
            'fields' => $this->parameters['settings'],
            'data' => $this->page->getSettings()
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