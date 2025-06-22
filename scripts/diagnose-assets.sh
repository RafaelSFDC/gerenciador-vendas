#!/bin/bash

# Script para diagnosticar problemas com assets em produção

echo "🔍 Diagnóstico de Assets - DC Tecnologia Vendas"
echo "=============================================="
echo ""

# Verificar se os assets existem localmente
check_local_assets() {
    echo "📁 Verificando assets locais..."

    if [ -d "public/build" ]; then
        echo "✅ Diretório public/build existe"

        if [ -d "public/build/assets" ]; then
            echo "✅ Diretório public/build/assets existe"
            echo "📄 Arquivos encontrados:"
            ls -la public/build/assets/ | grep -E '\.(css|js)$'
        else
            echo "❌ Diretório public/build/assets não existe"
        fi

        if [ -f "public/build/manifest.json" ]; then
            echo "✅ Arquivo manifest.json existe"
            echo "📄 Conteúdo do manifest:"
            cat public/build/manifest.json
        else
            echo "❌ Arquivo manifest.json não existe"
        fi
    else
        echo "❌ Diretório public/build não existe"
        echo "💡 Execute: npm run build"
    fi
    echo ""
}

# Verificar configurações do Vite
check_vite_config() {
    echo "⚙️ Verificando configuração do Vite..."

    if [ -f "vite.config.ts" ]; then
        echo "✅ vite.config.ts encontrado"
        echo "📋 Configuração atual:"
        echo "   - Input: resources/css/app.css, resources/js/app.js"
        echo "   - Output: public/build"
        echo "   - Manifest: habilitado"
    else
        echo "❌ vite.config.ts não encontrado"
    fi
    echo ""
}

# Verificar configurações do Laravel
check_laravel_config() {
    echo "🔧 Verificando configuração do Laravel..."

    echo "📋 Template Blade (app.blade.php):"
    if grep -q "@vite" resources/views/app.blade.php; then
        echo "✅ Diretiva @vite encontrada"
        grep "@vite" resources/views/app.blade.php
    else
        echo "❌ Diretiva @vite não encontrada"
    fi
    echo ""
}

# Verificar configurações do Docker
check_docker_config() {
    echo "🐳 Verificando configuração do Docker..."

    echo "📋 Dockerfile - Build dos assets:"
    if grep -q "npm run build" Dockerfile; then
        echo "✅ Build dos assets configurado no Dockerfile"
    else
        echo "❌ Build dos assets não encontrado no Dockerfile"
    fi

    echo "📋 Dockerfile - Cópia dos assets:"
    if grep -q "COPY.*build" Dockerfile; then
        echo "✅ Cópia dos assets configurada no Dockerfile"
    else
        echo "❌ Cópia dos assets não encontrada no Dockerfile"
    fi

    echo "📋 .dockerignore:"
    if grep -q "^/public/build" .dockerignore; then
        echo "❌ public/build está sendo excluído no .dockerignore"
    else
        echo "✅ public/build não está sendo excluído"
    fi
    echo ""
}

# Verificar configurações do Nginx
check_nginx_config() {
    echo "🌐 Verificando configuração do Nginx..."

    if [ -f "docker/default.conf" ]; then
        echo "✅ Configuração do Nginx encontrada"

        if grep -q "location /build/" docker/default.conf; then
            echo "✅ Configuração para /build/ encontrada"
        else
            echo "❌ Configuração para /build/ não encontrada"
        fi

        if grep -q "location.*\.(css|js)" docker/default.conf; then
            echo "✅ Configuração para arquivos estáticos encontrada"
        else
            echo "❌ Configuração para arquivos estáticos não encontrada"
        fi
    else
        echo "❌ Configuração do Nginx não encontrada"
    fi
    echo ""
}

# Verificar configurações do Render
check_render_config() {
    echo "☁️ Verificando configuração do Render..."

    if [ -f "render.yaml" ]; then
        echo "✅ render.yaml encontrado"

        if grep -q "ASSET_URL" render.yaml; then
            echo "✅ ASSET_URL configurado"
            grep -A 1 "ASSET_URL" render.yaml
        else
            echo "❌ ASSET_URL não configurado"
        fi
    else
        echo "❌ render.yaml não encontrado"
    fi
    echo ""
}

# Sugestões de correção
show_suggestions() {
    echo "💡 Sugestões para resolver problemas de CSS em produção:"
    echo ""
    echo "1. 🏗️ Garantir que o build está funcionando:"
    echo "   npm run build"
    echo ""
    echo "2. 🐳 Verificar se o Docker está copiando os assets:"
    echo "   - Verificar se /public/build não está no .dockerignore"
    echo "   - Verificar se o Dockerfile está copiando os assets buildados"
    echo ""
    echo "3. 🌐 Verificar configuração do Nginx:"
    echo "   - Adicionar location /build/ para servir assets"
    echo "   - Configurar cache para arquivos estáticos"
    echo ""
    echo "4. ⚙️ Verificar variáveis de ambiente:"
    echo "   - APP_ENV=production"
    echo "   - ASSET_URL=https://dc-tecnologia-vendas.onrender.com"
    echo ""
    echo "5. 🔄 Limpar cache do Laravel em produção:"
    echo "   php artisan config:clear"
    echo "   php artisan view:clear"
    echo ""
}

# Executar todos os checks
main() {
    check_local_assets
    check_vite_config
    check_laravel_config
    check_docker_config
    check_nginx_config
    check_render_config
    show_suggestions

    echo "✅ Diagnóstico concluído!"
    echo ""
    echo "📝 Próximos passos:"
    echo "1. Corrigir os problemas identificados acima"
    echo "2. Fazer commit das mudanças"
    echo "3. Fazer deploy no Render"
    echo "4. Verificar se o CSS está carregando em produção"
}

# Executar diagnóstico
main
