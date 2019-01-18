<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;

class HomeController extends BaseController
{
    /**
     * @Route("/", name="home")
     */
    public function index()
    {
        return $this->render('home/index.html.twig', []);
    }
    
    /**
     * Allow us to use "redirect" path with no errors even if the redirect path is down
     * Will allow us to log such errors if we need so
     * Will be removed after all legacy code will be put down
     * @Route("/redirect", name="redirect")
     */
    public function redirectRoute($target) {
        if(in_array($target, ['forum',])) {
            $r = $this->redirect($this->getParameter('uri.forum'));
        } else {
            $r = $this->redirectToRoute('home');
        }
        return $r;
    }
}
