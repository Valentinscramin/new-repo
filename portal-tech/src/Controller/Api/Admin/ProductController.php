<?php
namespace App\Controller\Api\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ODM\MongoDB\DocumentManager;
use App\Document\Product;
use App\Document\ProductOffer;
use App\Document\Category;
use App\Document\Supplier;

class ProductController extends AbstractController
{
    use AdminAuthTrait;

    #[Route('/api/admin/products', name: 'admin_products_list', methods: ['GET'])]
    public function list(Request $request, DocumentManager $dm): JsonResponse
    {
        if ($error = $this->requireAdmin($request, $dm)) return $error;

        $products = $dm->getRepository(Product::class)->findAll();
        $data = array_map(function($p) use ($dm) {
            $offersCount = count($dm->getRepository(ProductOffer::class)->findBy(['product' => $p]));
            return [
                'id' => (string)$p->getId(),
                'name' => $p->getName(),
                'slug' => $p->getSlug(),
                'description' => $p->getDescription(),
                'category' => $p->getCategory() ? [
                    'id' => (string)$p->getCategory()->getId(),
                    'name' => $p->getCategory()->getName(),
                ] : null,
                'supplier' => $p->getSupplier() ? [
                    'id' => (string)$p->getSupplier()->getId(),
                    'name' => $p->getSupplier()->getName(),
                ] : null,
                'offersCount' => $offersCount,
                'createdAt' => $p->getCreatedAt()->format(DATE_ATOM),
            ];
        }, $products);

        return new JsonResponse($data);
    }

    #[Route('/api/admin/products', name: 'admin_products_create', methods: ['POST'])]
    public function create(Request $request, DocumentManager $dm): JsonResponse
    {
        if ($error = $this->requireAdmin($request, $dm)) return $error;

        $data = json_decode($request->getContent(), true);
        $name = $data['name'] ?? null;
        $slug = $data['slug'] ?? null;
        $categoryId = $data['categoryId'] ?? null;
        $supplierId = $data['supplierId'] ?? null;

        if (!$name || !$slug) {
            return new JsonResponse(['error' => 'name and slug required'], 400);
        }

        // Rule: Product must have category
        if (!$categoryId) {
            return new JsonResponse(['error' => 'categoryId required - Product must have category'], 400);
        }

        $category = $dm->getRepository(Category::class)->find($categoryId);
        if (!$category) {
            return new JsonResponse(['error' => 'Category not found'], 404);
        }

        // Supplier is now optional - products can have multiple offers from different marketplaces
        $supplier = null;
        if ($supplierId) {
            $supplier = $dm->getRepository(Supplier::class)->find($supplierId);
            if (!$supplier) {
                return new JsonResponse(['error' => 'Supplier not found'], 404);
            }
        }

        $existing = $dm->getRepository(Product::class)->findOneBy(['slug' => $slug]);
        if ($existing) {
            return new JsonResponse(['error' => 'Slug already exists'], 400);
        }

        $product = new Product();
        $product->setName($name);
        $product->setSlug($slug);
        $product->setCategory($category);
        $product->setSupplier($supplier);
        if (isset($data['description'])) $product->setDescription($data['description']);
        
        $dm->persist($product);
        $dm->flush();

        return new JsonResponse([
            'id' => (string)$product->getId(),
            'name' => $product->getName(),
            'slug' => $product->getSlug(),
        ], 201);
    }

    #[Route('/api/admin/products/{id}', name: 'admin_products_get', methods: ['GET'])]
    public function get(string $id, Request $request, DocumentManager $dm): JsonResponse
    {
        if ($error = $this->requireAdmin($request, $dm)) return $error;

        $product = $dm->getRepository(Product::class)->find($id);
        if (!$product) {
            return new JsonResponse(['error' => 'Not found'], 404);
        }

        $offers = $dm->getRepository(ProductOffer::class)->findBy(['product' => $product], ['price' => 'ASC']);
        $offersData = array_map(fn($o) => [
            'id' => (string)$o->getId(),
            'price' => $o->getPrice(),
            'affiliateLink' => $o->getAffiliateLink(),
            'marketplace' => $o->getMarketplace() ? [
                'id' => (string)$o->getMarketplace()->getId(),
                'name' => $o->getMarketplace()->getName(),
            ] : null,
            'lastUpdatedAt' => $o->getLastUpdatedAt()->format(DATE_ATOM),
        ], $offers);

        return new JsonResponse([
            'id' => (string)$product->getId(),
            'name' => $product->getName(),
            'slug' => $product->getSlug(),
            'description' => $product->getDescription(),
            'category' => $product->getCategory() ? [
                'id' => (string)$product->getCategory()->getId(),
                'name' => $product->getCategory()->getName(),
            ] : null,
            'supplier' => $product->getSupplier() ? [
                'id' => (string)$product->getSupplier()->getId(),
                'name' => $product->getSupplier()->getName(),
            ] : null,
            'offers' => $offersData,
            'offersCount' => count($offersData),
            'createdAt' => $product->getCreatedAt()->format(DATE_ATOM),
        ]);
    }

    #[Route('/api/admin/products/{id}', name: 'admin_products_update', methods: ['PUT'])]
    public function update(string $id, Request $request, DocumentManager $dm): JsonResponse
    {
        if ($error = $this->requireAdmin($request, $dm)) return $error;

        $product = $dm->getRepository(Product::class)->find($id);
        if (!$product) {
            return new JsonResponse(['error' => 'Not found'], 404);
        }

        $data = json_decode($request->getContent(), true);
        if (isset($data['name'])) $product->setName($data['name']);
        if (isset($data['slug'])) {
            $newSlug = $data['slug'];
            $existing = $dm->getRepository(Product::class)->findOneBy(['slug' => $newSlug]);
            if ($existing && (string)$existing->getId() !== (string)$product->getId()) {
                return new JsonResponse(['error' => 'Slug already exists'], 400);
            }
            $product->setSlug($newSlug);
        }
        if (isset($data['description'])) $product->setDescription($data['description']);
        
        if (isset($data['categoryId'])) {
            $category = $dm->getRepository(Category::class)->find($data['categoryId']);
            if (!$category) {
                return new JsonResponse(['error' => 'Category not found'], 404);
            }
            $product->setCategory($category);
        }

        if (isset($data['supplierId'])) {
            $supplier = $dm->getRepository(Supplier::class)->find($data['supplierId']);
            if (!$supplier) {
                return new JsonResponse(['error' => 'Supplier not found'], 404);
            }
            $product->setSupplier($supplier);
        }

        $dm->flush();

        return new JsonResponse([
            'id' => (string)$product->getId(),
            'name' => $product->getName(),
        ]);
    }

    #[Route('/api/admin/products/{id}', name: 'admin_products_delete', methods: ['DELETE'])]
    public function delete(string $id, Request $request, DocumentManager $dm): JsonResponse
    {
        if ($error = $this->requireAdmin($request, $dm)) return $error;

        $product = $dm->getRepository(Product::class)->find($id);
        if (!$product) {
            return new JsonResponse(['error' => 'Not found'], 404);
        }

        // Remove all offers associated with this product
        $offers = $dm->getRepository(ProductOffer::class)->findBy(['product' => $product]);
        foreach ($offers as $offer) {
            $dm->remove($offer);
        }

        $dm->remove($product);
        $dm->flush();

        return new JsonResponse(['success' => true]);
    }
}
