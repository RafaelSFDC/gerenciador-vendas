#!/bin/bash

# Script para testar o build do Docker localmente
echo "🐳 Testando build do Docker localmente..."

# Verificar se Docker está rodando
if ! docker info > /dev/null 2>&1; then
    echo "❌ Docker não está rodando"
    exit 1
fi

# Limpar builds anteriores
echo "🧹 Limpando builds anteriores..."
rm -rf public/build
docker rmi dc-tecnologia-test 2>/dev/null || true

# Fazer build dos assets localmente primeiro
echo "🏗️ Fazendo build dos assets localmente..."
npm run build

# Verificar se o build foi bem-sucedido
if [ ! -d "public/build" ]; then
    echo "❌ Build dos assets falhou"
    exit 1
fi

echo "✅ Assets buildados com sucesso!"
echo "📋 Arquivos gerados:"
ls -la public/build/
ls -la public/build/assets/

# Verificar se Bootstrap está incluído
echo "🔍 Verificando se Bootstrap está incluído no CSS..."
if grep -q "Bootstrap" public/build/assets/*.css; then
    echo "✅ Bootstrap encontrado no CSS compilado!"
else
    echo "❌ Bootstrap não encontrado no CSS compilado"
    exit 1
fi

# Fazer build do Docker
echo "🐳 Fazendo build do Docker..."
docker build -t dc-tecnologia-test . --target production

if [ $? -eq 0 ]; then
    echo "✅ Build do Docker bem-sucedido!"
    
    # Testar se o container inicia corretamente
    echo "🚀 Testando se o container inicia..."
    docker run -d --name dc-test -p 8081:80 dc-tecnologia-test
    
    # Aguardar um pouco para o container inicializar
    sleep 10
    
    # Testar se a aplicação responde
    echo "🔍 Testando se a aplicação responde..."
    if curl -f http://localhost:8081/health > /dev/null 2>&1; then
        echo "✅ Aplicação respondendo corretamente!"
        echo "🌐 Acesse: http://localhost:8081"
        echo ""
        echo "Para parar o teste:"
        echo "docker stop dc-test && docker rm dc-test"
    else
        echo "❌ Aplicação não está respondendo"
        echo "📋 Logs do container:"
        docker logs dc-test
        docker stop dc-test && docker rm dc-test
        exit 1
    fi
else
    echo "❌ Build do Docker falhou"
    exit 1
fi

echo ""
echo "🎉 Teste concluído com sucesso!"
echo "📝 Próximos passos:"
echo "1. Se tudo estiver funcionando, faça o commit das mudanças"
echo "2. Faça push para o repositório"
echo "3. O Render fará o deploy automaticamente"
