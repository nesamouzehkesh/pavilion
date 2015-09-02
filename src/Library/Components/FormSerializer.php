<?php

namespace Library\Components;

use Symfony\Component\Form\FormInterface;

class FormSerializer
{
    public function serializeForm(FormInterface $form)
    {
        if ($form->isBound() && ! $form->isValid()) {
            $data = $this->serializeFormError($form);
        } else {
            $data = $this->serializeForm($form);
        }        

        return $data;
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
            $result['error'][] = $error->getMessage();
        }
        
        foreach ($form->all() as $child) {
            $errors = $this->serializeFormError($child);
            if ($errors) {
                $result['children'][$child->getName()] = $errors;
            }
        }
        
        return $result;
    }
    
    /**
     * 
     * @param FormInterface $form
     * @return type
     */
    private function serializeFormContent(FormInterface $form)
    {
        if (!$form->all()) {
            return $form->getViewData();
        }

        $data = array();
        foreach ($form->all() as $child) {
            $config = $child->getConfig();
            
            $data[] = array(
                "type" => $config->getType()->getName(),
                "name" => $child->getName(),
                "label" => (string) $config->getOption("label"),
                "required" => $child->isRequired(),
                "value" => $this->serializeFormContent($child)
            );
        }

        return $data;
    }    
}