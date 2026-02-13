<?php
try {
    $m = new MongoDB\Driver\Manager('mongodb://127.0.0.1:27017');
    $cmd = new MongoDB\Driver\Command(['ping' => 1]);
    $r = $m->executeCommand('admin', $cmd);
    echo "MongoDB connection OK\n";
    exit(0);
} catch (Throwable $e) {
    echo 'MongoDB connection ERROR: ' . $e->getMessage() . "\n";
    exit(1);
}
