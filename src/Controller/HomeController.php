<?php

namespace App\Controller;

use App\Entity\DiscordInvite;
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
        ;
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
        $rep = $this->getDoctrine()->getRepository(DiscordInvite::class);
        $di = $rep->generate($this->getUser());
        $link = null;
        if(!empty($di)) {
            $did = $di->getId();
            if(empty($did)) { // generate a single
                $inviteData = $this->groom->generateSingleInvite();
                if(!empty($inviteData)) {
                    $link = rtrim($this->getParameter('uri.discordinvite'),'/').'/'.$inviteData['code'];
                    $di->setId($inviteData['code']);
                    $di->setLnk($link);
                    $di->setUser($this->getUser());
                    $rep->persist($di);
                    $rep->flush();
                }
            } else {
                $link = $di->getLink();
            }
        }
        return $this->render('home/discord.html.twig', [
            'errors' => [],
            'link' => $link,
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
