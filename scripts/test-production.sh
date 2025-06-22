#!/bin/bash

# Script para testar a aplicação em produção
echo "🚀 Testando aplicação em produção..."

# Verificar se Docker está rodando
if ! docker info > /dev/null 2>&1; then
    echo "❌ Docker não está rodando"
    exit 1
fi

# Parar containers existentes
echo "🛑 Parando containers existentes..."
docker-compose down

# Limpar build anterior
echo "🧹 Limpando build anterior..."
rm -rf public/build

# Fazer build dos assets
echo "🏗️ Fazendo build dos assets..."
npm run build

# Verificar se o build foi bem-sucedido
if [ ! -d "public/build" ]; then
    echo "❌ Build dos assets falhou"
    exit 1
fi

echo "✅ Build dos assets concluído"

# Construir e iniciar containers
echo "🐳 Construindo e iniciando containers..."
docker-compose up --build -d

# Aguardar containers iniciarem
echo "⏳ Aguardando containers iniciarem..."
sleep 30

# Verificar se a aplicação está respondendo
echo "🔍 Verificando se a aplicação está respondendo..."
for i in {1..10}; do
    if curl -f http://localhost:8080/health > /dev/null 2>&1; then
        echo "✅ Aplicação está respondendo"
        break
    fi
    echo "⏳ Tentativa $i/10 - aguardando..."
    sleep 10
done

# Verificar se os assets estão sendo servidos
echo "🔍 Verificando se os assets estão sendo servidos..."

# Verificar CSS
if curl -f http://localhost:8080/build/assets/app-*.css > /dev/null 2>&1; then
    echo "✅ CSS está sendo servido"
else
    echo "❌ CSS não está sendo servido"
fi

# Verificar JS
if curl -f http://localhost:8080/build/assets/app-*.js > /dev/null 2>&1; then
    echo "✅ JavaScript está sendo servido"
else
    echo "❌ JavaScript não está sendo servido"
fi

# Verificar manifest
if curl -f http://localhost:8080/build/manifest.json > /dev/null 2>&1; then
    echo "✅ Manifest está sendo servido"
else
    echo "❌ Manifest não está sendo servido"
fi

# Mostrar logs dos containers
echo "📋 Logs dos containers:"
docker-compose logs --tail=20

echo "🎉 Teste de produção concluído!"
echo "🌐 Acesse: http://localhost:8080"
