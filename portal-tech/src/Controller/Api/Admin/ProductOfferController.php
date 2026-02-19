<?php
namespace App\Controller\Api\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ODM\MongoDB\DocumentManager;
use App\Document\ProductOffer;
use App\Document\Product;
use App\Document\Marketplace;

class ProductOfferController extends AbstractController
{
    use AdminAuthTrait;

    // Product-specific offer routes
    #[Route('/api/admin/products/{productId}/offers', name: 'admin_product_offers_list', methods: ['GET'])]
    public function listByProduct(string $productId, Request $request, DocumentManager $dm): JsonResponse
    {
        if ($error = $this->requireAdmin($request, $dm)) return $error;

        $product = $dm->getRepository(Product::class)->find($productId);
        if (!$product) {
            return new JsonResponse(['error' => 'Product not found'], 404);
        }

        $offers = $dm->getRepository(ProductOffer::class)->findBy(['product' => $product]);
        
        $data = array_map(fn($offer) => [
            'id' => (string)$offer->getId(),
            'price' => $offer->getPrice(),
            'affiliateLink' => $offer->getAffiliateLink(),
            'marketplace' => $offer->getMarketplace() ? [
                'id' => (string)$offer->getMarketplace()->getId(),
                'name' => $offer->getMarketplace()->getName(),
                'logo' => $offer->getMarketplace()->getLogo(),
            ] : null,
            'lastUpdatedAt' => $offer->getLastUpdatedAt()->format(DATE_ATOM),
        ], $offers);

        return new JsonResponse($data);
    }

    #[Route('/api/admin/products/{productId}/offers', name: 'admin_product_offers_create', methods: ['POST'])]
    public function createForProduct(string $productId, Request $request, DocumentManager $dm): JsonResponse
    {
        if ($error = $this->requireAdmin($request, $dm)) return $error;

        $product = $dm->getRepository(Product::class)->find($productId);
        if (!$product) {
            return new JsonResponse(['error' => 'Product not found'], 404);
        }

        $data = json_decode($request->getContent(), true);
        $marketplaceId = $data['marketplaceId'] ?? null;
        $price = $data['price'] ?? null;
        $affiliateLink = $data['affiliateLink'] ?? null;

        if (!$marketplaceId || $price === null || !$affiliateLink) {
            return new JsonResponse(['error' => 'marketplaceId, price and affiliateLink required'], 400);
        }

        $marketplace = $dm->getRepository(Marketplace::class)->find($marketplaceId);
        if (!$marketplace) {
            return new JsonResponse(['error' => 'Marketplace not found'], 404);
        }

        // Check if offer already exists for this product and marketplace
        $existing = $dm->getRepository(ProductOffer::class)->findOneBy([
            'product' => $product,
            'marketplace' => $marketplace
        ]);
        if ($existing) {
            return new JsonResponse(['error' => 'Offer already exists for this marketplace'], 400);
        }

        $offer = new ProductOffer();
        $offer->setProduct($product);
        $offer->setMarketplace($marketplace);
        $offer->setPrice((float)$price);
        $offer->setAffiliateLink($affiliateLink);

        $dm->persist($offer);
        $dm->flush();

        return new JsonResponse([
            'id' => (string)$offer->getId(),
            'price' => $offer->getPrice(),
            'affiliateLink' => $offer->getAffiliateLink(),
            'marketplace' => [
                'id' => (string)$marketplace->getId(),
                'name' => $marketplace->getName(),
            ],
        ], 201);
    }

    #[Route('/api/admin/products/{productId}/offers/{offerId}', name: 'admin_product_offers_update', methods: ['PUT'])]
    public function updateForProduct(string $productId, string $offerId, Request $request, DocumentManager $dm): JsonResponse
    {
        if ($error = $this->requireAdmin($request, $dm)) return $error;

        $offer = $dm->getRepository(ProductOffer::class)->find($offerId);
        if (!$offer) {
            return new JsonResponse(['error' => 'Offer not found'], 404);
        }

        // Verify offer belongs to product
        if ((string)$offer->getProduct()->getId() !== $productId) {
            return new JsonResponse(['error' => 'Offer does not belong to this product'], 400);
        }

        $data = json_decode($request->getContent(), true);
        
        if (isset($data['price'])) {
            $offer->setPrice((float)$data['price']);
        }
        if (isset($data['affiliateLink'])) {
            $offer->setAffiliateLink($data['affiliateLink']);
        }
        if (isset($data['marketplaceId'])) {
            $marketplace = $dm->getRepository(Marketplace::class)->find($data['marketplaceId']);
            if (!$marketplace) {
                return new JsonResponse(['error' => 'Marketplace not found'], 404);
            }
            // prevent creating duplicate offer for this product+marketplace
            $existing = $dm->getRepository(ProductOffer::class)->findOneBy([
                'product' => $offer->getProduct(),
                'marketplace' => $marketplace,
            ]);
            if ($existing && (string)$existing->getId() !== (string)$offer->getId()) {
                return new JsonResponse(['error' => 'Offer already exists for this marketplace'], 400);
            }
            $offer->setMarketplace($marketplace);
        }

        $offer->touch();
        $dm->flush();

        return new JsonResponse([
            'id' => (string)$offer->getId(),
            'price' => $offer->getPrice(),
            'affiliateLink' => $offer->getAffiliateLink(),
        ]);
    }

    #[Route('/api/admin/products/{productId}/offers/{offerId}', name: 'admin_product_offers_delete', methods: ['DELETE'])]
    public function deleteForProduct(string $productId, string $offerId, Request $request, DocumentManager $dm): JsonResponse
    {
        if ($error = $this->requireAdmin($request, $dm)) return $error;

        $offer = $dm->getRepository(ProductOffer::class)->find($offerId);
        if (!$offer) {
            return new JsonResponse(['error' => 'Offer not found'], 404);
        }

        // Verify offer belongs to product
        if ((string)$offer->getProduct()->getId() !== $productId) {
            return new JsonResponse(['error' => 'Offer does not belong to this product'], 400);
        }

        $dm->remove($offer);
        $dm->flush();

        return new JsonResponse(['success' => true]);
    }
}
