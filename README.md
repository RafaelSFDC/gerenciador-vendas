# 🏪 DC Tecnologia - Sistema de Vendas

Sistema completo de gerenciamento de vendas desenvolvido em Laravel com interface Bootstrap, focado em simplicidade e eficiência para pequenas e médias empresas.

## 📋 Sobre o Projeto

O **DC Tecnologia Sistema de Vendas** é uma aplicação web completa para gerenciamento de vendas que permite:

- 👥 **Gestão de Clientes**: Cadastro e gerenciamento de clientes pessoa física e jurídica
- 📦 **Controle de Produtos**: Catálogo de produtos com controle de estoque
- 💰 **Vendas**: Criação de vendas com múltiplos itens e formas de pagamento
- 📊 **Parcelas**: Sistema completo de parcelamento com controle de vencimentos
- 📈 **Dashboard**: Visão geral das vendas, parcelas e indicadores
- 📄 **Relatórios**: Geração de relatórios em PDF e consultas personalizadas

## 🌐 Aplicação em Produção

🚀 **Acesse a aplicação rodando**: [https://dc-tecnologia-vendas.onrender.com](https://dc-tecnologia-vendas.onrender.com)

> ⚠️ **Nota**: A aplicação pode demorar até 2 minutos para abrir, pois está hospedada em um plano gratuito no Render que hiberna quando não está em uso.

### Credenciais de Acesso
- **Email**: `vendedor@dctecnologia.com`
- **Senha**: `123456`

## 🛠️ Tecnologias Utilizadas

### Backend
- **Laravel 12** - Framework PHP moderno e robusto
- **SQLite** - Banco de dados leve e eficiente
- **PHP 8.4** - Linguagem de programação

### Frontend
- **Bootstrap 5** - Framework CSS responsivo
- **JavaScript Vanilla** - Interatividade sem dependências pesadas
- **Blade Templates** - Sistema de templates do Laravel
- **Font Awesome** - Ícones

### Infraestrutura
- **Docker** - Containerização para deploy
- **Nginx** - Servidor web de alta performance
- **Supervisor** - Gerenciamento de processos
- **Vite** - Build tool moderno

## 🚀 Funcionalidades Principais

### 👥 Gestão de Clientes
- Cadastro de clientes pessoa física (CPF) e jurídica (CNPJ)
- Informações de contato completas (telefone, email, endereço)
- Listagem e busca de clientes
- Edição e exclusão de registros

### 📦 Controle de Produtos
- Cadastro de produtos com descrição detalhada
- Controle de preços e estoque
- Status ativo/inativo para produtos
- Categorização e organização

### 💰 Sistema de Vendas
- Criação de vendas com múltiplos itens
- Cálculo automático de totais e subtotais
- Múltiplas formas de pagamento:
  - Dinheiro
  - Cartão de Crédito
  - Cartão de Débito
  - PIX
  - Boleto Bancário

### 📊 Gestão de Parcelas
- Parcelamento automático das vendas
- Controle de vencimentos
- Status das parcelas (pendente, paga, vencida)
- Marcação de pagamento com data
- Filtros por status e período

### 📈 Dashboard e Relatórios
- Indicadores de vendas do mês
- Parcelas vencendo e em aberto
- Gráficos e estatísticas
- Relatórios em PDF personalizáveis
- Exportação de dados

## 🏗️ Arquitetura do Sistema

### Padrão MVC (Model-View-Controller)
O sistema segue o padrão arquitetural MVC do Laravel:

- **Models**: Representam as entidades do negócio (Cliente, Produto, Venda, etc.)
- **Views**: Templates Blade para renderização das páginas
- **Controllers**: Lógica de negócio e coordenação entre Models e Views

### Banco de Dados
Estrutura relacional com as seguintes entidades principais:

```
Users (Usuários)
├── Vendas (1:N)
    ├── Itens da Venda (1:N)
    │   └── Produtos (N:1)
    ├── Parcelas (1:N)
    ├── Clientes (N:1)
    └── Forma de Pagamento (N:1)
```

### Fluxo de Funcionamento

1. **Autenticação**: Usuário faz login no sistema
2. **Dashboard**: Visualiza resumo de vendas e parcelas
3. **Cadastros**: Gerencia clientes e produtos
4. **Vendas**: Cria nova venda selecionando:
   - Cliente (opcional)
   - Produtos e quantidades
   - Forma de pagamento
   - Número de parcelas
5. **Parcelas**: Sistema gera automaticamente as parcelas
6. **Controle**: Acompanha vencimentos e marca pagamentos
7. **Relatórios**: Gera relatórios e PDFs

## 🔐 Sistema de Autenticação

O sistema possui autenticação completa com:
- Login seguro com email e senha
- Sessões protegidas
- Middleware de autenticação
- Logout seguro
- Proteção CSRF em formulários

### Credenciais de Teste
- **Email**: `vendedor@dctecnologia.com`
- **Senha**: `123456`

## 📁 Estrutura do Projeto

```
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/                    # Autenticação
│   │   │   ├── ClienteController.php    # Gestão de clientes
│   │   │   ├── DashboardController.php  # Dashboard principal
│   │   │   ├── ParcelaController.php    # Controle de parcelas
│   │   │   ├── ProdutoController.php    # Gestão de produtos
│   │   │   ├── RelatorioController.php  # Relatórios e PDFs
│   │   │   └── VendaController.php      # Gestão de vendas
│   │   └── Requests/                    # Validações de formulários
│   ├── Models/
│   │   ├── Cliente.php                  # Modelo de cliente
│   │   ├── FormaPagamento.php           # Formas de pagamento
│   │   ├── ItemVenda.php                # Itens da venda
│   │   ├── Parcela.php                  # Parcelas
│   │   ├── Produto.php                  # Produtos
│   │   ├── User.php                     # Usuários
│   │   └── Venda.php                    # Vendas
│   └── Console/Commands/                # Comandos artisan customizados
├── database/
│   ├── migrations/                      # Estrutura do banco
│   │   ├── *_create_users_table.php
│   │   ├── *_create_clientes_table.php
│   │   ├── *_create_produtos_table.php
│   │   ├── *_create_formas_pagamento_table.php
│   │   ├── *_create_vendas_table.php
│   │   ├── *_create_itens_venda_table.php
│   │   └── *_create_parcelas_table.php
│   └── seeders/
│       ├── DatabaseSeeder.php           # Seeder principal
│       └── VendasSeeder.php             # Dados de teste
├── resources/
│   ├── views/
│   │   ├── auth/                        # Telas de login
│   │   ├── clientes/                    # CRUD de clientes
│   │   ├── dashboard/                   # Dashboard
│   │   ├── parcelas/                    # Gestão de parcelas
│   │   ├── produtos/                    # CRUD de produtos
│   │   ├── relatorios/                  # Relatórios
│   │   ├── vendas/                      # CRUD de vendas
│   │   └── layouts/                     # Layouts base
│   ├── css/app.css                      # Estilos principais
│   └── js/app.js                        # JavaScript da aplicação
├── routes/
│   ├── web.php                          # Rotas principais
│   ├── auth.php                         # Rotas de autenticação
│   └── settings.php                     # Configurações
├── docker/                              # Configurações Docker
│   ├── nginx.conf                       # Configuração Nginx
│   ├── default.conf                     # Virtual host
│   ├── php.ini                          # Configuração PHP
│   ├── supervisord.conf                 # Supervisor
│   └── start.sh                         # Script de inicialização
├── scripts/                             # Scripts de automação
│   ├── deploy.sh                        # Deploy simples
│   ├── deploy-prod.sh                   # Deploy com verificações
│   └── dev.sh                           # Ambiente de desenvolvimento
└── public/                              # Arquivos públicos
    ├── build/                           # Assets compilados
    └── storage/                         # Link simbólico para storage
```

## 🏃‍♂️ Como Executar o Projeto

### Pré-requisitos

#### Requisitos Mínimos
- **PHP**: 8.2 ou superior
- **Composer**: 2.0 ou superior
- **Node.js**: 18.0 ou superior
- **NPM**: 8.0 ou superior
- **Git**: Para controle de versão

#### Extensões PHP Necessárias
- `pdo_sqlite` - Para banco de dados SQLite
- `mbstring` - Para manipulação de strings
- `xml` - Para processamento XML
- `zip` - Para compressão de arquivos
- `gd` - Para manipulação de imagens
- `opcache` - Para otimização (recomendado)

#### Opcional (para Docker)
- **Docker**: 20.10 ou superior
- **Docker Compose**: 2.0 ou superior

### Opção 1: Execução Local (Recomendado para Desenvolvimento)

1. **Clone o repositório**:
```bash
git clone <url-do-repositorio>
cd example-app
```

2. **Instale as dependências PHP**:
```bash
composer install
```

3. **Instale as dependências Node.js**:
```bash
npm install
```

4. **Configure o ambiente**:
```bash
cp .env.example .env
php artisan key:generate
```

5. **Configure o banco de dados**:
```bash
touch database/database.sqlite
php artisan migrate
php artisan db:seed
```

6. **Execute a aplicação**:
```bash
# Terminal 1 - Servidor Laravel
composer run dev

```

7. **Acesse a aplicação**:
   - URL: `http://localhost:8000`
   - Login: `vendedor@dctecnologia.com` / `123456`

### Opção 2: Execução com Docker

1. **Execute o script de desenvolvimento**:
```bash
./scripts/dev.sh
```

2. **Acesse a aplicação**:
   - URL: `http://localhost:8080`
   - Login: `vendedor@dctecnologia.com` / `123456`

### Opção 3: Usando Composer Script

```bash
# Executa servidor, queue e vite simultaneamente
composer run dev
```

## 🔧 Comandos Úteis

### Laravel
```bash
# Limpar cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Executar migrações
php artisan migrate

# Executar seeds
php artisan db:seed

# Gerar dados de teste
php artisan db:seed --class=VendasSeeder
```

### Assets
```bash
# Desenvolvimento
npm run dev

# Produção
npm run build

# Linting
npm run lint

# Formatação
npm run format
```

### Docker
```bash
# Iniciar ambiente
./scripts/dev.sh

# Ver logs
docker-compose logs -f

# Parar containers
docker-compose down
```

## 🖥️ Principais Interfaces

### Dashboard Principal
- **Resumo de vendas** do mês atual
- **Parcelas vencendo** nos próximos dias
- **Indicadores** de performance
- **Acesso rápido** às principais funcionalidades

### Gestão de Vendas
- **Formulário intuitivo** para criação de vendas
- **Seleção múltipla** de produtos com cálculo automático
- **Configuração de parcelas** com datas automáticas
- **Visualização detalhada** de vendas existentes

### Controle de Parcelas
- **Lista completa** de todas as parcelas
- **Filtros por status**: pendente, paga, vencida
- **Marcação rápida** de pagamentos
- **Alertas visuais** para vencimentos

### Relatórios
- **Geração de PDFs** personalizados
- **Filtros por período** e status
- **Relatórios de vendas** detalhados
- **Exportação** de dados

## 📊 Dados de Teste

O sistema vem com dados de teste pré-configurados:

### Usuários
- **Vendedor**: `vendedor@dctecnologia.com` / `123456`

### Clientes
- João Silva (CPF: 123.456.789-00)
- Maria Santos (CPF: 987.654.321-00)
- Empresa ABC Ltda (CNPJ: 12.345.678/0001-90)

### Produtos
- Notebook Dell Inspiron - R$ 2.499,99
- Mouse Logitech MX Master - R$ 299,99
- Teclado Mecânico Corsair - R$ 599,99
- Monitor LG UltraWide - R$ 1.299,99
- Headset HyperX Cloud - R$ 399,99

### Formas de Pagamento
- Dinheiro, Cartão de Crédito, Cartão de Débito, PIX, Boleto

## 🌐 Deploy em Produção

O projeto está configurado para deploy automático no Render.com. Consulte:
- `DEPLOY.md` - Guia completo de deploy
- `RENDER_SETUP.md` - Configuração específica do Render

Para fazer deploy:
```bash
./scripts/deploy.sh
```

## 🚨 Troubleshooting

### Problemas Comuns

#### Erro: "Class 'PDO' not found"
```bash
# Instalar extensão PDO SQLite
sudo apt-get install php-sqlite3  # Ubuntu/Debian
# ou
brew install php@8.2              # macOS
```

#### Erro: "Permission denied" no storage
```bash
# Configurar permissões
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

#### Assets não carregam
```bash
# Limpar cache e rebuild
npm run build
php artisan view:clear
php artisan config:clear
```

#### Banco de dados não encontrado
```bash
# Criar arquivo SQLite
touch database/database.sqlite
php artisan migrate
php artisan db:seed
```

**DC Tecnologia** - Sistema de Vendas Completo 🚀
