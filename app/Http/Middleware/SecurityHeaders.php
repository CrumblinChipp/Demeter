<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Adds security headers to every HTTP response.
 * Prevents clickjacking, XSS, MIME-sniffing, and enforces referrer policy.
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Prevent clickjacking — block this site from being embedded in iframes
        $response->headers->set('X-Frame-Options', 'DENY');

        // Prevent browsers from MIME-sniffing the content type
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Enable XSS filter in older browsers
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // Only send the origin as referrer, not the full URL
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Restrict what resources the browser is allowed to load
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');

        // Content Security Policy — allow scripts/styles from self and CDNs used by the app
        $response->headers->set('Content-Security-Policy',
            "default-src 'self'; " .
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.tailwindcss.com https://cdn.jsdelivr.net; " .
            "style-src 'self' 'unsafe-inline' https://cdn.tailwindcss.com https://fonts.googleapis.com; " .
            "font-src 'self' https://fonts.gstatic.com; " .
            "img-src 'self' data: blob:; " .
            "connect-src 'self'; " .
            "frame-ancestors 'none';"
        );

        return $response;
    }
}
