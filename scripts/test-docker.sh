#!/bin/bash

# Script para testar o build Docker localmente antes do deploy

set -e

echo "🧪 Testando configuração Docker..."

# Verificar se o Docker está instalado
if ! command -v docker &> /dev/null; then
    echo "❌ Docker não encontrado. Instale o Docker primeiro."
    exit 1
fi

# Limpar builds anteriores
echo "🧹 Limpando builds anteriores..."
docker system prune -f

# Fazer build da imagem
echo "🏗️ Fazendo build da imagem Docker..."
docker build -t dc-tecnologia-test . --target production

# Testar se a imagem foi criada com sucesso
if [ $? -eq 0 ]; then
    echo "✅ Build bem-sucedido!"
    
    # Executar container de teste
    echo "🚀 Iniciando container de teste..."
    docker run -d --name dc-test -p 8080:80 \
        -e APP_ENV=production \
        -e APP_DEBUG=false \
        -e APP_URL=http://localhost:8080 \
        -e DB_CONNECTION=sqlite \
        -e DB_DATABASE=/var/www/html/database/database.sqlite \
        -e CACHE_STORE=file \
        -e SESSION_DRIVER=file \
        -e QUEUE_CONNECTION=database \
        -e FORCE_HTTPS=false \
        -e FORCE_SEED=true \
        dc-tecnologia-test
    
    # Aguardar inicialização
    echo "⏳ Aguardando inicialização (30 segundos)..."
    sleep 30
    
    # Verificar se o container está rodando
    if docker ps | grep -q dc-test; then
        echo "✅ Container está rodando!"
        
        # Testar health check
        echo "🔍 Testando health check..."
        if curl -f http://localhost:8080/health > /dev/null 2>&1; then
            echo "✅ Health check passou!"
        else
            echo "❌ Health check falhou!"
            echo "📋 Logs do container:"
            docker logs dc-test
        fi
        
        # Testar página principal
        echo "🔍 Testando página principal..."
        if curl -f http://localhost:8080 > /dev/null 2>&1; then
            echo "✅ Página principal acessível!"
        else
            echo "❌ Página principal não acessível!"
            echo "📋 Logs do container:"
            docker logs dc-test
        fi
        
        echo ""
        echo "🌐 Acesse http://localhost:8080 para testar manualmente"
        echo "🔐 Credenciais: vendedor@dctecnologia.com / 123456"
        echo ""
        echo "Para parar o teste, execute:"
        echo "docker stop dc-test && docker rm dc-test"
        
    else
        echo "❌ Container não está rodando!"
        echo "📋 Logs do container:"
        docker logs dc-test
        docker rm dc-test
        exit 1
    fi
    
else
    echo "❌ Build falhou!"
    exit 1
fi
