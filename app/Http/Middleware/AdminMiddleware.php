<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
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
        //Если пользователь авторизован и админ
        // то разрешить идти ему дальше (в админку)
        // иначе ошибка 404
        if (Auth::check() && Auth::user()->is_admin) {
            // Auth::user() - получаем текущего пользователя
            return $next($request);
        }
        abort(404);
    }
}
