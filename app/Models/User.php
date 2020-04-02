<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model {

    protected $table = 'users';

    public function fullName(){
        return $this->firstName . ' ' . $this->lastName;
    }

    public function tickets(){
        return $this->belongsToMany('App\Models\Ticket', 'ticket', 'userId');
    }

}