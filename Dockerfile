# Multi-stage build para otimizar o tamanho da imagem final
FROM node:22-alpine AS node-builder

WORKDIR /app

# Copiar arquivos de dependências do Node.js
COPY package*.json ./
COPY vite.config.ts ./
COPY tsconfig.json ./

# Instalar dependências do Node.js (incluindo dev para build)
RUN npm ci --silent

# Copiar código fonte do frontend
COPY resources/ ./resources/
COPY public/ ./public/

# Build dos assets
RUN npm run build

# Verificar se o build foi bem-sucedido
RUN ls -la ./public/build/ && ls -la ./public/build/assets/ && cat ./public/build/manifest.json

# Stage 2: PHP Runtime
FROM php:8.4-fpm-alpine AS php-base

# Instalar dependências do sistema
RUN apk add --no-cache \
    nginx \
    supervisor \
    sqlite \
    sqlite-dev \
    zip \
    unzip \
    curl \
    oniguruma-dev \
    libxml2-dev \
    freetype-dev \
    libjpeg-turbo-dev \
    libpng-dev \
    libzip-dev

# Instalar extensões PHP
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_sqlite \
        pdo_mysql \
        mbstring \
        xml \
        zip \
        gd \
        opcache

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configurar diretório de trabalho
WORKDIR /var/www/html

# Copiar arquivos de dependências do PHP
COPY composer.json composer.lock ./

# Instalar dependências do PHP (somente produção)
RUN composer install --optimize-autoloader --no-interaction --prefer-dist --no-scripts --no-dev

# Stage 3: Final Production Image
FROM php-base AS production

# Copiar código fonte da aplicação
COPY . .

# Copiar assets buildados do stage anterior
COPY --from=node-builder /app/public/build ./public/build

# Verificar se os assets foram copiados corretamente
RUN ls -la ./public/build/ || echo "Diretório build não encontrado"

# Configurar permissões
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache \
    && chmod -R 755 /var/www/html/public/build

# Copiar configurações
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/default-https.conf /etc/nginx/http.d/default.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/php.ini /usr/local/etc/php/conf.d/custom.ini
COPY docker/start.sh /usr/local/bin/start.sh

# Tornar script executável
RUN chmod +x /usr/local/bin/start.sh

# Criar usuário não-root
RUN addgroup -g 1000 -S www && \
    adduser -u 1000 -D -S -G www www

# Criar diretórios necessários
RUN mkdir -p /var/www/html/storage/logs \
    && mkdir -p /var/www/html/storage/framework/cache \
    && mkdir -p /var/www/html/storage/framework/sessions \
    && mkdir -p /var/www/html/storage/framework/views \
    && mkdir -p /var/www/html/bootstrap/cache \
    && mkdir -p /var/www/html/database \
    && mkdir -p /var/log/supervisor \
    && mkdir -p /var/run

# Configurar permissões finais
RUN chown -R www-data:www-data /var/www/html/storage \
    && chown -R www-data:www-data /var/www/html/bootstrap/cache \
    && chown -R www-data:www-data /var/www/html/database \
    && chmod -R 755 /var/log/supervisor \
    && chmod -R 755 /var/run

# Configurar variáveis de ambiente padrão para produção
ENV APP_ENV=production \
    APP_DEBUG=false \
    FORCE_HTTPS=true \
    APP_URL=https://dc-tecnologia-vendas.onrender.com \
    DB_CONNECTION=sqlite \
    DB_DATABASE=/var/www/html/database/database.sqlite \
    CACHE_STORE=file \
    SESSION_DRIVER=file \
    QUEUE_CONNECTION=database \
    LOG_CHANNEL=stderr \
    LOG_LEVEL=info \
    SESSION_LIFETIME=120 \
    BCRYPT_ROUNDS=12 \
    FORCE_SEED=false

# Expor porta
EXPOSE 80

# Comando de inicialização
CMD ["/usr/local/bin/start.sh"]
