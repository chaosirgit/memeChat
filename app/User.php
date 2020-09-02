<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use Notifiable,HasApiTokens;

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $appends = ['total_ticket_count'];


    public function getTotalTicketCountAttribute(){
        return $this->userTickets()->sum('count');
    }

    public function userTickets(){
        return $this->hasMany('App\UserTicket','user_id','id');
    }



}
