<?php
namespace App\Controller\Api\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ODM\MongoDB\DocumentManager;
use App\Document\Marketplace;

class MarketplaceController extends AbstractController
{
    use AdminAuthTrait;

    #[Route('/api/admin/marketplaces', name: 'admin_marketplaces_list', methods: ['GET'])]
    public function list(Request $request, DocumentManager $dm): JsonResponse
    {
        if ($error = $this->requireAdmin($request, $dm)) return $error;

        $marketplaces = $dm->getRepository(Marketplace::class)->findAll();
        $data = array_map(fn($m) => [
            'id' => (string)$m->getId(),
            'name' => $m->getName(),
            'slug' => $m->getSlug(),
            'affiliateBaseUrl' => $m->getAffiliateBaseUrl(),
            'logo' => $m->getLogo(),
            'createdAt' => $m->getCreatedAt()->format(DATE_ATOM),
        ], $marketplaces);

        return new JsonResponse($data);
    }

    #[Route('/api/admin/marketplaces', name: 'admin_marketplaces_create', methods: ['POST'])]
    public function create(Request $request, DocumentManager $dm): JsonResponse
    {
        if ($error = $this->requireAdmin($request, $dm)) return $error;

        $data = json_decode($request->getContent(), true);
        $name = $data['name'] ?? null;
        $slug = $data['slug'] ?? null;
        $affiliateBaseUrl = $data['affiliateBaseUrl'] ?? null;

        if (!$name || !$slug || !$affiliateBaseUrl) {
            return new JsonResponse(['error' => 'name, slug and affiliateBaseUrl required'], 400);
        }

        $existing = $dm->getRepository(Marketplace::class)->findOneBy(['slug' => $slug]);
        if ($existing) {
            return new JsonResponse(['error' => 'Slug already exists'], 400);
        }

        $marketplace = new Marketplace();
        $marketplace->setName($name);
        $marketplace->setSlug($slug);
        $marketplace->setAffiliateBaseUrl($affiliateBaseUrl);
        if (isset($data['logo'])) $marketplace->setLogo($data['logo']);
        
        $dm->persist($marketplace);
        $dm->flush();

        return new JsonResponse([
            'id' => (string)$marketplace->getId(),
            'name' => $marketplace->getName(),
            'slug' => $marketplace->getSlug(),
        ], 201);
    }

    #[Route('/api/admin/marketplaces/{id}', name: 'admin_marketplaces_get', methods: ['GET'])]
    public function get(string $id, Request $request, DocumentManager $dm): JsonResponse
    {
        if ($error = $this->requireAdmin($request, $dm)) return $error;

        $marketplace = $dm->getRepository(Marketplace::class)->find($id);
        if (!$marketplace) {
            return new JsonResponse(['error' => 'Not found'], 404);
        }

        return new JsonResponse([
            'id' => (string)$marketplace->getId(),
            'name' => $marketplace->getName(),
            'slug' => $marketplace->getSlug(),
            'affiliateBaseUrl' => $marketplace->getAffiliateBaseUrl(),
            'logo' => $marketplace->getLogo(),
            'createdAt' => $marketplace->getCreatedAt()->format(DATE_ATOM),
        ]);
    }

    #[Route('/api/admin/marketplaces/{id}', name: 'admin_marketplaces_update', methods: ['PUT'])]
    public function update(string $id, Request $request, DocumentManager $dm): JsonResponse
    {
        if ($error = $this->requireAdmin($request, $dm)) return $error;

        $marketplace = $dm->getRepository(Marketplace::class)->find($id);
        if (!$marketplace) {
            return new JsonResponse(['error' => 'Not found'], 404);
        }

        $data = json_decode($request->getContent(), true);
        if (isset($data['name'])) $marketplace->setName($data['name']);
        if (isset($data['slug'])) {
            $newSlug = $data['slug'];
            $existing = $dm->getRepository(Marketplace::class)->findOneBy(['slug' => $newSlug]);
            if ($existing && (string)$existing->getId() !== (string)$marketplace->getId()) {
                return new JsonResponse(['error' => 'Slug already exists'], 400);
            }
            $marketplace->setSlug($newSlug);
        }
        if (isset($data['affiliateBaseUrl'])) $marketplace->setAffiliateBaseUrl($data['affiliateBaseUrl']);
        if (isset($data['logo'])) $marketplace->setLogo($data['logo']);

        $dm->flush();

        return new JsonResponse([
            'id' => (string)$marketplace->getId(),
            'name' => $marketplace->getName(),
        ]);
    }

    #[Route('/api/admin/marketplaces/{id}', name: 'admin_marketplaces_delete', methods: ['DELETE'])]
    public function delete(string $id, Request $request, DocumentManager $dm): JsonResponse
    {
        if ($error = $this->requireAdmin($request, $dm)) return $error;

        $marketplace = $dm->getRepository(Marketplace::class)->find($id);
        if (!$marketplace) {
            return new JsonResponse(['error' => 'Not found'], 404);
        }

        $dm->remove($marketplace);
        $dm->flush();

        return new JsonResponse(['success' => true]);
    }
}
