<?php

/**
 * Router script for PHP's built-in web server.
 * Routes all requests through public/index.php unless they're static files.
 */

// Get the requested URI
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// Check if it's a static file in the public directory
$publicPath = __DIR__ . '/public' . $uri;

// If it's a file and it exists, serve it
if ($uri !== '/' && is_file($publicPath)) {
    return false; // Let PHP's built-in web server serve the file
}

// Otherwise, route through index.php
$_SERVER['SCRIPT_FILENAME'] = __DIR__ . '/public/index.php';
$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['PHP_SELF'] = '/index.php';

chdir(__DIR__ . '/public');
require __DIR__ . '/public/index.php';
