<?php

namespace App\Controllers;

use App\Models\Event;
use App\Models\User;
use Laminas\Diactoros\Response\RedirectResponse;

class HomeController extends BaseController {

    protected $title = 'Home - Tickera.com';


    public function getUserDashboard(){
        $events = Event::all();
        $ad = $_SESSION['userId'];
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
        var_dump($parsedBody);
        $events = Event::all();
        $ad = $_SESSION['userId'];
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

}