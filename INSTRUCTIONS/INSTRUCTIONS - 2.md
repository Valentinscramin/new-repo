# INSTRUCTIONS.md
## Estrutura Base de Dados MongoDB

Objetivo:
Criar estrutura inicial utilizando Doctrine MongoDB ODM.

---

## 1. Criar Documents

Criar Documents em src/Document:

### User
- id
- name
- email (unique)
- password
- role (USER | ADMIN)
- createdAt

### Category
- id
- name
- slug
- createdAt

### Marketplace
- id
- name
- slug
- affiliateBaseUrl
- logo
- createdAt

### Supplier
- id
- name
- website
- createdAt

### Product
- id
- name
- slug
- description
- specifications (collection)
- category (ReferenceOne)
- supplier (ReferenceOne)
- createdAt

### ProductOffer
- id
- product (ReferenceOne)
- marketplace (ReferenceOne)
- price
- affiliateLink
- lastUpdatedAt

---

## 2. Criar Índices

Adicionar índices nos Documents:

- unique index em user.email
- unique index em category.slug
- unique index em marketplace.slug
- unique index em product.slug
- index em productOffer.price

---

## 3. Criar Seed Manual (Command)

Criar Command:

php bin/console make:command SeedDatabaseCommand

Inserir:
- 2 categorias
- 3 marketplaces
- 2 suppliers
- 5 produtos mock
- 1 admin user

Persistir com DocumentManager.

Executar:
php bin/console app:seed-database

---

Objetivo final:
Banco MongoDB populado e estruturado para expansão.
