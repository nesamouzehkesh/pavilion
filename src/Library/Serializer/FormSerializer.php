<?php

namespace Library\Serializer;

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
        
        $formContent = $this->serializeFormContent($form, $isValid);
        $result['name'] = $form->getName();
        $result['template'] = $formContent['template'];
        $result['data'] = $formContent['data'];
        
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
            $data = $this->serializeFormContent($child);
            
            $template = array(
                'type' => $config->getType()->getName(),
                'model' => $child->getName(),
                'label' => (string) $config->getOption('label'),
                'required' => $child->isRequired(),
            );
            
            if (!$isValid) {
                $errors = array();
                foreach ($child->getErrors() as $error) {
                    $errors[] = $error->getMessage();
                }
                $template['error'] = $errors;
            }
            $result['template'][] = $template;
            $result['data'][$child->getName()] = $data;
        }

        return $result;
    }    
}