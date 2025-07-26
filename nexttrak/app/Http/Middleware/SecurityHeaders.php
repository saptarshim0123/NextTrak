<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Security Headers
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');

        // Content Security Policy (if enabled)
        if (config('security.csp.enabled', false)) {
            $cspDirectives = config('security.csp.directives', []);
            $cspString = $this->buildCspString($cspDirectives);
            $response->headers->set('Content-Security-Policy', $cspString);
        }

        return $response;
    }

    /**
     * Build CSP string from directives array.
     */
    private function buildCspString(array $directives): string
    {
        $cspParts = [];
        
        foreach ($directives as $directive => $sources) {
            if (is_array($sources)) {
                $cspParts[] = $directive . ' ' . implode(' ', $sources);
            } else {
                $cspParts[] = $directive . ' ' . $sources;
            }
        }
        
        return implode('; ', $cspParts);
    }
} 