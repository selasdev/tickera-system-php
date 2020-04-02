<?php

namespace App\Controllers;

use App\Models\Event;
use Laminas\Diactoros\Response\RedirectResponse;

class DashboardController extends BaseController {

    protected $title = 'Dashboard - Tickera.com';


    public function getUserDashboard(){
        $events = Event::all();
        $user = User::where('id', $_SESSION['userId'])->first();
        $this->renderHTML('dashboard.twig', [
            'events' => $events,
            'username' => $user->username
        ]);
    }

}