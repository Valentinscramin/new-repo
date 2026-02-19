<?php
namespace App\Controller\Api\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ODM\MongoDB\DocumentManager;
use App\Document\Category;

class CategoryController extends AbstractController
{
    use AdminAuthTrait;

    #[Route('/api/admin/categories', name: 'admin_categories_list', methods: ['GET'])]
    public function list(Request $request, DocumentManager $dm): JsonResponse
    {
        if ($error = $this->requireAdmin($request, $dm)) return $error;

        $categories = $dm->getRepository(Category::class)->findAll();
        $data = array_map(fn($c) => [
            'id' => (string)$c->getId(),
            'name' => $c->getName(),
            'slug' => $c->getSlug(),
            'createdAt' => $c->getCreatedAt()->format(DATE_ATOM),
        ], $categories);

        return new JsonResponse($data);
    }

    #[Route('/api/admin/categories', name: 'admin_categories_create', methods: ['POST'])]
    public function create(Request $request, DocumentManager $dm): JsonResponse
    {
        if ($error = $this->requireAdmin($request, $dm)) return $error;

        $data = json_decode($request->getContent(), true);
        $name = $data['name'] ?? null;
        $slug = $data['slug'] ?? null;

        if (!$name || !$slug) {
            return new JsonResponse(['error' => 'name and slug required'], 400);
        }

        $existing = $dm->getRepository(Category::class)->findOneBy(['slug' => $slug]);
        if ($existing) {
            return new JsonResponse(['error' => 'Slug already exists'], 400);
        }

        $category = new Category();
        $category->setName($name);
        $category->setSlug($slug);
        $dm->persist($category);
        $dm->flush();

        return new JsonResponse([
            'id' => (string)$category->getId(),
            'name' => $category->getName(),
            'slug' => $category->getSlug(),
        ], 201);
    }

    #[Route('/api/admin/categories/{id}', name: 'admin_categories_get', methods: ['GET'])]
    public function get(string $id, Request $request, DocumentManager $dm): JsonResponse
    {
        if ($error = $this->requireAdmin($request, $dm)) return $error;

        $category = $dm->getRepository(Category::class)->find($id);
        if (!$category) {
            return new JsonResponse(['error' => 'Not found'], 404);
        }

        return new JsonResponse([
            'id' => (string)$category->getId(),
            'name' => $category->getName(),
            'slug' => $category->getSlug(),
            'createdAt' => $category->getCreatedAt()->format(DATE_ATOM),
        ]);
    }

    #[Route('/api/admin/categories/{id}', name: 'admin_categories_update', methods: ['PUT'])]
    public function update(string $id, Request $request, DocumentManager $dm): JsonResponse
    {
        if ($error = $this->requireAdmin($request, $dm)) return $error;

        $category = $dm->getRepository(Category::class)->find($id);
        if (!$category) {
            return new JsonResponse(['error' => 'Not found'], 404);
        }

        $data = json_decode($request->getContent(), true);
        if (isset($data['name'])) $category->setName($data['name']);
        if (isset($data['slug'])) {
            $newSlug = $data['slug'];
            $existing = $dm->getRepository(Category::class)->findOneBy(['slug' => $newSlug]);
            if ($existing && (string)$existing->getId() !== (string)$category->getId()) {
                return new JsonResponse(['error' => 'Slug already exists'], 400);
            }
            $category->setSlug($newSlug);
        }

        $dm->flush();

        return new JsonResponse([
            'id' => (string)$category->getId(),
            'name' => $category->getName(),
            'slug' => $category->getSlug(),
        ]);
    }

    #[Route('/api/admin/categories/{id}', name: 'admin_categories_delete', methods: ['DELETE'])]
    public function delete(string $id, Request $request, DocumentManager $dm): JsonResponse
    {
        if ($error = $this->requireAdmin($request, $dm)) return $error;

        $category = $dm->getRepository(Category::class)->find($id);
        if (!$category) {
            return new JsonResponse(['error' => 'Not found'], 404);
        }

        $dm->remove($category);
        $dm->flush();

        return new JsonResponse(['success' => true]);
    }
}
