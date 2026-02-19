@echo off
echo === Testando Endpoint /api/products ===
echo.

REM Testar endpoint de produtos
curl -s http://localhost:8000/api/products | jq ".[:2]" 2>nul || curl -s http://localhost:8000/api/products

echo.
echo === Teste Completo ===
