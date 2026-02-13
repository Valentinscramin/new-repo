# INSTRUCTIONS.md
## Setup Inicial – Symfony + MongoDB + Vue 3

Objetivo:
Criar estrutura base utilizando Symfony com MongoDB (Doctrine ODM) e Vue 3 como frontend desacoplado.
Diretorio principal é dentro do product_comparison
---

## 1. Criar Projeto Symfony

composer create-project symfony/skeleton portal-tech
cd portal-tech

---

## 2. Instalar Dependências Principais

composer require symfony/security-bundle
composer require symfony/validator
composer require symfony/serializer
composer require symfony/property-access
composer require nelmio/cors-bundle
composer require doctrine/mongodb-odm-bundle
composer require symfony/maker-bundle --dev

---

## 3. Configurar MongoDB

No .env:

MONGODB_URL="mongodb://127.0.0.1:27017"
MONGODB_DB="portal_tech"

---

## 4. Configurar doctrine_mongodb.yaml

config/packages/doctrine_mongodb.yaml:

doctrine_mongodb:
    connections:
        default:
            server: '%env(MONGODB_URL)%'
    default_database: '%env(MONGODB_DB)%'
    document_managers:
        default:
            auto_mapping: true

---

## 5. Criar Estrutura Base de Pastas

src/Document
src/Repository
src/Controller/Api
src/Service
src/Enum

---

## 6. Criar Projeto Vue 3

npm create vue@latest frontend

Selecionar:
- Router
- Pinia
- ESLint
- Prettier

cd frontend
npm install
npm install axios

---

## 7. Configurar CORS

Permitir acesso do frontend (localhost:5173).

---

## 8. Executar Projeto

Backend:
symfony server:start

Frontend:
npm run dev

---

Objetivo final:
Backend conectado ao MongoDB
Frontend comunicando via API REST
