<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\Token;

class AdminToken extends Token
{
    protected $table = 'oauth_admin_tokens';

    public function user()
    {
        $provider = config('auth.guards.admin.provider');

        return $this->belongsTo(config('auth.providers.'.$provider.'.model'));
    }

}
