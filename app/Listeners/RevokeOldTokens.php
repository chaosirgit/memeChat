<?php

namespace App\Listeners;

use App\AdminToken;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Laravel\Passport\Token;

class RevokeOldTokens
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        //如需登录踢下线-请开启注释

//        if ($event->clientId == config('auth.clients.user')){
//            Token::where('id', '!=', $event->tokenId)
//                ->where('user_id', $event->userId)
//                ->where('client_id', $event->clientId)
////            ->where('expires_at', '<', now())
////            ->orWhere('revoked', true)
//                ->delete();
//        }
//        if ($event->clientId == config('auth.clients.admin')){
//            AdminToken::where('id', '!=', $event->tokenId)
//                ->where('user_id', $event->userId)
//                ->where('client_id', $event->clientId)
////            ->where('expires_at', '<', now())
////            ->orWhere('revoked', true)
//                ->delete();
//        }
    }
}
