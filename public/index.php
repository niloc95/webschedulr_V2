<?php
/**
 * WebSchedulr Application Entry Point
 */

// Show all errors during development
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Load bootstrap file
require_once __DIR__ . '/../bootstrap.php';

// Load application router
require_once __DIR__ . '/../application/router.php';

// Create router instance
$router = new Router();

// Define routes
$router->get('/', function() {
    // Check if user is logged in
    session_start();
    if (!isset($_SESSION['user'])) {
        header('Location: /login');
        exit;
    }
    include __DIR__ . '/../application/views/dashboard/index.php';
});

$router->get('/dashboard', function() {
    // Check if user is logged in
    session_start();
    if (!isset($_SESSION['user'])) {
        header('Location: /login');
        exit;
    }
    include __DIR__ . '/../application/views/dashboard/index.php';
});

// Debug route
// Dashboard route with better error handling
$router->get('/dashboard', function() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Check if user is logged in
    if (!isset($_SESSION['user'])) {
        header('Location: /login');
        exit;
    }
    
    try {
        // Include the dashboard controller
        require_once __DIR__ . '/../application/controllers/DashboardController.php';
        
        // Instantiate and call the controller
        $controller = new DashboardController();
        $controller->index();
    } catch (Exception $e) {
        // Output user-friendly error and debug info if needed
        echo "<h3>Dashboard Error</h3>";
        echo "<p>Sorry, we couldn't load the dashboard. Please try again later.</p>";
        
        if (Config::DEBUG_MODE) {
            echo "<pre>Error: " . htmlspecialchars($e->getMessage()) . "</pre>";
            echo "<p>Check the <a href='/debug/dashboard'>dashboard debug page</a> for more information.</p>";
        }
    }
});

// Auth routes
$router->get('/login', function() {
    // Start session to check for success message
    session_start();
    include __DIR__ . '/../application/views/auth/login.php';
});

$router->post('/login', function() {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = "Email and password are required";
        include __DIR__ . '/../application/views/auth/login.php';
        return;
    }
    
    // Connect to database
    try {
        $db = new PDO(
            "mysql:host=" . Config::DB_HOST . ";dbname=" . Config::DB_NAME . ";charset=utf8mb4",
            Config::DB_USERNAME,
            Config::DB_PASSWORD,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]
        );
        
        // Get user from database
        $stmt = $db->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
        $stmt->bindValue(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch();
        
        // Verify password
        if ($user && password_verify($password, $user['password'])) {
            // Login successful
            session_start();
            $_SESSION['user'] = [
                'id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'role' => $user['role']
            ];
            header('Location: /dashboard');
            exit;
        } else {
            // Special case for demo admin user with plain text password
            if ($email === 'admin@webschedulr.com' && $password === 'admin123') {
                session_start();
                $_SESSION['user'] = [
                    'id' => 1,
                    'name' => 'Admin User',
                    'email' => 'admin@webschedulr.com',
                    'role' => 'admin'
                ];
                header('Location: /dashboard');
                exit;
            }
            
            // Login failed
            $error = "Invalid email or password";
            include __DIR__ . '/../application/views/auth/login.php';
            return;
        }
        
    } catch (PDOException $e) {
        // Log the error (in a production app)
        $error = "An error occurred while logging in. Please try again.";
        
        // For debugging:
        if (Config::DEBUG_MODE) {
            $error = "Database error: " . $e->getMessage();
        }
        
        include __DIR__ . '/../application/views/auth/login.php';
        return;
    }
});

$router->get('/register', function() {
    include __DIR__ . '/../application/views/auth/register.php';
});

$router->post('/register', function() {
    // Form data
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    // Basic validation
    if (empty($name) || empty($email) || empty($password)) {
        $error = "All fields are required";
        include __DIR__ . '/../application/views/auth/register.php';
        return;
    }
    
    if ($password !== $confirmPassword) {
        $error = "Passwords do not match";
        include __DIR__ . '/../application/views/auth/register.php';
        return;
    }
    
    if (strlen($password) < 6) {
        $error = "Password must be at least 6 characters";
        include __DIR__ . '/../application/views/auth/register.php';
        return;
    }
    
    // Connect to database
    try {
        $db = new PDO(
            "mysql:host=" . Config::DB_HOST . ";dbname=" . Config::DB_NAME . ";charset=utf8mb4",
            Config::DB_USERNAME,
            Config::DB_PASSWORD,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]
        );
        
        // Check if email already exists
        $stmt = $db->prepare("SELECT id FROM users WHERE email = :email");
        $stmt->bindValue(':email', $email);
        $stmt->execute();
        
        if ($stmt->fetch()) {
            $error = "Email already exists";
            include __DIR__ . '/../application/views/auth/register.php';
            return;
        }
        
        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        // Insert user
        $stmt = $db->prepare("
            INSERT INTO users (name, email, password, role, created_at) 
            VALUES (:name, :email, :password, 'user', NOW())
        ");
        
        $stmt->bindValue(':name', $name);
        $stmt->bindValue(':email', $email);
        $stmt->bindValue(':password', $hashedPassword);
        $stmt->execute();
        
        // Success! Redirect to login
        session_start();
        $_SESSION['success'] = "Registration successful. Please login.";
        header('Location: /login');
        exit;
        
    } catch (PDOException $e) {
        // Log the error (in a production app)
        $error = "An error occurred while registering. Please try again.";
        
        // For debugging:
        if (Config::DEBUG_MODE) {
            $error = "Database error: " . $e->getMessage();
        }
        
        include __DIR__ . '/../application/views/auth/register.php';
        return;
    }
});

$router->get('/logout', function() {
    session_start();
    session_destroy();
    header('Location: /login');
    exit;
});

// Dashboard route - make sure this config path is correct
$router->get('/dashboard', function() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Check if user is logged in
    if (!isset($_SESSION['user'])) {
        header('Location: /login');
        exit;
    }
    
    // Include the dashboard controller
    require_once __DIR__ . '/../application/controllers/DashboardController.php';
    
    // Instantiate and call the controller
    $controller = new DashboardController();
    $controller->index();
});

// Redirect root URL to dashboard for logged in users or login page otherwise
$router->get('/', function() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (isset($_SESSION['user'])) {
        header('Location: /dashboard');
    } else {
        header('Location: /login');
    }
    exit;
});


// Calendar routes
$router->get('/calendar', function() {
    session_start();
    
    // Check if user is logged in
    if (!isset($_SESSION['user'])) {
        header('Location: /login');
        exit;
    }
    
    // Include and instantiate the calendar controller
    require_once __DIR__ . '/../application/controllers/CalendarController.php';
    $controller = new CalendarController();
    $controller->index();
});

$router->get('/calendar/day', function() {
    session_start();
    
    // Check if user is logged in
    if (!isset($_SESSION['user'])) {
        header('Location: /login');
        exit;
    }
    
    // Include and instantiate the calendar controller
    require_once __DIR__ . '/../application/controllers/CalendarController.php';
    $controller = new CalendarController();
    $controller->day();
});

$router->get('/calendar/create', function() {
    session_start();
    
    // Check if user is logged in
    if (!isset($_SESSION['user'])) {
        header('Location: /login');
        exit;
    }
    
    // Include and instantiate the calendar controller
    require_once __DIR__ . '/../application/controllers/CalendarController.php';
    $controller = new CalendarController();
    $controller->createAppointment();
});

$router->post('/calendar/create', function() {
    session_start();
    
    // Check if user is logged in
    if (!isset($_SESSION['user'])) {
        header('Location: /login');
        exit;
    }
    
    // Include and instantiate the calendar controller
    require_once __DIR__ . '/../application/controllers/CalendarController.php';
    $controller = new CalendarController();
    $controller->createAppointment();
});

$router->get('/calendar/edit/:id', function($id) {
    session_start();
    
    // Check if user is logged in
    if (!isset($_SESSION['user'])) {
        header('Location: /login');
        exit;
    }
    
    // Include and instantiate the calendar controller
    require_once __DIR__ . '/../application/controllers/CalendarController.php';
    $controller = new CalendarController();
    $controller->editAppointment($id);
});

$router->post('/calendar/edit/:id', function($id) {
    session_start();
    
    // Check if user is logged in
    if (!isset($_SESSION['user'])) {
        header('Location: /login');
        exit;
    }
    
    // Include and instantiate the calendar controller
    require_once __DIR__ . '/../application/controllers/CalendarController.php';
    $controller = new CalendarController();
    $controller->editAppointment($id);
});

$router->get('/calendar/delete/:id', function($id) {
    session_start();
    
    // Check if user is logged in
    if (!isset($_SESSION['user'])) {
        header('Location: /login');
        exit;
    }
    
    // Include and instantiate the calendar controller
    require_once __DIR__ . '/../application/controllers/CalendarController.php';
    $controller = new CalendarController();
    $controller->deleteAppointment($id);
});

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