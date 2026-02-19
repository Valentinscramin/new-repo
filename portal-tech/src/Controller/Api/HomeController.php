<?php

namespace App\Controller\Api;

use App\Document\Product;
use App\Document\ProductOffer;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    private DocumentManager $dm;

    public function __construct(DocumentManager $dm)
    {
        $this->dm = $dm;
    }

    #[Route('/api/home', name: 'api_home', methods: ['GET'])]
    public function index(): JsonResponse
    {
        // Buscar produtos (aumentando para 10 produtos para ter mais variedade)
        $products = $this->dm->getRepository(Product::class)
            ->findBy([], ['createdAt' => 'DESC'], 10);

        $sampleProducts = [];
        $comparisonRows = [];
        $rankingItems = [];
        $bestPrices = [];

        // Construir dados dos produtos
        foreach ($products as $index => $product) {
            // Buscar preço mínimo das ofertas
            $minPrice = null;
            $offers = $this->dm->getRepository(ProductOffer::class)
                ->findBy(['product' => $product], ['price' => 'ASC'], 1);
            if (!empty($offers)) {
                $minPrice = $offers[0]->getPrice();
            }

            // Construir especificações como array associativo
            $specs = [];
            foreach ($product->getSpecifications() as $spec) {
                $specs[$spec->getKey()] = $spec->getValue();
            }

            // Buscar TODAS as ofertas do produto, ordenadas por preço
            $allOffers = $this->dm->getRepository(ProductOffer::class)
                ->findBy(['product' => $product], ['price' => 'ASC']);

            $offersData = [];
            foreach ($allOffers as $offer) {
                $marketplace = $offer->getMarketplace();
                $supplier = $product->getSupplier();
                $offersData[] = [
                    'price' => $offer->getPrice(),
                    'marketplace' => $marketplace ? $marketplace->getName() : null,
                    'supplier' => $supplier ? $supplier->getName() : null,
                    'url' => $offer->getAffiliateLink(),
                    'condition' => 'Novo',
                ];
            }

            $productData = [
                'id' => (string)$product->getId(),
                'name' => $product->getName(),
                'title' => $product->getName(), // Compatibilidade
                'slug' => $product->getSlug(),
                'description' => $product->getDescription(),
                'image' => $product->getImage() ?: '/img/monitor-' . ($index + 1) . '.svg',
                'price' => $minPrice,
                'rating' => $product->getRating() ?: (4.8 - ($index * 0.1)),
                'specifications' => $specs,
                'category' => $product->getCategory() ? [
                    'id' => (string)$product->getCategory()->getId(),
                    'name' => $product->getCategory()->getName()
                ] : null,
                'offers' => $offersData,
            ];

            if ($index === 0) {
                $productData['badge'] = 'Melhor Escolha 2026';
            }

            $sampleProducts[] = $productData;

            // Adicionar ao ranking
            $rankingItems[] = [
                'title' => $product->getName(),
                'subtitle' => $product->getDescription() ?? 'Produto de qualidade',
                'rating' => '4.' . (8 - $index),
            ];
        }

        // Construir tabela de comparação
        if (count($products) >= 3) {
            $specs = ['Taxa de Atualização', 'Painel', 'Preço', 'Avaliação'];
            
            foreach ($specs as $specName) {
                $row = ['feature' => $specName];
                
                foreach ($products as $index => $product) {
                    $colName = chr(97 + $index); // 'a', 'b', 'c'
                    
                    if ($specName === 'Avaliação') {
                        $row[$colName] = '4.' . (8 - $index);
                    } elseif ($specName === 'Preço') {
                        // Buscar preço real das ofertas
                        $offers = $this->dm->getRepository(ProductOffer::class)
                            ->findBy(['product' => $product], ['price' => 'ASC'], 1);
                        
                        if (!empty($offers)) {
                            $row[$colName] = '€' . number_format($offers[0]->getPrice(), 0);
                        } else {
                            $row[$colName] = '€' . (899 - ($index * 50));
                        }
                    } else {
                        // Buscar especificação real
                        $specValue = $this->findSpecValue($product, $specName);
                        $row[$colName] = $specValue ?: '-';
                    }
                }
                
                $comparisonRows[] = $row;
            }
        }

        // Buscar melhores preços (primeiro produto) - ordenado por preço ASC
        if (!empty($products)) {
            $firstProduct = $products[0];
            $offers = $this->dm->getRepository(ProductOffer::class)
                ->findBy(['product' => $firstProduct], ['price' => 'ASC']);

            foreach ($offers as $offer) {
                $marketplace = $offer->getMarketplace();
                $supplier = $firstProduct->getSupplier();
                if ($marketplace) {
                    $bestPrices[] = [
                        'marketplace' => $marketplace->getName(),
                        'supplier' => $supplier ? $supplier->getName() : null,
                        'price' => $offer->getPrice(),
                        'priceFormatted' => 'R$ ' . number_format($offer->getPrice(), 2, ',', '.'),
                        'link' => $offer->getAffiliateLink(),
                    ];
                }
            }

            // Se não houver ofertas, retornar dados de exemplo
            if (empty($bestPrices)) {
                $bestPrices = [
                    ['marketplace' => 'Amazon', 'supplier' => null, 'price' => 879, 'priceFormatted' => 'R$ 879,00', 'link' => '#'],
                    ['marketplace' => 'Worten', 'supplier' => null, 'price' => 899, 'priceFormatted' => 'R$ 899,00', 'link' => '#'],
                    ['marketplace' => 'AliExpress', 'supplier' => null, 'price' => 845, 'priceFormatted' => 'R$ 845,00', 'link' => '#'],
                ];
            }
        }

        return $this->json([
            'sampleProducts' => $sampleProducts,
            'comparisonRows' => $comparisonRows,
            'rankingItems' => $rankingItems,
            'bestPrices' => $bestPrices,
        ]);
    }

    private function findSpecValue(Product $product, string $specName): ?string
    {
        foreach ($product->getSpecifications() as $spec) {
            if (stripos($spec->getKey(), $specName) !== false) {
                return $spec->getValue();
            }
        }
        return null;
    }
}
