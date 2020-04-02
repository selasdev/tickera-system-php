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
}