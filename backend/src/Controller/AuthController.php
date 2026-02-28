<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api', name: 'api_')]
class AuthController extends AbstractController
{
    #[Route('/register', name: 'register', methods: ['POST'])]
    public function register(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        UserRepository $userRepository,
        ValidatorInterface $validator,
        JWTTokenManagerInterface $jwtManager
    ): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['email']) || !isset($data['password'])) {
            return $this->json([
                'error' => 'Email and password are required'
            ], Response::HTTP_BAD_REQUEST);
        }

        // Check if user already exists
        $existingUser = $userRepository->findOneBy(['email' => $data['email']]);
        if ($existingUser) {
            return $this->json([
                'error' => 'Email already registered'
            ], Response::HTTP_CONFLICT);
        }

        $user = new User();
        $user->setEmail($data['email']);
        
        $hashedPassword = $passwordHasher->hashPassword(
            $user,
            $data['password']
        );
        $user->setPassword($hashedPassword);

        $errors = $validator->validate($user);
        if (count($errors) > 0) {
            return $this->json([
                'error' => 'Validation failed',
                'details' => (string) $errors
            ], Response::HTTP_BAD_REQUEST);
        }

        $userRepository->save($user, true);

        // Generate JWT token for auto-login
        $token = $jwtManager->create($user);

        return $this->json([
            'message' => 'User registered successfully',
            'user' => [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
            ],
            'token' => $token
        ], Response::HTTP_CREATED);
    }

    #[Route('/login', name: 'login', methods: ['POST'])]
    public function login(): JsonResponse
    {
        // This will be handled by lexik_jwt_authentication
        // Just a placeholder for the route
        return $this->json([
            'message' => 'Login endpoint - should be handled by JWT bundle'
        ]);
    }

    #[Route('/me', name: 'me', methods: ['GET'])]
    public function me(): JsonResponse
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->json([
                'error' => 'Not authenticated'
            ], Response::HTTP_UNAUTHORIZED);
        }

        return $this->json([
            'id' => $user->getId(),
            'email' => $user->getEmail(),
        ]);
    }
}
