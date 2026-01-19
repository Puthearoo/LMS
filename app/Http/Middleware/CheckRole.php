<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Check if user is authenticated
        if (!auth()->check()) {
            return redirect('/login');
        }

        // Check if user has one of the required roles
        $userRole = auth()->user()->role;

        // If no specific roles provided, allow access
        if (empty($roles)) {
            return $next($request);
        }

        // Check if user has required role
        if (in_array($userRole, $roles)) {
            return $next($request);
        }

        // Redirect based on user's actual role
        switch ($userRole) {
            case 'admin':
                return redirect('/admin/dashboard');
            case 'librarian':
                return redirect('/librarian/dashboard');
            case 'student':
                return redirect('/student/dashboard');
            default:
                return redirect('/home');
        }

    }
}
