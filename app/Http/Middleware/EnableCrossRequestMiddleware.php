<?php

namespace App\Http\Middleware;

use Closure;

class EnableCrossRequestMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        $origin   = $request->server('HTTP_ORIGIN') ? $request->server('HTTP_ORIGIN') : '*';
//        $response->header('Access-Control-Allow-Origin', $origin);
//        $response->header('Access-Control-Allow-Headers', 'Origin, Content-Type, Cookie, X-CSRF-TOKEN, Accept, Authorization, X-XSRF-TOKEN');
//        $response->header('Access-Control-Expose-Headers', 'Authorization, authenticated');
//        $response->header('Access-Control-Allow-Methods', 'GET, POST, PATCH, PUT, OPTIONS, DELETE');
//        $response->header('Access-Control-Allow-Credentials', 'true');
        $response->headers->add([
            'Access-Control-Allow-Origin'=> $origin,
            'Access-Control-Allow-Methods'=>'GET, POST, PATCH, PUT, OPTIONS, DELETE',
            'Access-Control-Allow-Headers'=>'Origin, Content-Type, Cookie, X-CSRF-TOKEN, Accept, Authorization, X-XSRF-TOKEN, X-PINGOTHER,X-Requested-With',
            'Access-Control-Expose-Headers' => 'Authorization, authenticated',
            'Access-Control-Allow-Credentials' => 'true'
        ]);

        return $response;
    }
}
