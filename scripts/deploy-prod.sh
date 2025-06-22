#!/bin/bash

# Script de deploy completo com verificações para produção

set -e

echo "🚀 Iniciando deploy para produção no Render.com..."

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

# Instalar dependências se necessário
if [ ! -d "vendor" ]; then
    echo "📦 Instalando dependências PHP..."
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

# Verificar se há mudanças para commit
if [ -n "$(git status --porcelain)" ]; then
    echo "📝 Fazendo commit das mudanças..."
    git add .
    git commit -m "feat: configuração Docker completa para deploy no Render.com

- Adicionado Dockerfile multi-stage otimizado
- Configurado render.yaml para deploy automático  
- Incluído nginx, supervisor e configurações PHP
- Scripts de inicialização e health check
- Suporte a SQLite para produção
- Otimizações de performance e segurança
- Assets buildados para produção"
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
echo "🔧 Para forçar re-execução dos seeds:"
echo "1. Vá em Environment Variables no Render"
echo "2. Defina FORCE_SEED=true"
echo "3. Clique em Deploy Latest Commit"
