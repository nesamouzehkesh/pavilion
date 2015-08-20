<?php

namespace MediaBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Doctrine\ORM\EntityManager;
use MediaBundle\Form\Type\CollectionType;

class MultipleType extends AbstractType
{
    const MULTIPLE_FIELD_NAME = 'saman_collection';
    
    /**
     *
     * @var EntityManager $em 
     */
    protected $em;

    /**
     * 
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }
    
    /**
     * 
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array('allow_add' => false));
        $resolver->setOptional(array('form_data'));
        $resolver->setRequired(array('fields'));
    }

    /**
     * 
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(self::MULTIPLE_FIELD_NAME, 'collection', array(
            'by_reference' => true,
            'type' => new CollectionType($this->em, $options['fields']),
            'data' => $this->getFormData($options),
            'allow_add' => true,
            'allow_delete' => true,            
            //'mapped' => false,
            //'options' => array(
            //    'required'  => false,
            //),            
        ));            
    }
    
//    /**
//     * 
//     * @param FormView $view
//     * @param FormInterface $form
//     * @param array $options
//     */
//    public function finishView(FormView $view, FormInterface $form, array $options)
//    {
//        $view->vars['fields'] = $options['fields'];
//    }       
    
    /**
     * 
     * @return string
     */
    public function getName()
    {
        return 'saman_multiple';
    }
    
    /**
     * 
     * @param type $options
     * @return type
     */
    private function getFormData($options)
    {
        $data = array();
        if (count($options['data']) > 0) {
            $data = $options['data'];
            foreach ($data as $dataKey => $dataParameters) {
                foreach ($dataParameters as $parameterKey => $dataParameter) {
                    $field = $options['fields'][$parameterKey];
                    $type = $field['type'];
                    switch ($type) {
                        case 'entity':
                            $entity = null;
                            if (intval($data)) {
                                $entity = $this->em
                                    ->getReference($field['class'], intval($data[$dataKey][$parameterKey]));
                            }
                            $data[$dataKey][$parameterKey] = $entity;
                            break;
                    }
                }
            }
        } else {
            $data = array();
            foreach ($options['fields'] as $key => $field) {
                $data[0][$key] = (array_key_exists('default', $field))? $field['default'] : null;
            }
        }
        
        return $data;
    }
}