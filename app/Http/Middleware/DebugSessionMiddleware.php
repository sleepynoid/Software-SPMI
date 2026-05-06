<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class DebugSessionMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        Log::info('--- Request Start ---');
        Log::info('URL: ' . $request->fullUrl());
        Log::info('Method: ' . $request->method());
        Log::info('Session ID: ' . $request->session()->getId());
        Log::info('User: ' . ($request->user() ? $request->user()->email : 'Guest'));
        
        $response = $next($request);
        
        Log::info('Response Status: ' . $response->getStatusCode());
        Log::info('Session ID After: ' . $request->session()->getId());
        Log::info('--- Request End ---');
        
        return $response;
    }
}
