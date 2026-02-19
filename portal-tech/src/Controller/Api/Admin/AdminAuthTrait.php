<?php
namespace App\Controller\Api\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ODM\MongoDB\DocumentManager;
use App\Document\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

trait AdminAuthTrait
{
    private function getAuthenticatedUser(Request $request, DocumentManager $dm): ?User
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

    private function requireAdmin(Request $request, DocumentManager $dm): ?JsonResponse
    {
        $user = $this->getAuthenticatedUser($request, $dm);
        if (!$user) {
            return new JsonResponse(['error' => 'Unauthorized'], 401);
        }
        if ($user->getRole() !== 'ADMIN') {
            return new JsonResponse(['error' => 'Forbidden - Admin access required'], 403);
        }
        return null;
    }
}
