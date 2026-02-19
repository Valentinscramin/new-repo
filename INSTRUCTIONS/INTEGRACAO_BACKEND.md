# Integração Backend PHP + Frontend Vue

## ✅ Alterações Realizadas

### Backend (Symfony + MongoDB)

1. **Criado HomeController.php** em `portal-tech/src/Controller/Api/HomeController.php`
   - Endpoint: `GET /api/home`
   - Busca produtos do MongoDB
   - Retorna dados formatados para a página inicial:
     - `sampleProducts`: Top 3 produtos com imagens e avaliações
     - `comparisonRows`: Tabela de comparação de especificações
     - `rankingItems`: Lista de produtos para o ranking
     - `bestPrices`: Melhores preços de marketplaces

2. **Configuração CORS**
   - Já configurado em `.env` para aceitar localhost
   - Permite requisições do frontend (porta 5173)

### Frontend (Vue 3 + Vite)

1. **Atualizado `src/services/api.js`**
   - Adicionado função `getHomeData()` que chama `/api/home`
   - Configurado com baseURL `http://localhost:8000/api`

2. **Atualizado `src/views/HomeView.vue`**
   - Chama primeiro o backend PHP (`getHomeDataApi`)
   - Faz fallback para `mockApi` se o backend não estiver disponível
   - Adicionado suporte para `bestPrices` (seção "Onde Está Mais Barato?")
   - Dados são carregados dinamicamente do backend

3. **Atualizado `src/services/mockApi.js`**
   - Adicionado campo `bestPrices` para manter compatibilidade

## 🚀 Como Testar

### 1. Iniciar o Backend PHP (Symfony)

```powershell
cd portal-tech
php -S localhost:8000 -t public
```

O backend estará disponível em `http://localhost:8000`

### 2. Iniciar o Frontend (Vue + Vite)

Em outro terminal:

```powershell
cd portal-tech/frontend
npm run dev
```

O frontend estará disponível em `http://localhost:5173`

### 3. Verificar os Dados

1. Acesse `http://localhost:5173` no navegador
2. A página inicial deve carregar dados do backend PHP
3. Você verá:
   - 3 cards de produtos no hero section
   - Tabela de comparação de especificações
   - Lista de rankings
   - Seção "Onde Está Mais Barato?" com preços dinâmicos

### 4. Testar o Fallback (Opcional)

Para testar se o fallback ao mock funciona:

1. Pare o backend PHP (Ctrl+C)
2. Recarregue a página no navegador
3. Os dados de exemplo (mock) devem ser exibidos

## 📊 Populando o Banco de Dados

Se o banco MongoDB estiver vazio, você pode usar o seed command:

```powershell
cd portal-tech
php bin/console app:seed-database
```

Este comando irá popular o banco com dados de exemplo de produtos, categorias, marketplaces e ofertas.

## 🔍 Verificar Endpoint Diretamente

Para testar o endpoint da API diretamente:

```powershell
# No navegador ou curl
curl http://localhost:8000/api/home
```

Deve retornar um JSON com a estrutura:
```json
{
  "sampleProducts": [...],
  "comparisonRows": [...],
  "rankingItems": [...],
  "bestPrices": [...]
}
```

## 📝 Próximos Passos

- [ ] Adicionar mais endpoints (produtos individuais, categorias, etc.)
- [ ] Implementar sistema de busca
- [ ] Adicionar filtros e ordenação
- [ ] Melhorar sistema de avaliações (atualmente é mockado)
- [ ] Implementar cache para melhor performance
- [ ] Adicionar imagens reais dos produtos
