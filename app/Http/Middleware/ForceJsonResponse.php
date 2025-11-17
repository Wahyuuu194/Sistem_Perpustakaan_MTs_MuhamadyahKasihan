<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ForceJsonResponse
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $request->headers->set('Accept', 'application/json');
        
        $response = $next($request);
        
        // If response is not JSON and is an error, convert to JSON
        if (!$response->headers->get('Content-Type') || 
            !str_contains($response->headers->get('Content-Type'), 'application/json')) {
            
            if ($response->getStatusCode() >= 400) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan pada server',
                    'error' => 'HTTP ' . $response->getStatusCode()
                ], $response->getStatusCode());
            }
        }
        
        return $response;
    }
}

