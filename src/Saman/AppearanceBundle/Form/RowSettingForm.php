<?php

namespace Saman\AppearanceBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Saman\AppearanceBundle\Entity\Theme;

class RowSettingForm extends AbstractType
{
    /**
     *
     * @var Widget $widget
     */
    protected $rowId;
    
    /**
     *
     * @var Theme $theme
     */
    protected $theme;
    
    /**
     * 
     * @param type $userConfig
     */
    public function __construct(Theme $theme, $rowId)
    {
        $this->theme = $theme;
        $this->rowId = $rowId;
    }
    
    /**
     * 
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        //$rowSettings = $this->theme->getRowSetting($this->rowId);
        $columns = $this->theme->getColumns($this->rowId);
        $row = $this->theme->getRow($this->rowId);
        $columnsDefultParameter = $this->theme->getDefultParameter('columns');
        $rowsDefultParameter = $this->theme->getDefultParameter('rows');

        $builder->add('rowSettings', 'saman_collection', array(
            'fields' => $rowsDefultParameter['settings'],
            'data' => $row['settings']
            ));
        
        foreach ($columns as $columnId => $column) {
            $builder->add($columnId, 'saman_collection', array(
                'fields' => $columnsDefultParameter['settings'],
                'data' => $column['settings']
                ));
        }
    }

    public function getName()
    {
        return 'saman_appearance_theme_row_setting_form';
    }
}