<?php

namespace App\Controllers;

use App\Models\Event;
use App\Models\User;
use Laminas\Diactoros\Response\RedirectResponse;

class HomeController extends BaseController {

    protected $title = 'Home - Tickera.com';

    private function changeSession($eventId, $buyTicketUri){
        if(!$eventId){
            unset($_SESSION['eventId']);
            unset($_SESSION['buyTicketUri']);
            return;
        }
        $_SESSION['eventId'] = $eventId;
        $_SESSION['buyTicketUri'] = "buy-ticket-$nameFixed";
    }


    public function getUserDashboard(){
        $this->changeSession(null, null);
        $events = Event::all();
        $user = User::where('id', $_SESSION['userId'])->first();
        $finalEvents = array();
        foreach ($events as $event) {
            if($event->canBuy()){
                array_push($finalEvents, $event);
            }
        }
        return $this->renderHTML('dashboard.twig', [
            'events' => $finalEvents,
            'username' => $user->username
        ]);
    }

    public function postUserDashboard($request){
        $parsedBody = $request->getParsedBody();
        $eventId = (int)$parsedBody['event'];
        $event = Event::where('id', $eventId)->first();
        $this->changeSession($eventId, null);
        return new RedirectResponse("buy-ticket");
    }

}