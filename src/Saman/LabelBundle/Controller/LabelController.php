<?php

namespace Saman\LabelBundle\Controller;

use Saman\Library\Base\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Saman\LabelBundle\Entity\Label;

class LabelController extends BaseController
{
    /**
     * Display all labels in the label main page
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return type
     */
    public function displayLabelsAction()
    {
        // Get all labels
        $labels = $this->getLabels();
        
        // Render and return the view
        return $this->render(
            'SamanLabelBundle:Label:index.html.twig',
            array(
                'labels' => $labels
                )
            );
    }
    
    /**
     * Display and handel add edit label action
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return type
     */
    public function addEditLabelAction(Request $request, $labelId = null)
    {
        // Get label object
        $label = $this->getLabel($labelId);
        
        // Create a form for this $label
        $form = $this->createFormBuilder($label)
            ->add('title', 'text')
            ->add('description', 'textarea')
            ->add('save', 'submit', array('label' => 'Add Label'))
            ->getForm();
        
        $form->handleRequest($request);
        // If form is submited and it is valid then add or update this $label
        if ($form->isValid()) {
            // Get ObjectManager
            $em = $this->getDoctrine()->getManager();
            
            $em->persist($label);
            $em->flush();
            
            // Redirect to label main page. First we create the url to label main page
            // from its rout 'saman_label_home' then we use redirect function to
            // redirect to this $url
            $url = $this->generateUrl('saman_label_home');
            return $this->redirect($url, 301);            
        }
        
        return $this->render(
            'SamanLabelBundle:Label:addEditLabel.html.twig', 
            array(
                'form' => $form->createView(),
                )
            );
    }
    
    /**
     * Delete a label
     * 
     * @param type $labelId
     * @return type
     */
    public function deleteLabelAction($labelId)
    {
        // Get label
        $label = $this->getLabel($labelId);
        
        // Get ObjectManager
        $em = $this->getDoctrine()->getManager();
        // Remove label and flush ObjectManager. Note: if this $label is used
        // running the following code will throw an exception. Before delete this 
        // object we need to be sure that it is not in any other places.
        $em->remove($label);
        $em->flush();
        
        // Redirect to label main page. First we create the url to label main page
        // from its rout 'saman_label_home' then we use redirect function to
        // redirect to this $url
        $url = $this->generateUrl('saman_label_home');
        return $this->redirect($url, 301);
    }
    
    /**
     * Get a label based on $labelId or create a new one if $labelId is null
     * 
     * In a good practice development we should not put these kind of functions 
     * in controller, we can create a label service to performance these kind of 
     * actions
     * 
     * @param type $labelId
     * @return Label
     * @throws NotFoundHttpException
     */
    private function getLabel($labelId = null)
    {
        // If $labelId is null it means that we want to create a new label object.
        // otherwise we find a label in DB based on this $labelId
        if (null === $labelId) {
            $label = new Label();
        } else {
            // Get ObjectManager
            $em = $this->getDoctrine()->getManager();
            
            // Get Label repository
            $label = $em->getRepository('SamanLabelBundle:Label')->find($labelId);
            
            // Check if $label is found
            if (!$label) {
                throw $this->createNotFoundException('No label found for id ' . $labelId);
            }
        }
        
        // Return label object
        return $label;
    }
    
    /**
     * Get all labels
     * 
     * @return Label[]
     */
    private function getLabels()
    {
        // Get ObjectManager
        $em = $this->getDoctrine()->getManager();
        
        // Get all Labels
        $labels = $em->getRepository('SamanLabelBundle:Label')->findAll();
        
        // Return labels
        return $labels;
    }    
}