<?php

namespace Saman\ConfigBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\ORM\EntityRepository;

class ConfigForm extends AbstractType
{
    /**
     *
     * @var type 
     */
    protected $userConfig;
    
    /**
     *
     * @var type 
     */
    protected $defaultConfig;
    
    /**
     *
     * @var type 
     */
    protected $em;

    /**
     * 
     * @param type $userConfig
     */
    public function __construct($defaultConfig, $em, $userConfig = array())
    {
        $this->userConfig = $userConfig;
        $this->defaultConfig = $defaultConfig;
        $this->em = $em;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $configCategories = $this->defaultConfig['configs'];
        
        //$builder->add('save', 'submit', array('label' => 'action.save'));
        foreach ($configCategories as $configCategoryKey => $configOptions) {
            foreach ($configOptions['options'] as $configOptionKey => $option) {
                $fullConfigKey = $configCategoryKey . '_' . $configOptionKey;
                
                $type = $option['type'];
                $data = (array_key_exists('default', $option))? $option['default'] : null;
                if (array_key_exists($fullConfigKey, $this->userConfig)) {
                    $data = $this->userConfig[$fullConfigKey];
                }
                $required = (array_key_exists('required', $option))? $option['required'] : false;
                $description = (array_key_exists('description', $option))? $option['description'] : null;
                $grid = (array_key_exists('grid', $option))? $option['grid'] : null;
                
                $fieldOptions = array(
                    'mapped' => false,
                    'label' => $option['label'],
                    'data' => $data,
                    'required' => $required,
                    'attr' => array(
                        'description' => $description,
                        'grid' => $grid
                        )
                    );
                
                switch ($type) {
                    case 'choice':
                        $fieldOptions['choices'] = $option['choices'];
                        break;
                    case 'entity':
                        $entity = null;
                        if (intval($data)) {
                            $entity = $this->em
                                ->getReference($option['class'], intval($data));
                        }
                        $fieldOptions['data'] = $entity;
                        $fieldOptions['class'] = $option['class'];
                        $fieldOptions['query_builder'] = function(EntityRepository $er) {
                            return $er->createQueryBuilder('l')
                                ->where('l.deleted = 0');
                            };

                        break;
                }
                
                
                $builder->add($fullConfigKey, $type, $fieldOptions);
            }
        }
    }

    public function getName()
    {
        return 'saman_config_config_form';
    }
}