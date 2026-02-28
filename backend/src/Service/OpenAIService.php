<?php

namespace App\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Log\LoggerInterface;

class OpenAIService
{
    private Client $client;
    private string $apiKey;
    private LoggerInterface $logger;

    public function __construct(string $openaiApiKey, LoggerInterface $logger)
    {
        $this->apiKey = $openaiApiKey;
        $this->logger = $logger;
        $this->client = new Client([
            'base_uri' => 'https://api.openai.com/',
            'timeout' => 60,
        ]);
    }

    public function extractProductInfo(string $htmlContent, string $url): ?array
    {
        // Clean and limit HTML content
        $cleanedContent = $this->cleanHtml($htmlContent);
        
        $prompt = $this->buildExtractionPrompt($cleanedContent, $url);

        try {
            $response = $this->client->post('v1/chat/completions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => 'gpt-3.5-turbo',
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'You are an expert in hardware, gaming equipment, and digital nomad gear. Extract structured product information from HTML content.'
                        ],
                        [
                            'role' => 'user',
                            'content' => $prompt
                        ]
                    ],
                    'temperature' => 0.3,
                    'max_tokens' => 1500,
                ],
            ]);

            $data = json_decode($response->getBody()->getContents(), true);
            
            if (isset($data['choices'][0]['message']['content'])) {
                $content = $data['choices'][0]['message']['content'];
                
                // Try to extract JSON from the response
                if (preg_match('/\{.*\}/s', $content, $matches)) {
                    $extractedData = json_decode($matches[0], true);
                    if ($extractedData) {
                        return $extractedData;
                    }
                }
                
                return json_decode($content, true);
            }

            return null;
        } catch (GuzzleException $e) {
            $this->logger->error('OpenAI API Error: ' . $e->getMessage());
            return null;
        } catch (\Exception $e) {
            $this->logger->error('Extraction Error: ' . $e->getMessage());
            return null;
        }
    }

    private function cleanHtml(string $html): string
    {
        // Remove scripts and styles
        $html = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $html);
        $html = preg_replace('/<style\b[^>]*>(.*?)<\/style>/is', '', $html);
        
        // Strip HTML tags
        $text = strip_tags($html);
        
        // Remove excessive whitespace
        $text = preg_replace('/\s+/', ' ', $text);
        
        // Limit length (approximately 3000 tokens = ~12000 characters)
        return substr(trim($text), 0, 12000);
    }

    private function buildExtractionPrompt(string $content, string $url): string
    {
        return <<<PROMPT
Você é um especialista em hardware gamer e equipamentos para nômades digitais.

Extraia as seguintes informações do produto do conteúdo fornecido:

Conteúdo do produto:
{$content}

URL do produto: {$url}

Retorne APENAS um JSON válido (sem markdown, sem texto adicional) com a seguinte estrutura:
{
    "name": "Nome completo do produto",
    "brand": "Marca",
    "model": "Modelo",
    "price": 0.00,
    "currency": "USD",
    "category": "notebook|monitor|keyboard|mouse|etc",
    "specs": {
        "processor": "...",
        "gpu": "...",
        "ram": "...",
        "storage": "...",
        "screen": "...",
        "weight": "...",
        "battery": "...",
        "connectivity": "..."
    },
    "strengths": ["Ponto forte 1", "Ponto forte 2"],
    "weaknesses": ["Ponto fraco 1", "Ponto fraco 2"],
    "targetAudience": "Descrição do público ideal"
}

Se alguma informação não estiver disponível, use null para valores numéricos e strings vazias para texto.
PROMPT;
    }
}
