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

    public function getAvailableStands(){
        $availableStands = array();

        if($this->platinumsAvailable > 0) {
            array_push($availableStands, 'Platino');
        }

        if($this->vipsAvailable > 0) {
            array_push($availableStands, 'VIP');
        }

        if($this->mediumAvailable > 0) {
            array_push($availableStands, 'Medios');
        }
        if($this->highsAvailable > 0) {
            array_push($availableStands, 'Altos');
        }

        return $availableStands;
    }

    public function updateAvailableStands($ticketLocation){
        switch ($ticketLocation) {
            case 'Platino':
                $this->platinumsAvailable -= 1;
                break;
            case 'VIP':
                $this->vipsAvailable -= 1;
                break;
            case 'Medios':
                $this->mediumAvailable -= 1;
                break;
            case 'Altos':
                $this->highsAvailable -= 1;
                break;
            
            default:
                break;
        }
        $this->update();
    }
}