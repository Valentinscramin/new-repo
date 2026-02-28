<?php

namespace App\Service;

use App\Entity\Product;

class ScoreCalculatorService
{
    public function calculateScore(Product $product, string $focusType = 'balanced'): float
    {
        $specs = $product->getSpecs() ?? [];
        $price = (float) $product->getPrice();

        $performanceScore = $this->calculatePerformanceScore($specs);
        $qualityScore = $this->calculateQualityScore($specs);
        $mobilityScore = $this->calculateMobilityScore($specs);
        $priceScore = $this->calculatePriceScore($price);

        // Weight based on focus type
        $weights = match($focusType) {
            'gamer' => [
                'performance' => 0.5,
                'quality' => 0.2,
                'mobility' => 0.1,
                'price' => 0.2
            ],
            'nomad' => [
                'performance' => 0.25,
                'quality' => 0.2,
                'mobility' => 0.35,
                'price' => 0.2
            ],
            default => [
                'performance' => 0.35,
                'quality' => 0.25,
                'mobility' => 0.2,
                'price' => 0.2
            ]
        };

        $totalScore = (
            $performanceScore * $weights['performance'] +
            $qualityScore * $weights['quality'] +
            $mobilityScore * $weights['mobility'] +
            $priceScore * $weights['price']
        );

        return round($totalScore, 2);
    }

    private function calculatePerformanceScore(array $specs): float
    {
        $score = 0;
        $maxScore = 100;

        // GPU Score (0-30 points)
        $gpu = strtolower($specs['gpu'] ?? '');
        if (str_contains($gpu, 'rtx 4090') || str_contains($gpu, 'rtx 4080')) {
            $score += 30;
        } elseif (str_contains($gpu, 'rtx 40') || str_contains($gpu, 'rtx 30')) {
            $score += 25;
        } elseif (str_contains($gpu, 'rtx') || str_contains($gpu, 'gtx')) {
            $score += 20;
        } elseif (str_contains($gpu, 'integrated') || str_contains($gpu, 'iris')) {
            $score += 10;
        }

        // CPU Score (0-25 points)
        $cpu = strtolower($specs['processor'] ?? '');
        if (str_contains($cpu, 'i9') || str_contains($cpu, 'ryzen 9')) {
            $score += 25;
        } elseif (str_contains($cpu, 'i7') || str_contains($cpu, 'ryzen 7')) {
            $score += 20;
        } elseif (str_contains($cpu, 'i5') || str_contains($cpu, 'ryzen 5')) {
            $score += 15;
        }

        // RAM Score (0-20 points)
        $ram = strtolower($specs['ram'] ?? '');
        if (preg_match('/(\d+)\s*gb/', $ram, $matches)) {
            $ramSize = (int) $matches[1];
            $score += min(20, ($ramSize / 32) * 20);
        }

        // Storage Score (0-15 points)
        $storage = strtolower($specs['storage'] ?? '');
        if (str_contains($storage, 'ssd')) {
            $score += 10;
            if (preg_match('/(\d+)\s*(gb|tb)/', $storage, $matches)) {
                $size = (int) $matches[1];
                $unit = $matches[2];
                if ($unit === 'tb' && $size >= 1) {
                    $score += 5;
                }
            }
        }

        // Screen Score (0-10 points)
        $screen = strtolower($specs['screen'] ?? '');
        if (str_contains($screen, '144') || str_contains($screen, '165') || str_contains($screen, '240')) {
            $score += 10;
        } elseif (str_contains($screen, '120')) {
            $score += 7;
        } elseif (str_contains($screen, '60')) {
            $score += 5;
        }

        return min($maxScore, $score);
    }

    private function calculateQualityScore(array $specs): float
    {
        $score = 50; // Base score

        // Build quality indicators
        $allSpecs = strtolower(json_encode($specs));
        
        if (str_contains($allSpecs, 'aluminum') || str_contains($allSpecs, 'metal')) {
            $score += 15;
        }
        
        if (str_contains($allSpecs, 'oled') || str_contains($allSpecs, 'qhd') || str_contains($allSpecs, '4k')) {
            $score += 20;
        }

        if (str_contains($allSpecs, 'mechanical')) {
            $score += 15;
        }

        return min(100, $score);
    }

    private function calculateMobilityScore(array $specs): float
    {
        $score = 50; // Base score

        // Weight
        $weight = strtolower($specs['weight'] ?? '');
        if (preg_match('/([\d.]+)\s*kg/', $weight, $matches)) {
            $kg = (float) $matches[1];
            if ($kg < 1.5) {
                $score += 25;
            } elseif ($kg < 2.0) {
                $score += 20;
            } elseif ($kg < 2.5) {
                $score += 10;
            }
        }

        // Battery
        $battery = strtolower($specs['battery'] ?? '');
        if (str_contains($battery, 'hour')) {
            if (preg_match('/(\d+)\s*hour/', $battery, $matches)) {
                $hours = (int) $matches[1];
                $score += min(25, ($hours / 10) * 25);
            }
        }

        return min(100, $score);
    }

    private function calculatePriceScore(float $price): float
    {
        if ($price <= 0) {
            return 50;
        }

        // Lower price = higher score
        // $500 = 100 points, $3000 = 20 points
        if ($price <= 500) {
            return 100;
        } elseif ($price >= 3000) {
            return 20;
        }

        return 100 - (($price - 500) / 2500 * 80);
    }

    public function determineWinner(array $products): ?Product
    {
        if (empty($products)) {
            return null;
        }

        usort($products, function(Product $a, Product $b) {
            return (float)$b->getScore() <=> (float)$a->getScore();
        });

        return $products[0];
    }
}
