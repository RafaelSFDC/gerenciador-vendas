<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ContentSecurityPolicy
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Gerar nonce único para esta requisição
        $nonce = base64_encode(random_bytes(16));
        
        // Armazenar nonce na view para uso nos templates
        view()->share('csp_nonce', $nonce);

        // Definir política CSP baseada no ambiente
        if (app()->environment('production')) {
            $csp = $this->getProductionCSP($nonce);
        } else {
            $csp = $this->getDevelopmentCSP($nonce);
        }

        // Adicionar header CSP apenas se não estiver sendo definido pelo Nginx
        if (!$response->headers->has('Content-Security-Policy')) {
            $response->headers->set('Content-Security-Policy', $csp);
        }

        return $response;
    }

    /**
     * Política CSP para produção
     */
    private function getProductionCSP(string $nonce): string
    {
        return implode('; ', [
            "default-src 'self' https:",
            "script-src 'self' 'nonce-{$nonce}' 'unsafe-inline' 'unsafe-eval' https://cdnjs.cloudflare.com https://cdn.jsdelivr.net",
            "style-src 'self' 'nonce-{$nonce}' 'unsafe-inline' https://cdnjs.cloudflare.com https://cdn.jsdelivr.net",
            "font-src 'self' https://cdnjs.cloudflare.com https://cdn.jsdelivr.net",
            "img-src 'self' data: https:",
            "connect-src 'self' https:",
            "media-src 'self' https:",
            "object-src 'none'",
            "base-uri 'self'",
            "form-action 'self'"
        ]);
    }

    /**
     * Política CSP para desenvolvimento (mais permissiva)
     */
    private function getDevelopmentCSP(string $nonce): string
    {
        return implode('; ', [
            "default-src 'self' http: https: data: blob:",
            "script-src 'self' 'nonce-{$nonce}' 'unsafe-inline' 'unsafe-eval' http: https:",
            "style-src 'self' 'nonce-{$nonce}' 'unsafe-inline' http: https:",
            "font-src 'self' http: https: data:",
            "img-src 'self' data: http: https:",
            "connect-src 'self' http: https: ws: wss:",
            "media-src 'self' http: https:",
            "object-src 'none'",
            "base-uri 'self'",
            "form-action 'self'"
        ]);
    }
}
