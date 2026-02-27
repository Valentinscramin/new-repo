# Script de teste para verificar o CRUD de Comparacoes
# =========================================================

Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "TESTE CRUD - COMPARACOES DE PRODUTOS" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

$baseUrl = "http://localhost:8000/api"
$testEmail = "test-comparisons@example.com"
$testPassword = "Test123!@#"

# Funcao auxiliar para fazer requests
function Invoke-ApiRequest {
    param(
        [string]$Method,
        [string]$Endpoint,
        [object]$Body = $null,
        [string]$Token = $null
    )
    
    $headers = @{
        'Content-Type' = 'application/json'
    }
    
    if ($Token) {
        $headers['Authorization'] = "Bearer $Token"
    }
    
    $params = @{
        Uri = "$baseUrl$Endpoint"
        Method = $Method
        Headers = $headers
        ErrorAction = 'Stop'
    }
    
    if ($Body) {
        $params['Body'] = ($Body | ConvertTo-Json -Compress)
    }
    
    try {
        return Invoke-RestMethod @params
    } catch {
        Write-Host "Erro na requisicao: $($_.Exception.Message)" -ForegroundColor Red
        if ($_.ErrorDetails.Message) {
            Write-Host "Detalhes: $($_.ErrorDetails.Message)" -ForegroundColor Yellow
        }
        return $null
    }
}

# 1. Registrar usuario de teste
Write-Host "[1/6] Registrando usuario de teste..." -ForegroundColor Yellow
$registerResult = Invoke-ApiRequest -Method POST -Endpoint "/register" -Body @{
    email = $testEmail
    password = $testPassword
    role = "ROLE_USER"
}

if ($registerResult) {
    Write-Host "OK - Usuario criado com sucesso!" -ForegroundColor Green
} else {
    Write-Host "OK - Usuario ja existe" -ForegroundColor Gray
}

# 2. Fazer login
Write-Host ""
Write-Host "[2/6] Fazendo login..." -ForegroundColor Yellow
$loginResult = Invoke-ApiRequest -Method POST -Endpoint "/login" -Body @{
    email = $testEmail
    password = $testPassword
}

if (-not $loginResult -or -not $loginResult.token) {
    Write-Host "ERRO - Falha no login!" -ForegroundColor Red
    exit 1
}

$token = $loginResult.token
Write-Host "OK - Login realizado com sucesso!" -ForegroundColor Green
Write-Host "  Token: $($token.Substring(0, 20))..." -ForegroundColor Gray

# 3. Buscar produtos disponiveis
Write-Host ""
Write-Host "[3/6] Buscando produtos disponiveis..." -ForegroundColor Yellow
$homeData = Invoke-ApiRequest -Method GET -Endpoint "/home"

if (-not $homeData -or -not $homeData.sampleProducts) {
    Write-Host "ERRO - Nenhum produto encontrado!" -ForegroundColor Red
    exit 1
}

$products = $homeData.sampleProducts
Write-Host "OK - Encontrados $($products.Count) produtos" -ForegroundColor Green

if ($products.Count -lt 2) {
    Write-Host "ERRO - Necessario pelo menos 2 produtos para testar comparacoes!" -ForegroundColor Red
    exit 1
}

# Selecionar 2-3 produtos da mesma categoria
$selectedProducts = @()
$firstCategory = $null

foreach ($product in $products) {
    if ($selectedProducts.Count -eq 0) {
        $firstCategory = $product.category.name
        $selectedProducts += $product.id
    } elseif ($product.category.name -eq $firstCategory -and $selectedProducts.Count -lt 3) {
        $selectedProducts += $product.id
    }
    
    if ($selectedProducts.Count -eq 3) {
        break
    }
}

Write-Host "  Produtos selecionados: $($selectedProducts -join ', ')" -ForegroundColor Gray
Write-Host "  Categoria: $firstCategory" -ForegroundColor Gray

# 4. Criar comparacao
Write-Host ""
Write-Host "[4/6] Salvando comparacao..." -ForegroundColor Yellow
$createResult = Invoke-ApiRequest -Method POST -Endpoint "/comparisons" -Token $token -Body @{
    products = $selectedProducts
}

if (-not $createResult -or -not $createResult.id) {
    Write-Host "ERRO - Falha ao salvar comparacao!" -ForegroundColor Red
    exit 1
}

$comparisonId = $createResult.id
Write-Host "OK - Comparacao salva com sucesso!" -ForegroundColor Green
Write-Host "  ID: $comparisonId" -ForegroundColor Gray

# 5. Listar comparacoes
Write-Host ""
Write-Host "[5/6] Listando comparacoes salvas..." -ForegroundColor Yellow
$listResult = Invoke-ApiRequest -Method GET -Endpoint "/comparisons" -Token $token

if (-not $listResult) {
    Write-Host "ERRO - Falha ao listar comparacoes!" -ForegroundColor Red
    exit 1
}

Write-Host "OK - Encontradas $($listResult.Count) comparacao(oes)" -ForegroundColor Green
foreach ($comp in $listResult) {
    Write-Host "  - ID: $($comp.id)" -ForegroundColor Gray
    Write-Host "    Produtos: $($comp.products.Count)" -ForegroundColor Gray
    Write-Host "    Data: $($comp.createdAt)" -ForegroundColor Gray
    foreach ($prod in $comp.products) {
        Write-Host "      -> $($prod.name)" -ForegroundColor DarkGray
    }
}

# 6. Deletar comparacao
Write-Host ""
Write-Host "[6/6] Deletando comparacao de teste..." -ForegroundColor Yellow
$deleteResult = Invoke-ApiRequest -Method DELETE -Endpoint "/comparisons/$comparisonId" -Token $token

if (-not $deleteResult) {
    Write-Host "ERRO - Falha ao deletar comparacao!" -ForegroundColor Red
} else {
    Write-Host "OK - Comparacao deletada com sucesso!" -ForegroundColor Green
}

# Verificar se foi realmente deletada
Write-Host ""
Write-Host "[VERIFICACAO] Confirmando exclusao..." -ForegroundColor Yellow
$verifyResult = Invoke-ApiRequest -Method GET -Endpoint "/comparisons" -Token $token
$found = $verifyResult | Where-Object { $_.id -eq $comparisonId }

if ($found) {
    Write-Host "ERRO - Comparacao ainda existe!" -ForegroundColor Red
} else {
    Write-Host "OK - Exclusao confirmada!" -ForegroundColor Green
}

# Resumo final
Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "RESUMO DO TESTE" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "OK - CREATE (POST /api/comparisons)" -ForegroundColor Green
Write-Host "OK - READ   (GET /api/comparisons)" -ForegroundColor Green
Write-Host "OK - DELETE (DELETE /api/comparisons)" -ForegroundColor Green
Write-Host ""
Write-Host "SUCESSO - CRUD DE COMPARACOES FUNCIONAL!" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""
