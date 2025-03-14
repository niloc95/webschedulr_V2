<?php
/**
 * Simple Router for WebSchedulr
 */

class Router {
    private $routes = [];
    private $notFoundCallback;
    
    public function addRoute($method, $pattern, $callback) {
        $this->routes[] = [
            'method' => $method,
            'pattern' => $pattern,
            'callback' => $callback
        ];
    }
    
    public function get($pattern, $callback) {
        $this->addRoute('GET', $pattern, $callback);
    }
    
    public function post($pattern, $callback) {
        $this->addRoute('POST', $pattern, $callback);
    }
    
    public function notFound($callback) {
        $this->notFoundCallback = $callback;
    }
    
    private function matchRoute($route, $method, $uri) {
        if ($route['method'] !== $method) {
            return false;
        }
        
        // Direct string comparison first - for exact matches
        if ($route['pattern'] === $uri) {
            return [];
        }
        
        return false;
    }
    
    public function run() {
        // Get the URI and clean it up
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $method = $_SERVER['REQUEST_METHOD'];
        
        // Debug information
        echo "<!-- DEBUG: Requested URI: '{$uri}', Method: '{$method}' -->\n";
        
        foreach ($this->routes as $route) {
            echo "<!-- DEBUG: Checking route '{$route['pattern']}' -->\n";
            
            $matches = $this->matchRoute($route, $method, $uri);
            
            if ($matches !== false) {
                echo "<!-- DEBUG: Route matched! -->\n";
                call_user_func_array($route['callback'], $matches);
                return;
            }
        }
        
        // If no route matched and we have a 404 handler
        echo "<!-- DEBUG: No route matched. Running 404 handler. -->\n";
        if ($this->notFoundCallback) {
            call_user_func($this->notFoundCallback);
        } else {
            header('HTTP/1.1 404 Not Found');
            echo '404 Not Found';
        }
    }
}