<?php
// Test if .htaccess is being read
if (function_exists('apache_get_modules')) {
    $modules = apache_get_modules();
    echo "mod_rewrite enabled: " . (in_array('mod_rewrite', $modules) ? 'YES' : 'NO') . "\n";
} else {
    echo "Cannot check Apache modules (apache_get_modules not available)\n";
}

echo "AllowOverride check: Create a file called test-htaccess.txt in .htaccess to see if rules work\n";
echo "REQUEST_URI: " . ($_SERVER['REQUEST_URI'] ?? 'not set') . "\n";
echo "SCRIPT_NAME: " . ($_SERVER['SCRIPT_NAME'] ?? 'not set') . "\n";
echo "SCRIPT_FILENAME: " . ($_SERVER['SCRIPT_FILENAME'] ?? 'not set') . "\n";
