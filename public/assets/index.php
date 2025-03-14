<?php
/**
 * WebSchedulr Application Entry Point
 */

// Load bootstrap file
require_once __DIR__ . '/../bootstrap.php';

// Load application router
require_once __DIR__ . '/../application/router.php';
require_once __DIR__ . '/../application/controllers/AuthController.php';

use WebSchedulr\Controllers\AuthController;

// Create router instance
$router = new Router();

// Define routes
$router->get('/', function() {
    // Check if user is logged in
    session_start();
    if (!isset($_SESSION['user'])) {
        redirect('login');
    }
    include __DIR__ . '/../application/views/dashboard/index.php';
});

$router->get('/dashboard', function() {
    // Check if user is logged in
    session_start();
    if (!isset($_SESSION['user'])) {
        redirect('login');
    }
    include __DIR__ . '/../application/views/dashboard/index.php';
});

// Auth routes
$router->any('/login', [AuthController::class, 'login']);
$router->any('/register', [AuthController::class, 'register']);
$router->get('/logout', [AuthController::class, 'logout']);

// 404 handler
$router->notFound(function() {
    header('HTTP/1.1 404 Not Found');
    echo '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Page Not Found - WebSchedulr</title>
        <link rel="stylesheet" href="/css/app.css">
    </head>
    <body>
        <div class="container mt-5">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header bg-danger text-white">
                            <h4 class="mb-0">404 - Page Not Found</h4>
                        </div>
                        <div class="card-body">
                            <p>The page you are looking for does not exist.</p>
                            <a href="/" class="btn btn-primary">Go to Dashboard</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
    </html>';
});

// Run the router
$router->run();