# 🚀 Quick Start Guide

## Sistema Pronto para Usar!

Este guia mostra rapidamente como testar o sistema de autenticação implementado.

---

## ✅ O que foi implementado

### Backend (Symfony + MongoDB)
- ✅ 4 Documents: User, Favorite, Review, SavedComparison
- ✅ AuthController: registro e login com JWT
- ✅ FavoritesController: listar e criar favoritos
- ✅ ReviewsController: listar e criar reviews
- ✅ ComparisonsController: listar e criar comparações
- ✅ Password hashing com bcrypt
- ✅ JWT com firebase/php-jwt

### Frontend (Vue 3 + Vite)
- ✅ Router com rotas /dashboard, /dashboard/favorites, /dashboard/reviews
- ✅ Views: Dashboard.vue, Favorites.vue, Reviews.vue
- ✅ Auth service helper (frontend/src/services/auth.js)
- ✅ API service com interceptors JWT

---

## 🏃 Como Executar

### 1. Backend (Terminal 1)

```powershell
cd c:\xampp\htdocs\product_comparison\portal-tech
php -S localhost:8000 -t public
```

Ou com Symfony CLI:
```powershell
symfony server:start
```

### 2. Frontend (Terminal 2)

```powershell
cd c:\xampp\htdocs\product_comparison\portal-tech\frontend
npm run dev
```

O frontend estará disponível em **http://localhost:5173** (ou outra porta se 5173 estiver em uso).

---

## 🧪 Testar Manualmente

### Opção 1: Via Browser Console

1. Abra http://localhost:5173 no navegador
2. Abra DevTools (F12) → Console
3. Execute:

```javascript
// Importar funções (se estiverem expostas globalmente, ou usar via Vue DevTools)
// Ou navegar para /dashboard/favorites e testar via Vue Devtools

// Exemplo direto com fetch:
// 1. Registrar
fetch('http://localhost:8000/api/register', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({
    email: 'teste@teste.com',
    password: 'senha123',
    name: 'Usuário Teste'
  })
}).then(r => r.json()).then(console.log)

// 2. Login
fetch('http://localhost:8000/api/login', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({
    email: 'teste@teste.com',
    password: 'senha123'
  })
}).then(r => r.json()).then(data => {
  console.log(data)
  localStorage.setItem('token', data.token)
})

// 3. Listar favoritos (após login)
fetch('http://localhost:8000/api/favorites', {
  headers: { 
    'Authorization': 'Bearer ' + localStorage.getItem('token')
  }
}).then(r => r.json()).then(console.log)
```

### Opção 2: Via Postman / Insomnia

#### 1. Registrar
```
POST http://localhost:8000/api/register
Content-Type: application/json

{
  "email": "teste@teste.com",
  "password": "senha123",
  "name": "Usuário Teste"
}
```

#### 2. Login
```
POST http://localhost:8000/api/login
Content-Type: application/json

{
  "email": "teste@teste.com",
  "password": "senha123"
}
```

Copie o `token` da resposta.

#### 3. Listar Favoritos
```
GET http://localhost:8000/api/favorites
Authorization: Bearer <SEU_TOKEN_AQUI>
```

#### 4. Adicionar Favorito
```
POST http://localhost:8000/api/favorites
Authorization: Bearer <SEU_TOKEN_AQUI>
Content-Type: application/json

{
  "productId": "65f1234567890abcdef"
}
```
*Nota: Use um ID de produto válido do seu banco MongoDB*

#### 5. Criar Review
```
POST http://localhost:8000/api/reviews
Authorization: Bearer <SEU_TOKEN_AQUI>
Content-Type: application/json

{
  "productId": "65f1234567890abcdef",
  "rating": 5,
  "comment": "Produto excelente!"
}
```

---

## 📍 Rotas Frontend Implementadas

Navegue diretamente no browser:

- **http://localhost:5173/dashboard** - Dashboard principal com links
- **http://localhost:5173/dashboard/favorites** - Lista de favoritos (requer token)
- **http://localhost:5173/dashboard/reviews** - Lista de reviews (requer token)

---

## 🔍 Verificar Rotas Backend

Liste todas as rotas da API:

```powershell
cd c:\xampp\htdocs\product_comparison\portal-tech
php bin/console debug:router | Select-String -Pattern "api_"
```

Resultado esperado:
```
api_register             POST     /api/register
api_login                POST     /api/login
api_comparisons_list     GET      /api/comparisons
api_comparisons_create   POST     /api/comparisons
api_favorites_list       GET      /api/favorites
api_favorites_create     POST     /api/favorites
api_reviews_list         GET      /api/reviews
api_reviews_create       POST     /api/reviews
```

---

## 🗂️ MongoDB - Verificar Dados

Para verificar se os dados estão sendo salvos no MongoDB:

```powershell
# Abrir MongoDB shell
mongo portal_tech

# Ver usuários
db.users.find().pretty()

# Ver favoritos
db.favorites.find().pretty()

# Ver reviews
db.reviews.find().pretty()

# Ver comparações
db.saved_comparisons.find().pretty()
```

---

## ⚠️ Troubleshooting

### Erro: "Unauthorized" ao acessar endpoints protegidos
- Verifique se fez login e obteve o token
- Verifique se o token está no header: `Authorization: Bearer <token>`
- No frontend, o token deve estar em localStorage

### Erro: "Product not found"
- Certifique-se de que o productId existe no MongoDB
- Use `db.products.find().pretty()` no MongoDB shell para ver IDs válidos

### Backend não inicia
- Verifique se a porta 8000 está livre
- Certifique-se que MongoDB está rodando (XAMPP ou serviço)
- Verifique erros no terminal

### Frontend não carrega
- Verifique se `npm install` foi executado
- Limpe cache: `npm cache clean --force` e reinstale
- Verifique porta disponível (Vite usa 5173, 5174, etc)

---

## 📦 Estrutura de Arquivos Criados

```
portal-tech/
├── src/
│   ├── Controller/Api/
│   │   ├── AuthController.php        ✨ NOVO
│   │   ├── FavoritesController.php   ✨ NOVO
│   │   ├── ReviewsController.php     ✨ NOVO
│   │   └── ComparisonsController.php ✨ NOVO
│   └── Document/
│       ├── User.php                   ✅ Atualizado (já existia)
│       ├── Favorite.php               ✨ NOVO
│       ├── Review.php                 ✨ NOVO
│       └── SavedComparison.php        ✨ NOVO
├── frontend/
│   └── src/
│       ├── router/
│       │   └── index.js               ✅ Atualizado
│       ├── services/
│       │   └── auth.js                ✨ NOVO
│       └── views/
│           ├── Dashboard.vue          ✨ NOVO
│           ├── Favorites.vue          ✨ NOVO
│           └── Reviews.vue            ✨ NOVO
└── composer.json                      ✅ Atualizado (firebase/php-jwt)
```

---

## 🎯 Próximas Funcionalidades Recomendadas

1. **Login/Register UI**: Criar páginas bonitas de login e registro
2. **Route Guards**: Proteger rotas que requerem autenticação
3. **User Menu**: Menu no canto superior direito com nome e logout
4. **Product Integration**: Buscar dados completos dos produtos nas listas
5. **Delete Operations**: Botões para remover favoritos e reviews
6. **Edit Review**: Permitir editar reviews já criadas
7. **Paginação**: Adicionar paginação nas listas longas
8. **Toast Notifications**: Feedback visual para ações (sucesso/erro)

---

## ✨ Sistema Completo e Pronto!

Todos os requisitos da INSTRUCTIONS - 4.md foram implementados:
- ✅ Autenticação (Registro, Login, JWT, Hash password)
- ✅ Documents Adicionais (Favorite, Review, SavedComparison)
- ✅ Frontend com rotas /dashboard, /dashboard/favorites, /dashboard/reviews

**O sistema está funcional e pronto para uso!** 🚀
