/**
 * EXEMPLO DE USO - Sistema de Ranqueamento
 * 
 * Este arquivo demonstra como usar o sistema de ranqueamento no frontend
 */

import { rankProducts, formatScore, getRankBadge } from './utils/productRanking';

// =============================================================================
// EXEMPLO 1: Ranquear produtos simples
// =============================================================================

const produtos = [
  {
    id: '1',
    name: 'Teclado Mecânico Logitech G Pro',
    price: 450.00,
    rating: 4.7,
    specifications: {
      'Switches': 'GX Blue',
      'RGB': 'Sim',
      'Latência': '1ms',
      'Peso': '980g'
    }
  },
  {
    id: '2',
    name: 'Teclado Mecânico Razer BlackWidow',
    price: 650.00,
    rating: 4.9,
    specifications: {
      'Switches': 'Razer Green',
      'RGB': 'Sim',
      'Latência': '0.5ms',
      'Peso': '1200g'
    }
  },
  {
    id: '3',
    name: 'Teclado Mecânico Redragon Kumara',
    price: 250.00,
    rating: 4.3,
    specifications: {
      'Switches': 'Outemu Blue',
      'RGB': 'Backlight Azul',
      'Latência': '2ms',
      'Peso': '750g'
    }
  }
];

const ranked = rankProducts(produtos);

console.log('=== PRODUTOS RANQUEADOS ===');
ranked.forEach((item, index) => {
  const badge = getRankBadge(item.rank);
  console.log(`${badge.emoji} #${item.rank}: ${item.product.name}`);
  console.log(`   Score: ${formatScore(item.score)}/100`);
  console.log(`   Preço: ${formatScore(item.breakdown.priceScore)}/100`);
  console.log(`   Avaliação: ${formatScore(item.breakdown.ratingScore)}/100`);
  console.log(`   Specs: ${formatScore(item.breakdown.specsScore)}/100`);
  console.log('');
});

// =============================================================================
// EXEMPLO 2: Uso em componente Vue
// =============================================================================

/*
<template>
  <div class="product-comparison">
    <h2>Comparação de Produtos</h2>
    
    <div class="products-grid">
      <div 
        v-for="item in rankedProducts" 
        :key="item.product.id"
        :class="['product-card', { 'best-choice': item.rank === 1 }]"
      >
        <!-- Badge de Ranking -->
        <div class="rank-badge">
          <span class="rank-emoji">{{ getRankBadge(item.rank).emoji }}</span>
          <span class="rank-text">{{ getRankBadge(item.rank).text }}</span>
        </div>
        
        <!-- Informações do Produto -->
        <h3>{{ item.product.name }}</h3>
        <p class="price">R$ {{ item.product.price.toFixed(2) }}</p>
        <p class="rating">⭐ {{ item.product.rating }}</p>
        
        <!-- Score Total -->
        <div class="score-display">
          <div class="score-bar">
            <div 
              class="score-fill" 
              :style="{ width: `${formatScore(item.score)}%` }"
            ></div>
          </div>
          <span class="score-text">{{ formatScore(item.score) }}/100</span>
        </div>
        
        <!-- Breakdown dos Scores -->
        <div class="score-breakdown">
          <div class="score-item">
            <span>💰 Preço</span>
            <span>{{ formatScore(item.breakdown.priceScore) }}</span>
          </div>
          <div class="score-item">
            <span>⭐ Avaliação</span>
            <span>{{ formatScore(item.breakdown.ratingScore) }}</span>
          </div>
          <div class="score-item">
            <span>🔧 Specs</span>
            <span>{{ formatScore(item.breakdown.specsScore) }}</span>
          </div>
        </div>
        
        <!-- Tag de Melhor Opção -->
        <div v-if="item.rank === 1" class="best-choice-tag">
          ⭐ MELHOR CUSTO-BENEFÍCIO
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { rankProducts, formatScore, getRankBadge } from '@/utils/productRanking';

const props = defineProps({
  products: {
    type: Array,
    required: true
  }
});

const rankedProducts = computed(() => {
  return rankProducts(props.products);
});
</script>
*/

// =============================================================================
// EXEMPLO 3: Filtrar apenas os top 3
// =============================================================================

function getTopProducts(products, limit = 3) {
  const ranked = rankProducts(products);
  return ranked.slice(0, limit);
}

const top3 = getTopProducts(produtos, 3);
console.log('\n=== TOP 3 PRODUTOS ===');
top3.forEach(item => {
  console.log(`${item.rank}. ${item.product.name} (Score: ${formatScore(item.score)})`);
});

// =============================================================================
// EXEMPLO 4: Encontrar o melhor produto
// =============================================================================

function getBestProduct(products) {
  const ranked = rankProducts(products);
  return ranked.length > 0 ? ranked[0] : null;
}

const best = getBestProduct(produtos);
if (best) {
  console.log('\n=== MELHOR PRODUTO ===');
  console.log(`🏆 ${best.product.name}`);
  console.log(`Score: ${formatScore(best.score)}/100`);
  console.log(`Preço: R$ ${best.product.price.toFixed(2)}`);
}

// =============================================================================
// EXEMPLO 5: Comparar dois produtos específicos
// =============================================================================

function compareProducts(product1, product2) {
  const ranked = rankProducts([product1, product2]);
  const winner = ranked[0].product;
  const loser = ranked[1].product;
  
  const scoreDiff = formatScore(ranked[0].score - ranked[1].score);
  
  return {
    winner: winner.name,
    loser: loser.name,
    scoreDifference: scoreDiff,
    recommendation: `${winner.name} é ${scoreDiff} pontos melhor que ${loser.name}`
  };
}

const comparison = compareProducts(produtos[0], produtos[1]);
console.log('\n=== COMPARAÇÃO DIRETA ===');
console.log(comparison.recommendation);

// =============================================================================
// EXEMPLO 6: Análise personalizada por critério
// =============================================================================

function analyzeByCriteria(products) {
  const ranked = rankProducts(products);
  
  // Melhor por preço
  const sortedByPrice = [...ranked].sort((a, b) => 
    b.breakdown.priceScore - a.breakdown.priceScore
  );
  
  // Melhor por avaliação
  const sortedByRating = [...ranked].sort((a, b) => 
    b.breakdown.ratingScore - a.breakdown.ratingScore
  );
  
  // Melhor por especificações
  const sortedBySpecs = [...ranked].sort((a, b) => 
    b.breakdown.specsScore - a.breakdown.specsScore
  );
  
  return {
    bestOverall: ranked[0].product.name,
    bestPrice: sortedByPrice[0].product.name,
    bestRating: sortedByRating[0].product.name,
    bestSpecs: sortedBySpecs[0].product.name
  };
}

const analysis = analyzeByCriteria(produtos);
console.log('\n=== ANÁLISE POR CRITÉRIO ===');
console.log(`Melhor Geral: ${analysis.bestOverall}`);
console.log(`Melhor Preço: ${analysis.bestPrice}`);
console.log(`Melhor Avaliação: ${analysis.bestRating}`);
console.log(`Melhores Specs: ${analysis.bestSpecs}`);

// =============================================================================
// EXEMPLO 7: Com produtos que têm ofertas
// =============================================================================

const produtosComOfertas = [
  {
    id: '1',
    name: 'Mouse Gamer Logitech G502',
    price: 350.00,
    rating: 4.8,
    offers: [
      { marketplace: 'Amazon', price: 299.90 },
      { marketplace: 'Kabum', price: 319.90 },
      { marketplace: 'Pichau', price: 309.90 }
    ],
    specifications: {
      'DPI': '25600',
      'Sensor': 'HERO 25K',
      'Peso': '121g'
    }
  },
  {
    id: '2',
    name: 'Mouse Gamer Razer DeathAdder',
    price: 400.00,
    rating: 4.7,
    offers: [
      { marketplace: 'Amazon', price: 380.00 },
      { marketplace: 'Kabum', price: 390.00 }
    ],
    specifications: {
      'DPI': '20000',
      'Sensor': 'Focus+',
      'Peso': '105g'
    }
  }
];

const rankedWithOffers = rankProducts(produtosComOfertas);
console.log('\n=== PRODUTOS COM OFERTAS ===');
rankedWithOffers.forEach(item => {
  const badge = getRankBadge(item.rank);
  const lowestPrice = Math.min(...item.product.offers.map(o => o.price));
  console.log(`${badge.emoji} ${item.product.name}`);
  console.log(`   Menor preço: R$ ${lowestPrice.toFixed(2)}`);
  console.log(`   Score: ${formatScore(item.score)}/100`);
});

console.log('\n✅ TODOS OS EXEMPLOS EXECUTADOS COM SUCESSO!');
