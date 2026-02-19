<?php
namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ODM\MongoDB\DocumentManager;
use App\Document\User;
use App\Document\Product;
use App\Document\Favorite;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class FavoritesController extends AbstractController
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

    #[Route('/api/favorites', name: 'api_favorites_list', methods: ['GET'])]
    public function list(Request $request, DocumentManager $dm): JsonResponse
    {
        $user = $this->getUserFromRequest($request, $dm);
        if (!$user) return new JsonResponse(['error' => 'Unauthorized'], 401);

        $favorites = $dm->getRepository(Favorite::class)->findBy(['user' => $user]);
        $data = array_map(function ($f) {
            return [
                'id' => (string)$f->getId(),
                'product' => $f->getProduct() ? (string)$f->getProduct()->getId() : null,
            ];
        }, $favorites);

        return new JsonResponse($data);
    }

    #[Route('/api/favorites', name: 'api_favorites_create', methods: ['POST'])]
    public function create(Request $request, DocumentManager $dm): JsonResponse
    {
        $user = $this->getUserFromRequest($request, $dm);
        if (!$user) return new JsonResponse(['error' => 'Unauthorized'], 401);

        $data = json_decode($request->getContent(), true);
        $productId = $data['productId'] ?? null;
        if (!$productId) return new JsonResponse(['error' => 'productId required'], 400);

        $product = $dm->getRepository(Product::class)->find($productId);
        if (!$product) return new JsonResponse(['error' => 'Product not found'], 404);

        $favorite = new Favorite();
        $favorite->setUser($user);
        $favorite->setProduct($product);
        $dm->persist($favorite);
        $dm->flush();

        return new JsonResponse(['id' => (string)$favorite->getId(), 'product' => (string)$product->getId()], 201);
    }
}
