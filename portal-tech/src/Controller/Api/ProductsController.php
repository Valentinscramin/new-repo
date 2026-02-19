<?php

namespace App\Controller\Api;

use App\Document\Product;
use App\Document\ProductOffer;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ProductsController extends AbstractController
{
    private DocumentManager $dm;

    public function __construct(DocumentManager $dm)
    {
        $this->dm = $dm;
    }

    #[Route('/api/products', name: 'api_products_list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        // Support filtering by IDs for comparison feature
        $idsParam = $request->query->get('ids');
        
        if ($idsParam) {
            $ids = explode(',', $idsParam);
            $products = [];
            
            foreach ($ids as $id) {
                $product = $this->dm->getRepository(Product::class)->find(trim($id));
                if ($product) {
                    $products[] = $this->formatProduct($product);
                }
            }
            
            return $this->json($products);
        }
        
        // Default: return all products
        $allProducts = $this->dm->getRepository(Product::class)
            ->findBy([], ['createdAt' => 'DESC'], 50);
        
        $result = [];
        foreach ($allProducts as $product) {
            $result[] = $this->formatProduct($product);
        }
        
        return $this->json($result);
    }

    #[Route('/api/products/{id}', name: 'api_products_get', methods: ['GET'])]
    public function get(string $id): JsonResponse
    {
        $product = $this->dm->getRepository(Product::class)->find($id);
        
        if (!$product) {
            return $this->json(['error' => 'Product not found'], 404);
        }
        
        return $this->json($this->formatProduct($product));
    }

    private function formatProduct(Product $product): array
    {
        // Get all offers for this product, sorted by price ASC
        $allOffers = $this->dm->getRepository(ProductOffer::class)
            ->findBy(['product' => $product], ['price' => 'ASC']);

        $minPrice = null;
        $offersData = [];
        foreach ($allOffers as $offer) {
            if ($minPrice === null) {
                $minPrice = $offer->getPrice();
            }
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

        // Build specifications as associative array
        $specs = [];
        foreach ($product->getSpecifications() as $spec) {
            $specs[$spec->getKey()] = $spec->getValue();
        }

        return [
            'id' => (string)$product->getId(),
            'name' => $product->getName(),
            'title' => $product->getName(), // Compatibility
            'slug' => $product->getSlug(),
            'description' => $product->getDescription(),
            'image' => $product->getImage(),
            'price' => $minPrice,
            'rating' => $product->getRating(),
            'specifications' => $specs,
            'category' => $product->getCategory() ? [
                'id' => (string)$product->getCategory()->getId(),
                'name' => $product->getCategory()->getName()
            ] : null,
            'offers' => $offersData,
        ];
    }
}
