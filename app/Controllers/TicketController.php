<?php

namespace App\Controllers;

use App\Models\Event;
use App\Models\Ticket;
use App\Models\User;
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

}