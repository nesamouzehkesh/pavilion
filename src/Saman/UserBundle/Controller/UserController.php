<?php

namespace Saman\UserBundle\Controller;

use Saman\Library\Base\BaseController;
use Saman\Library\Map\ViewMap;

class UserController extends BaseController
{
    public function indexAction($name)
    {
        return $this->render('SamanUserBundle:User:index.html.twig', array('name' => $name));
    }
}
