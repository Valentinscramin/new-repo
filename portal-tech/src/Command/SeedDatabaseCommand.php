<?php
namespace App\Command;

use App\Document\Category;
use App\Document\Marketplace;
use App\Document\Supplier;
use App\Document\Product;
use App\Document\ProductOffer;
use App\Document\User;
use App\Document\Embedded\Specification;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'app:seed-database')]
class SeedDatabaseCommand extends Command
{
    private $dm;

    public function __construct(DocumentManager $dm)
    {
        parent::__construct();
        $this->dm = $dm;
    }

    protected function configure()
    {
        $this->setDescription('Seed initial data into MongoDB');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Seeding database...');

        // Categories
        $cat1 = (new Category())->setName('Notebooks')->setSlug('notebooks');
        $cat2 = (new Category())->setName('Monitores')->setSlug('monitores');
        $this->dm->persist($cat1);
        $this->dm->persist($cat2);

        // Marketplaces
        $m1 = (new Marketplace())->setName('Loja A')->setSlug('loja-a')->setAffiliateBaseUrl('https://lojaa.example/aff');
        $m2 = (new Marketplace())->setName('Loja B')->setSlug('loja-b')->setAffiliateBaseUrl('https://lojab.example/aff');
        $m3 = (new Marketplace())->setName('Loja C')->setSlug('loja-c')->setAffiliateBaseUrl('https://lojac.example/aff');
        $this->dm->persist($m1);
        $this->dm->persist($m2);
        $this->dm->persist($m3);

        // Suppliers
        $s1 = (new Supplier())->setName('Fornecedor X')->setWebsite('https://fornecedorx.example');
        $s2 = (new Supplier())->setName('Fornecedor Y')->setWebsite('https://fornecedory.example');
        $this->dm->persist($s1);
        $this->dm->persist($s2);

        // Products
        $p1 = (new Product())->setName('Notebook Ultra')->setSlug('notebook-ultra')->setDescription('Notebook potente');
        $p1->setCategory($cat1)->setSupplier($s1);
        $p1->addSpecification(new Specification('CPU', 'i7'));
        $p1->addSpecification(new Specification('RAM', '16GB'));
        $this->dm->persist($p1);

        $p2 = (new Product())->setName('Monitor 27"')->setSlug('monitor-27')->setDescription('Monitor 27 polegadas');
        $p2->setCategory($cat2)->setSupplier($s2);
        $p2->addSpecification(new Specification('Resolução', '2560x1440'));
        $this->dm->persist($p2);

        // Additional simple products
        for ($i = 3; $i <= 5; $i++) {
            $p = (new Product())->setName("Produto $i")->setSlug("produto-$i")->setDescription('Descrição');
            $p->setCategory($cat1)->setSupplier($s1);
            $this->dm->persist($p);
        }

        // Admin user
        $admin = (new User())->setName('Admin')->setEmail('admin@example.com')->setPassword(password_hash('changeme', PASSWORD_DEFAULT));
        $admin->setRole('ADMIN');
        $this->dm->persist($admin);

        // Offers
        $offer1 = (new ProductOffer())->setProduct($p1)->setMarketplace($m1)->setPrice(4999.90)->setAffiliateLink('https://lojaa.example/aff/notebook-ultra');
        $offer2 = (new ProductOffer())->setProduct($p1)->setMarketplace($m2)->setPrice(4899.00)->setAffiliateLink('https://lojab.example/aff/notebook-ultra');
        $this->dm->persist($offer1);
        $this->dm->persist($offer2);

        $this->dm->flush();

        // Ensure indexes (if mapping defines them)
        try {
            $this->dm->getSchemaManager()->ensureIndexes();
            $output->writeln('Indexes ensured.');
        } catch (\Throwable $e) {
            $output->writeln('<comment>Could not ensure indexes: '.$e->getMessage().'</comment>');
        }

        $output->writeln('Seeding complete.');
        return Command::SUCCESS;
    }
}
