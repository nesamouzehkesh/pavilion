<?php

namespace Saman\PlayGroundBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Saman\PlayGroundBundle\Form\CityCountryForm;
use Saman\PlayGroundBundle\Form\SampleForm;

class DefaultController extends Controller
{
    public function indexAction(Request $request, $name)
    {

        $form = $this->createForm(new SampleForm());

        
        // Handling Form Submissions and validation
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $helper = $this->get('saman.helper');
                
                $multiple = $helper->fetchFormFieldValue($form, 'multiple');
                //var_dump($multiple);
            }
        }            
        
        
        return $this->render('SamanPlayGroundBundle:Default:index.html.twig', array(
            'name' => $name,
            'form' => $form->createView()
            ));
    }
    
    public function formEventsAction($name)
    {
        $form = $this->createForm(new CityCountryForm());        
        
        return $this->render('SamanPlayGroundBundle:Default:formEvents.html.twig', array(
            'name' => $name,
            'form' => $form->createView()
            ));
    }
}
