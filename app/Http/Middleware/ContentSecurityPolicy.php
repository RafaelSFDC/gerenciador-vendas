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
        // Gerar nonce único para esta requisição ANTES de processar a resposta
        $nonce = base64_encode(random_bytes(16));

        // Armazenar nonce na view para uso nos templates
        view()->share('csp_nonce', $nonce);

        // Processar a resposta
        $response = $next($request);

        // Definir política CSP baseada no ambiente
        if (app()->environment('production')) {
            $csp = $this->getProductionCSP($nonce);
        } else {
            $csp = $this->getDevelopmentCSP($nonce);
        }

        // Sempre definir o header CSP (sobrescrever qualquer configuração do Nginx)
        $response->headers->set('Content-Security-Policy', $csp);

        return $response;
    }

    /**
     * Política CSP para produção
     */
    private function getProductionCSP(string $nonce): string
    {
        return implode('; ', [
            "default-src 'self' https: data: blob:",
            "script-src 'self' 'nonce-{$nonce}' 'unsafe-inline' 'unsafe-eval' https://cdnjs.cloudflare.com https://cdn.jsdelivr.net https://unpkg.com",
            "style-src 'self' 'nonce-{$nonce}' 'unsafe-inline' https://cdnjs.cloudflare.com https://cdn.jsdelivr.net https://unpkg.com",
            "font-src 'self' https://cdnjs.cloudflare.com https://cdn.jsdelivr.net https://unpkg.com data:",
            "img-src 'self' data: https: blob:",
            "connect-src 'self' https: wss:",
            "media-src 'self' https: data:",
            "object-src 'none'",
            "base-uri 'self'",
            "form-action 'self'",
            "frame-ancestors 'self'"
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
            "img-src 'self' data: http: https: blob:",
            "connect-src 'self' http: https: ws: wss:",
            "media-src 'self' http: https: data:",
            "object-src 'none'",
            "base-uri 'self'",
            "form-action 'self'",
            "frame-ancestors 'self'"
        ]);
    }
}
