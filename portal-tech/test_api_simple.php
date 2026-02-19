<?php
// Script de teste rápido para verificar produtos

echo "=== Testando Carregamento de Produtos ===\n\n";

// Testar endpoint /api/products
$url = 'http://localhost:8000/api/products';
echo "Fazendo requisição para: $url\n";

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    echo "✅ Status: 200 OK\n\n";
    
    $products = json_decode($response, true);
    
    if (is_array($products)) {
        echo "Total de produtos retornados: " . count($products) . "\n\n";
        
        if (count($products) > 0) {
            echo "Primeiros 3 produtos:\n";
            foreach (array_slice($products, 0, 3) as $index => $product) {
                echo "\n" . ($index + 1) . ". " . ($product['name'] ?? 'N/A') . "\n";
                echo "   ID: " . ($product['id'] ?? 'N/A') . "\n";
                echo "   Preço: " . ($product['price'] ? 'R$ ' . $product['price'] : 'N/A') . "\n";
                echo "   Rating: " . ($product['rating'] ?? 'N/A') . "\n";
                echo "   Imagem: " . ($product['image'] ?? 'N/A') . "\n";
            }
        }
    } else {
        echo "❌ Resposta não é um array válido\n";
    }
} else {
    echo "❌ Erro: Status $httpCode\n";
    echo "Certifique-se de que o servidor está rodando: php -S localhost:8000 -t public\n";
}

echo "\n=== Fim do Teste ===\n";
