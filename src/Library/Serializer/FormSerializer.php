<?php

namespace Library\Serializer;

use Symfony\Component\Form\FormInterface;
use Library\Components\SerializedForm;

/**
 * A very Simple FormSerializer
 */
class FormSerializer
{
    /**
     * 
     * @param FormInterface $form
     * @return SerializedForm
     */
    public function serialize(FormInterface $form)
    {
        $serializedError = array();
        $hasError = false;
        // Get form serialized errors
        if ($form->isBound() && ! $form->isValid()) {
            $serializedError = $this->serializeError($form);
            $hasError = true;
        }
        
        // Get form serialized content, including its tamplate (structure) and data
        $serializedContent = $this->serializeForm($form, $hasError);
        
        return new SerializedForm($form, $serializedContent, $serializedError);
    }
    
    /**
     * 
     * @param FormInterface $form
     * @return type
     */
    private function serializeError(FormInterface $form)
    {
        $result = array();
        foreach ($form->getErrors() as $error) {
            $result[] = $error->getMessage();
        }
        
        return $result;
    }
    
    /**
     * 
     * @param FormInterface $form
     * @param type $hasError
     * @return type
     */
    private function serializeForm(FormInterface $form, $hasError = false)
    {
        if (!$form->all()) {
            return $form->getViewData();
        }
        
        $result = array();
        foreach ($form->all() as $child) {
            $data = $this->serializeForm($child, $hasError);
            $result['template'][] = $this->getFieldTemplate($child, $hasError);
            $result['data'][$child->getName()] = $this->filterFieldData($child, $data);
        }

        $result['template'][] = array(
            'type' => 'submit',
            'label' => 'submit',
        );
        /*
        $result['template'][] = array(
            'type' => 'reset',
            'label' => 'reset',
        );
        */

        return $result;
    }
    
    /**
     * 
     * @param FormInterface $child
     * @return type
     */
    private function getFormLabel(FormInterface $child)
    {
        $config = $child->getConfig();
        $label = $config->getOption('label');
        
        if (null === $label) {
            $label = ucfirst(preg_replace('/(?<!^)([A-Z])/', '-\\1', $child->getName()));
        }
        
        return $label;
    }
    
    /**
     * 
     * @param FormInterface $child
     * @param type $hasError
     * @return type
     */
    private function getFieldTemplate(FormInterface $child, $hasError = false)
    {
        $config = $child->getConfig();
        $options = $config->getOptions();
        $type = $config->getType()->getName();
        
        $template = array(
            'type' => $type,
            'model' => $child->getName(),
            'label' => $this->getFormLabel($child),
            'required' => $child->isRequired(),
        );
        
        if ('choice' === $type) {
            $choices = array();
            foreach ($options['choices'] as $key => $option) {
                $choices[$key] = array('label' => $option);
            }
            $template['options'] = $choices;
        }
        if ($hasError) {
            $errors = array();
            foreach ($child->getErrors() as $error) {
                $errors[] = $error->getMessage();
            }

            if (count($errors) > 0) {
                $template['error'] = implode('. ', $errors);
            }
        }
        
        return $template;
    }
    
    /**
     * 
     * @param FormInterface $child
     * @param type $data
     * @return type
     */
    private function filterFieldData(FormInterface $child, $data)
    {
        $config = $child->getConfig();
        $type = $config->getType()->getName();
        switch ($type) {
            case 'number': 
                $data = floatval($data);
                break;
        }

        return $data;
    }    
}