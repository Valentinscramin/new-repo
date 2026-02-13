# INSTRUCTIONS.md
## Área do Utilizador

Objetivo:
Criar sistema autenticado com MongoDB.

---

## 1. Autenticação

Implementar:
- Registro
- Login
- JWT
- Hash password

Salvar utilizadores como Document.

---

## 2. Criar Documents Adicionais

### Favorite
- id
- user (ReferenceOne)
- product (ReferenceOne)

### Review
- id
- user (ReferenceOne)
- product (ReferenceOne)
- rating
- comment
- createdAt

### SavedComparison
- id
- user (ReferenceOne)
- productA (ReferenceOne)
- productB (ReferenceOne)
- createdAt

---

## 3. Frontend

Rotas:
- /dashboard
- /dashboard/favorites
- /dashboard/reviews

---

Objetivo:
Sistema comunitário funcional usando MongoDB.
