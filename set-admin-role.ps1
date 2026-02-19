# Set User as Admin in MongoDB
# Run this script to grant admin privileges to a user

param(
    [string]$email = "admin@admin.com"
)

Write-Host "Setting user $email as ADMIN..." -ForegroundColor Cyan

# MongoDB command
$mongoCmd = "db.users.updateOne({email: '$email'}, {`$set: {role: 'ADMIN'}})"

Write-Host ""
Write-Host "MongoDB command:" -ForegroundColor Yellow
Write-Host $mongoCmd -ForegroundColor Gray
Write-Host ""

# Try to run MongoDB command
try {
    $result = & mongo portal_tech --eval $mongoCmd 2>&1
    
    if ($LASTEXITCODE -eq 0) {
        Write-Host "[OK] User updated successfully!" -ForegroundColor Green
        Write-Host ""
        Write-Host "You can now access admin panel at:" -ForegroundColor Cyan
        Write-Host "  http://localhost:5175/admin" -ForegroundColor Gray
    } else {
        Write-Host "[INFO] MongoDB command completed. Check output above." -ForegroundColor Yellow
    }
} catch {
    Write-Host "[INFO] Could not run MongoDB command automatically." -ForegroundColor Yellow
    Write-Host ""
    Write-Host "Please run this command manually in MongoDB shell:" -ForegroundColor Yellow
    Write-Host "  use portal_tech" -ForegroundColor Gray
    Write-Host "  $mongoCmd" -ForegroundColor Gray
}

Write-Host ""
