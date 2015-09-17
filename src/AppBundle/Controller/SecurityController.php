<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Library\Base\BaseController;

class SecurityController extends BaseController
{
    public function loginAction(Request $request, $isWeb)
    {
        $session = $request->getSession();

        // get the login error if there is one
        if ($request->attributes->has(SecurityContextInterface::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(
                SecurityContextInterface::AUTHENTICATION_ERROR
            );
        } elseif (null !== $session && $session->has(SecurityContextInterface::AUTHENTICATION_ERROR)) {
            $error = $session->get(SecurityContextInterface::AUTHENTICATION_ERROR);
            $session->remove(SecurityContextInterface::AUTHENTICATION_ERROR);
        } else {
            $error = '';
        }

        // last username entered by the user
        $lastUsername = (null === $session) ? '' : $session->get(SecurityContextInterface::LAST_USERNAME);
        
        if ($isWeb) {
            return $this->render(
                '::web/login.html.twig',
                array(
                    'title' => 'Login',
                    'last_username' => $lastUsername,
                    'action'        => 'saman_login_check_web',
                    'error'         => $error,
                    )
                );            
        } else {
            return $this->render(
                'AppBundle:Security:login.html.twig',
                array(
                    // last username entered by the user
                    'last_username' => $lastUsername,
                    'action'        => 'saman_login_check',
                    'error'         => $error,
                )
            );
        }
    }
}
