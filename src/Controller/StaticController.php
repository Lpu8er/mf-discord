<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;

class StaticController extends BaseController
{
    /**
     * @Route("/decouvrez-minefield", name="static-discover")
     */
    public function index() {
        return $this->render('static/discover.html.twig');
    }
}
