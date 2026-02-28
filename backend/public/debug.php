<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

use App\Kernel;

try {
    require_once dirname(__DIR__).'/vendor/autoload_runtime.php';
    echo "Autoloader OK\n";
    
    $kernel = function (array $context) {
        return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
    };
    
    echo "Kernel created OK\n";
    $handle = $kernel(['APP_ENV' => 'dev', 'APP_DEBUG' => true]);
    echo "Kernel instantiated: " . get_class($handle) . "\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
