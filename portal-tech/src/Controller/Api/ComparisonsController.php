<?php
namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ODM\MongoDB\DocumentManager;
use App\Document\User;
use App\Document\Product;
use App\Document\ProductOffer;
use App\Document\SavedComparison;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class ComparisonsController extends AbstractController
{
    private function getUserFromRequest(Request $request, DocumentManager $dm): ?User
    {
        $auth = $request->headers->get('Authorization');
        if (!$auth || !str_starts_with($auth, 'Bearer ')) {
            return null;
        }
        $token = substr($auth, 7);
        $secret = $_ENV['APP_SECRET'] ?? getenv('APP_SECRET');
        try {
            $payload = JWT::decode($token, new Key($secret, 'HS256'));
            $userId = $payload->sub ?? null;
            if (!$userId) return null;
            return $dm->getRepository(User::class)->find($userId);
        } catch (\Throwable $e) {
            return null;
        }
    }

    #[Route('/api/comparisons', name: 'api_comparisons_list', methods: ['GET'])]
    public function list(Request $request, DocumentManager $dm): JsonResponse
    {
        $user = $this->getUserFromRequest($request, $dm);
        if (!$user) return new JsonResponse(['error' => 'Unauthorized'], 401);

        $items = $dm->getRepository(SavedComparison::class)->findBy(['user' => $user]);
        $data = array_map(function ($i) use ($dm) {
            $products = [];
            
            // Get products from new array field
            foreach ($i->getProducts() as $product) {
                $products[] = $this->formatProductForComparison($product, $dm);
            }

            // Fallback to legacy fields if products array is empty
            if (empty($products)) {
                if ($i->getProductA()) {
                    $products[] = $this->formatProductForComparison($i->getProductA(), $dm);
                }
                if ($i->getProductB()) {
                    $products[] = $this->formatProductForComparison($i->getProductB(), $dm);
                }
            }

            return [
                'id' => (string)$i->getId(),
                'products' => $products,
                'createdAt' => $i->getCreatedAt()->format(DATE_ATOM),
            ];
        }, $items);

        return new JsonResponse($data);
    }

    #[Route('/api/comparisons', name: 'api_comparisons_create', methods: ['POST'])]
    public function create(Request $request, DocumentManager $dm): JsonResponse
    {
        $user = $this->getUserFromRequest($request, $dm);
        if (!$user) return new JsonResponse(['error' => 'Unauthorized'], 401);

        $data = json_decode($request->getContent(), true);
        
        // Support both new format (products array) and legacy format (productA, productB)
        $productIds = $data['products'] ?? [];
        
        // Fallback to legacy format
        if (empty($productIds)) {
            $a = $data['productA'] ?? null;
            $b = $data['productB'] ?? null;
            if ($a) $productIds[] = $a;
            if ($b) $productIds[] = $b;
        }

        if (count($productIds) < 2) {
            return new JsonResponse(['error' => 'At least 2 products required'], 400);
        }

        if (count($productIds) > 3) {
            return new JsonResponse(['error' => 'Maximum 3 products allowed'], 400);
        }

        $products = [];
        foreach ($productIds as $productId) {
            $product = $dm->getRepository(Product::class)->find($productId);
            if (!$product) {
                return new JsonResponse(['error' => "Product {$productId} not found"], 404);
            }
            $products[] = $product;
        }

        $item = new SavedComparison();
        $item->setUser($user);
        $item->setProducts($products);
        
        // Also set legacy fields for backward compatibility
        if (isset($products[0])) $item->setProductA($products[0]);
        if (isset($products[1])) $item->setProductB($products[1]);
        
        $dm->persist($item);
        $dm->flush();

        return new JsonResponse(['id' => (string)$item->getId()], 201);
    }

    #[Route('/api/comparisons/{id}', name: 'api_comparisons_delete', methods: ['DELETE'])]
    public function delete(string $id, Request $request, DocumentManager $dm): JsonResponse
    {
        $user = $this->getUserFromRequest($request, $dm);
        if (!$user) return new JsonResponse(['error' => 'Unauthorized'], 401);

        $comparison = $dm->getRepository(SavedComparison::class)->find($id);
        if (!$comparison) {
            return new JsonResponse(['error' => 'Comparison not found'], 404);
        }

        // Verify ownership
        if ($comparison->getUser()->getId() !== $user->getId()) {
            return new JsonResponse(['error' => 'Forbidden'], 403);
        }

        $dm->remove($comparison);
        $dm->flush();

        return new JsonResponse(['message' => 'Comparison deleted successfully']);
    }

    private function formatProductForComparison(Product $product, DocumentManager $dm): array
    {
        // Get offers sorted by price
        $allOffers = $dm->getRepository(ProductOffer::class)
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
