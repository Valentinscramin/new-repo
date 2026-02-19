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

        // Limpar coleções existentes para evitar erros de chave duplicada
        $collectionsToClear = [
            Category::class,
            Marketplace::class,
            Supplier::class,
            Product::class,
            ProductOffer::class,
            User::class,
        ];
        foreach ($collectionsToClear as $class) {
            try {
                $this->dm->getDocumentCollection($class)->deleteMany([]);
            } catch (\Throwable $e) {
                $output->writeln('<comment>Could not clear collection for '.(is_string($class) ? $class : gettype($class)).': '.$e->getMessage().'</comment>');
            }
        }
        // Categories
        $cat1 = (new Category())->setName('Notebooks')->setSlug('notebooks');
        $cat2 = (new Category())->setName('Monitores')->setSlug('monitores');
        $this->dm->persist($cat1);
        $this->dm->persist($cat2);

        // Marketplaces
        $m1 = (new Marketplace())->setName('Amazon')->setSlug('amazon')->setAffiliateBaseUrl('https://amazon.com/aff');
        $m2 = (new Marketplace())->setName('Worten')->setSlug('worten')->setAffiliateBaseUrl('https://worten.pt/aff');
        $m3 = (new Marketplace())->setName('AliExpress')->setSlug('aliexpress')->setAffiliateBaseUrl('https://aliexpress.com/aff');
        $this->dm->persist($m1);
        $this->dm->persist($m2);
        $this->dm->persist($m3);

        // Suppliers
        $s1 = (new Supplier())->setName('Fornecedor X')->setWebsite('https://fornecedorx.example');
        $s2 = (new Supplier())->setName('Fornecedor Y')->setWebsite('https://fornecedory.example');
        $this->dm->persist($s1);
        $this->dm->persist($s2);

        // Products - Monitores Gaming
        $p1 = (new Product())->setName('ASUS ROG Swift 27" 240Hz')->setSlug('asus-rog-swift-27')->setDescription('Monitor gaming premium com 240Hz e painel IPS');
        $p1->setCategory($cat2)->setSupplier($s1);
        $p1->setImage('/img/monitor-1.svg');
        $p1->setRating(4.8);
        $p1->addSpecification(new Specification('Taxa de Atualização', '240Hz'));
        $p1->addSpecification(new Specification('Painel', 'IPS'));
        $p1->addSpecification(new Specification('Resolução', '2560x1440'));
        $p1->addSpecification(new Specification('Tempo de Resposta', '1ms'));
        $this->dm->persist($p1);

        $p2 = (new Product())->setName('Samsung Odyssey G5 27" 165Hz')->setSlug('samsung-odyssey-g5')->setDescription('Monitor curvo gaming com excelente custo-benefício');
        $p2->setCategory($cat2)->setSupplier($s2);
        $p2->setImage('/img/monitor-2.svg');
        $p2->setRating(4.7);
        $p2->addSpecification(new Specification('Taxa de Atualização', '165Hz'));
        $p2->addSpecification(new Specification('Painel', 'VA'));
        $p2->addSpecification(new Specification('Resolução', '2560x1440'));
        $p2->addSpecification(new Specification('Tempo de Resposta', '1ms'));
        $this->dm->persist($p2);

        $p3 = (new Product())->setName('LG UltraGear 24" 144Hz')->setSlug('lg-ultragear-24')->setDescription('Ótima opção econômica para gamers iniciantes');
        $p3->setCategory($cat2)->setSupplier($s1);
        $p3->setImage('/img/monitor-3.svg');
        $p3->setRating(4.6);
        $p3->addSpecification(new Specification('Taxa de Atualização', '144Hz'));
        $p3->addSpecification(new Specification('Painel', 'IPS'));
        $p3->addSpecification(new Specification('Resolução', '1920x1080'));
        $p3->addSpecification(new Specification('Tempo de Resposta', '1ms'));
        $this->dm->persist($p3);

        // Additional notebooks
        $nb1 = (new Product())->setName('Notebook Ultra')->setSlug('notebook-ultra')->setDescription('Notebook potente');
        $nb1->setCategory($cat1)->setSupplier($s1);
        $nb1->setImage('/img/notebook-1.svg');
        $nb1->setRating(4.5);
        $nb1->addSpecification(new Specification('CPU', 'i7'));
        $nb1->addSpecification(new Specification('RAM', '16GB'));
        $this->dm->persist($nb1);

        // Mais produtos - Monitores
        $p4 = (new Product())->setName('BenQ MOBIUZ 27" 144Hz')->setSlug('benq-mobiuz-27')->setDescription('Monitor para entretenimento e jogos');
        $p4->setCategory($cat2)->setSupplier($s2);
        $p4->setImage('/img/monitor-1.svg');
        $p4->setRating(4.4);
        $p4->addSpecification(new Specification('Taxa de Atualização', '144Hz'));
        $p4->addSpecification(new Specification('Painel', 'IPS'));
        $p4->addSpecification(new Specification('Resolução', '2560x1440'));
        $p4->addSpecification(new Specification('Tempo de Resposta', '1ms'));
        $this->dm->persist($p4);

        $p5 = (new Product())->setName('AOC Gaming 24" 165Hz')->setSlug('aoc-gaming-24')->setDescription('Excelente custo-benefício para esports');
        $p5->setCategory($cat2)->setSupplier($s1);
        $p5->setImage('/img/monitor-2.svg');
        $p5->setRating(4.3);
        $p5->addSpecification(new Specification('Taxa de Atualização', '165Hz'));
        $p5->addSpecification(new Specification('Painel', 'VA'));
        $p5->addSpecification(new Specification('Resolução', '1920x1080'));
        $p5->addSpecification(new Specification('Tempo de Resposta', '1ms'));
        $this->dm->persist($p5);

        $p6 = (new Product())->setName('Dell UltraSharp 27" 4K')->setSlug('dell-ultrasharp-27')->setDescription('Monitor profissional 4K para designers');
        $p6->setCategory($cat2)->setSupplier($s2);
        $p6->setImage('/img/monitor-3.svg');
        $p6->setRating(4.9);
        $p6->addSpecification(new Specification('Taxa de Atualização', '60Hz'));
        $p6->addSpecification(new Specification('Painel', 'IPS'));
        $p6->addSpecification(new Specification('Resolução', '3840x2160'));
        $p6->addSpecification(new Specification('Tempo de Resposta', '5ms'));
        $this->dm->persist($p6);

        // Mais Notebooks
        $nb2 = (new Product())->setName('Lenovo ThinkPad X1 Carbon')->setSlug('lenovo-thinkpad-x1')->setDescription('Ultrabook empresarial premium');
        $nb2->setCategory($cat1)->setSupplier($s2);
        $nb2->setImage('/img/notebook-1.svg');
        $nb2->setRating(4.8);
        $nb2->addSpecification(new Specification('CPU', 'i7-12th Gen'));
        $nb2->addSpecification(new Specification('RAM', '32GB'));
        $nb2->addSpecification(new Specification('SSD', '1TB'));
        $this->dm->persist($nb2);

        $nb3 = (new Product())->setName('MacBook Air M2')->setSlug('macbook-air-m2')->setDescription('Notebook Apple com chip M2');
        $nb3->setCategory($cat1)->setSupplier($s1);
        $nb3->setImage('/img/notebook-1.svg');
        $nb3->setRating(4.9);
        $nb3->addSpecification(new Specification('CPU', 'Apple M2'));
        $nb3->addSpecification(new Specification('RAM', '16GB'));
        $nb3->addSpecification(new Specification('SSD', '512GB'));
        $this->dm->persist($nb3);

        $nb4 = (new Product())->setName('ASUS ROG Zephyrus G14')->setSlug('asus-rog-zephyrus-g14')->setDescription('Notebook gamer compacto e poderoso');
        $nb4->setCategory($cat1)->setSupplier($s2);
        $nb4->setImage('/img/notebook-1.svg');
        $nb4->setRating(4.7);
        $nb4->addSpecification(new Specification('CPU', 'AMD Ryzen 9'));
        $nb4->addSpecification(new Specification('RAM', '16GB'));
        $nb4->addSpecification(new Specification('GPU', 'RTX 4060'));
        $this->dm->persist($nb4);

        $nb5 = (new Product())->setName('Dell Inspiron 15')->setSlug('dell-inspiron-15')->setDescription('Notebook para uso diário e trabalho');
        $nb5->setCategory($cat1)->setSupplier($s1);
        $nb5->setImage('/img/notebook-1.svg');
        $nb5->setRating(4.2);
        $nb5->addSpecification(new Specification('CPU', 'i5-11th Gen'));
        $nb5->addSpecification(new Specification('RAM', '8GB'));
        $nb5->addSpecification(new Specification('SSD', '256GB'));
        $this->dm->persist($nb5);

        // Admin user
        $admin = (new User())->setName('Admin')->setEmail('valentinscramin@gmail.com')->setPassword(password_hash('V4l3nt1n', PASSWORD_DEFAULT));
        $admin->setRole('ADMIN');
        $this->dm->persist($admin);

        // Offers - Monitor 1 (ASUS ROG Swift)
        $offer1 = (new ProductOffer())->setProduct($p1)->setMarketplace($m1)->setPrice(879)->setAffiliateLink('https://amazon.com/asus-rog-swift');
        $offer2 = (new ProductOffer())->setProduct($p1)->setMarketplace($m2)->setPrice(899)->setAffiliateLink('https://worten.pt/asus-rog-swift');
        $offer3 = (new ProductOffer())->setProduct($p1)->setMarketplace($m3)->setPrice(845)->setAffiliateLink('https://aliexpress.com/asus-rog-swift');
        $this->dm->persist($offer1);
        $this->dm->persist($offer2);
        $this->dm->persist($offer3);

        // Offers - Monitor 2 (Samsung Odyssey)
        $offer4 = (new ProductOffer())->setProduct($p2)->setMarketplace($m1)->setPrice(799)->setAffiliateLink('https://amazon.com/samsung-odyssey');
        $offer5 = (new ProductOffer())->setProduct($p2)->setMarketplace($m2)->setPrice(820)->setAffiliateLink('https://worten.pt/samsung-odyssey');
        $offer6 = (new ProductOffer())->setProduct($p2)->setMarketplace($m3)->setPrice(775)->setAffiliateLink('https://aliexpress.com/samsung-odyssey');
        $this->dm->persist($offer4);
        $this->dm->persist($offer5);
        $this->dm->persist($offer6);

        // Offers - Monitor 3 (LG UltraGear)
        $offer7 = (new ProductOffer())->setProduct($p3)->setMarketplace($m1)->setPrice(749)->setAffiliateLink('https://amazon.com/lg-ultragear');
        $offer8 = (new ProductOffer())->setProduct($p3)->setMarketplace($m2)->setPrice(769)->setAffiliateLink('https://worten.pt/lg-ultragear');
        $offer9 = (new ProductOffer())->setProduct($p3)->setMarketplace($m3)->setPrice(720)->setAffiliateLink('https://aliexpress.com/lg-ultragear');
        $this->dm->persist($offer7);
        $this->dm->persist($offer8);
        $this->dm->persist($offer9);

        // Offer - Notebook Ultra
        $offer10 = (new ProductOffer())->setProduct($nb1)->setMarketplace($m1)->setPrice(1299)->setAffiliateLink('https://amazon.com/notebook-ultra');
        $offer11 = (new ProductOffer())->setProduct($nb1)->setMarketplace($m2)->setPrice(1349)->setAffiliateLink('https://worten.pt/notebook-ultra');
        $offer12 = (new ProductOffer())->setProduct($nb1)->setMarketplace($m3)->setPrice(1279)->setAffiliateLink('https://aliexpress.com/notebook-ultra');
        $this->dm->persist($offer10);
        $this->dm->persist($offer11);
        $this->dm->persist($offer12);

        // Offers - BenQ MOBIUZ (p4)
        $offer13 = (new ProductOffer())->setProduct($p4)->setMarketplace($m1)->setPrice(649)->setAffiliateLink('https://amazon.com/benq-mobiuz');
        $offer14 = (new ProductOffer())->setProduct($p4)->setMarketplace($m2)->setPrice(679)->setAffiliateLink('https://worten.pt/benq-mobiuz');
        $offer15 = (new ProductOffer())->setProduct($p4)->setMarketplace($m3)->setPrice(625)->setAffiliateLink('https://aliexpress.com/benq-mobiuz');
        $this->dm->persist($offer13);
        $this->dm->persist($offer14);
        $this->dm->persist($offer15);

        // Offers - AOC Gaming (p5)
        $offer16 = (new ProductOffer())->setProduct($p5)->setMarketplace($m1)->setPrice(599)->setAffiliateLink('https://amazon.com/aoc-gaming');
        $offer17 = (new ProductOffer())->setProduct($p5)->setMarketplace($m2)->setPrice(619)->setAffiliateLink('https://worten.pt/aoc-gaming');
        $offer18 = (new ProductOffer())->setProduct($p5)->setMarketplace($m3)->setPrice(575)->setAffiliateLink('https://aliexpress.com/aoc-gaming');
        $this->dm->persist($offer16);
        $this->dm->persist($offer17);
        $this->dm->persist($offer18);

        // Offers - Dell UltraSharp (p6)
        $offer19 = (new ProductOffer())->setProduct($p6)->setMarketplace($m1)->setPrice(999)->setAffiliateLink('https://amazon.com/dell-ultrasharp');
        $offer20 = (new ProductOffer())->setProduct($p6)->setMarketplace($m2)->setPrice(1049)->setAffiliateLink('https://worten.pt/dell-ultrasharp');
        $offer21 = (new ProductOffer())->setProduct($p6)->setMarketplace($m3)->setPrice(959)->setAffiliateLink('https://aliexpress.com/dell-ultrasharp');
        $this->dm->persist($offer19);
        $this->dm->persist($offer20);
        $this->dm->persist($offer21);

        // Offers - Lenovo ThinkPad (nb2)
        $offer22 = (new ProductOffer())->setProduct($nb2)->setMarketplace($m1)->setPrice(1899)->setAffiliateLink('https://amazon.com/lenovo-thinkpad');
        $offer23 = (new ProductOffer())->setProduct($nb2)->setMarketplace($m2)->setPrice(1949)->setAffiliateLink('https://worten.pt/lenovo-thinkpad');
        $offer24 = (new ProductOffer())->setProduct($nb2)->setMarketplace($m3)->setPrice(1849)->setAffiliateLink('https://aliexpress.com/lenovo-thinkpad');
        $this->dm->persist($offer22);
        $this->dm->persist($offer23);
        $this->dm->persist($offer24);

        // Offers - MacBook Air M2 (nb3)
        $offer25 = (new ProductOffer())->setProduct($nb3)->setMarketplace($m1)->setPrice(1499)->setAffiliateLink('https://amazon.com/macbook-air-m2');
        $offer26 = (new ProductOffer())->setProduct($nb3)->setMarketplace($m2)->setPrice(1549)->setAffiliateLink('https://worten.pt/macbook-air-m2');
        $offer27 = (new ProductOffer())->setProduct($nb3)->setMarketplace($m3)->setPrice(1459)->setAffiliateLink('https://aliexpress.com/macbook-air-m2');
        $this->dm->persist($offer25);
        $this->dm->persist($offer26);
        $this->dm->persist($offer27);

        // Offers - ASUS ROG Zephyrus (nb4)
        $offer28 = (new ProductOffer())->setProduct($nb4)->setMarketplace($m1)->setPrice(1799)->setAffiliateLink('https://amazon.com/asus-rog-zephyrus');
        $offer29 = (new ProductOffer())->setProduct($nb4)->setMarketplace($m2)->setPrice(1859)->setAffiliateLink('https://worten.pt/asus-rog-zephyrus');
        $offer30 = (new ProductOffer())->setProduct($nb4)->setMarketplace($m3)->setPrice(1749)->setAffiliateLink('https://aliexpress.com/asus-rog-zephyrus');
        $this->dm->persist($offer28);
        $this->dm->persist($offer29);
        $this->dm->persist($offer30);

        // Offers - Dell Inspiron (nb5)
        $offer31 = (new ProductOffer())->setProduct($nb5)->setMarketplace($m1)->setPrice(899)->setAffiliateLink('https://amazon.com/dell-inspiron');
        $offer32 = (new ProductOffer())->setProduct($nb5)->setMarketplace($m2)->setPrice(929)->setAffiliateLink('https://worten.pt/dell-inspiron');
        $offer33 = (new ProductOffer())->setProduct($nb5)->setMarketplace($m3)->setPrice(869)->setAffiliateLink('https://aliexpress.com/dell-inspiron');
        $this->dm->persist($offer31);
        $this->dm->persist($offer32);
        $this->dm->persist($offer33);

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
