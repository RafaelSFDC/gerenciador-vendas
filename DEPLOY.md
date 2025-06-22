# 🚀 Guia de Deploy - DC Tecnologia Sistema de Vendas

## 📋 Resumo das Configurações

### ✅ Configurações Implementadas

- **Docker Multi-stage**: Build otimizado para produção
- **SQLite**: Banco de dados simples e eficiente para produção
- **Nginx + PHP-FPM**: Stack de alta performance
- **Supervisor**: Gerenciamento de processos
- **Health Check**: Monitoramento automático
- **Seeds Automáticos**: Dados de teste criados automaticamente
- **Cache Otimizado**: Assets com cache de longo prazo
- **Headers de Segurança**: Proteção contra ataques comuns

## 🔧 Como Fazer Deploy

### 1. Deploy Rápido (Recomendado)

```bash
# Execute o script de deploy
./scripts/deploy.sh
```

### 2. Deploy com Verificações Completas

```bash
# Para deploy com verificações extras
./scripts/deploy-prod.sh
```

Este script irá:
- ✅ Verificar dependências
- ✅ Construir assets
- ✅ Testar build Docker localmente
- ✅ Fazer commit e push das mudanças

## 🌐 Configurações do Render.com

### Variáveis de Ambiente Configuradas

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://dc-tecnologia-vendas.onrender.com
DB_CONNECTION=sqlite
FORCE_HTTPS=true
FORCE_SEED=false
```

### Para Forçar Re-execução dos Seeds

1. Acesse o dashboard do Render.com
2. Vá em **Environment Variables**
3. Defina `FORCE_SEED=true`
4. Clique em **Deploy Latest Commit**

## 🔐 Credenciais de Acesso

### Usuário de Teste

- **Email**: `vendedor@dctecnologia.com`
- **Senha**: `123456`

Este usuário é criado automaticamente pelos seeds e permite acesso completo ao sistema.

## 📊 Endpoints Importantes

- **Aplicação**: `https://dc-tecnologia-vendas.onrender.com`
- **Login**: `https://dc-tecnologia-vendas.onrender.com/login`
- **Health Check**: `https://dc-tecnologia-vendas.onrender.com/health`

## 🛠️ Desenvolvimento Local

### Opção 1: Com Docker (Recomendado)

```bash
# Iniciar ambiente completo
./scripts/dev.sh

# Acessar em http://localhost:8080
```

### Opção 2: Sem Docker

```bash
# Instalar dependências
composer install
npm install

# Configurar ambiente
cp .env.example .env
php artisan key:generate

# Banco de dados
touch database/database.sqlite
php artisan migrate
php artisan db:seed

# Executar
php artisan serve &
npm run dev
```

## 🔄 Processo de Deploy Automático

1. **Commit**: Mudanças são commitadas no git
2. **Push**: Código é enviado para o repositório
3. **Render**: Detecta mudanças automaticamente
4. **Build**: Executa o Dockerfile
5. **Deploy**: Aplicação fica disponível

## 📈 Monitoramento

### Health Check

- **Endpoint**: `/health`
- **Resposta**: `healthy`
- **Uso**: Monitoramento automático do Render.com

### Logs

- Acesse o dashboard do Render.com
- Vá em "Logs" para ver logs em tempo real
- Logs incluem: Nginx, PHP-FPM, Laravel, Queue

## 🚨 Troubleshooting

### Build falha

- Verifique se `npm run build` funciona localmente
- Confirme que todas as dependências estão no package.json

### App não inicia

- Verifique logs no dashboard do Render
- Confirme que as variáveis de ambiente estão corretas

### Problemas de login

- Verifique se os seeds foram executados
- Use `FORCE_SEED=true` para recriar dados

### Performance lenta

- O plano starter do Render pode ser lento
- Considere upgrade para plano pago para melhor performance

## 🔧 Comandos Úteis

### Docker Local

```bash
# Ver logs
docker-compose logs -f

# Parar containers
docker-compose down

# Rebuild completo
docker-compose up --build --force-recreate

# Executar comando Laravel
docker-compose exec app php artisan migrate
```

### Render.com

```bash
# Forçar novo deploy
git commit --allow-empty -m "trigger deploy"
git push
```

## 📋 Checklist Pré-Deploy

- [ ] Código commitado e testado
- [ ] Assets buildados (`npm run build`)
- [ ] Variáveis de ambiente configuradas
- [ ] Health check funcionando
- [ ] Credenciais de teste validadas

## 🔄 Atualizações Futuras

Para fazer deploy de novas versões:

1. Faça suas mudanças no código
2. Execute `./scripts/deploy.sh`
3. Aguarde o deploy automático no Render

O Render detectará automaticamente as mudanças e fará o deploy da nova versão.
