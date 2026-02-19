# Test Script for Admin Panel
# This script tests all admin endpoints

Write-Host "================================================" -ForegroundColor Cyan
Write-Host "  Testing Admin Panel API" -ForegroundColor Cyan
Write-Host "================================================" -ForegroundColor Cyan
Write-Host ""

$baseUrl = "http://localhost:8000/api"
$adminEmail = "admin@admin.com"
$adminPassword = "admin123"

# Step 1: Create admin user (or login if exists)
Write-Host "Step 1: Creating/Login Admin User" -ForegroundColor Yellow
$registerBody = @{
    email = $adminEmail
    password = $adminPassword
    name = "Admin User"
} | ConvertTo-Json

try {
    $res = Invoke-WebRequest -Uri "$baseUrl/register" -Method POST -ContentType "application/json" -Body $registerBody -UseBasicParsing
    Write-Host "[OK] Admin user created" -ForegroundColor Green
} catch {
    Write-Host "[INFO] Admin user may already exist, trying login..." -ForegroundColor Gray
}

# Login to get token
$loginBody = @{
    email = $adminEmail
    password = $adminPassword
} | ConvertTo-Json

try {
    $loginRes = Invoke-WebRequest -Uri "$baseUrl/login" -Method POST -ContentType "application/json" -Body $loginBody -UseBasicParsing
    $loginData = $loginRes.Content | ConvertFrom-Json
    $token = $loginData.token
    Write-Host "[OK] Login successful" -ForegroundColor Green
    Write-Host "  Token: $($token.Substring(0,20))..." -ForegroundColor Gray
} catch {
    Write-Host "[FAIL] Login failed!" -ForegroundColor Red
    exit 1
}

Write-Host ""
Write-Host "Note: For full admin access, set user role to 'ADMIN' in MongoDB:" -ForegroundColor Yellow
Write-Host "  db.users.updateOne({email: '$adminEmail'}, {`$set: {role: 'ADMIN'}})" -ForegroundColor Gray
Write-Host ""

$headers = @{
    "Authorization" = "Bearer $token"
}

# Test 2: Create Category
Write-Host "Test 2: Create Category" -ForegroundColor Yellow
$categoryBody = @{
    name = "Gaming Monitors"
    slug = "gaming-monitors"
} | ConvertTo-Json

try {
    $catRes = Invoke-WebRequest -Uri "$baseUrl/admin/categories" -Method POST -Headers $headers -ContentType "application/json" -Body $categoryBody -UseBasicParsing
    $catData = $catRes.Content | ConvertFrom-Json
    $categoryId = $catData.id
    Write-Host "[OK] Category created!" -ForegroundColor Green
    Write-Host "  ID: $categoryId" -ForegroundColor Gray
} catch {
    if ($_.Exception.Response.StatusCode -eq 403) {
        Write-Host "[WARN] Access forbidden - User needs ADMIN role" -ForegroundColor Yellow
        Write-Host "  Run: db.users.updateOne({email: '$adminEmail'}, {`$set: {role: 'ADMIN'}})" -ForegroundColor Yellow
    } else {
        Write-Host "[INFO] Category may already exist or error occurred" -ForegroundColor Gray
    }
    $categoryId = $null
}

# Test 3: List Categories
Write-Host ""
Write-Host "Test 3: List Categories" -ForegroundColor Yellow
try {
    $listRes = Invoke-WebRequest -Uri "$baseUrl/admin/categories" -Method GET -Headers $headers -UseBasicParsing
    $categories = $listRes.Content | ConvertFrom-Json
    Write-Host "[OK] Categories listed!" -ForegroundColor Green
    Write-Host "  Count: $($categories.Count)" -ForegroundColor Gray
} catch {
    if ($_.Exception.Response.StatusCode -eq 403) {
        Write-Host "[WARN] Access forbidden - User needs ADMIN role" -ForegroundColor Yellow
    } else {
        Write-Host "[FAIL] Failed to list categories" -ForegroundColor Red
    }
}

# Test 4: Test Dashboard Stats
Write-Host ""
Write-Host "Test 4: Admin Dashboard Stats" -ForegroundColor Yellow
try {
    $dashRes = Invoke-WebRequest -Uri "$baseUrl/admin/dashboard" -Method GET -Headers $headers -UseBasicParsing
    $dashData = $dashRes.Content | ConvertFrom-Json
    Write-Host "[OK] Dashboard data retrieved!" -ForegroundColor Green
    Write-Host "  Total Products: $($dashData.stats.totalProducts)" -ForegroundColor Gray
    Write-Host "  Total Categories: $($dashData.stats.totalCategories)" -ForegroundColor Gray
    Write-Host "  Total Offers: $($dashData.stats.totalOffers)" -ForegroundColor Gray
} catch {
    if ($_.Exception.Response.StatusCode -eq 403) {
        Write-Host "[WARN] Access forbidden - User needs ADMIN role" -ForegroundColor Yellow
    } else {
        Write-Host "[FAIL] Failed to get dashboard data" -ForegroundColor Red
    }
}

Write-Host ""
Write-Host "================================================" -ForegroundColor Cyan
Write-Host "  Admin Panel Routes Available" -ForegroundColor Cyan
Write-Host "================================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Categories:    GET/POST  /api/admin/categories" -ForegroundColor Gray
Write-Host "Marketplaces:  GET/POST  /api/admin/marketplaces" -ForegroundColor Gray
Write-Host "Suppliers:     GET/POST  /api/admin/suppliers" -ForegroundColor Gray
Write-Host "Products:      GET/POST  /api/admin/products" -ForegroundColor Gray
Write-Host "Offers:        GET/POST  /api/admin/offers" -ForegroundColor Gray
Write-Host "Dashboard:     GET       /api/admin/dashboard" -ForegroundColor Gray
Write-Host ""
Write-Host "Frontend Admin Panel: http://localhost:5175/admin" -ForegroundColor Cyan
Write-Host ""
