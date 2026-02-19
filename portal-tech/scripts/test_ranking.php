#!/usr/bin/env php
<?php
/**
 * Script de Teste - Sistema de Ranqueamento de Produtos
 * 
 * Este script demonstra o uso do ProductRankingService com produtos de exemplo
 */

require_once __DIR__ . '/../vendor/autoload.php';

use App\Document\Product;
use App\Document\Embedded\Specification;
use App\Service\ProductRankingService;

echo "\n=== TESTE DO SISTEMA DE RANQUEAMENTO ===\n\n";

// Criar produtos de exemplo - Notebooks
$products = [];

// Notebook 1 - Equilíbrio perfeito
$notebook1 = new Product();
$notebook1->setName('Notebook Dell Inspiron 15')
    ->setSlug('dell-inspiron-15')
    ->setPrice(3500.00)
    ->setRating(4.5)
    ->setDescription('Notebook intermediário com excelente custo-benefício');

$notebook1->addSpecification(new Specification('RAM', '16GB'))
    ->addSpecification(new Specification('Processador', 'Intel Core i7'))
    ->addSpecification(new Specification('Armazenamento', '512GB SSD'))
    ->addSpecification(new Specification('Tela', '15.6 polegadas'))
    ->addSpecification(new Specification('Peso', '1.8kg'));

$products[] = $notebook1;

// Notebook 2 - Top de linha, mais caro
$notebook2 = new Product();
$notebook2->setName('Notebook Lenovo Legion 5')
    ->setSlug('lenovo-legion-5')
    ->setPrice(4200.00)
    ->setRating(4.8)
    ->setDescription('Notebook gamer de alta performance');

$notebook2->addSpecification(new Specification('RAM', '16GB'))
    ->addSpecification(new Specification('Processador', 'Intel Core i7'))
    ->addSpecification(new Specification('Armazenamento', '1TB SSD'))
    ->addSpecification(new Specification('Tela', '15.6 polegadas'))
    ->addSpecification(new Specification('Peso', '2.3kg'));

$products[] = $notebook2;

// Notebook 3 - Entrada, mais barato
$notebook3 = new Product();
$notebook3->setName('Notebook Acer Aspire 5')
    ->setSlug('acer-aspire-5')
    ->setPrice(3000.00)
    ->setRating(4.0)
    ->setDescription('Notebook básico para tarefas do dia a dia');

$notebook3->addSpecification(new Specification('RAM', '8GB'))
    ->addSpecification(new Specification('Processador', 'Intel Core i5'))
    ->addSpecification(new Specification('Armazenamento', '256GB SSD'))
    ->addSpecification(new Specification('Tela', '15.6 polegadas'))
    ->addSpecification(new Specification('Peso', '1.7kg'));

$products[] = $notebook3;

// Executar ranqueamento
$rankingService = new ProductRankingService();
$rankedProducts = $rankingService->rankProducts($products);

// Exibir resultados
echo "PRODUTOS RANQUEADOS:\n";
echo str_repeat("-", 80) . "\n\n";

foreach ($rankedProducts as $item) {
    $product = $item['product'];
    $rank = $item['rank'];
    $breakdown = $item['breakdown'];
    
    // Emoji da medalha
    $medals = [1 => '🏆', 2 => '🥈', 3 => '🥉'];
    $medal = $medals[$rank] ?? '';
    
    echo "{$medal} #{$rank} - {$product->getName()}\n";
    echo str_repeat("=", 80) . "\n";
    
    echo "Preço: R$ " . number_format($product->getPrice(), 2, ',', '.') . "\n";
    echo "Avaliação: {$product->getRating()}/5.0 ⭐\n";
    
    echo "\nEspecificações:\n";
    foreach ($product->getSpecifications() as $spec) {
        echo "  • {$spec->getKey()}: {$spec->getValue()}\n";
    }
    
    echo "\nScores:\n";
    echo sprintf("  💰 Preço:          %3d/100\n", $rankingService->formatScore($breakdown['priceScore']));
    echo sprintf("  ⭐ Avaliação:      %3d/100\n", $rankingService->formatScore($breakdown['ratingScore']));
    echo sprintf("  🔧 Especificações: %3d/100\n", $rankingService->formatScore($breakdown['specsScore']));
    echo sprintf("  📊 TOTAL:          %3d/100\n", $rankingService->formatScore($breakdown['totalScore']));
    
    if ($rank === 1) {
        echo "\n⭐ MELHOR CUSTO-BENEFÍCIO ⭐\n";
    }
    
    echo "\n" . str_repeat("-", 80) . "\n\n";
}

echo "\n=== ANÁLISE ===\n\n";

$winner = $rankedProducts[0]['product'];
$second = $rankedProducts[1]['product'];
$third = $rankedProducts[2]['product'];

echo "🏆 VENCEDOR: {$winner->getName()}\n";
echo "   O sistema identificou este produto como a melhor opção considerando:\n";
echo "   - Preço competitivo de R$ " . number_format($winner->getPrice(), 2, ',', '.') . "\n";
echo "   - Avaliação sólida de {$winner->getRating()}/5.0\n";
echo "   - Especificações balanceadas\n\n";

echo "🥈 SEGUNDO LUGAR: {$second->getName()}\n";
$priceDiff = (($second->getPrice() / $winner->getPrice()) - 1) * 100;
echo "   - " . number_format($priceDiff, 1) . "% mais caro que o vencedor\n";
echo "   - Pode ter especificações superiores, mas o custo não justifica\n\n";

echo "🥉 TERCEIRO LUGAR: {$third->getName()}\n";
$priceDiff = (($winner->getPrice() / $third->getPrice()) - 1) * 100;
echo "   - " . number_format($priceDiff, 1) . "% mais barato que o vencedor\n";
echo "   - Especificações inferiores não compensam a economia\n\n";

echo "=== TESTE CONCLUÍDO COM SUCESSO ===\n\n";
