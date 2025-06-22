#!/bin/bash

# Script para deploy com correções de assets
# Este script aplica todas as correções necessárias para resolver o problema do CSS em produção

set -e

echo "🚀 Deploy com Correções de Assets - DC Tecnologia"
echo "================================================"
echo ""

# Verificar se estamos no diretório correto
if [ ! -f "composer.json" ] || [ ! -f "package.json" ]; then
    echo "❌ Execute este script no diretório raiz do projeto Laravel"
    exit 1
fi

# Função para verificar dependências
check_dependencies() {
    echo "🔍 Verificando dependências..."
    
    if ! command -v npm &> /dev/null; then
        echo "❌ npm não encontrado. Instale o Node.js"
        exit 1
    fi
    
    if ! command -v composer &> /dev/null; then
        echo "❌ composer não encontrado. Instale o Composer"
        exit 1
    fi
    
    echo "✅ Dependências verificadas"
    echo ""
}

# Função para instalar dependências
install_dependencies() {
    echo "📦 Instalando dependências..."
    
    if [ ! -d "vendor" ]; then
        echo "📦 Instalando dependências PHP..."
        composer install --no-dev --optimize-autoloader
    fi
    
    if [ ! -d "node_modules" ]; then
        echo "📦 Instalando dependências Node.js..."
        npm install
    fi
    
    echo "✅ Dependências instaladas"
    echo ""
}

# Função para fazer build dos assets
build_assets() {
    echo "🏗️ Construindo assets..."
    
    # Limpar build anterior
    if [ -d "public/build" ]; then
        rm -rf public/build
    fi
    
    # Fazer build
    npm run build
    
    # Verificar se o build foi bem-sucedido
    if [ ! -d "public/build" ]; then
        echo "❌ Build dos assets falhou"
        exit 1
    fi
    
    if [ ! -f "public/build/.vite/manifest.json" ]; then
        echo "❌ Manifest.json não foi gerado"
        exit 1
    fi
    
    echo "✅ Assets construídos com sucesso"
    echo "📄 Arquivos gerados:"
    ls -la public/build/assets/
    echo ""
}

# Função para verificar configurações
verify_configs() {
    echo "🔍 Verificando configurações..."
    
    # Verificar .dockerignore
    if grep -q "^/public/build" .dockerignore; then
        echo "❌ ERRO: public/build está sendo excluído no .dockerignore"
        echo "💡 Corrija comentando a linha: # /public/build"
        exit 1
    fi
    
    # Verificar vite.config.ts
    if ! grep -q "resources/css/app.css" vite.config.ts; then
        echo "❌ ERRO: vite.config.ts não está configurado corretamente"
        exit 1
    fi
    
    # Verificar render.yaml
    if ! grep -q "ASSET_URL" render.yaml; then
        echo "❌ ERRO: ASSET_URL não está configurado no render.yaml"
        exit 1
    fi
    
    echo "✅ Configurações verificadas"
    echo ""
}

# Função para fazer commit das mudanças
commit_changes() {
    echo "📝 Fazendo commit das mudanças..."
    
    # Verificar se há mudanças
    if git diff --quiet && git diff --staged --quiet; then
        echo "ℹ️ Nenhuma mudança para commit"
    else
        echo "📋 Mudanças detectadas:"
        git status --porcelain
        echo ""
        
        # Adicionar arquivos
        git add .
        
        # Fazer commit
        git commit -m "fix: Corrigir carregamento de CSS em produção

- Corrigir .dockerignore para não excluir public/build
- Melhorar configuração do Vite para produção
- Adicionar ASSET_URL no render.yaml
- Melhorar configuração do Nginx para servir assets
- Adicionar verificações de build no Dockerfile"
        
        echo "✅ Commit realizado"
    fi
    echo ""
}

# Função para fazer push
push_changes() {
    echo "🚀 Fazendo push para o repositório..."
    
    # Verificar branch atual
    current_branch=$(git branch --show-current)
    echo "📍 Branch atual: $current_branch"
    
    # Fazer push
    git push origin "$current_branch"
    
    echo "✅ Push realizado"
    echo ""
}

# Função para mostrar próximos passos
show_next_steps() {
    echo "🎯 Próximos Passos:"
    echo ""
    echo "1. ✅ Verificar se o deploy foi iniciado no Render:"
    echo "   https://dashboard.render.com/"
    echo ""
    echo "2. 🔍 Monitorar os logs de build no Render"
    echo "   Verificar se os assets estão sendo buildados corretamente"
    echo ""
    echo "3. 🌐 Testar a aplicação em produção:"
    echo "   https://dc-tecnologia-vendas.onrender.com"
    echo ""
    echo "4. 🎨 Verificar se o CSS está carregando:"
    echo "   - Abrir DevTools (F12)"
    echo "   - Verificar se não há erros 404 para arquivos CSS/JS"
    echo "   - Verificar se os estilos estão sendo aplicados"
    echo ""
    echo "5. 🐛 Se ainda houver problemas:"
    echo "   - Verificar logs do Render"
    echo "   - Executar: ./scripts/diagnose-assets.sh"
    echo ""
}

# Função principal
main() {
    echo "🔧 Iniciando processo de deploy com correções..."
    echo ""
    
    check_dependencies
    install_dependencies
    build_assets
    verify_configs
    commit_changes
    push_changes
    show_next_steps
    
    echo "🎉 Deploy iniciado com sucesso!"
    echo "⏳ Aguarde o build no Render completar (pode levar alguns minutos)"
    echo ""
}

# Executar apenas se chamado diretamente
if [[ "${BASH_SOURCE[0]}" == "${0}" ]]; then
    main "$@"
fi
