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
        'password', 'remember_token','parent_id','session_key',
    ];


    public function getAvatarAttribute() {
        if (empty($this->attributes['avatar'])){
            if ($this->gender == 0){
                return url('/female.jpg');
            }else{
                return url('/male.jpg');
            }
        }
        return $this->attributes['avatar'];
    }



}
