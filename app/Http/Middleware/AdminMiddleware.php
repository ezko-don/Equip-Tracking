<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // Debug logging
        \Log::info('AdminMiddleware Check', [
            'user_id' => Auth::id(),
            'is_logged_in' => Auth::check(),
            'is_admin' => Auth::user()?->isAdmin(),
            'url' => $request->url()
        ]);

        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'Please login first.');
        }

        if (!Auth::user()->isAdmin()) {
            return redirect()->route('dashboard')
                ->with('error', 'You do not have admin access.');
        }

        return $next($request);
    }
} 