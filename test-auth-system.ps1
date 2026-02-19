# Test Script for Authentication System
# Run this script to verify the API is working

Write-Host "================================================" -ForegroundColor Cyan
Write-Host "  Testing Authentication System API" -ForegroundColor Cyan
Write-Host "================================================" -ForegroundColor Cyan
Write-Host ""

$baseUrl = "http://localhost:8000/api"
$testEmail = "test-$(Get-Random)@example.com"
$testPassword = "senha123"

# Test 1: Register
Write-Host "Test 1: Register User" -ForegroundColor Yellow
Write-Host "POST $baseUrl/register" -ForegroundColor Gray

$registerBody = @{
    email = $testEmail
    password = $testPassword
    name = "Test User"
} | ConvertTo-Json

try {
    $registerResponse = Invoke-WebRequest -Uri "$baseUrl/register" `
        -Method POST `
        -ContentType "application/json" `
        -Body $registerBody `
        -UseBasicParsing

    $registerData = $registerResponse.Content | ConvertFrom-Json
    Write-Host "[OK] Registration successful!" -ForegroundColor Green
    Write-Host "  User ID: $($registerData.id)" -ForegroundColor Gray
    Write-Host "  Email: $($registerData.email)" -ForegroundColor Gray
} catch {
    Write-Host "[FAIL] Registration failed!" -ForegroundColor Red
    Write-Host "  Error: $($_.Exception.Message)" -ForegroundColor Red
    exit 1
}

Write-Host ""

# Test 2: Login
Write-Host "Test 2: Login" -ForegroundColor Yellow
Write-Host "POST $baseUrl/login" -ForegroundColor Gray

$loginBody = @{
    email = $testEmail
    password = $testPassword
} | ConvertTo-Json

try {
    $loginResponse = Invoke-WebRequest -Uri "$baseUrl/login" `
        -Method POST `
        -ContentType "application/json" `
        -Body $loginBody `
        -UseBasicParsing

    $loginData = $loginResponse.Content | ConvertFrom-Json
    $token = $loginData.token
    
    Write-Host "[OK] Login successful!" -ForegroundColor Green
    Write-Host "  Token: $($token.Substring(0,20))..." -ForegroundColor Gray
    Write-Host "  User: $($loginData.user.name)" -ForegroundColor Gray
} catch {
    Write-Host "[FAIL] Login failed!" -ForegroundColor Red
    Write-Host "  Error: $($_.Exception.Message)" -ForegroundColor Red
    exit 1
}

Write-Host ""

# Test 3: Access Protected Endpoint (Favorites)
Write-Host "Test 3: Access Protected Endpoint (Favorites)" -ForegroundColor Yellow
Write-Host "GET $baseUrl/favorites" -ForegroundColor Gray

try {
    $headers = @{
        "Authorization" = "Bearer $token"
    }
    
    $favoritesResponse = Invoke-WebRequest -Uri "$baseUrl/favorites" `
        -Method GET `
        -Headers $headers `
        -UseBasicParsing

    $favorites = $favoritesResponse.Content | ConvertFrom-Json
    Write-Host "[OK] Protected endpoint accessible!" -ForegroundColor Green
    Write-Host "  Favorites count: $($favorites.Count)" -ForegroundColor Gray
} catch {
    Write-Host "[FAIL] Protected endpoint failed!" -ForegroundColor Red
    Write-Host "  Error: $($_.Exception.Message)" -ForegroundColor Red
    exit 1
}

Write-Host ""

# Test 4: Access Reviews Endpoint
Write-Host "Test 4: Access Reviews Endpoint" -ForegroundColor Yellow
Write-Host "GET $baseUrl/reviews" -ForegroundColor Gray

try {
    $reviewsResponse = Invoke-WebRequest -Uri "$baseUrl/reviews" `
        -Method GET `
        -Headers $headers `
        -UseBasicParsing

    $reviews = $reviewsResponse.Content | ConvertFrom-Json
    Write-Host "[OK] Reviews endpoint accessible!" -ForegroundColor Green
    Write-Host "  Reviews count: $($reviews.Count)" -ForegroundColor Gray
} catch {
    Write-Host "[FAIL] Reviews endpoint failed!" -ForegroundColor Red
    Write-Host "  Error: $($_.Exception.Message)" -ForegroundColor Red
    exit 1
}

Write-Host ""
Write-Host "================================================" -ForegroundColor Cyan
Write-Host "  All Tests Passed!" -ForegroundColor Green
Write-Host "================================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Authentication system is working correctly!" -ForegroundColor Green
Write-Host ""
Write-Host "Next steps:" -ForegroundColor Yellow
Write-Host "  1. Open http://localhost:5175 in your browser" -ForegroundColor Gray
Write-Host "  2. Navigate to /dashboard, /dashboard/favorites, /dashboard/reviews" -ForegroundColor Gray
Write-Host "  3. Check QUICKSTART.md for more testing options" -ForegroundColor Gray
Write-Host ""
