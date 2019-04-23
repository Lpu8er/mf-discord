<?php
namespace App\Controller;

use App\Entity\Event;
use App\Repository\EventRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Description of EventController
 *
 * @author lpu8er
 * @Route("/events")
 */
class EventController extends BaseController {
    /**
     * @Route("/", name="events", methods="GET")
     */
    public function index(Request $request, EventRepository $eventRepository): Response {
        $rs = [];
        
        $month = $request->request->get('month', date('Y-m'));
        if(!preg_match('`^([0-9]{4})-([0-9]{2})$`', $month)) {
            $month = date('Y-m');
        }
        $x = array_map('intval', explode('-', $month));
        $rs['year'] = $x[0];
        $rs['month'] = $x[1];
        
        $rs['events'] = $eventRepository->retrieveForMonth($rs['year'], $rs['month']);
        $rs['splitted'] = $eventRepository->splitEvents($rs['year'], $rs['month'], $rs['events']);

        $colorPalette = [];
        $findColor = function($palette){
            $mi = 10; $ai = 0;
            do {
                $c = dechex(mt_rand(100, 250)).dechex(mt_rand(100, 250)).dechex(mt_rand(100, 250));
                $mi++;
            } while(in_array($c, $palette) && ($ai < $mi));
            return $c;
        };
        foreach($rs['events'] as $ek => $ev) {
            $c = $findColor($colorPalette);
            $colorPalette[] = $c;
            $rs['events'][$ek]->setColor($c);
        }
        
        return $this->render('event/events.html.twig', $rs);
    }
}
