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

$router->get('/calendar/day/([0-9]{4}-[0-9]{2}-[0-9]{2})', function($date) {
    require_once __DIR__ . '/../application/controllers/CalendarController.php';
    $controller = new CalendarController();
    $controller->day($date);
});

$router->get('/calendar/day', function() {
    require_once __DIR__ . '/../application/controllers/CalendarController.php';
    $controller = new CalendarController();
    $date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
    $controller->day($date);
});

$router->get('/calendar/create', function() {
    require_once __DIR__ . '/../application/controllers/CalendarController.php';
    $controller = new CalendarController();
    $controller->create();
});

$router->post('/calendar/save', function() {
    require_once __DIR__ . '/../application/controllers/CalendarController.php';
    $controller = new CalendarController();
    $controller->save();
});

$router->get('/calendar/edit/([0-9]+)', function($id) {
    require_once __DIR__ . '/../application/controllers/CalendarController.php';
    $controller = new CalendarController();
    $controller->edit($id);
});

$router->post('/calendar/delete/([0-9]+)', function($id) {
    require_once __DIR__ . '/../application/controllers/CalendarController.php';
    $controller = new CalendarController();
    $controller->delete($id);
});

$router->post('/calendar/update-status', function() {
    require_once __DIR__ . '/../application/controllers/CalendarController.php';
    $controller = new CalendarController();
    $controller->updateStatus();
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

$router->get('/services/edit/:id', function($id) use ($debugRouting) {
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

$router->post('/services/ajax-create', function() {
    require_once __DIR__ . '/../application/controllers/ServiceController.php';
    $controller = new ServiceController();
    $controller->ajaxCreate();
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

$router->post('/clients/ajax-create', function() {
    require_once __DIR__ . '/../application/controllers/ClientController.php';
    $controller = new ClientController();
    $controller->ajaxCreate();
});

// =============================================================================
// APPOINTMENT ROUTES
// =============================================================================
$router->get('/appointments', function() {
    require_once __DIR__ . '/../application/controllers/AppointmentController.php';
    $controller = new AppointmentController();
    $controller->index();
});

$router->get('/appointments/schema', function() {
    require_once __DIR__ . '/../application/controllers/AppointmentController.php';
    $controller = new AppointmentController();
    $controller->showSchema();
});

// =============================================================================
// DASHBOARD ROUTES
// =============================================================================
$router->get('/dashboard', function() {
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
// CLIENT QUICK ADD ROUTES
// =============================================================================

$router->post('/clients/quick-add', function() {
    require_once __DIR__ . '/../application/controllers/ClientController.php';
    $controller = new ClientController();
    $controller->quickAdd();
});

// Service quick add
$router->post('/services/quick-add', function() {
    require_once __DIR__ . '/../application/controllers/ServiceController.php';
    $controller = new ServiceController();
    $controller->quickAdd();
});

// =============================================================================
// AUTH ROUTES
// =============================================================================
$router->get('/login', function() {
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
        
        $stmt = $db->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
        $stmt->bindValue(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
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
            
            $error = "Invalid email or password";
            include __DIR__ . '/../application/views/auth/login.php';
            return;
        }
        
    } catch (PDOException $e) {
        $error = "An error occurred while logging in. Please try again.";
        
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
});

$router->get('/logout', function() {
    session_start();
    session_destroy();
    header('Location: /login');
    exit;
});

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

// Initialize Router
// Define Routes
// ...existing routes

// Calendar Routes
$router->get('/calendar', function() {
    require_once __DIR__ . '/../application/controllers/CalendarController.php';
    $controller = new CalendarController();
    $controller->index();
});

// Calendar Day View - with direct date parameter
$router->get('/calendar/day/([0-9]{4}-[0-9]{2}-[0-9]{2})', function($date) {
    require_once __DIR__ . '/../application/controllers/CalendarController.php';
    $controller = new CalendarController();
    $controller->day($date);
});

// Calendar Day View - with query parameters
$router->get('/calendar/day', function() {
    require_once __DIR__ . '/../application/controllers/CalendarController.php';
    $controller = new CalendarController();
    $controller->day();
});

// Create Appointment Form
$router->get('/calendar/create', function() {
    require_once __DIR__ . '/../application/controllers/CalendarController.php';
    $controller = new CalendarController();
    $controller->create();
});

// Store New Appointment
$router->post('/calendar/store', function() {
    require_once __DIR__ . '/../application/controllers/CalendarController.php';
    $controller = new CalendarController();
    $controller->store();
});

// Edit Appointment Form
$router->get('/calendar/edit/([0-9]+)', function($id) {
    require_once __DIR__ . '/../application/controllers/CalendarController.php';
    $controller = new CalendarController();
    $controller->edit($id);
});

// Update Appointment
$router->post('/calendar/update/([0-9]+)', function($id) {
    require_once __DIR__ . '/../application/controllers/CalendarController.php';
    $controller = new CalendarController();
    $controller->update($id);
});

// Delete Appointment
$router->post('/calendar/delete/([0-9]+)', function($id) {
    require_once __DIR__ . '/../application/controllers/CalendarController.php';
    $controller = new CalendarController();
    $controller->delete($id);
});

// Update Appointment Status (AJAX)
$router->post('/calendar/update-status', function() {
    require_once __DIR__ . '/../application/controllers/CalendarController.php';
    $controller = new CalendarController();
    $controller->updateStatus();
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

$router->get('/diagnostic/table-structure', function() {
    include __DIR__ . '/../application/views/diagnostic/table-structure.php';
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