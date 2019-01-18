<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;

class StaticController extends BaseController
{
    /**
     * @Route("/decouvrez-minefield", name="static-discover")
     */
    public function discover() {
        return $this->render('static/discover.html.twig');
    }
    
    /**
     * wrapper
     * @TODO
     * @Route("/cgv", name="static-cgv")
     */
    public function cgv() {
        return $this->render('static/cgv.html.twig');
    }
}
