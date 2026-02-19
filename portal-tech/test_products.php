<?php
// Script de teste para verificar se os produtos estão sendo carregados corretamente

require __DIR__ . '/vendor/autoload.php';

use App\Document\Product;
use Doctrine\ODM\MongoDB\DocumentManager;

// Boot Symfony kernel
$kernel = new \App\Kernel('dev', true);
$kernel->boot();
$container = $kernel->getContainer();

// Get DocumentManager
$dm = $container->get('doctrine_mongodb.odm.document_manager');

echo "=== Teste de Carregamento de Produtos ===\n\n";

// Buscar todos os produtos
$products = $dm->getRepository(Product::class)->findAll();

echo "Total de produtos no banco: " . count($products) . "\n\n";

if (empty($products)) {
    echo "❌ ERRO: Nenhum produto encontrado no banco de dados!\n";
    echo "Execute: php bin/console app:seed-database\n\n";
    exit(1);
}

echo "✅ Produtos encontrados:\n\n";

foreach ($products as $index => $product) {
    echo "Produto #" . ($index + 1) . ":\n";
    echo "  ID: " . $product->getId() . "\n";
    echo "  Nome: " . $product->getName() . "\n";
    echo "  Slug: " . $product->getSlug() . "\n";
    echo "  Descrição: " . ($product->getDescription() ?: 'N/A') . "\n";
    echo "  Imagem: " . ($product->getImage() ?: 'N/A') . "\n";
    echo "  Preço: " . ($product->getPrice() ? '€' . $product->getPrice() : 'N/A') . "\n";
    echo "  Rating: " . ($product->getRating() ?: 'N/A') . "\n";
    echo "  Especificações: " . count($product->getSpecifications()) . " itens\n";
    
    foreach ($product->getSpecifications() as $spec) {
        echo "    - " . $spec->getName() . ": " . $spec->getValue() . "\n";
    }
    
    echo "\n";
}

echo "=== Teste de API /api/home ===\n\n";

// Simular requisição para /api/home
$homeController = $container->get('App\Controller\Api\HomeController');
$response = $homeController->index();
$data = json_decode($response->getContent(), true);

echo "Produtos retornados pela API: " . count($data['sampleProducts'] ?? []) . "\n";

if (!empty($data['sampleProducts'])) {
    echo "✅ API /api/home funcionando!\n\n";
    echo "Primeiro produto retornado:\n";
    print_r($data['sampleProducts'][0]);
} else {
    echo "❌ API /api/home não retornou produtos!\n";
}

echo "\n=== Teste Completo ===\n";
