<?php

namespace Library\Components;

use Symfony\Component\Form\FormInterface;

/**
 * A very Simple FormSerializer
 */
class SerializedForm
{
    /**
     * @var string
     */
    private $name;
    
    /**
     * @var string
     */
    private $template;
    
    /**
     *
     * @var type 
     */
    private $data;
    
    /**
     *
     * @var type 
     */
    private $error;
    
    /**
     * 
     * @param FormInterface $form
     * @param array $serializedContent
     * @param array $serializedError
     */
    public function __construct(FormInterface $form, array $serializedContent, array $serializedError = array())
    {
        $this->name = $form->getName();
        $this->template = $serializedContent['template'];
        $this->data = $serializedContent['data'];
        $this->error = $serializedError;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get template
     *
     * @return string 
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * Get data
     *
     * @return string 
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Get content
     *
     * @return array 
     */
    public function getContent()
    {
        $date = new \DateTime();
        $content = array(
            'name' => $this->name,
            'time' => $date->getTimestamp(),
            'template' => $this->template,
            'data' => $this->data
        );
        
        if (count($this->error) > 0) {
            $content['error'] = $this->error;
        }
        
        return $content;
    }
    
    /**
     * Get error
     *
     * @return array 
     */
    public function getError()
    {
        return $this->error;
    }
}