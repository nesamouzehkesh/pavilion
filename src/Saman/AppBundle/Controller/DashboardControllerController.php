<?php

namespace Saman\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DashboardControllerController extends Controller
{
    public function displayDashboardAction()
    {
        return $this->render(
            'SamanAppBundle:DashboardController:displayDashboard.html.twig', 
            array()
            );
    }
}