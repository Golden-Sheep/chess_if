<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Redirect;

class CheckBloqueado
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
        if(auth()->check()){

            if(auth()->user()->banido){
                auth()->logout();
                return Redirect::to('/')->withErrors(['login_message' => 'Sua conta foi bloqueada, entre em contato para mais informações.']);
            }
        }
        return $next($request);
    }
}
