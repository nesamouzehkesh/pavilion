<?php

namespace PlaygroundBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Library\Helpers\UserAgentDetector;

class IndexController extends Controller
{
    public function indexAction()
    {
        return $this->render('::Playground/Index/index.html.twig');
    }
    
    public function securityAction()
    {
        $testText = 'This is a <b>sample</b> text <script>bootbox.alert("This could be an XSS malicious code, you should protect your system form these attacks");</script>';
        
        $userAgentDetector = new UserAgentDetector;
        if ($userAgentDetector->isCrawler()) {
            throw $this->createAccessDeniedException('Crawlers and Robots cannot access to this page');
        }
        
        return $this->render(
            '::Playground/Index/security.html.twig', 
            array(
                'testText' => $testText,
                'userAgentDetector' => $userAgentDetector
                )
            );
    }
}