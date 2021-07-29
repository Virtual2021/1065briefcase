<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  ...$guards
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $guard=null)
    {
        // $guards = empty($guards) ? [null] : $guards;

     if (!$request->session()->exists('data')) {
            // user value cannot be found in session
            return redirect('/login');
        }
        return $next($request);
    }
}
