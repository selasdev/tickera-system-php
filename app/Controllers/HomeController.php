<?php

namespace App\Controllers;

use App\Models\Event;
use App\Models\UserTicket;
use App\Models\Ticket;
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

    public function getAdminDashboard(){
        unset($_SESSION['ticketId']);
        $tickets = Ticket::all();
        $usersTickets = array();
        $user = User::where('id', $_SESSION['userId'])->first();

        foreach($tickets as $ticket){
            $userTicket = new UserTicket();
            $event = Event::where('id', $ticket->eventId)->first();
            $user = User::where('id', $ticket->userId)->first();

            $userTicket->event = $event;
            $userTicket->user = $user;
            $userTicket->ticket = $ticket;
            array_push($usersTickets, $userTicket);
        }
        
        return $this->renderHTML('homeAdmin.twig', [
            'ticketsInfo' => $usersTickets,
            'username' => $user->username
        ]);
    }

    public function handleButtonClick($request){
        $parsedData = $request->getParsedBody();
        $ticketIdDelete = $parsedData['ticketIdDelete'] ?? null;
        $ticketIdShow = $parsedData['ticketIdShow'] ?? null;
        if($ticketIdDelete){
            $ticket = Ticket::where('id', $parsedData['ticketIdDelete'])->first();
            $ticket->delete();
    
            return new RedirectResponse('homeAdmin');
        }
        else if($ticketIdShow){
            $_SESSION['ticketId'] = $ticketIdShow;
            return new RedirectResponse('entry/show');
        }
        else{
            return new RedirectResponse('homeAdmin');
        }
    }

}