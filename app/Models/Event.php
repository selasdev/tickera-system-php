<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model {

    protected $table = 'events';

    public function canBuy() {
        return $this->vipsAvailable > 0 ||
        $this->platinumsAvailable > 0 ||
        $this->highsAvailable > 0 ||
        $this->mediumAvailable > 0;
    }

    public function getAvailableStands($ticketLocation = null){
        $availableStands = array();
        if(!$ticketLocation){
            $ticketLocation = '';
        }

        if($this->platinumsAvailable > 0 || $ticketLocation == 'Platino') {
            array_push($availableStands, 'Platino');
        }

        if($this->vipsAvailable > 0 || $ticketLocation == 'VIP') {
            array_push($availableStands, 'VIP');
        }

        if($this->highsAvailable > 0 || $ticketLocation == 'Altos') {
            array_push($availableStands, 'Altos');
        }
        
        if($this->mediumAvailable > 0 || $ticketLocation == 'Medios') {
            array_push($availableStands, 'Medios');
        }

        return $availableStands;
    }

    public function updateAvailableStands($ticketLocation, $sumValue = 1){
        switch ($ticketLocation) {
            case 'Platino':
                $this->platinumsAvailable -= $sumValue;
                break;
            case 'VIP':
                $this->vipsAvailable -= $sumValue;
                break;
            case 'Medios':
                $this->mediumAvailable -= $sumValue;
                break;
            case 'Altos':
                $this->highsAvailable -= $sumValue;
                break;
            
            default:
                break;
        }
        $this->update();
    }
}