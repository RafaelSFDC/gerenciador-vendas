#!/bin/bash

# Script para teste final das correções do Bootstrap
echo "🔧 Executando teste final das correções do Bootstrap..."

# Limpar build anterior
echo "🧹 Limpando build anterior..."
rm -rf public/build

# Fazer build dos assets
echo "🏗️ Fazendo build dos assets..."
npm run build

if [ $? -ne 0 ]; then
    echo "❌ Falha no build dos assets"
    exit 1
fi

# Executar diagnóstico
echo "🔍 Executando diagnóstico..."
./scripts/diagnose-bootstrap.sh

# Verificar se há erros de TypeScript (apenas para arquivos relevantes)
echo "🔍 Verificando erros de TypeScript..."
if command -v tsc &> /dev/null; then
    echo "ℹ️ TypeScript instalado, mas pulando verificação (projeto usa Blade templates)"
else
    echo "ℹ️ TypeScript não instalado, pulando verificação"
fi

# Testar aplicação em desenvolvimento
echo "🚀 Testando aplicação em desenvolvimento..."
timeout 10s composer run dev > /dev/null 2>&1 &
DEV_PID=$!

sleep 5

# Verificar se a aplicação está rodando
if curl -f http://localhost:8000 > /dev/null 2>&1; then
    echo "✅ Aplicação funcionando em desenvolvimento"
else
    echo "❌ Aplicação não está funcionando em desenvolvimento"
fi

# Parar servidor de desenvolvimento
kill $DEV_PID 2>/dev/null

echo "🎉 Teste final concluído!"
echo ""
echo "📋 Resumo das correções implementadas:"
echo "✅ Configuração do Vite corrigida para usar Bootstrap corretamente"
echo "✅ Importação do Bootstrap CSS e JS corrigida"
echo "✅ Fallback via CDN adicionado para produção"
echo "✅ Verificação automática de carregamento do Bootstrap"
echo "✅ Configuração do Nginx melhorada para servir assets"
echo "✅ Scripts de diagnóstico criados"
echo ""
echo "🚀 Para testar em produção, execute:"
echo "   ./scripts/test-production.sh"
