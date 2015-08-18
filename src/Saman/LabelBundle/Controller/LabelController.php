<?php

namespace Saman\LabelBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class LabelController extends Controller
{
    public function indexAction()
    {
        return $this->render('SamanLabelBundle:Label:index.html.twig');
    }
}
