<?php
require __DIR__ . '/../vendor/autoload.php';

// Load environment variables from .env
use Symfony\Component\Dotenv\Dotenv;
if (class_exists(Dotenv::class)) {
    (new Dotenv())->loadEnv(__DIR__ . '/../.env');
}

require __DIR__ . '/../src/Kernel.php';
use App\Kernel;

$kernel = new Kernel('dev', true);
$kernel->boot();
$container = $kernel->getContainer();
$dm = $container->get('doctrine_mongodb.odm.document_manager');

$collections = [
    'App\\Document\\Category',
    'App\\Document\\Marketplace',
    'App\\Document\\Supplier',
    'App\\Document\\Product',
    'App\\Document\\ProductOffer',
    'App\\Document\\User',
];
foreach ($collections as $c) {
    try {
        $repo = $dm->getRepository($c);
        $items = $repo->findAll();
        echo $c . ': ' . count($items) . PHP_EOL;
    } catch (Throwable $e) {
        echo 'Error for ' . $c . ': ' . $e->getMessage() . PHP_EOL;
    }
}
