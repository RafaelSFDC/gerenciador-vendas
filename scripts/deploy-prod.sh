#!/bin/bash

# Script de deploy completo com verificações para produção

set -e

echo "🚀 Iniciando deploy para produção no Render.com..."

# Executar verificação de configurações
if [ -f "scripts/verify-config.sh" ]; then
    echo "🔍 Executando verificação de configurações..."
    chmod +x scripts/verify-config.sh
    ./scripts/verify-config.sh

    if [ $? -ne 0 ]; then
        echo "❌ Verificação de configurações falhou. Corrija os erros antes de continuar."
        exit 1
    fi
    echo "✅ Verificação de configurações passou!"
    echo ""
else
    echo "⚠️ Script de verificação não encontrado, continuando..."
fi

# Verificar se estamos em um repositório git
if [ ! -d ".git" ]; then
    echo "❌ Este não é um repositório git. Inicialize um repositório primeiro."
    exit 1
fi

# Verificar se o Node.js está instalado
if ! command -v node &> /dev/null; then
    echo "❌ Node.js não encontrado. Instale o Node.js primeiro."
    exit 1
fi

# Verificar se o Composer está instalado
if ! command -v composer &> /dev/null; then
    echo "❌ Composer não encontrado. Instale o Composer primeiro."
    exit 1
fi

echo "🔍 Executando verificações pré-deploy..."

# Limpar cache do composer e reinstalar dependências
echo "🧹 Limpando cache do composer..."
composer clear-cache

# Instalar dependências se necessário
if [ ! -d "vendor" ]; then
    echo "📦 Instalando dependências PHP..."
    composer install --no-dev --optimize-autoloader
else
    echo "📦 Atualizando dependências PHP..."
    composer install --no-dev --optimize-autoloader
fi

if [ ! -d "node_modules" ]; then
    echo "📦 Instalando dependências Node.js..."
    npm install
fi

# Executar build dos assets
echo "🏗️ Construindo assets..."
npm run build

# Verificar se o build foi bem-sucedido
if [ ! -d "public/build" ]; then
    echo "❌ Build dos assets falhou. Verifique os erros acima."
    exit 1
fi

# Testar build do Docker localmente (opcional)
if command -v docker &> /dev/null; then
    echo "🐳 Testando build do Docker..."
    docker build -t dc-tecnologia-test . --target production
    echo "✅ Build do Docker bem-sucedido!"
else
    echo "⚠️ Docker não encontrado. Pulando teste de build local."
fi

# Verificar configurações do render.yaml
echo "🔍 Verificando configurações do render.yaml..."
if [ -f "render.yaml" ]; then
    if grep -q "FORCE_HTTPS.*true" render.yaml && grep -q "APP_URL.*https://dc-tecnologia-vendas.onrender.com" render.yaml; then
        echo "✅ Configurações HTTPS corretas no render.yaml"
    else
        echo "⚠️ Atualizando configurações HTTPS no render.yaml..."
        # Backup do arquivo original
        cp render.yaml render.yaml.backup

        # Atualizar FORCE_HTTPS se necessário
        if ! grep -q "FORCE_HTTPS.*true" render.yaml; then
            sed -i 's/FORCE_HTTPS.*$/FORCE_HTTPS\n        value: true/' render.yaml
        fi

        # Atualizar APP_URL se necessário
        if ! grep -q "APP_URL.*https://dc-tecnologia-vendas.onrender.com" render.yaml; then
            sed -i 's|APP_URL.*$|APP_URL\n        value: https://dc-tecnologia-vendas.onrender.com|' render.yaml
        fi

        echo "✅ Configurações HTTPS atualizadas"
    fi
else
    echo "❌ Arquivo render.yaml não encontrado!"
    exit 1
fi

# Verificar se há mudanças para commit
if [ -n "$(git status --porcelain)" ]; then
    echo "📝 Fazendo commit das mudanças..."
    git add .
    git commit -m "feat: corrigir Content Security Policy para deploy Docker

- Atualizada política CSP no nginx.conf para permitir CDNs necessários
- Criada configuração específica HTTPS (default-https.conf) para produção
- Implementado middleware CSP Laravel com suporte a nonces
- Adicionados scripts inline com nonces no template app.blade.php
- Separadas configurações de desenvolvimento e produção
- Dockerfile atualizado para usar configuração HTTPS em produção
- Configurações automáticas FORCE_HTTPS=true e APP_URL correto
- Corrigidos erros de bloqueio de recursos por CSP muito restritivo"
else
    echo "ℹ️ Nenhuma mudança para commit."
fi

# Push para o repositório
echo "📤 Enviando para o repositório..."
git push

echo "✅ Deploy para produção iniciado!"
echo ""
echo "🔗 Próximos passos:"
echo "1. Acesse https://dashboard.render.com"
echo "2. Conecte seu repositório se ainda não conectou"
echo "3. O Render detectará automaticamente o render.yaml"
echo "4. Aguarde o build e deploy completarem (~5-10 minutos)"
echo ""
echo "📊 Endpoints importantes:"
echo "- Aplicação: https://dc-tecnologia-vendas.onrender.com"
echo "- Health check: https://dc-tecnologia-vendas.onrender.com/health"
echo ""
echo "🔐 Credenciais de teste:"
echo "- Email: vendedor@dctecnologia.com"
echo "- Senha: 123456"
echo ""
echo "⚙️ Configurações automáticas aplicadas:"
echo "- FORCE_HTTPS=true (redirecionamento automático para HTTPS)"
echo "- APP_URL=https://dc-tecnologia-vendas.onrender.com"
echo "- Content Security Policy otimizada para produção"
echo "- Headers de segurança HSTS configurados"
echo ""
echo "🔧 Para forçar re-execução dos seeds:"
echo "1. Vá em Environment Variables no Render"
echo "2. Defina FORCE_SEED=true"
echo "3. Clique em Deploy Latest Commit"
echo ""
echo "🛡️ Correções CSP implementadas:"
echo "- Política CSP atualizada para permitir Bootstrap e Font Awesome"
echo "- Suporte a CDNs: cdnjs.cloudflare.com e cdn.jsdelivr.net"
echo "- Scripts inline com nonces para melhor segurança"
echo "- Configuração separada para desenvolvimento e produção"
