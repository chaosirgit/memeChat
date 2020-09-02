<?php

namespace App\Http\Middleware;

use Closure;
use Laravel\Passport\Passport;

class UserPassport
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
        Passport::useTokenModel('Laravel\Passport\Token');
        //设置 ClientId
        Passport::personalAccessClientId(config('auth.clients.user'));
        return $next($request);
    }
}
