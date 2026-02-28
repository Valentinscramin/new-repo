<?php

namespace App\MessageHandler;

use App\Message\ProcessComparisonMessage;
use App\Repository\ComparisonRepository;
use App\Repository\ProductRepository;
use App\Service\OpenAIService;
use App\Service\ProductScraperService;
use App\Service\ScoreCalculatorService;
use App\Entity\Product;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class ProcessComparisonMessageHandler implements MessageHandlerInterface
{
    private ComparisonRepository $comparisonRepository;
    private ProductRepository $productRepository;
    private ProductScraperService $scraperService;
    private OpenAIService $openAIService;
    private ScoreCalculatorService $scoreCalculator;
    private LoggerInterface $logger;

    public function __construct(
        ComparisonRepository $comparisonRepository,
        ProductRepository $productRepository,
        ProductScraperService $scraperService,
        OpenAIService $openAIService,
        ScoreCalculatorService $scoreCalculator,
        LoggerInterface $logger
    ) {
        $this->comparisonRepository = $comparisonRepository;
        $this->productRepository = $productRepository;
        $this->scraperService = $scraperService;
        $this->openAIService = $openAIService;
        $this->scoreCalculator = $scoreCalculator;
        $this->logger = $logger;
    }

    public function __invoke(ProcessComparisonMessage $message)
    {
        $comparisonId = $message->getComparisonId();
        $this->logger->info("Processing comparison: {$comparisonId}");

        $comparison = $this->comparisonRepository->find($comparisonId);
        
        if (!$comparison) {
            $this->logger->error("Comparison not found: {$comparisonId}");
            return;
        }

        try {
            $products = $comparison->getProducts();

            foreach ($products as $product) {
                $this->processProduct($product);
            }

            // Calculate winner
            $productsArray = $comparison->getProducts()->toArray();
            $winner = $this->scoreCalculator->determineWinner($productsArray);
            
            if ($winner) {
                $comparison->setWinnerProduct($winner);
            }

            $comparison->setStatus('completed');
            $this->comparisonRepository->save($comparison, true);

            $this->logger->info("Comparison {$comparisonId} completed successfully");

        } catch (\Exception $e) {
            $this->logger->error("Error processing comparison {$comparisonId}: " . $e->getMessage());
            $comparison->setStatus('failed');
            $this->comparisonRepository->save($comparison, true);
        }
    }

    private function processProduct(Product $product): void
    {
        $url = $product->getUrl();
        
        if (!$url) {
            $this->logger->warning("Product {$product->getId()} has no URL");
            return;
        }

        // Fetch HTML
        $html = $this->scraperService->fetchHtml($url);
        
        if (!$html) {
            $this->logger->error("Failed to fetch HTML for: {$url}");
            return;
        }

        // Save to temp
        $tempFile = $this->scraperService->saveHtmlToTemp($html, $url);

        try {
            // Extract product info with OpenAI
            $extractedData = $this->openAIService->extractProductInfo($html, $url);

            if ($extractedData) {
                // Update product with extracted data
                $product->setName($extractedData['name'] ?? 'Unknown Product');
                $product->setBrand($extractedData['brand'] ?? null);
                $product->setPrice($extractedData['price'] ?? null);
                $product->setCurrency($extractedData['currency'] ?? 'USD');
                $product->setCategory($extractedData['category'] ?? null);
                $product->setSpecs($extractedData['specs'] ?? []);
                $product->setStrengths($extractedData['strengths'] ?? []);
                $product->setWeaknesses($extractedData['weaknesses'] ?? []);
                $product->setRawExtraction($extractedData);

                // Calculate score
                $score = $this->scoreCalculator->calculateScore($product);
                $product->setScore((string)$score);

                $this->productRepository->save($product, true);
            }

        } finally {
            // Cleanup temp file
            $this->scraperService->cleanupTempFile($tempFile);
        }
    }
}
