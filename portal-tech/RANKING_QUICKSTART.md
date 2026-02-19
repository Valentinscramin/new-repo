# 🏆 Guia Rápido - Sistema de Ranqueamento

## 🚀 Início Rápido

### Frontend (JavaScript/Vue)

```javascript
import { rankProducts, formatScore, getRankBadge } from '@/utils/productRanking';

// Seus produtos
const products = [
  { id: 1, name: 'Produto A', price: 100, rating: 4.5, specifications: {...} },
  { id: 2, name: 'Produto B', price: 150, rating: 4.8, specifications: {...} },
  { id: 3, name: 'Produto C', price: 80, rating: 4.0, specifications: {...} }
];

// Ranquear
const ranked = rankProducts(products);

// Melhor produto
const best = ranked[0];
console.log(`🏆 Melhor: ${best.product.name}`);
console.log(`Score: ${formatScore(best.score)}/100`);

// Badge/Medalha
const badge = getRankBadge(best.rank);
console.log(`${badge.emoji} ${badge.text}`); // 🏆 Melhor Opção
```

### Backend (PHP)

```php
use App\Service\ProductRankingService;

$service = new ProductRankingService();
$ranked = $service->rankProducts($products);

$best = $ranked[0];
echo "🏆 Melhor: " . $best['product']->getName();
echo "\nScore: " . $service->formatScore($best['breakdown']['totalScore']) . "/100";
```

## 📊 O que o Sistema Analisa

| Critério | Peso | Descrição |
|----------|------|-----------|
| 💰 **Preço** | 40% | Menor preço = melhor score |
| ⭐ **Avaliação** | 20% | Maior rating = melhor score |
| 🔧 **Especificações** | 40% | Analisa specs técnicas automaticamente |

## 🎯 Especificações Suportadas

### ⬆️ Maior é Melhor
- RAM / Memória / Storage
- Processador / Cores / Threads / GHz
- Bateria / mAh
- Resolução / Megapixels / Hz
- Bandwidth / Mbps / Gbps

### ⬇️ Menor é Melhor
- Latência / ms
- Tempo de Resposta
- Peso / kg / g

## 💡 Exemplos de Uso

### 1. Comparação Básica

```javascript
const products = [
  { id: 1, name: 'Notebook A', price: 3500, rating: 4.5, specifications: { RAM: '16GB' } },
  { id: 2, name: 'Notebook B', price: 4200, rating: 4.8, specifications: { RAM: '32GB' } }
];

const ranked = rankProducts(products);
// ranked[0] = melhor opção
// ranked[1] = segunda melhor
```

### 2. Exibir Só o Melhor

```javascript
function getBestProduct(products) {
  const ranked = rankProducts(products);
  return ranked[0];
}

const best = getBestProduct(myProducts);
```

### 3. Top 3 Produtos

```javascript
const top3 = rankProducts(allProducts).slice(0, 3);
```

### 4. Score Detalhado

```javascript
const ranked = rankProducts(products);

ranked.forEach(item => {
  console.log(item.product.name);
  console.log(`  Preço: ${formatScore(item.breakdown.priceScore)}/100`);
  console.log(`  Rating: ${formatScore(item.breakdown.ratingScore)}/100`);
  console.log(`  Specs: ${formatScore(item.breakdown.specsScore)}/100`);
  console.log(`  TOTAL: ${formatScore(item.breakdown.totalScore)}/100`);
});
```

## 🎨 Interface Visual

### Componente Vue

```vue
<template>
  <div v-for="item in rankedProducts" :key="item.product.id">
    <!-- Badge de Ranking -->
    <span>{{ getRankBadge(item.rank).emoji }}</span>
    
    <!-- Nome e Score -->
    <h3>{{ item.product.name }}</h3>
    <p>Score: {{ formatScore(item.score) }}/100</p>
    
    <!-- Destaque para o melhor -->
    <div v-if="item.rank === 1" class="best-choice">
      🏆 MELHOR OPÇÃO
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { rankProducts, formatScore, getRankBadge } from '@/utils/productRanking';

const props = defineProps(['products']);
const rankedProducts = computed(() => rankProducts(props.products));
</script>
```

## 🧪 Testar o Sistema

### Backend
```bash
cd scripts
php test_ranking.php
```

### Frontend
```bash
cd frontend
node src/utils/productRanking.example.js
```

## ⚙️ Personalização

Ajuste os pesos em `productRanking.js` ou `ProductRankingService.php`:

```javascript
const WEIGHT_PRICE = 0.40;   // 40% - Ajuste aqui
const WEIGHT_RATING = 0.20;  // 20% - Ajuste aqui  
const WEIGHT_SPECS = 0.40;   // 40% - Ajuste aqui
// Total deve somar 1.0 (100%)
```

### Exemplos de Configuração

**Priorizar Preço:**
```javascript
const WEIGHT_PRICE = 0.60;   // 60%
const WEIGHT_RATING = 0.10;  // 10%
const WEIGHT_SPECS = 0.30;   // 30%
```

**Priorizar Qualidade:**
```javascript
const WEIGHT_PRICE = 0.20;   // 20%
const WEIGHT_RATING = 0.30;  // 30%
const WEIGHT_SPECS = 0.50;   // 50%
```

**Balanceado:**
```javascript
const WEIGHT_PRICE = 0.33;   // 33%
const WEIGHT_RATING = 0.33;  // 33%
const WEIGHT_SPECS = 0.34;   // 34%
```

## 📱 Integração Completa

O sistema já está integrado automaticamente em:
- ✅ `/compare` - Página de comparação de produtos
- ✅ `ComparisonTable.vue` - Tabela de comparação
- ✅ `CompareView.vue` - View principal de comparação

### Como Usar na Sua Página

```vue
<script setup>
import { rankProducts } from '@/utils/productRanking';

const myProducts = [...]; // seus produtos
const ranked = rankProducts(myProducts);

// ranked já vem ordenado, primeiro = melhor
</script>
```

## 🎓 Resultado Final

```
🏆 #1 - Produto A (Score: 87/100) ⭐ MELHOR OPÇÃO
   💰 Preço: 92/100
   ⭐ Avaliação: 85/100
   🔧 Especificações: 84/100

🥈 #2 - Produto B (Score: 78/100)
   💰 Preço: 68/100
   ⭐ Avaliação: 95/100
   🔧 Especificações: 88/100

🥉 #3 - Produto C (Score: 65/100)
   💰 Preço: 100/100
   ⭐ Avaliação: 60/100
   🔧 Especificações: 45/100
```

## 📚 Documentação Completa

Veja [SISTEMA_RANQUEAMENTO.md](./SISTEMA_RANQUEAMENTO.md) para:
- Explicação detalhada do algoritmo
- Exemplos avançados
- Casos de uso
- Limitações e melhorias futuras

## 🆘 Problemas Comuns

### Scores muito similares?
- Adicione mais especificações técnicas
- Ajuste os pesos dos critérios
- Certifique-se que especificações têm valores numéricos

### Ranking não faz sentido?
- Verifique se os preços estão corretos
- Confirme que ratings estão na escala 0-5
- Veja se especificações são comparáveis

### Produto sem score?
- Produto precisa ter pelo menos preço OU rating OU especificações
- Valores null recebem score neutro (0.5)

## 📞 Suporte

Dúvidas? Veja os exemplos completos em:
- `frontend/src/utils/productRanking.example.js`
- `scripts/test_ranking.php`
