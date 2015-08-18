<?php

namespace Saman\MediaBundle\Form\Type;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManager;

class CollectionType extends AbstractType
{
    /**
     *
     * @var type 
     */
    protected $em;
    
    /**
     *
     * @var type 
     */
    protected $fields;

    /**
     * 
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em, $fields = null)
    {
        $this->em = $em;
        $this->fields = $fields;
    }
    
    /**
     * 
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setOptional(array('fields'));
    }    

    /**
     * 
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $fields = isset($options['fields'])? $options['fields'] : $this->fields;
        
        foreach ($fields as $key => $field) {
            $type = $field['type'];
            $required = isset($field['required'])? $field['required'] : false;
            $description = isset($field['description'])? $field['description'] : null;
            $grid = isset($field['grid'])? $field['grid'] : null;
            $label = isset($field['label'])? $field['label'] : null;
            
            $attr = array(
                'description' => $description,
                'grid' => $grid
            );
            
            $fieldOptions = array(
                //'mapped' => false,
                'label' => $label,
                'required' => $required,
                'attr' => isset($field['attr'])? array_merge($attr, $field['attr']): $attr,
                );
            
            switch ($type) {
                case 'choice':
                    $fieldOptions['choices'] = $field['choices'];
                    break;
                case 'entity':
                    $fieldOptions['class'] = $field['class'];
                    $fieldOptions['query_builder'] = function(EntityRepository $er) {
                        return $er->createQueryBuilder('l')
                            ->where('l.deleted = 0');
                        };

                    break;
            }

            $builder->add($key, $type, $fieldOptions);
        }        

    }

    /**
     * 
     * @param FormView $view
     * @param FormInterface $form
     * @param array $options
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $fields = isset($options['fields'])? $options['fields'] : $this->fields;
        
        $view->vars['fields'] = $fields;
    } 
    
    /**
     * 
     * @return string
     */
    public function getName()
    {
        return 'saman_collection';
    }
}