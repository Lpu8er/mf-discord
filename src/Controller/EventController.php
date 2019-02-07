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
        $month = $request->request->get('month', date('Y-m'));
        if(!preg_match('`^([0-9]{4})-([0-9]{2})$`', $month)) {
            $month = date('Y-m');
        }
        $x = array_map('intval', explode('-', $month));
    }
}
