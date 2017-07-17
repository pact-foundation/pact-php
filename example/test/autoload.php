<?php
// include parents composer dependencies
require __DIR__ . '/../../vendor/autoload.php';

// create new autoloard to load the library
function pact_php_example_autoloader($className)
{
    $base = __DIR__ . '/../../src';

    $className = str_ireplace('\\PhpPact', '', $className);
    $className = str_ireplace('PhpPact', '', $className);

    $path = $className;
    $file = $base . $path . '.php';

    if (file_exists($file)) {
        require $file;
    } else {
        echo 'Class "' . $className . '" could not be autoloaded: ' . $file;
    }
}

spl_autoload_register('pact_php_example_autoloader');