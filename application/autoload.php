<?php
/**
 * Custom autoloader for classes that don't follow PSR-4
 * This provides compatibility for legacy codebase
 */

spl_autoload_register(function ($class) {
    // Convert namespace separators to directory separators
    $class = str_replace('\\', DIRECTORY_SEPARATOR, $class);
    
    // Map of legacy class prefixes to directories
    $prefixes = [
        'Controller_' => 'application/controllers/',
        'Model_' => 'application/models/',
    ];
    
    // Check if class uses a known prefix
    foreach ($prefixes as $prefix => $dir) {
        if (strpos($class, $prefix) === 0) {
            $class = substr($class, strlen($prefix));
            $file = $dir . $class . '.php';
            
            if (file_exists($file)) {
                require $file;
                return true;
            }
        }
    }
    
    return false;
});

// Include general helper functions
require_once __DIR__ . '/helpers/general_helper.php';
