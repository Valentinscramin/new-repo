<?php
namespace App\Controller\Api\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ODM\MongoDB\DocumentManager;
use App\Document\Product;
use App\Document\Category;
use App\Document\ProductOffer;
use App\Document\Marketplace;
use App\Document\Supplier;

class DashboardController extends AbstractController
{
    use AdminAuthTrait;

    #[Route('/api/admin/dashboard', name: 'admin_dashboard', methods: ['GET'])]
    public function dashboard(Request $request, DocumentManager $dm): JsonResponse
    {
        if ($error = $this->requireAdmin($request, $dm)) return $error;

        // Total counts
        $totalProducts = $dm->getRepository(Product::class)->createQueryBuilder()->count()->getQuery()->execute();
        $totalCategories = $dm->getRepository(Category::class)->createQueryBuilder()->count()->getQuery()->execute();
        $totalOffers = $dm->getRepository(ProductOffer::class)->createQueryBuilder()->count()->getQuery()->execute();
        $totalMarketplaces = $dm->getRepository(Marketplace::class)->createQueryBuilder()->count()->getQuery()->execute();
        $totalSuppliers = $dm->getRepository(Supplier::class)->createQueryBuilder()->count()->getQuery()->execute();

        // Latest updates - get recent offers (last updated)
        $recentOffers = $dm->getRepository(ProductOffer::class)
            ->createQueryBuilder()
            ->sort('lastUpdatedAt', 'DESC')
            ->limit(5)
            ->getQuery()
            ->execute();

        $latestUpdates = [];
        foreach ($recentOffers as $offer) {
            $latestUpdates[] = [
                'id' => (string)$offer->getId(),
                'product' => $offer->getProduct() ? $offer->getProduct()->getName() : 'N/A',
                'marketplace' => $offer->getMarketplace() ? $offer->getMarketplace()->getName() : 'N/A',
                'price' => $offer->getPrice(),
                'updatedAt' => $offer->getLastUpdatedAt()->format(DATE_ATOM),
            ];
        }

        return new JsonResponse([
            'stats' => [
                'totalProducts' => $totalProducts,
                'totalCategories' => $totalCategories,
                'totalOffers' => $totalOffers,
                'totalMarketplaces' => $totalMarketplaces,
                'totalSuppliers' => $totalSuppliers,
            ],
            'latestUpdates' => $latestUpdates,
        ]);
    }
}
