# 🔒 Correção de Content Security Policy (CSP)

## 📋 Problema Identificado

O deploy Docker estava falhando devido a políticas CSP muito restritivas que bloqueavam:

- ✅ **Scripts inline** (verificação de Bootstrap)
- ✅ **Stylesheets inline** (estilos dinâmicos)
- ✅ **CDNs externos** (Bootstrap e Font Awesome)
- ✅ **Assets do Vite** (CSS e JS compilados)

## 🔧 Soluções Implementadas

### 1. Configuração CSP Atualizada

**Arquivo:** `docker/nginx.conf`
- Política CSP mais permissiva para produção HTTPS
- Suporte a CDNs necessários (cdnjs.cloudflare.com, cdn.jsdelivr.net)
- Permissões para scripts e estilos inline com 'unsafe-inline'

### 2. Configuração Específica para HTTPS

**Arquivo:** `docker/default-https.conf`
- Configuração otimizada para produção no Render.com
- Headers de segurança HSTS para HTTPS
- CSP específico para ambiente de produção

### 3. Middleware CSP Laravel

**Arquivo:** `app/Http/Middleware/ContentSecurityPolicy.php`
- Middleware personalizado para controle fino do CSP
- Geração de nonces únicos para scripts
- Políticas diferentes para desenvolvimento e produção

### 4. Templates com Nonces

**Arquivo:** `resources/views/app.blade.php`
- Scripts inline agora usam nonces CSP
- Melhor compatibilidade com políticas de segurança

### 5. Separação Dev/Prod

**Arquivos:**
- `Dockerfile` - Produção (usa default-https.conf)
- `Dockerfile.dev` - Desenvolvimento (usa default.conf)
- `docker-compose.yml` - Atualizado para usar Dockerfile.dev

## 🚀 Como Fazer Deploy

### Verificação Pré-Deploy

```bash
# Verificar configurações antes do deploy
./scripts/verify-config.sh
```

### Desenvolvimento Local

```bash
# Usar configuração de desenvolvimento (HTTP simples)
docker-compose up --build
```

### Produção (Render.com)

```bash
# Executar script de deploy (inclui verificação automática)
./scripts/deploy-prod.sh
```

### Configurações Automáticas

O script de deploy agora configura automaticamente:

- ✅ **FORCE_HTTPS=true** no render.yaml e Dockerfile
- ✅ **APP_URL=https://dc-tecnologia-vendas.onrender.com**
- ✅ **Verificação de configurações** antes do deploy
- ✅ **Backup automático** do render.yaml antes de alterações
- ✅ **Variáveis de ambiente** padrão no Dockerfile

## 📊 Configurações CSP por Ambiente

### Desenvolvimento
```
default-src 'self' http: https: data: blob:
script-src 'self' 'nonce-{nonce}' 'unsafe-inline' 'unsafe-eval' http: https:
style-src 'self' 'nonce-{nonce}' 'unsafe-inline' http: https:
```

### Produção
```
default-src 'self' https: data: blob: 'unsafe-inline' 'unsafe-eval'
script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdnjs.cloudflare.com https://cdn.jsdelivr.net
style-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com https://cdn.jsdelivr.net
```

## 🔐 Headers de Segurança

### Produção HTTPS
- `Strict-Transport-Security: max-age=31536000; includeSubDomains; preload`
- `X-Frame-Options: SAMEORIGIN`
- `X-XSS-Protection: 1; mode=block`
- `X-Content-Type-Options: nosniff`
- `Referrer-Policy: no-referrer-when-downgrade`

## 🧪 Testes

### Verificar CSP Local
```bash
# Testar com curl
curl -I http://localhost:8080

# Verificar headers de segurança
curl -I http://localhost:8080 | grep -i "content-security-policy"
```

### Verificar Produção
```bash
# Testar Render.com
curl -I https://dc-tecnologia-vendas.onrender.com

# Verificar HSTS
curl -I https://dc-tecnologia-vendas.onrender.com | grep -i "strict-transport"
```

## 🔧 Troubleshooting

### CSP ainda bloqueando recursos

1. **Verificar logs do navegador** (F12 → Console)
2. **Ajustar política no nginx.conf** se necessário
3. **Usar nonces** para scripts específicos
4. **Verificar URLs dos CDNs** estão na whitelist

### Assets não carregando

1. **Verificar build do Vite** (`npm run build`)
2. **Confirmar MIME types** no nginx
3. **Verificar permissões** dos arquivos

### HTTPS não funcionando

1. **Verificar FORCE_HTTPS=true** no Render.com
2. **Confirmar APP_URL** usa https://
3. **Verificar proxy headers** no nginx

## 📝 Variáveis de Ambiente Render.com

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://dc-tecnologia-vendas.onrender.com
FORCE_HTTPS=true
DB_CONNECTION=sqlite
CACHE_STORE=file
SESSION_DRIVER=file
```

## ✅ Checklist de Deploy

- [ ] Build local funciona sem erros CSP
- [ ] Assets compilados corretamente
- [ ] Headers de segurança configurados
- [ ] HTTPS forçado em produção
- [ ] Health check respondendo
- [ ] Logs sem erros CSP

## 🔗 Referências

- [Content Security Policy MDN](https://developer.mozilla.org/en-US/docs/Web/HTTP/CSP)
- [Nginx Security Headers](https://nginx.org/en/docs/http/ngx_http_headers_module.html)
- [Laravel Security](https://laravel.com/docs/security)
- [Render.com Deploy](https://render.com/docs/deploy-laravel)
