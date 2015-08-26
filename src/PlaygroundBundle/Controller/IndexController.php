<?php

namespace PlaygroundBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class IndexController extends Controller
{
    public function indexAction()
    {
        return $this->render('::Playground/Index/index.html.twig');
    }
    
    public function htmlPurifierAction()
    {
        $testText = 'This is a <b>sample</b> un purified text <script>bootbox.alert("This can be an XSS malicious code");</script>';
        
        return $this->render(
            '::Playground/Index/htmlPurifier.html.twig', 
            array('testText' => $testText)
            );
    }
}