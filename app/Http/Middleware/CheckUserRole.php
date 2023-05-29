<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next,$roles): Response
    {
        $role = auth()->user()->role->role;
        //return ok($role);
        if($role == $roles) {
            return $next($request);
        }
        else {
           return error('Access denied!!!',[],'unauthenticated');
        }
    }
}
