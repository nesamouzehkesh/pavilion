<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DashboardControllerController extends Controller
{
    public function displayDashboardAction()
    {
        return $this->render(
            'AppBundle:DashboardController:displayDashboard.html.twig', 
            array()
            );
    }
}