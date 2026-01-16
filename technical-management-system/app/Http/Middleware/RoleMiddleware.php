<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $role  The role slug required to access the route
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = $request->user();
        
        // Check if user is authenticated
        if (!$user) {
            return redirect()->route('login');
        }
        
        // Check if user has a role
        if (!$user->role) {
            abort(403, 'No role assigned to user.');
        }
        
        // Check if user's role matches the required role
        if ($user->role->slug !== $role) {
            // Redirect to their appropriate dashboard
            return redirect()->route('dashboard');
        }
        
        return $next($request);
    }
}

