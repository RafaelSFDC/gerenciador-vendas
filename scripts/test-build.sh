#!/bin/bash

# Script para testar o build dos assets
echo "🔧 Testando build dos assets..."

# Limpar build anterior
echo "🧹 Limpando build anterior..."
rm -rf public/build

# Instalar dependências
echo "📦 Instalando dependências..."
npm install

# Executar build
echo "🏗️ Executando build..."
npm run build

# Verificar se o build foi bem-sucedido
echo "✅ Verificando build..."

if [ -d "public/build" ]; then
    echo "✅ Diretório build criado"
    
    if [ -f "public/build/manifest.json" ]; then
        echo "✅ Manifest criado"
        echo "📋 Conteúdo do manifest:"
        cat public/build/manifest.json | jq '.' 2>/dev/null || cat public/build/manifest.json
    else
        echo "❌ Manifest não encontrado"
    fi
    
    if [ -d "public/build/assets" ]; then
        echo "✅ Diretório assets criado"
        echo "📋 Assets encontrados:"
        ls -la public/build/assets/
    else
        echo "❌ Diretório assets não encontrado"
    fi
    
    # Verificar se CSS do Bootstrap está incluído
    echo "🔍 Verificando se Bootstrap está incluído..."
    if find public/build -name "*.css" -exec grep -l "bootstrap" {} \; | head -1; then
        echo "✅ Bootstrap CSS encontrado nos assets"
    else
        echo "❌ Bootstrap CSS não encontrado nos assets"
    fi
    
    # Verificar se JS do Bootstrap está incluído
    if find public/build -name "*.js" -exec grep -l "bootstrap" {} \; | head -1; then
        echo "✅ Bootstrap JS encontrado nos assets"
    else
        echo "❌ Bootstrap JS não encontrado nos assets"
    fi
    
else
    echo "❌ Diretório build não foi criado"
    exit 1
fi

echo "🎉 Teste de build concluído!"
