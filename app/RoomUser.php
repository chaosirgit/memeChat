<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RoomUser extends Model
{
    protected $table = 'room_user';

    public function room(){
        return $this->hasOne('App\Room','id','room_id');
    }

    public function user(){
        return $this->hasOne('App\User','id','user_id');
    }
}
