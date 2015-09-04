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
            $config = $child->getConfig();
            $data = $this->serializeForm($child, $hasError);
            
            $template = array(
                'type' => $config->getType()->getName(),
                'model' => $child->getName(),
                'label' => $this->getFormLabel($child),
                'required' => $child->isRequired(),
            );
            
            if ($hasError) {
                $errors = array();
                foreach ($child->getErrors() as $error) {
                    $errors[] = $error->getMessage();
                }
                $template['error'] = $errors;
            }
            $result['template'][] = $template;
            $result['data'][$child->getName()] = $data;
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
    
    private function getFormLabel($child)
    {
        $config = $child->getConfig();
        $label = $config->getOption('label');
        
        if (null === $label) {
            $label = ucfirst(preg_replace('/(?<!^)([A-Z])/', '-\\1', $child->getName()));
        }
        
        return $label;
    }
    
}