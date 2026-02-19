<?php
namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ODM\MongoDB\DocumentManager;
use App\Document\User;
use Firebase\JWT\JWT;

class AuthController extends AbstractController
{
    #[Route('/api/register', name: 'api_register', methods: ['POST'])]
    public function register(Request $request, DocumentManager $dm): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;
        $name = $data['name'] ?? null;

        if (!$email || !$password) {
            return new JsonResponse(['error' => 'email and password required'], 400);
        }

        $existing = $dm->getRepository(User::class)->findOneBy(['email' => $email]);
        if ($existing) {
            return new JsonResponse(['error' => 'Email already used'], 400);
        }

        $user = new User();
        if ($name) {
            $user->setName($name);
        }
        $user->setEmail($email);
        $user->setPassword(password_hash($password, PASSWORD_BCRYPT));
        $dm->persist($user);
        $dm->flush();

        return new JsonResponse(['id' => (string)$user->getId(), 'email' => $user->getEmail()], 201);
    }

    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function login(Request $request, DocumentManager $dm): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;

        if (!$email || !$password) {
            return new JsonResponse(['error' => 'email and password required'], 400);
        }

        $user = $dm->getRepository(User::class)->findOneBy(['email' => $email]);
        if (!$user || !password_verify($password, $user->getPassword())) {
            return new JsonResponse(['error' => 'Invalid credentials'], 401);
        }

        $now = time();
        $exp = $now + 3600 * 24 * 7;
        $payload = [
            'sub' => (string)$user->getId(),
            'email' => $user->getEmail(),
            'iat' => $now,
            'exp' => $exp,
        ];

        $secret = $_ENV['APP_SECRET'] ?? getenv('APP_SECRET');
        $token = JWT::encode($payload, $secret, 'HS256');

        return new JsonResponse([
            'token' => $token,
            'user' => [
                'id' => (string)$user->getId(),
                'email' => $user->getEmail(),
                'name' => $user->getName(),
            ],
        ]);
    }
}
