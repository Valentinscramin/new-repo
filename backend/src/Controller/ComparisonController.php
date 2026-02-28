<?php

namespace App\Controller;

use App\Entity\Comparison;
use App\Entity\Product;
use App\Message\ProcessComparisonMessage;
use App\Repository\ComparisonRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/comparisons', name: 'api_comparisons_')]
class ComparisonController extends AbstractController
{
    #[Route('', name: 'create', methods: ['POST'])]
    public function create(
        Request $request,
        ComparisonRepository $comparisonRepository,
        MessageBusInterface $messageBus
    ): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['urls']) || !is_array($data['urls']) || count($data['urls']) !== 3) {
            return $this->json([
                'error' => 'Exactly 3 product URLs are required'
            ], Response::HTTP_BAD_REQUEST);
        }

        // Validate URLs
        foreach ($data['urls'] as $url) {
            if (!filter_var($url, FILTER_VALIDATE_URL)) {
                return $this->json([
                    'error' => 'Invalid URL provided: ' . $url
                ], Response::HTTP_BAD_REQUEST);
            }
        }

        $comparison = new Comparison();
        
        // Set user if authenticated
        $user = $this->getUser();
        if ($user) {
            $comparison->setUser($user);
        }

        // Create product placeholders
        foreach ($data['urls'] as $url) {
            $product = new Product();
            $product->setUrl($url);
            $product->setName('Processing...');
            $comparison->addProduct($product);
        }

        $comparisonRepository->save($comparison, true);

        // Dispatch async message for processing
        $messageBus->dispatch(new ProcessComparisonMessage($comparison->getId()));

        return $this->json([
            'message' => 'Comparison created and processing started',
            'comparisonId' => $comparison->getId(),
            'status' => $comparison->getStatus()
        ], Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(int $id, ComparisonRepository $comparisonRepository): JsonResponse
    {
        $comparison = $comparisonRepository->find($id);

        if (!$comparison) {
            return $this->json([
                'error' => 'Comparison not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $productsData = [];
        foreach ($comparison->getProducts() as $product) {
            $productsData[] = [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'brand' => $product->getBrand(),
                'price' => $product->getPrice(),
                'currency' => $product->getCurrency(),
                'category' => $product->getCategory(),
                'specs' => $product->getSpecs(),
                'strengths' => $product->getStrengths(),
                'weaknesses' => $product->getWeaknesses(),
                'score' => $product->getScore(),
                'url' => $product->getUrl(),
            ];
        }

        return $this->json([
            'id' => $comparison->getId(),
            'status' => $comparison->getStatus(),
            'createdAt' => $comparison->getCreatedAt()?->format('Y-m-d H:i:s'),
            'products' => $productsData,
            'winnerId' => $comparison->getWinnerProduct()?->getId(),
        ]);
    }

    #[Route('', name: 'list', methods: ['GET'])]
    public function list(ComparisonRepository $comparisonRepository): JsonResponse
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->json([
                'error' => 'Authentication required'
            ], Response::HTTP_UNAUTHORIZED);
        }

        $comparisons = $comparisonRepository->findByUser($user);

        $data = array_map(function(Comparison $comparison) {
            return [
                'id' => $comparison->getId(),
                'status' => $comparison->getStatus(),
                'createdAt' => $comparison->getCreatedAt()?->format('Y-m-d H:i:s'),
                'productCount' => $comparison->getProducts()->count(),
                'winnerId' => $comparison->getWinnerProduct()?->getId(),
            ];
        }, $comparisons);

        return $this->json($data);
    }
}
