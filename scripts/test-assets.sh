#!/bin/bash

# Script para testar se os assets estão sendo servidos corretamente

echo "🔍 Testando assets em produção..."

# Verificar se os assets existem localmente
if [ -d "public/build" ]; then
    echo "✅ Diretório public/build existe"
    echo "📁 Conteúdo do diretório build:"
    ls -la public/build/

    if [ -d "public/build/assets" ]; then
        echo "✅ Diretório public/build/assets existe"
        echo "📁 Conteúdo do diretório assets:"
        ls -la public/build/assets/
    else
        echo "❌ Diretório public/build/assets não existe"
    fi

    if [ -f "public/build/manifest.json" ]; then
        echo "✅ Arquivo manifest.json existe"
        echo "📄 Conteúdo do manifest.json:"
        cat public/build/manifest.json
    else
        echo "❌ Arquivo manifest.json não existe"
    fi
else
    echo "❌ Diretório public/build não existe"
    echo "🏗️ Executando build dos assets..."
    npm run build
fi

# Testar se o servidor local consegue servir os assets
echo ""
echo "🌐 Testando acesso aos assets via HTTP..."

# Iniciar servidor PHP temporário em background
php -S localhost:8001 -t public > /dev/null 2>&1 &
SERVER_PID=$!

# Aguardar servidor iniciar
sleep 2

# Testar acesso aos assets
echo "🔗 Testando CSS..."
if curl -s -o /dev/null -w "%{http_code}" http://localhost:8001/build/assets/app-*.css | grep -q "200"; then
    echo "✅ CSS acessível via HTTP"
else
    echo "❌ CSS não acessível via HTTP"
fi

echo "🔗 Testando JS..."
if curl -s -o /dev/null -w "%{http_code}" http://localhost:8001/build/assets/app-*.js | grep -q "200"; then
    echo "✅ JS acessível via HTTP"
else
    echo "❌ JS não acessível via HTTP"
fi

# Parar servidor temporário
kill $SERVER_PID 2>/dev/null

echo ""
echo "✅ Teste de assets concluído!"
