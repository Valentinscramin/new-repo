/**
 * Serviço de ranqueamento de produtos
 * Analisa especificações técnicas e preços para determinar a melhor opção
 */

// Pesos para diferentes critérios (total deve somar 1.0)
const WEIGHT_PRICE = 0.40;      // 40% - Preço é muito importante
const WEIGHT_RATING = 0.20;     // 20% - Avaliações dos usuários
const WEIGHT_SPECS = 0.40;      // 40% - Especificações técnicas

// Especificações que quanto maior, melhor (valores numéricos)
const HIGHER_IS_BETTER = [
  'RAM', 'Memória', 'Armazenamento', 'Storage', 
  'Núcleos', 'Cores', 'Threads',
  'Frequência', 'Clock', 'GHz',
  'Bateria', 'Battery', 'mAh',
  'Resolução', 'Resolution', 'Megapixels', 'MP',
  'Taxa de Atualização', 'Refresh Rate', 'Hz',
  'Largura de Banda', 'Bandwidth', 'Mbps', 'Gbps'
];

// Especificações que quanto menor, melhor
const LOWER_IS_BETTER = [
  'Latência', 'Latency', 'ms',
  'Tempo de Resposta', 'Response Time',
  'Peso', 'Weight', 'kg', 'g'
];

/**
 * Ranqueia uma lista de produtos
 * @param {Array} products - Lista de produtos
 * @returns {Array} Array de produtos ordenados com scores
 */
export function rankProducts(products) {
  if (!products || products.length === 0) {
    return [];
  }

  const rankedProducts = [];

  for (const product of products) {
    const score = calculateProductScore(product, products);
    
    rankedProducts.push({
      product,
      score: score.total,
      breakdown: {
        priceScore: score.price,
        ratingScore: score.rating,
        specsScore: score.specs,
        totalScore: score.total
      }
    });
  }

  // Ordenar por score total (maior para menor)
  rankedProducts.sort((a, b) => b.breakdown.totalScore - a.breakdown.totalScore);

  // Adicionar ranking position
  rankedProducts.forEach((item, index) => {
    item.rank = index + 1;
  });

  return rankedProducts;
}

/**
 * Calcula o score de um produto específico
 * @param {Object} product - Produto a ser analisado
 * @param {Array} allProducts - Todos os produtos para normalização
 * @returns {Object} Scores detalhados
 */
function calculateProductScore(product, allProducts) {
  const priceScore = calculatePriceScore(product, allProducts);
  const ratingScore = calculateRatingScore(product, allProducts);
  const specsScore = calculateSpecsScore(product, allProducts);

  const totalScore = (
    priceScore * WEIGHT_PRICE +
    ratingScore * WEIGHT_RATING +
    specsScore * WEIGHT_SPECS
  );

  return {
    price: priceScore,
    rating: ratingScore,
    specs: specsScore,
    total: totalScore
  };
}

/**
 * Calcula score baseado no preço (menor preço = maior score)
 */
function calculatePriceScore(product, allProducts) {
  const productPrice = getLowestPrice(product);
  
  if (productPrice === null || productPrice <= 0) {
    return 0.0;
  }

  // Coletar todos os preços válidos
  const prices = allProducts
    .map(p => getLowestPrice(p))
    .filter(price => price !== null && price > 0);

  if (prices.length === 0) {
    return 0.0;
  }

  const minPrice = Math.min(...prices);
  const maxPrice = Math.max(...prices);

  // Se todos os preços são iguais
  if (maxPrice === minPrice) {
    return 1.0;
  }

  // Normalizar: menor preço = score 1.0, maior preço = score 0.0
  const normalizedScore = 1 - ((productPrice - minPrice) / (maxPrice - minPrice));
  
  return Math.max(0.0, Math.min(1.0, normalizedScore));
}

/**
 * Calcula score baseado no rating (maior rating = maior score)
 */
function calculateRatingScore(product, allProducts) {
  const productRating = product.rating;
  
  if (productRating === null || productRating === undefined) {
    return 0.5; // Score neutro se não tem rating
  }

  // Coletar todos os ratings válidos
  const ratings = allProducts
    .map(p => p.rating)
    .filter(rating => rating !== null && rating !== undefined);

  if (ratings.length === 0) {
    return 0.5;
  }

  const minRating = Math.min(...ratings);
  const maxRating = Math.max(...ratings);

  // Se todos os ratings são iguais
  if (maxRating === minRating) {
    return 1.0;
  }

  // Normalizar: maior rating = score 1.0, menor rating = score 0.0
  const normalizedScore = (productRating - minRating) / (maxRating - minRating);
  
  return Math.max(0.0, Math.min(1.0, normalizedScore));
}

/**
 * Calcula score baseado em especificações técnicas
 */
function calculateSpecsScore(product, allProducts) {
  const specifications = product.specifications;
  
  if (!specifications || Object.keys(specifications).length === 0) {
    return 0.5; // Score neutro se não tem specs
  }

  const scores = [];
  
  for (const [key, value] of Object.entries(specifications)) {
    // Tentar extrair valor numérico
    const numericValue = extractNumericValue(value);
    
    if (numericValue === null) {
      continue; // Ignorar valores não numéricos
    }
    
    // Verificar se é uma especificação comparável
    const direction = getSpecDirection(key);
    
    if (direction === null) {
      continue; // Ignorar specs que não sabemos como comparar
    }
    
    // Obter todos os valores para esta especificação
    const valuesForSpec = allProducts
      .map(p => {
        if (p.specifications && p.specifications[key]) {
          return extractNumericValue(p.specifications[key]);
        }
        return null;
      })
      .filter(val => val !== null);
    
    if (valuesForSpec.length < 2) {
      continue; // Precisa de pelo menos 2 valores para comparar
    }
    
    const minVal = Math.min(...valuesForSpec);
    const maxVal = Math.max(...valuesForSpec);
    
    if (maxVal === minVal) {
      scores.push(1.0); // Todos iguais
      continue;
    }
    
    // Normalizar baseado na direção
    let score;
    if (direction === 'higher') {
      // Maior é melhor
      score = (numericValue - minVal) / (maxVal - minVal);
    } else {
      // Menor é melhor
      score = 1 - ((numericValue - minVal) / (maxVal - minVal));
    }
    
    scores.push(Math.max(0.0, Math.min(1.0, score)));
  }
  
  // Retornar média dos scores das especificações
  return scores.length === 0 ? 0.5 : scores.reduce((a, b) => a + b, 0) / scores.length;
}

/**
 * Extrai valor numérico de uma string
 * Exemplos: "16GB" -> 16, "3.5 GHz" -> 3.5, "1920x1080" -> 1920
 */
function extractNumericValue(value) {
  if (value === null || value === undefined) {
    return null;
  }
  
  // Converter para string se não for
  const str = String(value);
  
  // Remover espaços e converter para minúsculas
  const cleaned = str.toLowerCase().trim();
  
  // Tentar extrair número
  const match = cleaned.match(/(\d+\.?\d*)/);
  if (match) {
    let number = parseFloat(match[1]);
    
    // Aplicar multiplicadores de unidade
    if (cleaned.includes('tb')) {
      number *= 1000; // TB para GB
    } else if (cleaned.includes('ghz')) {
      number *= 1000; // GHz para MHz
    }
    
    return number;
  }
  
  return null;
}

/**
 * Determina a direção de comparação para uma especificação
 * @returns {string|null} 'higher', 'lower', ou null se não for comparável
 */
function getSpecDirection(key) {
  const keyLower = key.toLowerCase();
  
  for (const term of HIGHER_IS_BETTER) {
    if (keyLower.includes(term.toLowerCase())) {
      return 'higher';
    }
  }
  
  for (const term of LOWER_IS_BETTER) {
    if (keyLower.includes(term.toLowerCase())) {
      return 'lower';
    }
  }
  
  return null; // Não sabemos como comparar esta spec
}

/**
 * Obtém o menor preço disponível para um produto (apenas via ofertas)
 */
function getLowestPrice(product) {
  if (!product) return null;
  
  // Preço vem apenas das ofertas
  if (product.offers && Array.isArray(product.offers) && product.offers.length > 0) {
    const prices = product.offers
      .map(offer => offer.price)
      .filter(price => price !== null && price !== undefined && price > 0);
    
    if (prices.length > 0) {
      return Math.min(...prices);
    }
  }
  
  // Fallback ao campo price retornado pela API (já é o menor preço das ofertas)
  return product.price || null;
}

/**
 * Formata score para exibição (0-100)
 */
export function formatScore(score) {
  return Math.round(score * 100);
}

/**
 * Retorna badge/medalha baseado no ranking
 */
export function getRankBadge(rank) {
  const badges = {
    1: { emoji: '🏆', text: 'Melhor Opção', color: 'gold' },
    2: { emoji: '🥈', text: '2º Lugar', color: 'silver' },
    3: { emoji: '🥉', text: '3º Lugar', color: 'bronze' }
  };
  
  return badges[rank] || { emoji: '', text: `${rank}º`, color: 'gray' };
}
