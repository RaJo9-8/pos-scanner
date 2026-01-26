<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserLevel
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$levels): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $userLevel = auth()->user()->level;

        if (!in_array($userLevel, $levels)) {
            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}
