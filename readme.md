# Portal Tech - Sistema de Comparação de Produtos

Sistema web full-stack para comparação de produtos com gerenciamento de usuários, favoritos, reviews e painel administrativo completo.

## 📋 Descrição

Portal Tech é uma aplicação web moderna desenvolvida com Symfony 6.0 (backend) e Vue 3 (frontend) que permite aos usuários comparar produtos de diferentes fornecedores e marketplaces. O sistema oferece autenticação JWT, sistema de favoritos, avaliações, rankings inteligentes e um painel administrativo robusto.

## 🚀 Tecnologias Utilizadas

### Backend
- **PHP 8.0+** - Linguagem de programação
- **Symfony 6.0** - Framework PHP moderno
- **MongoDB** - Banco de dados NoSQL
- **Doctrine MongoDB ODM** - Object Document Mapper
- **JWT (Firebase PHP-JWT)** - Autenticação via tokens
- **Nelmio CORS Bundle** - Gerenciamento de CORS

### Frontend
- **Vue 3** - Framework JavaScript progressivo
- **Vite 8** - Build tool e dev server
- **Vue Router 5** - Roteamento SPA
- **Pinia 3** - Gerenciamento de estado
- **Tailwind CSS 4** - Framework CSS utility-first
- **Axios** - Cliente HTTP
- **Lucide Vue Next** - Biblioteca de ícones

## 📂 Estrutura do Projeto

```
product_comparison/
├── portal-tech/                    # Backend Symfony
│   ├── config/                     # Configurações
│   │   ├── packages/              # Configurações de bundles
│   │   │   ├── doctrine_mongodb.yaml
│   │   │   ├── security.yaml
│   │   │   ├── nelmio_cors.yaml
│   │   │   └── ...
│   │   ├── routes.yaml            # Rotas da API
│   │   └── services.yaml          # Serviços
│   ├── src/
│   │   ├── Controller/Api/        # Controllers da API REST
│   │   │   ├── AuthController.php
│   │   │   ├── ProductsController.php
│   │   │   ├── ComparisonsController.php
│   │   │   ├── FavoritesController.php
│   │   │   ├── ReviewsController.php
│   │   │   ├── HomeController.php
│   │   │   └── Admin/
│   │   ├── Document/              # Modelos MongoDB ODM
│   │   │   ├── User.php
│   │   │   ├── Product.php
│   │   │   ├── Category.php
│   │   │   ├── Supplier.php
│   │   │   ├── Marketplace.php
│   │   │   ├── Review.php
│   │   │   ├── Favorite.php
│   │   │   ├── SavedComparison.php
│   │   │   └── ProductOffer.php
│   │   ├── Service/               # Lógica de negócio
│   │   │   └── ProductRankingService.php
│   │   ├── Repository/            # Repositórios
│   │   └── Command/               # Comandos CLI
│   │       └── SeedDatabaseCommand.php
│   ├── public/
│   │   └── index.php              # Entry point
│   ├── composer.json              # Dependências PHP
│   └── frontend/                  # Frontend Vue 3
│       ├── src/
│       │   ├── components/        # Componentes Vue
│       │   ├── views/             # Páginas
│       │   │   ├── HomeView.vue
│       │   │   ├── Login.vue
│       │   │   ├── ProductsView.vue
│       │   │   ├── CompareView.vue
│       │   │   ├── Favorites.vue
│       │   │   ├── Reviews.vue
│       │   │   ├── Dashboard.vue
│       │   │   ├── SavedComparisonsView.vue
│       │   │   └── admin/
│       │   ├── router/            # Configuração de rotas
│       │   ├── stores/            # Pinia stores
│       │   ├── services/          # Serviços API
│       │   ├── utils/             # Utilitários
│       │   ├── layouts/           # Layouts
│       │   ├── App.vue            # Componente raiz
│       │   └── main.js            # Entry point
│       ├── public/
│       ├── index.html
│       ├── vite.config.js         # Configuração Vite
│       ├── tailwind.config.cjs    # Configuração Tailwind
│       └── package.json           # Dependências Node
├── package.json                    # Scripts do projeto
└── readme.md                       # Este arquivo
```

## 🗄️ Banco de Dados

### MongoDB Collections

O projeto utiliza MongoDB com as seguintes collections principais:

- **users** - Usuários do sistema (admin/comum)
- **products** - Catálogo de produtos
- **categories** - Categorias de produtos
- **suppliers** - Fornecedores
- **marketplaces** - Marketplaces/lojas
- **product_offers** - Ofertas de produtos por marketplace
- **reviews** - Avaliações de produtos
- **favorites** - Produtos favoritos dos usuários
- **saved_comparisons** - Histórico de comparações salvas

### Configuração do MongoDB

O sistema usa MongoDB com Doctrine ODM para mapeamento objeto-documento. A configuração padrão utiliza:
- **Host:** mongodb://127.0.0.1:27017
- **Database:** portal_tech

## ⚙️ Instalação

### Pré-requisitos

- **XAMPP** ou Apache + PHP 8.0+
- **MongoDB 4.4+** instalado e rodando
- **Composer** para gerenciar dependências PHP
- **Node.js 18+** e npm/pnpm para o frontend
- **PHP Extensions:** ext-ctype, ext-iconv, ext-mongodb

### Passos para Instalação

#### 1. Clone o repositório
```bash
git clone <repository-url>
cd product_comparison
```

#### 2. Configure o Backend

```bash
cd portal-tech

# Instale as dependências PHP
composer install

# Configure as variáveis de ambiente
# Crie um arquivo .env.local baseado no .env
```

Edite o arquivo `.env` ou crie `.env.local`:
```env
MONGODB_URL="mongodb://127.0.0.1:27017"
MONGODB_DB="portal_tech"
APP_SECRET=your-secret-key-here
JWT_SECRET=your-jwt-secret-here
```

#### 3. Configure o MongoDB

Certifique-se de que o MongoDB está rodando:
```bash
# Windows
net start MongoDB

# Ou inicie via MongoDB Compass ou outro gerenciador
```

#### 4. Seed do Banco de Dados

```bash
# Execute o comando de seed para popular o banco
php bin/console app:seed-database
```

#### 5. Configure o Frontend

```bash
cd frontend

# Instale as dependências
npm install
# ou
pnpm install
```

#### 6. Inicie os Servidores

**Backend (Symfony):**
```bash
# No diretório portal-tech/
symfony server:start
# ou
php -S localhost:8000 -t public/
```

**Frontend (Vue + Vite):**
```bash
# No diretório portal-tech/frontend/
npm run dev
# ou
pnpm dev
```

O frontend estará disponível em: `http://localhost:5173`
A API backend estará em: `http://localhost:8000`

#### 7. Mock Server (Opcional)

Para desenvolvimento com dados mockados:
```bash
cd portal-tech/frontend
npm run mock
```

## 🔑 Acesso ao Sistema

### Usuários Padrão

Os usuários são criados pelo comando de seed. Consulte os scripts de seed para credenciais de teste.

### Scripts PowerShell de Teste

O projeto inclui scripts PowerShell para testes:
- `test-auth-system.ps1` - Testa autenticação
- `test-admin-panel.ps1` - Testa funcionalidades admin
- `test-comparisons-crud.ps1` - Testa CRUD de comparações
- `set-admin-role.ps1` - Define role de admin para usuário

## 💻 Funcionalidades

### Usuários
- ✅ Registro e autenticação com JWT
- ✅ Perfis de usuário (admin/comum)
- ✅ Gerenciamento de sessão
- ✅ Proteção de rotas por role

### Produtos
- ✅ Catálogo completo de produtos
- ✅ Busca e filtros avançados
- ✅ Múltiplas ofertas por produto (diferentes marketplaces)
- ✅ Sistema de ranking de produtos
- ✅ Categorização hierárquica
- ✅ Informações de fornecedores e marketplaces

### Comparações
- ✅ Comparação lado a lado de produtos
- ✅ Salvar comparações para acesso posterior
- ✅ Histórico de comparações
- ✅ Comparação de preços entre marketplaces

### Interação
- ✅ Sistema de favoritos
- ✅ Avaliações e reviews de produtos
- ✅ Rating de produtos

### Painel Administrativo
- ✅ Gerenciamento de produtos
- ✅ Gerenciamento de usuários
- ✅ Gerenciamento de categorias
- ✅ Gerenciamento de fornecedores
- ✅ Gerenciamento de marketplaces
- ✅ Dashboard com estatísticas
- ✅ Sistema de permissões

## 🔒 Segurança

- **JWT Authentication** - Tokens seguros para autenticação
- **Password Hashing** - Senhas com hash automático (Symfony Security)
- **CORS Configuration** - Configuração adequada de CORS
- **Input Validation** - Validação com Symfony Validator
- **Route Protection** - Proteção de rotas sensíveis
- **Role-Based Access Control** - Controle de acesso baseado em roles

## 🛠️ Desenvolvimento

### Comandos Úteis

**Backend:**
```bash
# Limpar cache
php bin/console cache:clear

# Listar rotas
php bin/console debug:router

# Seed do banco
php bin/console app:seed-database

# Verificar configuração MongoDB
php test_mongo.php
```

**Frontend:**
```bash
# Desenvolvimento
npm run dev

# Build de produção
npm run build

# Preview do build
npm run preview

# Mock server
npm run mock
```

## 📡 API Endpoints

### Autenticação
- `POST /api/auth/register` - Registro de usuário
- `POST /api/auth/login` - Login
- `GET /api/auth/me` - Dados do usuário autenticado

### Produtos
- `GET /api/products` - Listar produtos
- `GET /api/products/{id}` - Detalhes do produto
- `POST /api/products` - Criar produto (admin)
- `PUT /api/products/{id}` - Atualizar produto (admin)
- `DELETE /api/products/{id}` - Deletar produto (admin)

### Comparações
- `GET /api/comparisons` - Listar comparações salvas
- `POST /api/comparisons` - Salvar comparação
- `DELETE /api/comparisons/{id}` - Deletar comparação

### Favoritos
- `GET /api/favorites` - Listar favoritos
- `POST /api/favorites` - Adicionar favorito
- `DELETE /api/favorites/{id}` - Remover favorito

### Reviews
- `GET /api/reviews` - Listar reviews
- `POST /api/reviews` - Criar review
- `PUT /api/reviews/{id}` - Atualizar review
- `DELETE /api/reviews/{id}` - Deletar review

## 🤝 Contribuindo

1. Fork o projeto
2. Crie uma branch para sua feature (`git checkout -b feature/NovaFuncionalidade`)
3. Commit suas mudanças (`git commit -m 'Adiciona nova funcionalidade'`)
4. Push para a branch (`git push origin feature/NovaFuncionalidade`)
5. Abra um Pull Request

## 📄 Licença

Este projeto é proprietário.

## 🐛 Reportar Bugs

Encontrou um bug? Por favor, abra uma issue descrevendo:
- Passos para reproduzir
- Comportamento esperado
- Comportamento atual
- Screenshots (se aplicável)
- Ambiente (SO, navegador, versão do PHP, etc.)

---

**Portal Tech** - Sistema de Comparação de Produtos © 2026
