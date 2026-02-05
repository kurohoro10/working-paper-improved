<?php

namespace App\Http\Middleware;

use App\Models\WorkingPaper;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateWithTokenOrAuth
{
    /**
     * Allow access if user authenticated OR has a valid acces token
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Logged-in users pass immediately
        if (auth()->check()) {
            return $next($request);
        }

        // Token-based access
        $token = $request->header('X-Access-Token') ?? $request->query('token');

        if (!$token) {
            if (!$request->expectsJson()) {
                return redirect()->route('login');
            }

            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $workingPaper = WorkingPaper::where('access_token', $token)->first();

        if (!$workingPaper || !$workingPaper->isTokenValid()) {
            return response()->json([
                'message' => 'Invalid or expired access token.'
            ], 403);
        }

        // Optionally bind for later use
        $request->attributes->set('workingPaper', $workingPaper);

        return $next($request);
    }
}
