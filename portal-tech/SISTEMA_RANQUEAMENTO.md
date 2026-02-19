# Sistema de Ranqueamento de Produtos

## Visão Geral

O sistema de ranqueamento analisa produtos baseado em múltiplos critérios para determinar automaticamente a melhor opção, considerando:
- **Preço** (40% do peso)
- **Avaliações** (20% do peso)
- **Especificações Técnicas** (40% do peso)

## Como Funciona

### 1. Análise de Preço
- Quanto **menor o preço**, maior o score
- Normalizado entre todos os produtos comparados
- Considera ofertas de múltiplos marketplaces (pega o menor preço disponível)

### 2. Análise de Avaliações
- Quanto **maior a avaliação**, maior o score
- Produtos sem avaliação recebem score neutro (0.5)

### 3. Análise de Especificações Técnicas

O sistema identifica automaticamente qual direção é melhor para cada especificação:

#### Maior é Melhor
- RAM / Memória
- Armazenamento / Storage
- Núcleos / Cores / Threads
- Frequência / Clock / GHz
- Bateria / mAh
- Resolução / Megapixels
- Taxa de Atualização / Refresh Rate / Hz
- Largura de Banda / Bandwidth

#### Menor é Melhor
- Latência / Latency
- Tempo de Resposta / Response Time
- Peso / Weight

### 4. Cálculo do Score Final

```
Score Total = (Preço × 0.40) + (Avaliação × 0.20) + (Especificações × 0.40)
```

Todos os valores são normalizados entre 0 e 1, depois convertidos para escala de 0-100.

## Uso no Backend (PHP)

```php
use App\Service\ProductRankingService;

$rankingService = new ProductRankingService();
$rankedProducts = $rankingService->rankProducts($products);

// Resultado:
// [
//   [
//     'product' => Product,
//     'rank' => 1,
//     'score' => 0.85,
//     'breakdown' => [
//       'priceScore' => 0.90,
//       'ratingScore' => 0.75,
//       'specsScore' => 0.88,
//       'totalScore' => 0.85
//     ]
//   ],
//   ...
// ]
```

## Uso no Frontend (JavaScript/Vue)

```javascript
import { rankProducts, formatScore, getRankBadge } from '@/utils/productRanking'

// Ranquear produtos
const ranked = rankProducts(products)

// Primeiro produto é sempre o melhor
const bestProduct = ranked[0]

console.log(`Ranking: ${bestProduct.rank}`)
console.log(`Score: ${formatScore(bestProduct.score)}/100`)

// Obter badge/medalha
const badge = getRankBadge(bestProduct.rank)
console.log(`${badge.emoji} ${badge.text}`)
```

## Interface Visual

### Na Página de Comparação

1. **Tabela de Comparação**
   - A coluna do melhor produto é destacada com borda dourada
   - Badge "🏆 MELHOR OPÇÃO" no topo
   - Score visível no cabeçalho

2. **Cards de Resumo**
   - Medalhas: 🏆 (1º), 🥈 (2º), 🥉 (3º)
   - Score total e breakdown detalhado
   - Barras de progresso para cada critério:
     - Verde = Preço
     - Azul = Avaliação
     - Roxo = Especificações

3. **Destaque da Melhor Opção**
   - Borda amarela/dourada
   - Background com gradiente sutil
   - Tag "MELHOR CUSTO-BENEFÍCIO"

## Exemplos de Ranqueamento

### Exemplo 1: Notebooks

```
Produto A: R$ 3.500 | Rating: 4.5 | 16GB RAM, i7, 512GB SSD
Produto B: R$ 4.200 | Rating: 4.8 | 16GB RAM, i7, 1TB SSD
Produto C: R$ 3.000 | Rating: 4.0 | 8GB RAM, i5, 256GB SSD

Resultado:
1. Produto A (Score: 87/100) ⭐ MELHOR OPÇÃO
   - Melhor equilíbrio entre preço e especificações
   
2. Produto B (Score: 78/100)
   - Melhores especificações, mas mais caro
   
3. Produto C (Score: 65/100)
   - Preço mais baixo, mas especificações inferiores
```

### Exemplo 2: Monitores

```
Monitor A: R$ 1.200 | Rating: 4.7 | 144Hz, 1ms, 27"
Monitor B: R$ 1.500 | Rating: 4.9 | 165Hz, 0.5ms, 27"
Monitor C: R$ 900   | Rating: 4.3 | 75Hz, 5ms, 24"

Resultado:
1. Monitor A (Score: 88/100) ⭐ MELHOR OPÇÃO
   - Excelentes specs por um preço competitivo
   
2. Monitor B (Score: 82/100)
   - Specs marginalmente melhores, mas 25% mais caro
   
3. Monitor C (Score: 70/100)
   - Preço baixo, mas specs significativamente inferiores
```

## Personalização

### Ajustar Pesos dos Critérios

**Backend (ProductRankingService.php):**
```php
private const WEIGHT_PRICE = 0.40;    // Aumentar para priorizar preço
private const WEIGHT_RATING = 0.20;   // Aumentar para confiar mais em reviews
private const WEIGHT_SPECS = 0.40;    // Aumentar para priorizar hardware
```

**Frontend (productRanking.js):**
```javascript
const WEIGHT_PRICE = 0.40;
const WEIGHT_RATING = 0.20;
const WEIGHT_SPECS = 0.40;
```

### Adicionar Novas Especificações

Adicione termos às listas:

```javascript
const HIGHER_IS_BETTER = [
  ...
  'Nova Especificação',  // Adicione aqui
];

const LOWER_IS_BETTER = [
  ...
  'Outra Especificação',  // Ou aqui
];
```

## Notas Técnicas

- Todos os valores são normalizados para garantir comparação justa
- O sistema extrai automaticamente valores numéricos de strings
- Suporta conversões de unidades (TB→GB, GHz→MHz)
- Produtos sem dados recebem scores neutros (0.5)
- Mínimo de 2 produtos necessário para comparação significativa

## Limitações Conhecidas

1. Especificações não numéricas não são comparadas
2. Valores textuais complexos podem não ser extraídos corretamente
3. Sistema assume que todas as especificações do mesmo tipo têm o mesmo peso
4. Não considera preferências individuais do usuário

## Melhorias Futuras

- [ ] Permitir usuário ajustar pesos dinamicamente
- [ ] Adicionar machine learning para aprender preferências do usuário
- [ ] Suportar comparação de especificações categóricas
- [ ] Considerar histórico de preços e tendências
- [ ] Integrar com análise de reviews textuais
