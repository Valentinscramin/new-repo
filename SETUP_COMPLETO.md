# Portal Tech - Comparador de Produtos

## Setup Completo - INSTRUCTIONS 1

### ✅ Backend (Symfony + MongoDB)

**Localização:** `portal-tech/`

#### Dependências Instaladas:
- Symfony 6.0 (skeleton)
- Security Bundle
- Validator
- Serializer
- Property Access
- Nelmio CORS Bundle
- Doctrine MongoDB ODM Bundle
- Maker Bundle

#### Configuração:

**MongoDB:**
- URL: `mongodb://127.0.0.1:27017`
- Database: `portal_tech`

**Estrutura de Pastas Criadas:**
- `src/Document` - Documents do MongoDB
- `src/Repository` - Repositories
- `src/Controller/Api` - Controllers da API
- `src/Service` - Services
- `src/Enum` - Enumerations

#### ⚠️ IMPORTANTE - Extensão MongoDB PHP

Para o MongoDB funcionar, é necessário instalar a extensão `mongodb` para PHP:

**No XAMPP (Windows):**

1. Baixe a DLL do MongoDB para PHP 8.0:
   - Acesse: https://pecl.php.net/package/mongodb
   - Baixe a versão compatível com PHP 8.0 Thread Safe (TS) x64

2. Extraia `php_mongodb.dll` para `C:\xampp\php\ext\`

3. Edite `C:\xampp\php\php.ini` e adicione:
   ```ini
   extension=mongodb
   ```

4. Reinicie o Apache no XAMPP

5. Verifique se está instalado:
   ```bash
   php -m | Select-String -Pattern "mongodb"
   ```

#### Executar Backend:

```bash
cd c:\xampp\htdocs\product_comparison\portal-tech
symfony server:start
```

Ou com PHP:
```bash
php -S localhost:8000 -t public
```

---

### ✅ Frontend (Vue 3)

**Localização:** `portal-tech/frontend/`

#### Dependências Instaladas:
- Vue 3
- Vue Router
- Pinia
- Axios

#### Estrutura de Pastas Criadas:
- `src/layouts` - Layouts da aplicação
- `src/views` - Views/Páginas
- `src/components` - Componentes reutilizáveis
- `src/services` - Serviços (API, etc)
- `src/stores` - Pinia stores
- `src/router` - Configuração de rotas

#### Arquivos Configurados:
- `services/api.js` - Cliente Axios com interceptors
- `router/index.js` - Vue Router configurado
- `stores/index.js` - Pinia configurado
- `views/HomeView.vue` - Página inicial

#### Executar Frontend:

```bash
cd c:\xampp\htdocs\product_comparison\portal-tech\frontend
npm run dev
```

Acesse: `http://localhost:5173`

---

### ✅ CORS Configurado

O backend está configurado para aceitar requisições do frontend:
- Padrão: `localhost` e `127.0.0.1` em qualquer porta
- Métodos: GET, POST, PUT, PATCH, DELETE, OPTIONS
- Headers: Content-Type, Authorization

---

## Próximos Passos

1. **Instalar extensão MongoDB PHP** (obrigatório)
2. **Instalar e iniciar MongoDB** (`mongod`)
3. Executar **INSTRUCTIONS 2** - Criar Documents e Seed
4. Executar **INSTRUCTIONS 3** - Criar Layout Frontend
5. Executar **INSTRUCTIONS 4** - Área do Utilizador
6. Executar **INSTRUCTIONS 5** - Admin Panel

---

## Tecnologias

- **Backend:** Symfony 6.0, MongoDB, Doctrine ODM
- **Frontend:** Vue 3, Vite, Router, Pinia, Axios
- **API:** RESTful com autenticação JWT (a implementar)
