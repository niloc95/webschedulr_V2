<?php
/**
 * WebSchedulr Application Entry Point
 */

// Show all errors during development
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Add routing debug
$debugRouting = isset($_GET['debug_router']) && $_GET['debug_router'] == 1;

// Load bootstrap file
require_once __DIR__ . '/../bootstrap.php';

// Load application router
require_once __DIR__ . '/../application/router.php';

// Create router instance
$router = new Router();

// =============================================================================
// CALENDAR ROUTES - Define these FIRST to ensure proper priority
// =============================================================================
$router->get('/calendar', function() {
    require_once __DIR__ . '/../application/controllers/CalendarController.php';
    $controller = new CalendarController();
    $controller->index();
});

$router->get('/calendar/day', function() {
    require_once __DIR__ . '/../application/controllers/CalendarController.php';
    $controller = new CalendarController();
    $controller->day();
});

$router->get('/calendar/create', function() {
    require_once __DIR__ . '/../application/controllers/CalendarController.php';
    $controller = new CalendarController();
    $controller->create();
});

$router->post('/calendar/create', function() {
    require_once __DIR__ . '/../application/controllers/CalendarController.php';
    $controller = new CalendarController();
    $controller->store();
});

// IMPORTANT - This is the key route that's being overridden
$router->get('/calendar/edit/:id', function($id) use ($debugRouting) {  // Add "use ($debugRouting)" here
    if ($debugRouting) {
        echo "DEBUG: Calendar edit route matched with ID: $id<br>";
    }
    require_once __DIR__ . '/../application/controllers/CalendarController.php';
    $controller = new CalendarController();
    $controller->edit($id);
});

$router->post('/calendar/update/:id', function($id) {
    require_once __DIR__ . '/../application/controllers/CalendarController.php';
    $controller = new CalendarController();
    $controller->update($id);
});

$router->post('/calendar/delete/:id', function($id) {
    require_once __DIR__ . '/../application/controllers/CalendarController.php';
    $controller = new CalendarController();
    $controller->delete($id);
});

// =============================================================================
// SERVICE ROUTES - Define these AFTER calendar routes
// =============================================================================
$router->get('/services', function() {
    require_once __DIR__ . '/../application/controllers/ServiceController.php';
    $controller = new ServiceController();
    $controller->index();
});

$router->get('/services/create', function() {
    require_once __DIR__ . '/../application/controllers/ServiceController.php';
    $controller = new ServiceController();
    $controller->create();
});

$router->post('/services/store', function() {
    require_once __DIR__ . '/../application/controllers/ServiceController.php';
    $controller = new ServiceController();
    $controller->store();
});

// IMPORTANT - Use a consistent format for route parameters
$router->get('/services/edit/:id', function($id) use ($debugRouting) {  // Add "use ($debugRouting)" here
    if ($debugRouting) {
        echo "DEBUG: Service edit route matched with ID: $id<br>";
    }
    require_once __DIR__ . '/../application/controllers/ServiceController.php';
    $controller = new ServiceController();
    $controller->edit($id);
});

$router->post('/services/update/:id', function($id) {
    require_once __DIR__ . '/../application/controllers/ServiceController.php';
    $controller = new ServiceController();
    $controller->update($id);
});

$router->post('/services/delete/:id', function($id) {
    require_once __DIR__ . '/../application/controllers/ServiceController.php';
    $controller = new ServiceController();
    $controller->delete($id);
});

// =============================================================================
// CLIENT ROUTES
// =============================================================================
$router->get('/clients', function() {
    require_once __DIR__ . '/../application/controllers/ClientController.php';
    $controller = new ClientController();
    $controller->index();
});

$router->get('/clients/create', function() {
    require_once __DIR__ . '/../application/controllers/ClientController.php';
    $controller = new ClientController();
    $controller->create();
});

$router->post('/clients/store', function() {
    require_once __DIR__ . '/../application/controllers/ClientController.php';
    $controller = new ClientController();
    $controller->store();
});

$router->get('/clients/show/:id', function($id) {
    require_once __DIR__ . '/../application/controllers/ClientController.php';
    $controller = new ClientController();
    $controller->show($id);
});

$router->get('/clients/edit/:id', function($id) {
    require_once __DIR__ . '/../application/controllers/ClientController.php';
    $controller = new ClientController();
    $controller->edit($id);
});

$router->post('/clients/update/:id', function($id) {
    require_once __DIR__ . '/../application/controllers/ClientController.php';
    $controller = new ClientController();
    $controller->update($id);
});

$router->post('/clients/delete/:id', function($id) {
    require_once __DIR__ . '/../application/controllers/ClientController.php';
    $controller = new ClientController();
    $controller->delete($id);
});

// Add this route for AJAX client creation
$router->post('/clients/ajax-create', function() {
    require_once __DIR__ . '/../application/controllers/ClientController.php';
    $controller = new ClientController();
    $controller->ajaxCreate();
});

// Add this route for AJAX service creation
$router->post('/services/ajax-create', function() {
    require_once __DIR__ . '/../application/controllers/ServiceController.php';
    $controller = new ServiceController();
    $controller->ajaxCreate();
});

// Add this route for updating appointment status
$router->post('/calendar/update-status', function() {
    require_once __DIR__ . '/../application/controllers/CalendarController.php';
    $controller = new CalendarController();
    $controller->updateStatus();
});

// =============================================================================
// DASHBOARD ROUTES
// =============================================================================
$router->get('/dashboard', function() {
    // Check if user is logged in
    session_start();
    if (!isset($_SESSION['user'])) {
        header('Location: /login');
        exit;
    }
    
    require_once __DIR__ . '/../application/controllers/DashboardController.php';
    $controller = new DashboardController();
    $controller->index();
});

// =============================================================================
// AUTH ROUTES
// =============================================================================
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
    // Form data processing...
    // [Keep your existing code here]
});

$router->get('/logout', function() {
    session_start();
    session_destroy();
    header('Location: /login');
    exit;
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

// =============================================================================
// DIAGNOSTIC ROUTES
// =============================================================================
$router->get('/diagnostic/db', function() {
    require_once __DIR__ . '/../application/controllers/DiagnosticController.php';
    $controller = new DiagnosticController();
    $controller->testDatabaseConnection();
});

$router->get('/diagnostic/appointments', function() {
    require_once __DIR__ . '/../application/controllers/DiagnosticController.php';
    $controller = new DiagnosticController();
    $controller->checkAppointmentsTable();
});

$router->get('/diagnostic/check-services', function() {
    require_once __DIR__ . '/../application/controllers/DiagnosticController.php';
    $controller = new DiagnosticController();
    $controller->checkServiceColumns();
});

$router->get('/diagnostic/services', function() {
    require_once __DIR__ . '/../application/controllers/DiagnosticController.php';
    $controller = new DiagnosticController();
    $controller->testServicesTable();
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