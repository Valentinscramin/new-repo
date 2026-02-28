<?php

namespace App\Service;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Psr\Log\LoggerInterface;

class ProductScraperService
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function fetchHtml(string $url): ?string
    {
        try {
            $client = HttpClient::create([
                'timeout' => 30,
                'headers' => [
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
                ],
            ]);

            $response = $client->request('GET', $url);
            
            if ($response->getStatusCode() === 200) {
                return $response->getContent();
            }

            $this->logger->error("Failed to fetch URL: {$url}, Status: " . $response->getStatusCode());
            return null;

        } catch (TransportExceptionInterface $e) {
            $this->logger->error("Transport error fetching URL: {$url}, Error: " . $e->getMessage());
            return null;
        } catch (\Exception $e) {
            $this->logger->error("Error fetching URL: {$url}, Error: " . $e->getMessage());
            return null;
        }
    }

    public function saveHtmlToTemp(string $html, string $identifier): string
    {
        $tmpDir = sys_get_temp_dir() . '/product_comparison';
        if (!is_dir($tmpDir)) {
            mkdir($tmpDir, 0777, true);
        }

        $fileName = $tmpDir . '/' . md5($identifier . time()) . '.html';
        file_put_contents($fileName, $html);

        return $fileName;
    }

    public function cleanupTempFile(string $filePath): void
    {
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }
}
