# PowerShell script to install Composer dependencies
# This script helps work around SSL certificate issues with Avast Firewall

Write-Host "Installing Composer dependencies..." -ForegroundColor Green
Write-Host ""
Write-Host "NOTE: If you see SSL certificate errors, you may need to:" -ForegroundColor Yellow
Write-Host "1. Temporarily disable Avast's SSL scanning, OR" -ForegroundColor Yellow
Write-Host "2. Add an exception for Composer in Avast Firewall settings" -ForegroundColor Yellow
Write-Host ""

# Set environment variables
$env:COMPOSER_DISABLE_XDEBUG_WARN = "1"
$env:COMPOSER_ALLOW_SUPERUSER = "1"

# Try to install with various workarounds
Write-Host "Attempting installation..." -ForegroundColor Cyan

# First, try with disabled TLS
php composer.phar config --global disable-tls true
php composer.phar config --global secure-http false

# Try installation
php composer.phar install --no-dev --prefer-dist --no-interaction

if ($LASTEXITCODE -ne 0) {
    Write-Host ""
    Write-Host "Installation failed. Common solutions:" -ForegroundColor Red
    Write-Host "1. Disable Avast's 'Scan SSL connections' feature temporarily" -ForegroundColor Yellow
    Write-Host "2. Add Composer to Avast's exceptions list" -ForegroundColor Yellow
    Write-Host "3. Use a different network/VPN" -ForegroundColor Yellow
    Write-Host ""
    Write-Host "You can also try running this manually after configuring Avast:" -ForegroundColor Cyan
    Write-Host "  php composer.phar install --no-dev" -ForegroundColor White
}
