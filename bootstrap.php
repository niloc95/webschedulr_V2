<?php
/* ----------------------------------------------------------------------------
 * @webSchedulr - Online Appointment Scheduler
 *
 * @package     @webSchedulr
 * @author      N.N Cara <nilo.cara@frontend.co.za>
 * @copyright   Copyright (c) Nilesh Cara
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://webschedulr.co.za
 * @since       v1.0.0
 * ---------------------------------------------------------------------------- */

// Load Composer autoloader if available
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
    
    // Load environment variables from .env file
    if (class_exists('Dotenv\Dotenv') && file_exists(__DIR__ . '/.env')) {
        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
        $dotenv->load();
    }
}

// Load Config class
if (file_exists(__DIR__ . '/config.php')) {
    require_once __DIR__ . '/config.php';
} else {
    die('Configuration file not found. Please create a config.php file in the root directory.');
}

// Set up custom autoloader
if (file_exists(__DIR__ . '/application/autoload.php')) {
    require_once __DIR__ . '/application/autoload.php';
}

// Set up logger if available
if (class_exists('Monolog\Logger')) {
    $logDir = __DIR__ . '/logs';
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }
    
    $logger = new Monolog\Logger('webschedulr');
    $logger->pushHandler(new Monolog\Handler\StreamHandler(
        $logDir . '/app.log',
        Config::DEBUG_MODE ? Monolog\Logger::DEBUG : Monolog\Logger::ERROR
    ));
    
    // Make logger globally available
    function app_logger() {
        global $logger;
        return $logger;
    }
}

// Create helper functions
if (!function_exists('site_url')) {
    /**
     * Generate URL for site pages
     * 
     * @param string $path Site path
     * @return string Full site URL
     */
    function site_url($path = '') {
        $baseUrl = Config::BASE_URL;
        return rtrim($baseUrl, '/') . '/' . ltrim($path, '/');
    }
}

if (!function_exists('redirect')) {
    /**
     * Redirect to another URL
     * 
     * @param string $url URL to redirect to
     * @return void
     */
    function redirect($url) {
        header('Location: ' . site_url($url));
        exit;
    }
}

// Load other helpers
if (file_exists(__DIR__ . '/application/helpers/general_helper.php')) {
    require_once __DIR__ . '/application/helpers/general_helper.php';
}