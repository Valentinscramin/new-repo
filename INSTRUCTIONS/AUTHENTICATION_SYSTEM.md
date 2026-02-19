# Sistema de Autenticação e Área do Utilizador

## ✅ Implementação Completa

Este documento descreve o sistema de autenticação com MongoDB implementado para o projeto Portal Tech.

---

## 📁 Estrutura Implementada

### Backend (PHP Symfony)

#### Documents MongoDB
- **User** (`src/Document/User.php`) - Utilizador com password hash
- **Favorite** (`src/Document/Favorite.php`) - Favoritos por utilizador
- **Review** (`src/Document/Review.php`) - Reviews de produtos
- **SavedComparison** (`src/Document/SavedComparison.php`) - Comparações guardadas

#### Controllers API
- **AuthController** (`src/Controller/Api/AuthController.php`)
  - `POST /api/register` - Registo de utilizador
  - `POST /api/login` - Login com JWT
  
- **FavoritesController** (`src/Controller/Api/FavoritesController.php`)
  - `GET /api/favorites` - Listar favoritos do utilizador autenticado
  - `POST /api/favorites` - Adicionar produto aos favoritos
  
- **ReviewsController** (`src/Controller/Api/ReviewsController.php`)
  - `GET /api/reviews` - Listar reviews do utilizador autenticado
  - `POST /api/reviews` - Criar review de produto
  
- **ComparisonsController** (`src/Controller/Api/ComparisonsController.php`)
  - `GET /api/comparisons` - Listar comparações guardadas
  - `POST /api/comparisons` - Guardar nova comparação

### Frontend (Vue.js)

#### Services
- **auth.js** (`frontend/src/services/auth.js`) - Helper functions para autenticação
- **api.js** - Já existente com interceptors JWT

#### Views
- **Dashboard** (`frontend/src/views/Dashboard.vue`) - `/dashboard`
- **Favorites** (`frontend/src/views/Favorites.vue`) - `/dashboard/favorites`
- **Reviews** (`frontend/src/views/Reviews.vue`) - `/dashboard/reviews`

#### Router
- Rotas adicionadas em `frontend/src/router/index.js`

---

## 🔐 Autenticação

### Como Funciona

1. **Registo**: Password é hasheada com `password_hash()` (bcrypt)
2. **Login**: Retorna JWT token válido por 7 dias
3. **Proteção**: Endpoints verificam token no header `Authorization: Bearer <token>`
4. **Frontend**: Interceptor automático anexa token em todas as chamadas da API

### JWT Payload
```json
{
  "sub": "user_id",
  "email": "user@example.com",
  "iat": 1234567890,
  "exp": 1234567890
}
```

---

## 🚀 Como Usar

### Backend

1. **Instalar dependências** (já feito):
```bash
cd portal-tech
composer install
```

2. **Iniciar servidor Symfony**:
```bash
php -S localhost:8000 -t public
# ou
symfony server:start
```

### Frontend

1. **Instalar dependências**:
```bash
cd portal-tech/frontend
npm install
```

2. **Iniciar dev server**:
```bash
npm run dev
```

---

## 📡 Endpoints API

### Autenticação

#### Registar Utilizador
```http
POST /api/register
Content-Type: application/json

{
  "email": "user@example.com",
  "password": "senha123",
  "name": "John Doe"
}
```

**Resposta** (201):
```json
{
  "id": "65f1234567890abcdef",
  "email": "user@example.com"
}
```

#### Login
```http
POST /api/login
Content-Type: application/json

{
  "email": "user@example.com",
  "password": "senha123"
}
```

**Resposta** (200):
```json
{
  "token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
  "user": {
    "id": "65f1234567890abcdef",
    "email": "user@example.com",
    "name": "John Doe"
  }
}
```

### Favoritos (Autenticado)

#### Listar Favoritos
```http
GET /api/favorites
Authorization: Bearer <token>
```

**Resposta**:
```json
[
  {
    "id": "65f1234567890abcdef",
    "product": "65f0987654321fedcba"
  }
]
```

#### Adicionar Favorito
```http
POST /api/favorites
Authorization: Bearer <token>
Content-Type: application/json

{
  "productId": "65f0987654321fedcba"
}
```

### Reviews (Autenticado)

#### Listar Reviews
```http
GET /api/reviews
Authorization: Bearer <token>
```

**Resposta**:
```json
[
  {
    "id": "65f1234567890abcdef",
    "product": "65f0987654321fedcba",
    "rating": 5,
    "comment": "Excelente produto!",
    "createdAt": "2026-02-14T12:30:00+00:00"
  }
]
```

#### Criar Review
```http
POST /api/reviews
Authorization: Bearer <token>
Content-Type: application/json

{
  "productId": "65f0987654321fedcba",
  "rating": 5,
  "comment": "Excelente produto!"
}
```

### Comparações (Autenticado)

#### Listar Comparações
```http
GET /api/comparisons
Authorization: Bearer <token>
```

#### Guardar Comparação
```http
POST /api/comparisons
Authorization: Bearer <token>
Content-Type: application/json

{
  "productA": "65f0987654321fedcba",
  "productB": "65f1111111111111111"
}
```

---

## 🧪 Testar

### Exemplo com cURL

```bash
# 1. Registar
curl -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{"email":"test@test.com","password":"senha123","name":"Test User"}'

# 2. Login
TOKEN=$(curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"test@test.com","password":"senha123"}' | jq -r .token)

# 3. Listar favoritos
curl http://localhost:8000/api/favorites \
  -H "Authorization: Bearer $TOKEN"
```

### Exemplo no Frontend (Vue)

```javascript
import { login, logout } from '@/services/auth';
import api from '@/services/api';

// Login
const { token, user } = await login({ 
  email: 'test@test.com', 
  password: 'senha123' 
});

// Adicionar favorito (token anexado automaticamente)
await api.post('/favorites', { productId: '65f123...' });

// Listar favoritos
const { data } = await api.get('/favorites');

// Logout
logout();
```

---

## 📝 Notas Técnicas

### Segurança
- Passwords hasheadas com bcrypt (PASSWORD_BCRYPT)
- JWT assinado com APP_SECRET do .env
- Tokens expiram em 7 dias
- Verificação de token em cada endpoint protegido

### MongoDB
- Usa Doctrine ODM
- Referências entre documentos (ReferenceOne)
- Índice único em User.email
- Timestamps automáticos (createdAt)

### Frontend
- Axios interceptors para anexar token automaticamente
- Redirect para /login em 401 (não autenticado)
- Token guardado em localStorage

---

## ✨ Próximos Passos Sugeridos

1. **Login/Register UI**: Criar páginas de login e registo
2. **Route Guards**: Proteger rotas /dashboard/* no Vue Router
3. **User Profile**: Página de perfil do utilizador
4. **Delete Endpoints**: Permitir remover favoritos/reviews
5. **Product Details**: Expandir dados do produto nas respostas da API
6. **Validação**: Adicionar validação mais robusta (Symfony Validator)
7. **Refresh Tokens**: Implementar refresh token para sessões longas
8. **Email Verification**: Verificar email no registo

---

## 🎯 Conclusão

Sistema de autenticação totalmente funcional com:
- ✅ Registo e Login com JWT
- ✅ Password hashing seguro
- ✅ 3 Documents adicionais (Favorite, Review, SavedComparison)
- ✅ 7 Endpoints API protegidos
- ✅ 3 Páginas frontend (/dashboard, /dashboard/favorites, /dashboard/reviews)
- ✅ Auth service helpers frontend

Tudo pronto para uso e extensão futura! 🚀
