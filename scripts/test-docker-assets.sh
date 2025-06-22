#!/bin/bash

# Script para testar se os assets estão sendo buildados corretamente no Docker
# ATENÇÃO: Este script é apenas para teste - NÃO EXECUTE em desenvolvimento

echo "🐳 Script de teste para Docker - Assets em Produção"
echo "⚠️  ESTE SCRIPT É APENAS PARA TESTE - NÃO EXECUTE EM DEV"
echo ""

# Função para testar build Docker
test_docker_build() {
    echo "🏗️ Testando build Docker..."
    echo "docker build -t dc-vendas-test ."
    echo ""
    
    echo "📦 Verificando se assets foram copiados:"
    echo "docker run --rm dc-vendas-test ls -la /var/www/html/public/build/"
    echo ""
    
    echo "📄 Verificando manifest.json:"
    echo "docker run --rm dc-vendas-test cat /var/www/html/public/build/.vite/manifest.json"
    echo ""
    
    echo "🌐 Testando servidor Nginx:"
    echo "docker run -d -p 8080:80 --name dc-vendas-test dc-vendas-test"
    echo "sleep 10"
    echo "curl -I http://localhost:8080/build/assets/app-*.css"
    echo "curl -I http://localhost:8080/build/assets/app-*.js"
    echo "docker stop dc-vendas-test"
    echo "docker rm dc-vendas-test"
    echo ""
}

# Função para testar build local
test_local_build() {
    echo "🏗️ Testando build local dos assets..."
    
    if [ ! -d "node_modules" ]; then
        echo "📦 Instalando dependências Node.js..."
        echo "npm install"
    fi
    
    echo "🔨 Executando build..."
    echo "npm run build"
    
    echo "✅ Verificando arquivos gerados:"
    echo "ls -la public/build/"
    echo "ls -la public/build/assets/"
    echo "cat public/build/.vite/manifest.json"
}

# Função para verificar configurações
check_configs() {
    echo "🔍 Verificando configurações..."
    
    echo "📋 Dockerfile - Stage de build Node.js:"
    echo "grep -A 10 'FROM node:' Dockerfile"
    echo ""
    
    echo "📋 Dockerfile - Cópia dos assets:"
    echo "grep -A 5 'COPY.*build' Dockerfile"
    echo ""
    
    echo "📋 Nginx - Configuração de assets:"
    echo "grep -A 10 'location.*build' docker/default.conf"
    echo ""
    
    echo "📋 Vite config:"
    echo "cat vite.config.ts"
    echo ""
    
    echo "📋 Render config:"
    echo "grep -A 5 'ASSET_URL' render.yaml"
}

# Menu principal
show_menu() {
    echo "Escolha uma opção de teste:"
    echo "1) Testar build local dos assets"
    echo "2) Mostrar comandos para testar Docker"
    echo "3) Verificar configurações"
    echo "4) Sair"
    echo ""
    echo "Digite sua escolha (1-4):"
}

# Função principal
main() {
    echo "🚀 Iniciando testes de assets..."
    echo ""
    
    while true; do
        show_menu
        read -r choice
        
        case $choice in
            1)
                test_local_build
                echo ""
                ;;
            2)
                test_docker_build
                echo ""
                ;;
            3)
                check_configs
                echo ""
                ;;
            4)
                echo "👋 Saindo..."
                break
                ;;
            *)
                echo "❌ Opção inválida. Tente novamente."
                echo ""
                ;;
        esac
    done
}

# Verificar se está sendo executado
if [[ "${BASH_SOURCE[0]}" == "${0}" ]]; then
    echo "⚠️  AVISO: Este script é apenas para referência!"
    echo "⚠️  Para executar, descomente a linha abaixo:"
    echo "# main"
    echo ""
    echo "📖 Para ver os comandos de teste, abra o arquivo:"
    echo "📖 scripts/test-docker-assets.sh"
fi
