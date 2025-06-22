#!/bin/bash

# Script para diagnosticar problemas com Bootstrap
echo "🔍 Diagnosticando problemas com Bootstrap..."

# Verificar se o Bootstrap está instalado
echo "📦 Verificando instalação do Bootstrap..."
if [ -d "node_modules/bootstrap" ]; then
    echo "✅ Bootstrap está instalado"
    echo "📋 Versão: $(cat node_modules/bootstrap/package.json | grep '"version"' | cut -d'"' -f4)"
else
    echo "❌ Bootstrap não está instalado"
    echo "💡 Execute: npm install bootstrap"
    exit 1
fi

# Verificar se o build existe
echo "🏗️ Verificando build dos assets..."
if [ -d "public/build" ]; then
    echo "✅ Diretório build existe"
    
    # Verificar arquivos CSS
    css_files=$(find public/build -name "*.css" | wc -l)
    if [ $css_files -gt 0 ]; then
        echo "✅ Arquivos CSS encontrados: $css_files"
        
        # Verificar se Bootstrap está no CSS
        if find public/build -name "*.css" -exec grep -l "bootstrap" {} \; | head -1 > /dev/null; then
            echo "✅ Bootstrap encontrado no CSS"
        else
            echo "❌ Bootstrap não encontrado no CSS"
        fi
    else
        echo "❌ Nenhum arquivo CSS encontrado"
    fi
    
    # Verificar arquivos JS
    js_files=$(find public/build -name "*.js" | wc -l)
    if [ $js_files -gt 0 ]; then
        echo "✅ Arquivos JS encontrados: $js_files"
        
        # Verificar se Bootstrap está no JS
        if find public/build -name "*.js" -exec grep -l "bootstrap" {} \; | head -1 > /dev/null; then
            echo "✅ Bootstrap encontrado no JS"
        else
            echo "❌ Bootstrap não encontrado no JS"
        fi
    else
        echo "❌ Nenhum arquivo JS encontrado"
    fi
    
    # Verificar manifest
    if [ -f "public/build/manifest.json" ]; then
        echo "✅ Manifest existe"
        echo "📋 Conteúdo do manifest:"
        cat public/build/manifest.json | jq '.' 2>/dev/null || cat public/build/manifest.json
    else
        echo "❌ Manifest não existe"
    fi
else
    echo "❌ Diretório build não existe"
    echo "💡 Execute: npm run build"
fi

# Verificar configuração do Vite
echo "⚙️ Verificando configuração do Vite..."
if [ -f "vite.config.ts" ]; then
    echo "✅ vite.config.ts existe"
    
    # Verificar se Bootstrap está configurado
    if grep -q "bootstrap" vite.config.ts; then
        echo "✅ Bootstrap configurado no Vite"
    else
        echo "❌ Bootstrap não configurado no Vite"
    fi
else
    echo "❌ vite.config.ts não existe"
fi

# Verificar arquivos de entrada
echo "📄 Verificando arquivos de entrada..."

# Verificar app.css
if [ -f "resources/css/app.css" ]; then
    echo "✅ app.css existe"
    
    if grep -q "bootstrap" resources/css/app.css; then
        echo "✅ Bootstrap importado no CSS"
    else
        echo "❌ Bootstrap não importado no CSS"
    fi
else
    echo "❌ app.css não existe"
fi

# Verificar app.js
if [ -f "resources/js/app.js" ]; then
    echo "✅ app.js existe"
    
    if grep -q "bootstrap" resources/js/app.js; then
        echo "✅ Bootstrap importado no JS"
    else
        echo "❌ Bootstrap não importado no JS"
    fi
else
    echo "❌ app.js não existe"
fi

# Verificar layout principal
echo "🎨 Verificando layout principal..."
if [ -f "resources/views/app.blade.php" ]; then
    echo "✅ app.blade.php existe"
    
    if grep -q "@vite" resources/views/app.blade.php; then
        echo "✅ Diretiva @vite encontrada"
    else
        echo "❌ Diretiva @vite não encontrada"
    fi
    
    if grep -q "bootstrap" resources/views/app.blade.php; then
        echo "✅ Fallback Bootstrap CDN encontrado"
    else
        echo "❌ Fallback Bootstrap CDN não encontrado"
    fi
else
    echo "❌ app.blade.php não existe"
fi

# Verificar se está em produção
echo "🌍 Verificando ambiente..."
if [ "$APP_ENV" = "production" ]; then
    echo "✅ Ambiente de produção detectado"
    echo "💡 Fallback Bootstrap CDN será usado"
else
    echo "ℹ️ Ambiente de desenvolvimento"
fi

echo "🎉 Diagnóstico concluído!"
