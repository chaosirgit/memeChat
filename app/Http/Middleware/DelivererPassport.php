<?php

namespace App\Http\Middleware;

use Closure;
use Laravel\Passport\Passport;

class DelivererPassport
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        Passport::useTokenModel('App\DelivererToken');
        //设置 ClientId
        Passport::personalAccessClientId(config('auth.clients.deliverer'));
        return $next($request);
    }
}
