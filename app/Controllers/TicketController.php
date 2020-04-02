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
            return new RedirectResponse('../home');
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
            return new RedirectResponse('../home');
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
        return new RedirectResponse('/buy/success');
    }

    public function getBuyTicketSuccess() {
        $eventId = $_SESSION['eventId'] ?? null;

        if(!$eventId){
            return new RedirectResponse('../home');
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
        $this->title = 'Ver Ticket - Tickera.com';
        $ticketId = $_SESSION['ticketId'] ?? null;
        if(!$ticketId){
            return new RedirectResponse('../home/admin');
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

    private function getEventAndStands($ticket){
        $myEvent = Event::where('id', $ticket->eventId)->first();
        $events = Event::all();
        $eventList = array();
        array_push($eventList, $myEvent);
        foreach ($events as $event) {
            if($event->id != $myEvent->id){
                array_push($eventList, $event);
            }
        }

        $stands = array();
        array_push($stands, 'Platino', 'VIP', 'Altos', 'Medios');
        return [
            'stands' => $stands,
            'events' => $eventList
        ];
    }

    public function getEditTicketEntry(){
        $this->title = 'Editar Ticket - Tickera.com';
        $ticketId = $_SESSION['ticketId'] ?? null;
        if(!$ticketId){
            return new RedirectResponse('../home/admin');
        }

        $ticket = Ticket::where('id', $ticketId)->first();
        $dict = $this->getEventAndStands($ticket);

        return $this->renderHTML('editTicketInfo.twig', [
            'ticket' => $ticket,
            'events' => $dict['events'],
            'stands' => $dict['stands']
        ]);
    }

    
    public function postEditTicketEntry($request){
        $parsedData = $request->getParsedBody();
        $responseMessage = null;
        $ticketId = (int)$_SESSION['ticketId'] ?? null;
        if(!$ticketId){
            return new RedirectResponse('../home/admin');
        }
        $eventId = (int)$parsedData['eventId'];
        $newLocation = $parsedData['ticketLocation'];

        $ticket = Ticket::where('id', $ticketId)->first();
        $event = Event::where('id', $eventId)->first();

        if($newLocation == $ticket->ticketLocation && $eventId == $ticket->eventId){
            $responseMessage = 'Se ha actualizado el registro correctamente.';
        }
        else {
            $stands = $event->getAvailableStands();
            if(in_array($newLocation, $stands)){
                $originalEvent = Event::where('id', $ticket->eventId)->first();
                $originalEvent->updateAvailableStands($ticket->ticketLocation, -1);
                $ticket->eventId = $eventId;
                $ticket->ticketLocation = $newLocation;
                $ticket->update();
                $event->updateAvailableStands($ticket->ticketLocation);
                $responseMessage = 'Se ha actualizado el registro correctamente.';
            }
            else {
                $responseMessage = "No hay ubicaciones disponibles para $newLocation en $event->eventName";
            }
        }

        $dict = $this->getEventAndStands($ticket);

        return $this->renderHTML('editTicketInfo.twig', [
            'ticket' => $ticket,
            'events' => $dict['events'],
            'stands' => $dict['stands'],
            'responseMessage' => $responseMessage
        ]);

    }

}