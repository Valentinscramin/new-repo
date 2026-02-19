# Sistema de Preços e Ofertas

## Estrutura de Dados

### 1. Produto (Product)
Cada produto tem um **preço base** opcional (`Product.price`), mas este é apenas informativo. O preço real vem das ofertas.

### 2. Oferta (ProductOffer)
Cada oferta representa um link específico para comprar o produto em um marketplace, com **preço próprio**:

```php
ProductOffer:
  - product: Produto (referência)
  - marketplace: Marketplace (Amazon, Worten, AliExpress, etc)
  - price: Preço ESPECÍFICO desta oferta/link
  - affiliateLink: URL do link de afiliado
  - lastUpdatedAt: Data de atualização
```

## Exemplo Real

**Produto:** ASUS ROG Swift 27" 240Hz
- Preço base no cadastro: R$ 879,00

**Ofertas cadastradas (cada uma com seu valor):**
- Amazon: R$ 879,00 → https://amazon.com/asus-rog-swift
- Worten: R$ 899,00 → https://worten.pt/asus-rog-swift
- AliExpress: R$ 845,00 → https://aliexpress.com/asus-rog-swift

## Como Funciona

1. **Cadastro:** Cada marketplace tem um preço independente para o mesmo produto
2. **API:** Retorna todas as ofertas com seus preços específicos
3. **Frontend:** Ordena as ofertas do menor ao maior preço
4. **Exibição:** Mostra a melhor oferta em destaque e calcula economia entre as opções

## Onde os Preços Aparecem

### Seção "Onde Comprar Mais Barato"
- Lista todas as ofertas do produto vencedor (ou primeiro produto)
- Ordenadas por preço crescente
- Mostra economia entre cada opção

### Modal de Produto
- Lista todas as ofertas do produto
- Ordenadas por preço crescente
- Destaca a melhor oferta
- Mostra diferença de preço entre as opções

## Vantagens

✅ **Flexibilidade:** Cada marketplace pode ter preço diferente  
✅ **Transparência:** Usuário vê todos os preços disponíveis  
✅ **Economia:** Sistema calcula automaticamente a melhor opção  
✅ **Atualização:** Possível atualizar preço de cada oferta independentemente  

## Dados no Seed

O arquivo `SeedDatabaseCommand.php` cadastra 3 ofertas para cada produto:
- Uma para cada marketplace (Amazon, Worten, AliExpress)
- Cada oferta com preço ligeiramente diferente
- Simulando variação real de preços entre marketplaces
