<?php
echo "=== Testando Endpoint /api/home ===\n\n";

$url = "http://localhost:8000/api/home";
echo "Fazendo requisição para: $url\n";

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_NOBODY, false);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);

curl_close($ch);

echo "Status HTTP: $httpCode\n\n";

if ($httpCode === 200) {
    $body = substr($response, $headerSize);
    $data = json_decode($body, true);
    
    if (json_last_error() === JSON_ERROR_NONE) {
        echo "✅ Sucesso! Dados recebidos:\n";
        echo "- Sample Products: " . count($data['sampleProducts'] ?? []) . "\n";
        echo "- Comparison Rows: " . count($data['comparisonRows'] ?? []) . "\n";
        echo "- Best Prices: " . count($data['bestPrices'] ?? []) . "\n";
        
        if (!empty($data['sampleProducts'][0])) {
            echo "\nPrimeiro produto:\n";
            print_r($data['sampleProducts'][0]);
        }
    } else {
        echo "❌ Erro ao decodificar JSON\n";
        echo "Body: " . substr($response, $headerSize) . "\n";
    }
} else {
    echo "❌ Erro: Status $httpCode\n";
    echo "Response:\n";
    echo substr($response, $headerSize) . "\n";
}

echo "\n=== Fim do Teste ===\n";
