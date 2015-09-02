<?php

namespace Library\Components;

use Symfony\Component\Form\FormInterface;

class FormSerializer
{
    public function serializeForm(FormInterface $form)
    {
        $isValid = true;
        $result = array();
        if ($form->isBound() && ! $form->isValid()) {
            $result['error'] = $this->serializeFormError($form);
            $isValid = false;
        }
        
        $result['name'] = $form->getName();
        $result['content'] = $this->serializeFormContent($form, $isValid);         
        
        return $result;
    }
    
    /**
     * 
     * @param FormInterface $form
     * @return type
     */
    private function serializeFormError(FormInterface $form)
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
     * @param type $isValid
     * @return type
     */
    private function serializeFormContent(FormInterface $form, $isValid = true)
    {
        if (!$form->all()) {
            return $form->getViewData();
        }
        
        $result = array();
        foreach ($form->all() as $child) {
            $config = $child->getConfig();
            $data = array(
                'type' => $config->getType()->getName(),
                'name' => $child->getName(),
                'label' => (string) $config->getOption('label'),
                'required' => $child->isRequired(),
                'value' => $this->serializeFormContent($child),
            );
            
            if (!$isValid) {
                $errors = array();
                foreach ($child->getErrors() as $error) {
                    $errors[] = $error->getMessage();
                }
                $data['error'] = $errors;
            }
            $result[] = $data;
        }

        return $result;
    }    
}