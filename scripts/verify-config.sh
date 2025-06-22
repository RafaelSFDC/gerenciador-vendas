#!/bin/bash

# Script de verificação de configurações para deploy
# Verifica se todas as configurações necessárias estão corretas

set -e

echo "🔍 Verificando configurações para deploy..."

# Cores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Função para imprimir status
print_status() {
    if [ "$2" = "OK" ]; then
        echo -e "${GREEN}✅ $1${NC}"
    elif [ "$2" = "WARNING" ]; then
        echo -e "${YELLOW}⚠️ $1${NC}"
    else
        echo -e "${RED}❌ $1${NC}"
    fi
}

# Verificar se estamos no diretório correto
if [ ! -f "composer.json" ]; then
    print_status "Execute este script na raiz do projeto Laravel" "ERROR"
    exit 1
fi

print_status "Diretório do projeto correto" "OK"

# Verificar arquivo render.yaml
echo ""
echo "📋 Verificando render.yaml..."

if [ ! -f "render.yaml" ]; then
    print_status "Arquivo render.yaml não encontrado" "ERROR"
    exit 1
fi

# Verificar configurações específicas no render.yaml
if grep -q "FORCE_HTTPS.*true" render.yaml; then
    print_status "FORCE_HTTPS=true configurado" "OK"
else
    print_status "FORCE_HTTPS não está configurado como true" "ERROR"
fi

if grep -q "APP_URL.*https://dc-tecnologia-vendas.onrender.com" render.yaml; then
    print_status "APP_URL configurado corretamente" "OK"
else
    print_status "APP_URL não está configurado corretamente" "ERROR"
fi

if grep -q "APP_ENV.*production" render.yaml; then
    print_status "APP_ENV=production configurado" "OK"
else
    print_status "APP_ENV não está configurado como production" "ERROR"
fi

# Verificar Dockerfile
echo ""
echo "🐳 Verificando Dockerfile..."

if [ ! -f "Dockerfile" ]; then
    print_status "Dockerfile não encontrado" "ERROR"
    exit 1
fi

if grep -q "FORCE_HTTPS=true" Dockerfile; then
    print_status "Variáveis de ambiente configuradas no Dockerfile" "OK"
else
    print_status "Variáveis de ambiente não configuradas no Dockerfile" "WARNING"
fi

if grep -q "default-https.conf" Dockerfile; then
    print_status "Configuração HTTPS do nginx configurada" "OK"
else
    print_status "Configuração HTTPS do nginx não encontrada" "ERROR"
fi

# Verificar arquivos de configuração nginx
echo ""
echo "🌐 Verificando configurações nginx..."

if [ -f "docker/default-https.conf" ]; then
    print_status "Arquivo default-https.conf existe" "OK"
    
    if grep -q "Strict-Transport-Security" docker/default-https.conf; then
        print_status "Headers HSTS configurados" "OK"
    else
        print_status "Headers HSTS não configurados" "WARNING"
    fi
    
    if grep -q "Content-Security-Policy" docker/default-https.conf; then
        print_status "Content Security Policy configurado" "OK"
    else
        print_status "Content Security Policy não configurado" "ERROR"
    fi
else
    print_status "Arquivo default-https.conf não encontrado" "ERROR"
fi

if [ -f "docker/nginx.conf" ]; then
    print_status "Arquivo nginx.conf existe" "OK"
else
    print_status "Arquivo nginx.conf não encontrado" "ERROR"
fi

# Verificar middleware CSP
echo ""
echo "🛡️ Verificando middleware CSP..."

if [ -f "app/Http/Middleware/ContentSecurityPolicy.php" ]; then
    print_status "Middleware CSP existe" "OK"
else
    print_status "Middleware CSP não encontrado" "WARNING"
fi

if grep -q "ContentSecurityPolicy" bootstrap/app.php; then
    print_status "Middleware CSP registrado" "OK"
else
    print_status "Middleware CSP não registrado" "WARNING"
fi

# Verificar template com nonces
echo ""
echo "📄 Verificando templates..."

if [ -f "resources/views/app.blade.php" ]; then
    print_status "Template app.blade.php existe" "OK"
    
    if grep -q "csp_nonce" resources/views/app.blade.php; then
        print_status "Nonces CSP configurados no template" "OK"
    else
        print_status "Nonces CSP não configurados no template" "WARNING"
    fi
else
    print_status "Template app.blade.php não encontrado" "ERROR"
fi

# Verificar assets build
echo ""
echo "📦 Verificando assets..."

if [ -d "public/build" ]; then
    print_status "Diretório public/build existe" "OK"
else
    print_status "Assets não foram buildados (execute npm run build)" "WARNING"
fi

# Verificar dependências
echo ""
echo "📚 Verificando dependências..."

if [ -d "vendor" ]; then
    print_status "Dependências PHP instaladas" "OK"
else
    print_status "Dependências PHP não instaladas (execute composer install)" "WARNING"
fi

if [ -d "node_modules" ]; then
    print_status "Dependências Node.js instaladas" "OK"
else
    print_status "Dependências Node.js não instaladas (execute npm install)" "WARNING"
fi

# Resumo final
echo ""
echo "📊 Resumo da verificação:"
echo "========================"

# Contar erros e warnings
errors=$(grep -c "❌" /tmp/verify_output 2>/dev/null || echo "0")
warnings=$(grep -c "⚠️" /tmp/verify_output 2>/dev/null || echo "0")

if [ "$errors" -eq 0 ]; then
    if [ "$warnings" -eq 0 ]; then
        print_status "Todas as configurações estão corretas! ✨" "OK"
        echo ""
        echo "🚀 Pronto para deploy! Execute: ./scripts/deploy-prod.sh"
    else
        print_status "Configurações básicas OK, mas há $warnings avisos" "WARNING"
        echo ""
        echo "⚠️ Você pode prosseguir com o deploy, mas considere corrigir os avisos"
    fi
else
    print_status "Encontrados $errors erros que devem ser corrigidos antes do deploy" "ERROR"
    echo ""
    echo "❌ Corrija os erros antes de fazer o deploy"
    exit 1
fi
