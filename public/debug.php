<?php
// Place this file in your public directory

// Load router
require_once __DIR__ . '/../application/router.php';

// Show requested URI
echo "<h1>Router Debug</h1>";
echo "<p>Requested URI: " . htmlspecialchars($_SERVER['REQUEST_URI']) . "</p>";

// Create router instance
$router = new Router();

// Register test routes
$router->get('/test/([0-9]+)', function($id) {
    echo "<p>Test route matched with ID: $id</p>";
});

// Show all registered routes (if your Router class exposes them)
echo "<h2>Testing Route Pattern Matching</h2>";

// Test routes
$testRoutes = [
    '/services/edit/1',
    '/services/edit/123',
    '/test/123'
];

foreach ($testRoutes as $route) {
    echo "<h3>Testing: $route</h3>";
    
    // Simulate a request to this route
    $_SERVER['REQUEST_URI'] = $route;
    
    try {
        // Try to match the route - we'll just check if the pattern would match
        if (preg_match('#^/services/edit/([0-9]+)$#', $route, $matches)) {
            echo "<p style='color:green'>✓ Route would match /services/edit/([0-9]+)</p>";
            echo "<p>ID parameter would be: " . $matches[1] . "</p>";
        } else {
            echo "<p style='color:red'>✗ Route would NOT match /services/edit/([0-9]+)</p>";
        }
        
        if (preg_match('#^/test/([0-9]+)$#', $route, $matches)) {
            echo "<p style='color:green'>✓ Route would match /test/([0-9]+)</p>";
            echo "<p>ID parameter would be: " . $matches[1] . "</p>";
        } else {
            echo "<p style='color:red'>✗ Route would NOT match /test/([0-9]+)</p>";
        }
        
    } catch (Exception $e) {
        echo "<p style='color:red'>Error: " . $e->getMessage() . "</p>";
    }
}
?>