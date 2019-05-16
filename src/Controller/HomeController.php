<?php

namespace App\Controller;

use App\Service\DiscordGroom;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends BaseController
{
    /**
     *
     * @var DiscordGroom 
     */
    protected $groom;
    
    public function __construct(DiscordGroom $groom) {
        $this->groom = $groom;
    }
    
    /**
     * @Route("/", name="home")
     */
    public function index()
    {
        return $this->render('home/index.html.twig', []);
    }
    
    /**
     * @Route("/discord", name="discord")
     */
    public function discord() {
        $u = $this->getUser();
        $code = $this->groom->prepareLinkCode($u);
        
        $ups = [];
        for($i = 0; $i<50; $i++) {
            $ups[] = $this->groom->generatePseudoRandom();
        }
        
        return $this->render('home/discord.html.twig', [
            'errors' => [],
            'code' => $code,
            'ups' => $ups,
        ]);
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
