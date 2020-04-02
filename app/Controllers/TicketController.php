<?php

namespace App\Controllers;

use App\Models\Event;
use App\Models\Ticket;
use App\Models\User;
use App\Models\UserTicket;
use Laminas\Diactoros\Response\RedirectResponse;

class TicketController extends BaseController {

    protected $title = 'Compra - Tickera.com';

    public function getTicketForm(){
        $eventId = $_SESSION['eventId'] ?? null;
        if(!$eventId){
            return new RedirectResponse('home');
        }
        $event = Event::where('id', $eventId)->first();
        $ticketId = Ticket::orderBy('id', 'DESC')->get()->first()->id ?? 0;
        $availableStands = $event->getAvailableStands();

        return $this->renderHTML('buyTicketForm.twig', [
            'event' => $event,
            'stands' => $availableStands,
            'ticketId' => $ticketId+1
        ]);
    }

    public function postTicketForm($request){
        $eventId = $_SESSION['eventId'] ?? null;
        if(!$eventId){
            return new RedirectResponse('home');
        }

        $parsedData = $request->getParsedBody();

        $event = Event::where('id', $eventId)->first();
        $ticket = new Ticket();
        $ticket->eventId = $eventId;
        $ticket->userId = $_SESSION['userId'];
        
        $ticket->ticketLocation = $parsedData['ticketLocation'];
        $event->updateAvailableStands($parsedData['ticketLocation']);
        $ticket->save();

        $_SESSION['ticketId'] = $ticket->id;
        return new RedirectResponse('/buy-success');
    }

    public function getBuyTicketSuccess() {
        $this->title = 'Ver Ticket - Tickera.com';
        $eventId = $_SESSION['eventId'] ?? null;

        if(!$eventId){
            return new RedirectResponse('home');
        }
        unset($_SESSION['eventId']);
        $ticketId = $_SESSION['ticketId'];
        $ticket = Ticket::where('id', $ticketId)->first();
        $event = Event::where('id', $ticket->eventId)->first();
        unset($_SESSION['ticketId']);

        return $this->renderHTML('buyTicketSuccess.twig', [
            'ticket' => $ticket,
            'event' => $event
        ]);
    }

    public function getShowTicketEntry(){
        $ticketId = $_SESSION['ticketId'] ?? null;
        if(!$ticketId){
            return new RedirectResponse('homeAdmin');
        }
        $ticket = Ticket::where('id', $ticketId)->first();
        $event = Event::where('id', $ticket->eventId)->first();
        $user = User::where('id', $ticket->userId)->first();

        $userTicket = new UserTicket();

        $userTicket->event = $event;
        $userTicket->user = $user;
        $userTicket->ticket = $ticket;

        return $this->renderHTML('showTicketInfo.twig', [
            'userTicket' => $userTicket
        ]);
    }

}