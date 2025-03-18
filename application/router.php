<?php
/**
 * Simple Router for WebSchedulr
 * 
 * This router handles incoming requests and maps them to appropriate controllers/views
 */

class Router {
    protected $routes = [];
    private $notFoundCallback;
    
    /**
     * Add a route
     * 
     * @param string $method HTTP method (GET, POST, etc.)
     * @param string $pattern URL pattern to match
     * @param callable|array $callback Function or [controllerClass, method] to call when route is matched
     */
    public function addRoute($method, $pattern, $callback) {
        $this->routes[$method][$pattern] = $callback;
    }
    
    /**
     * Add GET route
     */
    public function get($pattern, $callback) {
        $this->addRoute('GET', $pattern, $callback);
    }
    
    /**
     * Add POST route
     */
    public function post($pattern, $callback) {
        $this->addRoute('POST', $pattern, $callback);
    }
    
    /**
     * Add route that handles both GET and POST
     */
    public function any($pattern, $callback) {
        $this->addRoute('GET', $pattern, $callback);
        $this->addRoute('POST', $pattern, $callback);
    }
    
    /**
     * Set not found handler
     */
    public function notFound($callback) {
        $this->notFoundCallback = $callback;
    }
    
    /**
     * Match route against current request
     */
    private function matchRoute($requestMethod, $requestPath) {
        if (isset($this->routes[$requestMethod])) {
            foreach ($this->routes[$requestMethod] as $route => $callback) {
                // Convert route pattern to regex
                $routePattern = $this->convertRouteToRegex($route);
                
                // Check if the path matches the route pattern
                if (preg_match($routePattern, $requestPath, $matches)) {
                    // Extract named parameters properly - this is the key fix
                    $params = [];
                    
                    // Filter out non-string keys (numbered indices)
                    foreach ($matches as $key => $value) {
                        if (is_string($key)) {
                            $params[$key] = $value;
                        }
                    }
                    
                    // If we have named parameters, use them; otherwise use positional parameters
                    if (empty($params)) {
                        array_shift($matches); // Remove the full match
                        $params = array_values($matches);
                    } else {
                        // Convert named params to positional params to avoid PHP 8 named argument issues
                        $params = array_values($params);
                    }
                    
                    // Debug output
                    if (isset($_GET['debug_router'])) {
                        echo "<p>Route matched: $route</p>";
                        echo "<p>Pattern used: $routePattern</p>";
                        echo "<p>Extracted parameters: " . json_encode($params) . "</p>";
                    }
                    
                    return [
                        'callback' => $callback,
                        'params' => $params
                    ];
                }
            }
        }
        
        return false;
    }
    
    /**
     * Convert route pattern to regex
     */
    private function convertRouteToRegex($route) {
        // Convert :param syntax to regex capture group
        $route = preg_replace('/:([a-zA-Z0-9_]+)/', '(?P<$1>[^/]+)', $route);
        
        // Escape forward slashes and wrap in regex delimiters
        $routePattern = "#^" . str_replace('/', '\/', $route) . "$#";
        
        return $routePattern;
    }
    
    /**
     * Run the router
     */
    public function run() {
        // Get request method and path
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        $requestPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        // For debugging
        if (isset($_GET['debug_router'])) {
            echo "<h1>Router Debug</h1>";
            echo "<p>Request Method: $requestMethod</p>";
            echo "<p>Request Path: $requestPath</p>";
            echo "<h2>Registered Routes</h2><ul>";
            foreach ($this->routes as $method => $routes) {
                foreach ($routes as $route => $callback) {
                    echo "<li>$method: $route</li>";
                }
            }
            echo "</ul>";
        }
        
        // Find matching route
        $match = $this->matchRoute($requestMethod, $requestPath);
        
        // If route matched
        if ($match) {
            // Debug info
            if (isset($_GET['debug_router'])) {
                echo "<h2>Route Match Found</h2>";
                echo "<p>Parameters: " . json_encode($match['params']) . "</p>";
            }
            
            $callback = $match['callback'];
            $params = $match['params'];
            
            if (is_callable($callback)) {
                // THIS IS THE LINE THAT WAS CAUSING THE ERROR
                call_user_func_array($callback, $params);
                return;
            }
            
            // Handle controller method calls [ControllerClass, method]
            if (is_array($callback) && count($callback) === 2) {
                $controller = $callback[0];
                $method = $callback[1];
                
                if (is_string($controller)) {
                    $controller = new $controller();
                }
                
                call_user_func_array([$controller, $method], $params);
                return;
            }
        }
        
        // If no route matched and we have a 404 handler
        if ($this->notFoundCallback) {
            call_user_func($this->notFoundCallback);
        } else {
            header("HTTP/1.0 404 Not Found");
            include __DIR__ . '/views/errors/404.php';
        }
    }
}

// Find where routes are defined and add:
// $router->get('/clients/debug', 'ClientController@debug');