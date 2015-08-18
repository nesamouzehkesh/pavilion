<?php

namespace Saman\MediaBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Doctrine\ORM\EntityManager;
use Saman\MediaBundle\Service\Select2Service;
use Saman\MediaBundle\Repository\Select2Repository;

class Select2Type extends AbstractType
{
    /**
     *
     * @var EntityManager $em 
     */
    protected $em;
    
    /**
     *
     * @var type 
     */
    protected $parameters;
    
    /**
     *
     * @var type 
     */
    protected $router;

    /**
     * 
     * @param \Doctrine\ORM\EntityManager $em
     * @param \Saman\MediaBundle\Form\Type\Router $router
     * @param type $parameters
     */
    public function __construct(EntityManager $em, Router $router, $parameters)
    {
        $this->em = $em;
        $this->router = $router;
        $this->parameters = $parameters;
    }
    
    /**
     * 
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_url' => null,
            'data_lookup_url' => null,
            'data' => array(),
            'multiple' => true,
            'minimumInputLength' => 0,
            'allowClear' => true,
            'class' => null,
            'choices' => null,
            'loading' => false,
            'placeholder' => 'word.selectAnItem'
            ));
    }
   
    public function getParent()
    {
        return 'text';
    }    
    
    /**
     * 
     * @param FormView $view
     * @param FormInterface $form
     * @param array $options
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $type = 'simple';
        if (isset($options['data_url'])) {
            $cssClass = 'saman-select2';
            $view->vars['data_url'] = $options['data_url'];
            
            if (isset($options['data_lookup_url'])) {
                $view->vars['data_lookup_url'] = $options['data_lookup_url'];
            }
        } elseif (isset($options['class'])) {
            $cssClass = 'saman-select-select2';
            if (!isset($options['class'])) {
                throw new \Exception('You should provide either data_url or class');
            }
            
            if (!$options['loading']) {
                $select2Repository = new Select2Repository(
                    $this->em,
                    $options['class']
                    );
                        
                $view->vars['options'] = $select2Repository->getSelect2Entities();
            }
            
            
            $class = Select2Service::encodeClass($options['class']);
            $view->vars['data_url'] = $this->router->generate(
                'saman_selec2_data_url', 
                array('class' => $class)
                );

            $view->vars['data_lookup_url'] = $this->router->generate(
                'saman_selec2_data_lookup_url', 
                array('class' => $class)
                );
        } else {
            $cssClass = 'saman-simple-select2';
            if (!isset($options['choices'])) {
                throw new \Exception('You should provide either data_url or class or choices');
            }
            
            $view->vars['options'] = $options['choices'];
        }
        
        if (isset($options['attr']['class'])) {
            $cssClass = $options['attr']['class'] . ' ' . $cssClass;
        }
        
        $options['attr']['class'] = $cssClass;
        $view->vars['type'] = $type;
        $view->vars['attr'] = $options['attr'];
        $view->vars['loading'] = $options['loading'];
        $view->vars['data'] = is_array($options['data'])? implode(',', $options['data']) : array();
        $view->vars['multiple'] = $options['multiple']? 'true' : 'false';
        $view->vars['allowClear'] = $options['allowClear']? 'true' : 'false';
        $view->vars['minimumInputLength'] = $options['minimumInputLength'];
        $view->vars['placeholder'] = $options['placeholder'];
    }       
    
    /**
     * 
     * @return string
     */
    public function getName()
    {
        return 'saman_select2';
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