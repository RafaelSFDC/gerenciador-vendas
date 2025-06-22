# Correção do Erro Laravel Sail em Produção

## Problema
Erro em produção: `Class 'Laravel\Sail\ServiceProvider' not found`

## Causa
O Laravel Sail é uma dependência de desenvolvimento que não deve estar disponível em produção, mas estava sendo descoberta automaticamente pelo Laravel.

## Soluções Implementadas

### 1. Configuração do composer.json
Adicionado o Laravel Sail à lista de pacotes que não devem ser descobertos:

```json
"extra": {
    "laravel": {
        "dont-discover": [
            "laravel/sail"
        ]
    }
}
```

### 2. Dockerfile Atualizado
Modificado para usar `--no-dev` na instalação do composer:

```dockerfile
# Instalar dependências do PHP (somente produção)
RUN composer install --optimize-autoloader --no-interaction --prefer-dist --no-scripts --no-dev
```

### 3. Script de Deploy Atualizado
Adicionada limpeza do cache do composer:

```bash
# Limpar cache do composer e reinstalar dependências
echo "🧹 Limpando cache do composer..."
composer clear-cache
```

### 4. Script de Inicialização Docker
Adicionada regeneração do autoloader sem dependências de desenvolvimento:

```bash
# Regenerar autoloader sem dependências de desenvolvimento
echo "🔄 Regenerando autoloader..."
composer dump-autoload --optimize --no-dev
```

## Verificação
Para verificar se o problema foi resolvido:

1. Execute o build do Docker localmente:
   ```bash
   docker build -t test-app . --target production
   ```

2. Execute o container:
   ```bash
   docker run -p 8080:80 test-app
   ```

3. Verifique se não há erros relacionados ao Laravel Sail nos logs.

## Prevenção
- Sempre use `--no-dev` ao instalar dependências em produção
- Configure `dont-discover` para pacotes de desenvolvimento
- Mantenha separação clara entre dependências de desenvolvimento e produção

## Comandos Úteis

### Limpar cache local
```bash
composer clear-cache
composer dump-autoload --optimize
```

### Verificar dependências instaladas
```bash
composer show --installed
```

### Verificar service providers registrados
```bash
php artisan package:discover --ansi
```
