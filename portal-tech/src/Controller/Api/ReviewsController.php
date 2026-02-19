<?php
namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ODM\MongoDB\DocumentManager;
use App\Document\User;
use App\Document\Product;
use App\Document\Review;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class ReviewsController extends AbstractController
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

    #[Route('/api/reviews', name: 'api_reviews_list', methods: ['GET'])]
    public function list(Request $request, DocumentManager $dm): JsonResponse
    {
        $user = $this->getUserFromRequest($request, $dm);
        if (!$user) return new JsonResponse(['error' => 'Unauthorized'], 401);

        $reviews = $dm->getRepository(Review::class)->findBy(['user' => $user]);
        $data = array_map(function ($r) {
            return [
                'id' => (string)$r->getId(),
                'product' => $r->getProduct() ? (string)$r->getProduct()->getId() : null,
                'rating' => $r->getRating(),
                'comment' => $r->getComment(),
                'createdAt' => $r->getCreatedAt()->format(DATE_ATOM),
            ];
        }, $reviews);

        return new JsonResponse($data);
    }

    #[Route('/api/reviews', name: 'api_reviews_create', methods: ['POST'])]
    public function create(Request $request, DocumentManager $dm): JsonResponse
    {
        $user = $this->getUserFromRequest($request, $dm);
        if (!$user) return new JsonResponse(['error' => 'Unauthorized'], 401);

        $data = json_decode($request->getContent(), true);
        $productId = $data['productId'] ?? null;
        $rating = isset($data['rating']) ? (int)$data['rating'] : null;
        $comment = $data['comment'] ?? '';

        if (!$productId || !$rating) return new JsonResponse(['error' => 'productId and rating required'], 400);

        $product = $dm->getRepository(Product::class)->find($productId);
        if (!$product) return new JsonResponse(['error' => 'Product not found'], 404);

        $review = new Review();
        $review->setUser($user);
        $review->setProduct($product);
        $review->setRating($rating);
        $review->setComment($comment);
        $dm->persist($review);
        $dm->flush();

        return new JsonResponse(['id' => (string)$review->getId()], 201);
    }
}
