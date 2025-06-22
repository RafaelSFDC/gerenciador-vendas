#!/bin/bash

# Script para desenvolvimento local com Docker

set -e

echo "🚀 Iniciando ambiente de desenvolvimento..."

# Verificar se o Docker está instalado
if ! command -v docker &> /dev/null; then
    echo "❌ Docker não encontrado. Instale o Docker primeiro."
    exit 1
fi

if ! command -v docker-compose &> /dev/null; then
    echo "❌ Docker Compose não encontrado. Instale o Docker Compose primeiro."
    exit 1
fi

# Parar containers existentes
echo "🛑 Parando containers existentes..."
docker-compose down 2>/dev/null || true

# Construir e iniciar containers
echo "🏗️ Construindo e iniciando containers..."
docker-compose up --build -d

# Aguardar containers iniciarem
echo "⏳ Aguardando containers iniciarem..."
sleep 10

# Verificar status dos containers
echo "📊 Status dos containers:"
docker-compose ps

# Verificar health check
echo "🔍 Verificando health check..."
for i in {1..30}; do
    if curl -f http://localhost:8080/health &>/dev/null; then
        echo "✅ Aplicação está rodando!"
        break
    fi
    echo "⏳ Aguardando aplicação inicializar... ($i/30)"
    sleep 2
done

echo ""
echo "🎉 Ambiente de desenvolvimento iniciado com sucesso!"
echo ""
echo "📊 Endpoints disponíveis:"
echo "- Aplicação: http://localhost:8080"
echo "- Health check: http://localhost:8080/health"
echo ""
echo "🔐 Credenciais de teste:"
echo "- Email: vendedor@dctecnologia.com"
echo "- Senha: 123456"
echo ""
echo "🔧 Comandos úteis:"
echo "- Ver logs: docker-compose logs -f"
echo "- Parar: docker-compose down"
echo "- Rebuild: docker-compose up --build"
echo "- Executar comando: docker-compose exec app php artisan [comando]"
