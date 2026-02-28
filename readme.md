# TechCompare - Comparador Gamer & Office para Nômades Digitais

Um comparador inteligente de produtos focado em equipamentos tech para gamers e nômades digitais, powered by AI.

## 🚀 Stack Tecnológica

### Backend
- **Symfony 5.4** (LTS) - PHP Framework
- **MySQL/MariaDB** - Database
- **Doctrine ORM** - Database abstraction
- **JWT Authentication** - Segurança
- **Symfony Messenger** - Processamento assíncrono
- **OpenAI API** - Extração inteligente de dados

### Frontend
- **Vue 3** - Framework JavaScript
- **TypeScript** - Type safety
- **Pinia** - State management
- **Vue Router** - Routing
- **TailwindCSS** - Styling
- **Axios** - HTTP Client

## 📋 Pré-requisitos

- XAMPP (Apache + MySQL + PHP 8.0+)
- Composer
- Node.js e NPM
- OpenAI API Key

## 🔧 Instalação

### 1. Configurar Backend

```bash
cd backend

# Instalar dependências (já feito)
# composer install

# Configurar banco de dados
# 1. Iniciar MySQL no XAMPP Control Panel
# 2. Criar banco de dados
php bin/console doctrine:database:create

# 3. Criar tabelas
php bin/console doctrine:migrations:migrate

# Configurar OpenAI API Key
# Editar .env e adicionar sua chave:
# OPENAI_API_KEY=sk-...

# Configurar servidor
# Iniciar Apache no XAMPP
```

### 2. Configurar Frontend

```bash
cd frontend

# Instalar dependências (já feito)
# npm install

# Iniciar servidor de desenvolvimento
npm run dev
```

### 3. Iniciar Worker Assíncrono

Para processar comparações em background:

```bash
cd backend
php bin/console messenger:consume async -vv
```

## 🎯 Funcionalidades

### ✅ Implementado

- **Landing Page** com hero estilo Netflix
- **Sistema de Autenticação**
  - Registro de usuários
  - Login com JWT
  - Proteção de rotas
- **Comparação de Produtos**
  - Formulário para 3 URLs
  - Scraping assíncrono
  - Extração com OpenAI
  - Cálculo automático de score
- **Dashboard do Usuário**
  - Histórico de comparações
  - Status de processamento
- **Resultado da Comparação**
  - Tabela comparativa
  - Destaque do vencedor
  - Pontos fortes/fracos
  - Score de custo-benefício

## 📁 Estrutura do Projeto

```
product_comparison/
├── backend/                 # Symfony Backend
│   ├── config/             # Configurações
│   ├── src/
│   │   ├── Controller/     # API Controllers
│   │   ├── Entity/         # Database Entities
│   │   ├── Repository/     # Data Repositories
│   │   ├── Service/        # Business Logic
│   │   ├── Message/        # Async Messages
│   │   └── MessageHandler/ # Message Handlers
│   └── .env               # Environment config
│
└── frontend/               # Vue 3 Frontend
    ├── src/
    │   ├── api/           # API Client
    │   ├── stores/        # Pinia Stores
    │   ├── views/         # Page Components
    │   ├── router/        # Route Configuration
    │   └── assets/        # CSS & Assets
    └── tailwind.config.js # Tailwind Config
```

## 🔑 Configuração do Banco de Dados

1. Abra XAMPP Control Panel
2. Inicie MySQL
3. Acesse phpMyAdmin (http://localhost/phpmyadmin)
4. O banco `techcompare` será criado automaticamente

## 🎨 Tema Dark

A aplicação usa um tema escuro inspirado em plataformas gamer:
- Background: `#0f0f0f`
- Cards: `#1a1a1a`
- Accent: Verde neon `#00ff88`
- Secondary: Azul neon `#00d4ff`

## 🔐 Segurança

- Senhas hasheadas com bcrypt
- JWT para autenticação stateless
- CORS configurado
- Validação de URLs
- Sanitização de HTML

## 📡 API Endpoints

### Autenticação
- `POST /api/register` - Registrar novo usuário
- `POST /api/login` - Login
- `GET /api/me` - Dados do usuário autenticado

### Comparações
- `POST /api/comparisons` - Criar nova comparação
- `GET /api/comparisons` - Listar comparações do usuário
- `GET /api/comparisons/{id}` - Ver comparação específica

## 🚀 Processos

### Fluxo de Comparação

1. Usuário submete 3 URLs
2. Backend cria comparação com status "processing"
3. Message assíncrona é disparada
4. Worker processa cada URL:
   - Faz scraping do HTML
   - Envia para OpenAI para extração
   - Calcula score baseado em specs
5. Define produto vencedor
6. Atualiza status para "completed"

### Cálculo de Score

O score (0-100) é calculado com base em:
- **Performance** (CPU, GPU, RAM, Storage)
- **Qualidade** (Materiais, tela, build)
- **Mobilidade** (Peso, bateria, portabilidade)
- **Preço** (Custo-benefício)

## 🛠️ Comandos Úteis

### Backend
```bash
# Criar uma migration
php bin/console make:migration

# Executar migrations
php bin/console doctrine:migrations:migrate

# Criar controller
php bin/console make:controller

# Limpar cache
php bin/console cache:clear
```

### Frontend
```bash
# Build para produção
npm run build

# Preview build
npm run preview

# Type check
npm run type-check
```

## 🐛 Troubleshooting

### Backend não conecta ao MySQL
- Verifique se MySQL está rodando no XAMPP
- Confirme credenciais no `.env`: `DATABASE_URL`

### Worker não processa comparações
- Certifique-se de que o worker está rodando
- Verifique logs: `var/log/`

### OpenAI API erro
- Verifique se a API key está correta no `.env`
- Confirme que tem créditos disponíveis

### CORS errors
- Configure `nelmio_cors` no backend
- Verifique se o frontend está acessando a URL correta

## 📝 Próximos Passos

- [ ] Adicionar mais categorias de produtos
- [ ] Implementar filtros avançados no dashboard
- [ ] Adicionar gráficos de comparação
- [ ] Sistema de notificações
- [ ] Export de comparações em PDF
- [ ] Compartilhamento de comparações
- [ ] Cache de resultados
- [ ] Testes unitários e E2E

## 📄 Licença

Este projeto é de uso educacional e demonstrativo.

## 👨‍💻 Desenvolvimento

Desenvolvido com ❤️ para gamers e nômades digitais.

---

**Note**: Este projeto foi criado com base nas especificações do arquivo `INSTRUCTIONS.MD`. Para mais detalhes sobre a arquitetura e requisitos, consulte esse arquivo.
