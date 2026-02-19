<?php
namespace App\Controller\Api\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ODM\MongoDB\DocumentManager;
use App\Document\Supplier;

class SupplierController extends AbstractController
{
    use AdminAuthTrait;

    #[Route('/api/admin/suppliers', name: 'admin_suppliers_list', methods: ['GET'])]
    public function list(Request $request, DocumentManager $dm): JsonResponse
    {
        if ($error = $this->requireAdmin($request, $dm)) return $error;

        $suppliers = $dm->getRepository(Supplier::class)->findAll();
        $data = array_map(fn($s) => [
            'id' => (string)$s->getId(),
            'name' => $s->getName(),
            'website' => $s->getWebsite(),
            'createdAt' => $s->getCreatedAt()->format(DATE_ATOM),
        ], $suppliers);

        return new JsonResponse($data);
    }

    #[Route('/api/admin/suppliers', name: 'admin_suppliers_create', methods: ['POST'])]
    public function create(Request $request, DocumentManager $dm): JsonResponse
    {
        if ($error = $this->requireAdmin($request, $dm)) return $error;

        $data = json_decode($request->getContent(), true);
        $name = $data['name'] ?? null;

        if (!$name) {
            return new JsonResponse(['error' => 'name required'], 400);
        }

        $supplier = new Supplier();
        $supplier->setName($name);
        if (isset($data['website'])) $supplier->setWebsite($data['website']);
        
        $dm->persist($supplier);
        $dm->flush();

        return new JsonResponse([
            'id' => (string)$supplier->getId(),
            'name' => $supplier->getName(),
            'website' => $supplier->getWebsite(),
        ], 201);
    }

    #[Route('/api/admin/suppliers/{id}', name: 'admin_suppliers_get', methods: ['GET'])]
    public function get(string $id, Request $request, DocumentManager $dm): JsonResponse
    {
        if ($error = $this->requireAdmin($request, $dm)) return $error;

        $supplier = $dm->getRepository(Supplier::class)->find($id);
        if (!$supplier) {
            return new JsonResponse(['error' => 'Not found'], 404);
        }

        return new JsonResponse([
            'id' => (string)$supplier->getId(),
            'name' => $supplier->getName(),
            'website' => $supplier->getWebsite(),
            'createdAt' => $supplier->getCreatedAt()->format(DATE_ATOM),
        ]);
    }

    #[Route('/api/admin/suppliers/{id}', name: 'admin_suppliers_update', methods: ['PUT'])]
    public function update(string $id, Request $request, DocumentManager $dm): JsonResponse
    {
        if ($error = $this->requireAdmin($request, $dm)) return $error;

        $supplier = $dm->getRepository(Supplier::class)->find($id);
        if (!$supplier) {
            return new JsonResponse(['error' => 'Not found'], 404);
        }

        $data = json_decode($request->getContent(), true);
        if (isset($data['name'])) $supplier->setName($data['name']);
        if (isset($data['website'])) $supplier->setWebsite($data['website']);

        $dm->flush();

        return new JsonResponse([
            'id' => (string)$supplier->getId(),
            'name' => $supplier->getName(),
        ]);
    }

    #[Route('/api/admin/suppliers/{id}', name: 'admin_suppliers_delete', methods: ['DELETE'])]
    public function delete(string $id, Request $request, DocumentManager $dm): JsonResponse
    {
        if ($error = $this->requireAdmin($request, $dm)) return $error;

        $supplier = $dm->getRepository(Supplier::class)->find($id);
        if (!$supplier) {
            return new JsonResponse(['error' => 'Not found'], 404);
        }

        $dm->remove($supplier);
        $dm->flush();

        return new JsonResponse(['success' => true]);
    }
}
