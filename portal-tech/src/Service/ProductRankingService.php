<?php
namespace App\Service;

use App\Document\Product;
use App\Document\ProductOffer;
use Doctrine\ODM\MongoDB\DocumentManager;

/**
 * Serviço para ranquear produtos baseado em especificações técnicas e preços
 */
class ProductRankingService
{
    private DocumentManager $dm;

    public function __construct(DocumentManager $dm)
    {
        $this->dm = $dm;
    }

    // Pesos para diferentes critérios (total deve somar 1.0)
    private const WEIGHT_PRICE = 0.40;      // 40% - Preço é muito importante
    private const WEIGHT_RATING = 0.20;     // 20% - Avaliações dos usuários
    private const WEIGHT_SPECS = 0.40;      // 40% - Especificações técnicas

    // Especificações que quanto maior, melhor (valores numéricos)
    private const HIGHER_IS_BETTER = [
        'RAM', 'Memória', 'Armazenamento', 'Storage', 
        'Núcleos', 'Cores', 'Threads',
        'Frequência', 'Clock', 'GHz',
        'Bateria', 'Battery', 'mAh',
        'Resolução', 'Resolution', 'Megapixels', 'MP',
        'Taxa de Atualização', 'Refresh Rate', 'Hz',
        'Largura de Banda', 'Bandwidth', 'Mbps', 'Gbps'
    ];

    // Especificações que quanto menor, melhor
    private const LOWER_IS_BETTER = [
        'Latência', 'Latency', 'ms',
        'Tempo de Resposta', 'Response Time',
        'Peso', 'Weight', 'kg', 'g'
    ];

    /**
     * Ranqueia uma lista de produtos
     * 
     * @param Product[] $products
     * @return array Array de produtos ordenados com scores
     */
    public function rankProducts(array $products): array
    {
        if (empty($products)) {
            return [];
        }

        $rankedProducts = [];

        foreach ($products as $product) {
            $score = $this->calculateProductScore($product, $products);
            
            $rankedProducts[] = [
                'product' => $product,
                'score' => $score,
                'breakdown' => [
                    'priceScore' => $score['price'],
                    'ratingScore' => $score['rating'],
                    'specsScore' => $score['specs'],
                    'totalScore' => $score['total']
                ]
            ];
        }

        // Ordenar por score total (maior para menor)
        usort($rankedProducts, function($a, $b) {
            return $b['breakdown']['totalScore'] <=> $a['breakdown']['totalScore'];
        });

        // Adicionar ranking position
        foreach ($rankedProducts as $index => &$item) {
            $item['rank'] = $index + 1;
        }

        return $rankedProducts;
    }

    /**
     * Calcula o score de um produto específico
     * 
     * @param Product $product
     * @param Product[] $allProducts - Todos os produtos para normalização
     * @return array
     */
    private function calculateProductScore(Product $product, array $allProducts): array
    {
        $priceScore = $this->calculatePriceScore($product, $allProducts);
        $ratingScore = $this->calculateRatingScore($product, $allProducts);
        $specsScore = $this->calculateSpecsScore($product, $allProducts);

        $totalScore = (
            $priceScore * self::WEIGHT_PRICE +
            $ratingScore * self::WEIGHT_RATING +
            $specsScore * self::WEIGHT_SPECS
        );

        return [
            'price' => $priceScore,
            'rating' => $ratingScore,
            'specs' => $specsScore,
            'total' => $totalScore
        ];
    }

    /**
     * Calcula score baseado no preço (menor preço = maior score)
     */
    private function calculatePriceScore(Product $product, array $allProducts): float
    {
        $productPrice = $this->getLowestPrice($product);
        
        if ($productPrice === null || $productPrice <= 0) {
            return 0.0;
        }

        // Coletar todos os preços válidos
        $prices = [];
        foreach ($allProducts as $p) {
            $price = $this->getLowestPrice($p);
            if ($price !== null && $price > 0) {
                $prices[] = $price;
            }
        }

        if (empty($prices)) {
            return 0.0;
        }

        $minPrice = min($prices);
        $maxPrice = max($prices);

        // Se todos os preços são iguais
        if ($maxPrice == $minPrice) {
            return 1.0;
        }

        // Normalizar: menor preço = score 1.0, maior preço = score 0.0
        $normalizedScore = 1 - (($productPrice - $minPrice) / ($maxPrice - $minPrice));
        
        return max(0.0, min(1.0, $normalizedScore));
    }

    /**
     * Calcula score baseado no rating (maior rating = maior score)
     */
    private function calculateRatingScore(Product $product, array $allProducts): float
    {
        $productRating = $product->getRating();
        
        if ($productRating === null) {
            return 0.5; // Score neutro se não tem rating
        }

        // Coletar todos os ratings válidos
        $ratings = [];
        foreach ($allProducts as $p) {
            $rating = $p->getRating();
            if ($rating !== null) {
                $ratings[] = $rating;
            }
        }

        if (empty($ratings)) {
            return 0.5;
        }

        $minRating = min($ratings);
        $maxRating = max($ratings);

        // Se todos os ratings são iguais
        if ($maxRating == $minRating) {
            return 1.0;
        }

        // Normalizar: maior rating = score 1.0, menor rating = score 0.0
        $normalizedScore = ($productRating - $minRating) / ($maxRating - $minRating);
        
        return max(0.0, min(1.0, $normalizedScore));
    }

    /**
     * Calcula score baseado em especificações técnicas
     */
    private function calculateSpecsScore(Product $product, array $allProducts): float
    {
        $specifications = $product->getSpecifications();
        
        if (empty($specifications)) {
            return 0.5; // Score neutro se não tem specs
        }

        // Coletar todas as especificações de todos os produtos
        $allSpecs = $this->collectAllSpecifications($allProducts);
        
        if (empty($allSpecs)) {
            return 0.5;
        }

        $scores = [];
        
        foreach ($specifications as $spec) {
            $key = $spec->getKey();
            $value = $spec->getValue();
            
            // Tentar extrair valor numérico
            $numericValue = $this->extractNumericValue($value);
            
            if ($numericValue === null) {
                continue; // Ignorar valores não numéricos
            }
            
            // Verificar se é uma especificação comparável
            $direction = $this->getSpecDirection($key);
            
            if ($direction === null) {
                continue; // Ignorar specs que não sabemos como comparar
            }
            
            // Obter todos os valores para esta especificação
            $valuesForSpec = [];
            foreach ($allProducts as $p) {
                $specs = $p->getSpecifications();
                foreach ($specs as $s) {
                    if ($s->getKey() === $key) {
                        $numVal = $this->extractNumericValue($s->getValue());
                        if ($numVal !== null) {
                            $valuesForSpec[] = $numVal;
                        }
                    }
                }
            }
            
            if (count($valuesForSpec) < 2) {
                continue; // Precisa de pelo menos 2 valores para comparar
            }
            
            $minVal = min($valuesForSpec);
            $maxVal = max($valuesForSpec);
            
            if ($maxVal == $minVal) {
                $scores[] = 1.0; // Todos iguais
                continue;
            }
            
            // Normalizar baseado na direção
            if ($direction === 'higher') {
                // Maior é melhor
                $score = ($numericValue - $minVal) / ($maxVal - $minVal);
            } else {
                // Menor é melhor
                $score = 1 - (($numericValue - $minVal) / ($maxVal - $minVal));
            }
            
            $scores[] = max(0.0, min(1.0, $score));
        }
        
        // Retornar média dos scores das especificações
        return empty($scores) ? 0.5 : array_sum($scores) / count($scores);
    }

    /**
     * Coleta todas as especificações de todos os produtos
     */
    private function collectAllSpecifications(array $products): array
    {
        $allSpecs = [];
        
        foreach ($products as $product) {
            $specs = $product->getSpecifications();
            foreach ($specs as $spec) {
                $key = $spec->getKey();
                if (!isset($allSpecs[$key])) {
                    $allSpecs[$key] = [];
                }
                $allSpecs[$key][] = $spec->getValue();
            }
        }
        
        return $allSpecs;
    }

    /**
     * Extrai valor numérico de uma string
     * Exemplos: "16GB" -> 16, "3.5 GHz" -> 3.5, "1920x1080" -> 1920
     */
    private function extractNumericValue(?string $value): ?float
    {
        if ($value === null) {
            return null;
        }
        
        // Remover espaços e converter para minúsculas
        $cleaned = strtolower(trim($value));
        
        // Tentar extrair número
        if (preg_match('/(\d+\.?\d*)/', $cleaned, $matches)) {
            $number = (float) $matches[1];
            
            // Aplicar multiplicadores de unidade
            if (strpos($cleaned, 'tb') !== false) {
                $number *= 1000; // TB para GB
            } elseif (strpos($cleaned, 'ghz') !== false) {
                $number *= 1000; // GHz para MHz
            }
            
            return $number;
        }
        
        return null;
    }

    /**
     * Determina a direção de comparação para uma especificação
     * @return string|null 'higher', 'lower', ou null se não for comparável
     */
    private function getSpecDirection(string $key): ?string
    {
        $keyLower = strtolower($key);
        
        foreach (self::HIGHER_IS_BETTER as $term) {
            if (stripos($keyLower, strtolower($term)) !== false) {
                return 'higher';
            }
        }
        
        foreach (self::LOWER_IS_BETTER as $term) {
            if (stripos($keyLower, strtolower($term)) !== false) {
                return 'lower';
            }
        }
        
        return null; // Não sabemos como comparar esta spec
    }

    /**
     * Obtém o menor preço disponível para um produto via ofertas
     */
    private function getLowestPrice(Product $product): ?float
    {
        $offers = $this->dm->getRepository(ProductOffer::class)
            ->findBy(['product' => $product], ['price' => 'ASC'], 1);
        
        if (!empty($offers)) {
            return $offers[0]->getPrice();
        }
        
        return null;
    }

    /**
     * Formata score para exibição (0-100)
     */
    public function formatScore(float $score): int
    {
        return (int) round($score * 100);
    }
}
