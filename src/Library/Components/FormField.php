<?php

namespace Library\Components;

class FormField
{
    /**
     * 
     * @var string
     */
    private $key;
    
    /**
     *
     * @var type 
     */
    private $type;
    
    /**
     *
     * @var type 
     */
    private $choices = array();
    
    /**
     *
     * @var type 
     */
    private $label;
    
    /**
     *
     * @var type 
     */
    private $default = null;
    
    /**
     *
     * @var type 
     */
    private $required = false;
    
    /**
     *
     * @var type 
     */
    private $visible = true;
    
    /**
     * @param type $data
     */
    public function __construct($key = null, $data = null)
    {
        if (null !== $key) {
            $this->setKey($key);
        }
        
        if (null !== $data) {
            $this->loadData($data);
        }
    }
    
    /**
     * Set name
     *
     * @param string $key
     * @return FormField
     */
    public function setKey($key)
    {
        $this->key = $key;

        return $this;
    }

    /**
     * Get key
     *
     * @return string 
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return FormField
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set choices
     *
     * @param array $choices
     * @return FormField
     */
    public function setChoices($choices, $convert = false)
    {
        if ($convert) {
            $themp = '';
            foreach ($choices as $key => $choice) {
                $themp = $themp . ',' . $key . ':' . $choice;
            }
            $choices = ltrim ($themp, ',');
        }

        $this->choices = $choices;

        return $this;
    }

    /**
     * Get choices
     *
     * @return array 
     */
    public function getChoices($convert = false)
    {
        if ($convert) {
            $choices = array();
            $themps = explode(',', $this->choices);
            foreach ($themps as $themp) {
                $data = explode(':', $themp);
                $choices[$data[0]] = $data[1];
            }
            
            return $choices;
        }
        
        return $this->choices;
    }

    /**
     * Set label
     *
     * @param string $label
     * @return FormField
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Get label
     *
     * @return string 
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Set default
     *
     * @param string $default
     * @return FormField
     */
    public function setDefault($default)
    {
        $this->default = $default;

        return $this;
    }

    /**
     * Get default
     *
     * @return string 
     */
    public function getDefault()
    {
        return $this->default;
    }
    
    /**
     * Set required
     *
     * @param bool $required
     * @return FormField
     */
    public function setRequired($required)
    {
        $this->required = $required;

        return $this;
    }

    /**
     * Is required
     *
     * @return bool 
     */
    public function isRequired()
    {
        return $this->required;
    }
    
    /**
     * 
     * @param type $visible
     * @return \Library\Components\FormField
     */
    public function setVisible($visible)
    {
        $this->visible = $visible;
        
        return $this;
    }
    
    /**
     * 
     * @return type
     */
    public function getVisible()
    {
        return $this->visible;
    }
    
    /**
     * 
     * @return type
     */
    public function getData()
    {
        return array(
            'type' => $this->getType(),
            'visible' => $this->getVisible(),
            'choices' => $this->getChoices(true),
            'label' => $this->getLabel(),
            'default' => $this->getDefault(),
            'required' => $this->isRequired(),
        );
    }
    
    /**
     * 
     * @param type $data
     * @return \Library\Components\FormField
     */
    private function loadData($data)
    {
        if (array_key_exists('type', $data)) {
            $this->setType($data['type']);
        }
        if (array_key_exists('choices', $data)) {
            $this->setChoices($data['choices'], true);
        }
        if (array_key_exists('label', $data)) {
            $this->setLabel($data['label']);
        }
        if (array_key_exists('default', $data)) {
            $this->setDefault($data['default']);
        }
        if (array_key_exists('required', $data)) {
            $this->setRequired($data['required']);
        }
        if (array_key_exists('visible', $data)) {
            $this->setVisible($data['visible']);
        }
        
        return $this;
    }
}