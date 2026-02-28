# instructions.md

Projeto: **TechCompare – Comparador Gamer & Office para Nômades Digitais**
Stack: **Symfony 7 + Vue 3 + MongoDB + OpenAI (ChatGPT API)**

---

## 1. Visão Geral do Projeto

O **TechCompare** é um comparador inteligente de produtos focado em:

* 🎮 Mundo Tech Gamer
* 💻 Equipamentos para escritório
* 🌍 Nômades digitais

O sistema permite que o usuário:

1. Insira **3 links de produtos**
2. O sistema:

   * Carrega o HTML em background
   * Salva temporariamente
   * Envia o conteúdo para o ChatGPT
   * Extrai informações técnicas estruturadas
3. Gera uma **tabela de comparação inteligente**
4. Calcula um **Score de Custo-Benefício**
5. Destaca claramente o **produto vencedor**
6. Permite login/registro
7. Mantém histórico de comparações no dashboard

---

# 2. Arquitetura Geral

## Backend

* Symfony 7
* API REST
* JWT Authentication
* MongoDB (Doctrine MongoDB ODM)
* Serviço de integração com OpenAI
* Processamento assíncrono (Messenger + Worker)

## Frontend

* Vue 3 (Composition API)
* Vue Router
* Pinia (state management)
* Axios
* TailwindCSS (tema escuro estilo Netflix gamer)

## Banco de Dados

* MongoDB
* Collections:

  * users
  * comparisons
  * products

---

# 3. Estrutura de Páginas (Apenas 2 Públicas + Auth + Dashboard)

## 3.1 Landing Page (Home)

Estilo:

* Dark mode
* Layout estilo Netflix
* Hero com call-to-action
* Foco gamer + minimalista tech

### Seções:

1. Hero

   * Título forte:

     > "Compare. Analise. Escolha o Melhor Setup."
   * Subtitulo:

     > Comparador inteligente para gamers e nômades digitais.
   * Botão:

     * **"Começar Agora"**
     * Redireciona para registro se não logado

2. Formulário de Comparação

   * 3 campos de input:

     * Link Produto 1
     * Link Produto 2
     * Link Produto 3
   * Botão: "Comparar Agora"

3. Call to Action

   * "Crie sua conta e salve suas comparações"
   * Botão: Registrar

---

## 3.2 Página de Resultado da Comparação

Mostra:

### Tabela de Comparação

Colunas:

* Produto 1
* Produto 2
* Produto 3

Linhas:

* Marca
* Modelo
* Preço
* Processador
* GPU
* RAM
* Armazenamento
* Tela
* Peso
* Autonomia de bateria
* Conectividade
* Avaliação geral
* Score Custo-Benefício

---

### Destaque do Vencedor

* Card destacado
* Glow verde
* Badge:

  > 🏆 Melhor Escolha

Explicação textual:

"Baseado em performance, preço e especificações técnicas, este produto oferece o melhor equilíbrio entre custo e benefício."

---

# 4. Fluxo Técnico da Comparação

## 4.1 Processo Backend

1. Usuário envia 3 URLs
2. Backend:

   * Valida URLs
   * Cria documento de comparação com status: "processing"
   * Dispara Job assíncrono

---

## 4.2 Worker de Processamento

Para cada URL:

### Passo 1 – Download do HTML

* Usar Symfony HttpClient
* Salvar temporariamente em `/var/tmp/products`

### Passo 2 – Limpeza do HTML

* Remover scripts
* Extrair conteúdo principal
* Limitar tamanho de tokens

### Passo 3 – Envio para OpenAI

Prompt para extração:

```text
Você é um especialista em hardware gamer e equipamentos para nômades digitais.

Extraia as seguintes informações do produto:

- Nome do Produto
- Marca
- Modelo
- Preço (moeda e valor)
- Categoria (notebook, monitor, teclado, etc.)
- Especificações técnicas completas
- Pontos fortes
- Pontos fracos
- Público ideal

Retorne em JSON estruturado.
```

---

## 4.3 Cálculo do Score

Criar algoritmo interno:

### Fórmula Base:

```
Score = (Performance * PesoPerformance)
      + (QualidadeConstrucao * PesoQualidade)
      + (Mobilidade * PesoMobilidade)
      - (PrecoNormalizado * PesoPreco)
```

Critérios:

### Gamer:

* GPU
* CPU
* RAM
* Refresh Rate

### Nômade Digital:

* Peso
* Bateria
* Portabilidade
* Conectividade

Normalizar preço entre os 3 produtos.

Score final entre 0 e 100.

Produto com maior score:

* Flag winner = true

---

# 5. Estrutura MongoDB

## User

```json
{
  "_id": ObjectId,
  "email": "string",
  "password": "hashed",
  "createdAt": ISODate
}
```

---

## Comparison

```json
{
  "_id": ObjectId,
  "userId": ObjectId,
  "products": [ProductReference],
  "winnerProductId": ObjectId,
  "status": "processing | completed",
  "createdAt": ISODate
}
```

---

## Product

```json
{
  "_id": ObjectId,
  "comparisonId": ObjectId,
  "name": "string",
  "brand": "string",
  "price": number,
  "currency": "string",
  "specs": {},
  "strengths": [],
  "weaknesses": [],
  "score": number,
  "rawExtraction": {}
}
```

---

# 6. Sistema de Autenticação

## Registro

* Email único
* Senha hash (Symfony PasswordHasher)
* Após registro:

  * Login automático
  * Redireciona para Dashboard

## Login

* JWT Token
* Armazenado no localStorage
* Axios interceptor

---

# 7. Dashboard

Após login:

Mostra:

* Lista de comparações
* Status
* Data
* Botão "Ver Detalhes"

Ao clicar:

* Abre página de comparação completa

---

# 8. Frontend Vue 3 Estrutura

```
src/
  views/
    Home.vue
    CompareResult.vue
    Login.vue
    Register.vue
    Dashboard.vue
  components/
    ProductInput.vue
    ComparisonTable.vue
    WinnerCard.vue
```

---

# 9. Requisitos de UI/UX

Tema:

* Fundo preto (#0f0f0f)
* Cards cinza escuro
* Verde neon para destaque
* Tipografia moderna
* Layout responsivo

Sensação:

* Plataforma gamer premium
* Minimalista
* Rápida

---

# 10. Segurança

* Sanitizar HTML
* Limitar tamanho de input
* Rate limit na API
* Validar URLs
* Proteção contra XSS

---

# 11. Extras Importantes

* Mostrar loading animado:

  > "Analisando hardware e performance..."

* Caso ChatGPT falhe:

  * Status = failed
  * Permitir retry

---

# 12. Resultado Esperado

Sistema completo com:

✔ Landing estilo Netflix
✔ Comparação inteligente
✔ Score automatizado
✔ Vencedor destacado claramente
✔ Registro/Login
✔ Dashboard com histórico
✔ Processamento assíncrono
✔ MongoDB estruturado
✔ Integração com ChatGPT

---

# Objetivo Final

Criar um comparador premium para:

🎮 Gamers exigentes
💼 Profissionais remotos
🌍 Nômades digitais

Foco total em **melhor custo-benefício com inteligência artificial**.
