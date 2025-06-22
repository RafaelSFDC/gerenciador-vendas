#!/bin/bash

# Script de deploy simples para o Render.com

set -e

echo "🚀 Iniciando deploy para o Render.com..."

# Verificar se estamos em um repositório git
if [ ! -d ".git" ]; then
    echo "❌ Este não é um repositório git. Inicialize um repositório primeiro."
    exit 1
fi

# Verificar se há mudanças para commit
if [ -n "$(git status --porcelain)" ]; then
    echo "📝 Fazendo commit das mudanças..."
    git add .
    git commit -m "feat: configuração Docker para deploy no Render.com

- Adicionado Dockerfile multi-stage otimizado
- Configurado render.yaml para deploy automático
- Incluído nginx, supervisor e configurações PHP
- Scripts de inicialização e health check
- Suporte a SQLite para produção
- Otimizações de performance e segurança"
else
    echo "ℹ️ Nenhuma mudança para commit."
fi

# Push para o repositório
echo "📤 Enviando para o repositório..."
git push

echo "✅ Deploy iniciado! Acesse o dashboard do Render.com para acompanhar o progresso."
echo ""
echo "🔗 Próximos passos:"
echo "1. Acesse https://dashboard.render.com"
echo "2. Conecte seu repositório se ainda não conectou"
echo "3. O Render detectará automaticamente o render.yaml"
echo "4. Aguarde o build e deploy completarem"
echo ""
echo "📊 Endpoints importantes:"
echo "- Aplicação: https://dc-tecnologia-vendas.onrender.com"
echo "- Health check: https://dc-tecnologia-vendas.onrender.com/health"
echo ""
echo "🔐 Credenciais de teste:"
echo "- Email: vendedor@dctecnologia.com"
echo "- Senha: 123456"
