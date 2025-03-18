<?php
/* WebSchedulr Service Controller */

class ServiceController {
    private $db;
    
    public function __construct() {
        // Load config file with DB credentials
        require_once __DIR__ . '/../../config.php';
        
        try {
            $this->db = new PDO(
                "mysql:host=" . Config::DB_HOST . ";dbname=" . Config::DB_NAME . ";charset=utf8mb4",
                Config::DB_USERNAME,
                Config::DB_PASSWORD,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        } catch (PDOException $e) {
            if (Config::DEBUG_MODE) {
                die("Database connection failed: " . $e->getMessage());
            } else {
                die("A system error has occurred. Please try again later.");
            }
        }
    }
    
    // Helper to safely start session
    private function startSession() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    // List services with optional search/filter
    public function index() {
        $this->startSession();
        
        // Check if user is logged in
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }
        
        $userId = $_SESSION['user']['id'];
        
        // Get search parameters
        $search = $_GET['search'] ?? '';
        $category = $_GET['category'] ?? '';
        
        try {
            // Get service categories for filter dropdown
            $categoryQuery = "SELECT DISTINCT category FROM services 
                             WHERE user_id = ? AND category IS NOT NULL AND category != ''
                             ORDER BY category";
            $stmt = $this->db->prepare($categoryQuery);
            $stmt->execute([$userId]);
            $categories = $stmt->fetchAll();
            
            // Build query based on filters
            if (empty($search) && empty($category)) {
                // No filters
                $query = "SELECT * FROM services WHERE user_id = ? ORDER BY name ASC";
                $stmt = $this->db->prepare($query);
                $stmt->execute([$userId]);
            } else if (!empty($search) && empty($category)) {
                // Search filter only
                $searchTerm = "%{$search}%";
                $query = "SELECT * FROM services 
                         WHERE user_id = ? 
                         AND (name LIKE ? OR description LIKE ? OR category LIKE ?)
                         ORDER BY name ASC";
                $stmt = $this->db->prepare($query);
                $stmt->execute([$userId, $searchTerm, $searchTerm, $searchTerm]);
            } else if (empty($search) && !empty($category)) {
                // Category filter only
                $query = "SELECT * FROM services 
                         WHERE user_id = ? AND category = ?
                         ORDER BY name ASC";
                $stmt = $this->db->prepare($query);
                $stmt->execute([$userId, $category]);
            } else {
                // Both search and category filters
                $searchTerm = "%{$search}%";
                $query = "SELECT * FROM services 
                         WHERE user_id = ? 
                         AND category = ?
                         AND (name LIKE ? OR description LIKE ?)
                         ORDER BY name ASC";
                $stmt = $this->db->prepare($query);
                $stmt->execute([$userId, $category, $searchTerm, $searchTerm]);
            }
            
            $services = $stmt->fetchAll();
            
            // Get service statistics
            $stats = $this->getServiceStatistics($userId);
            
        } catch (PDOException $e) {
            error_log("Error retrieving services: " . $e->getMessage());
            $services = [];
            $categories = [];
            $stats = [
                'total' => 0,
                'categories' => 0,
                'popular' => null,
                'longest' => null
            ];
        }
        
        include __DIR__ . '/../views/services/index.php';
    }
    
    // Display service creation form
    public function create() {
        $this->startSession();
        
        // Check if user is logged in
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }
        
        $userId = $_SESSION['user']['id'];
        
        // Get existing categories for the dropdown
        try {
            $query = "SELECT DISTINCT category FROM services 
                     WHERE user_id = ? AND category IS NOT NULL AND category != ''
                     ORDER BY category";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$userId]);
            $categories = $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error retrieving service categories: " . $e->getMessage());
            $categories = [];
        }
        
        include __DIR__ . '/../views/services/create.php';
    }
    
    // Store new service
    public function store() {
        $this->startSession();
        
        // Check if user is logged in
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }
        
        $userId = $_SESSION['user']['id'];
        
        // Validate input
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $duration = intval($_POST['duration'] ?? 60); // Default 60 minutes
        $price = filter_var($_POST['price'] ?? 0, FILTER_VALIDATE_FLOAT);
        $category = trim($_POST['category'] ?? '');
        $color = trim($_POST['color'] ?? '#3498db'); // Default color
        $errors = [];
        
        // Validation rules
        if (empty($name)) {
            $errors['name'] = 'Service name is required';
        }
        
        if ($duration < 5) {
            $errors['duration'] = 'Duration must be at least 5 minutes';
        }
        
        if ($price < 0) {
            $errors['price'] = 'Price cannot be negative';
        }
        
        // If validation fails, return to form with errors
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $_POST;
            header('Location: /services/create');
            exit;
        }
        
        // Insert service
        try {
            $query = "INSERT INTO services (user_id, name, description, duration, price, category, color, created_at) 
                     VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
                     
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                $userId, 
                $name, 
                $description, 
                $duration, 
                $price, 
                $category,
                $color
            ]);
            
            $serviceId = $this->db->lastInsertId();
            
            $_SESSION['success'] = 'Service created successfully';
            header('Location: /services');
            exit;
            
        } catch (PDOException $e) {
            error_log("Database error creating service: " . $e->getMessage());
            $_SESSION['errors'] = ['db_error' => 'Failed to create service: ' . (Config::DEBUG_MODE ? $e->getMessage() : 'Database error')];
            $_SESSION['old'] = $_POST;
            header('Location: /services/create');
            exit;
        }
    }
    
    // Edit service form
    public function edit($id) {
        $this->startSession();
        
        // Check if user is logged in
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }
        
        $userId = $_SESSION['user']['id'];
        
        try {
            // Get service details
            $query = "SELECT * FROM services WHERE id = ? AND user_id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$id, $userId]);
            $service = $stmt->fetch();
            
            // If service not found or doesn't belong to user
            if (!$service) {
                $_SESSION['error'] = 'Service not found';
                header('Location: /services');
                exit;
            }
            
            // For debugging - you can remove this after confirming data is retrieved
            if (isset($_GET['debug'])) {
                echo "<pre>";
                print_r($service);
                echo "</pre>";
            }
            
            // Get existing categories for the dropdown
            $query = "SELECT DISTINCT category FROM services 
                     WHERE user_id = ? AND category IS NOT NULL AND category != ''
                     ORDER BY category";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$userId]);
            $categories = $stmt->fetchAll();
            
        } catch (PDOException $e) {
            error_log("Error retrieving service: " . $e->getMessage());
            $_SESSION['error'] = 'An error occurred while retrieving the service';
            header('Location: /services');
            exit;
        }
        
        // Make sure we're passing the service data to the view
        include __DIR__ . '/../views/services/edit.php';
    }

/**
 * Create a service via AJAX and return JSON response
 */
public function ajaxCreate() {
    $this->startSession();
    
    if (!isset($_SESSION['user'])) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        return;
    }
    
    // Set JSON content type
    header('Content-Type: application/json');
    
    $userId = $_SESSION['user']['id'];
    
    // Get input data
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input && !empty($_POST)) {
        $input = $_POST;
    }
    
    error_log("Service AJAX Create - Input: " . print_r($input, true));
    
    // Validate inputs
    $name = trim($input['name'] ?? '');
    $duration = (int)($input['duration'] ?? 0);
    $price = (float)($input['price'] ?? 0);
    $color = trim($input['color'] ?? '#3498db');
    
    if (empty($name)) {
        echo json_encode(['success' => false, 'message' => 'Name is required']);
        return;
    }
    
    if ($duration < 5) {
        echo json_encode(['success' => false, 'message' => 'Duration must be at least 5 minutes']);
        return;
    }
    
    try {
        // Insert new service
        $query = "INSERT INTO services (user_id, name, duration, price, color, created_at) 
                 VALUES (?, ?, ?, ?, ?, NOW())";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$userId, $name, $duration, $price, $color]);
        
        // Get the new service ID
        $serviceId = $this->db->lastInsertId();
        
        error_log("Service created with ID: $serviceId");
        
        // Return success with service data
        echo json_encode([
            'success' => true, 
            'message' => 'Service created successfully',
            'service' => [
                'id' => $serviceId,
                'name' => $name,
                'duration' => $duration,
                'price' => $price,
                'color' => $color
            ]
        ]);
        
    } catch (PDOException $e) {
        error_log("Error creating service via AJAX: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Database error occurred: ' . $e->getMessage()]);
    }
}

// Add this method to your ServiceController class

public function quickAdd() {
    $this->startSession();
    
    // Check if user is logged in and request is AJAX
    if (!isset($_SESSION['user'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Unauthorized']);
        exit;
    }
    
    $userId = $_SESSION['user']['id'];
    
    // Validate input
    if (!isset($_POST['name']) || empty($_POST['name'])) {
        echo json_encode(['success' => false, 'error' => 'Service name is required']);
        exit;
    }
    
    if (!isset($_POST['duration']) || !is_numeric($_POST['duration']) || $_POST['duration'] < 5) {
        echo json_encode(['success' => false, 'error' => 'Valid duration is required']);
        exit;
    }
    
    try {
        $stmt = $this->db->prepare("
            INSERT INTO services (user_id, name, duration, price, color, created_at)
            VALUES (?, ?, ?, ?, ?, NOW())
        ");
        
        $stmt->execute([
            $userId,
            $_POST['name'],
            $_POST['duration'],
            $_POST['price'] ?? null,
            $_POST['color'] ?? '#4e73df'
        ]);
        
        $serviceId = $this->db->lastInsertId();
        
        // Return success with new service data
        echo json_encode([
            'success' => true,
            'service' => [
                'id' => $serviceId,
                'name' => $_POST['name'],
                'duration' => $_POST['duration'],
                'price' => $_POST['price'] ?? null,
                'color' => $_POST['color'] ?? '#4e73df'
            ]
        ]);
        
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
    }
}
    
    // Update service method
    
    public function update($id) {
        $this->startSession();
        
        // Check if user is logged in
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }
        
        $userId = $_SESSION['user']['id'];
        
        // Validate input
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $duration = intval($_POST['duration'] ?? 60);
        $price = filter_var($_POST['price'] ?? 0, FILTER_VALIDATE_FLOAT);
        $category = trim($_POST['category'] ?? '');
        $color = trim($_POST['color'] ?? '#3498db');
        $errors = [];
        
        // Validation rules
        if (empty($name)) {
            $errors['name'] = 'Service name is required';
        }
        
        if ($duration < 5) {
            $errors['duration'] = 'Duration must be at least 5 minutes';
        }
        
        if ($price < 0) {
            $errors['price'] = 'Price cannot be negative';
        }
        
        // If validation fails, return to form with errors
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $_POST;
            header("Location: /services/edit/{$id}");
            exit;
        }
        
        try {
            // First check if service exists and belongs to user
            $checkQuery = "SELECT id FROM services WHERE id = ? AND user_id = ?";
            $checkStmt = $this->db->prepare($checkQuery);
            $checkStmt->execute([$id, $userId]);
            
            if (!$checkStmt->fetch()) {
                $_SESSION['error'] = 'Service not found';
                header('Location: /services');
                exit;
            }
            
            // Simplify the update query - remove updated_at if causing issues
            $query = "UPDATE services 
                     SET name = ?, 
                         description = ?, 
                         duration = ?, 
                         price = ?, 
                         category = ?, 
                         color = ?
                     WHERE id = ? AND user_id = ?";
                     
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                $name,
                $description,
                $duration,
                $price,
                $category,
                $color,
                $id,
                $userId
            ]);
            
            $_SESSION['success'] = 'Service updated successfully';
            header('Location: /services');
            exit;
            
        } catch (PDOException $e) {
            // Enhanced error logging
            error_log("Failed to update service: " . $e->getMessage() . " Query: " . ($stmt->queryString ?? 'unknown'));
            $_SESSION['errors'] = ['db_error' => 'Database error: ' . ($e->getMessage())];
            $_SESSION['old'] = $_POST;
            header("Location: /services/edit/{$id}");
            exit;
        }
    }
    
    // Delete service
    public function delete($id) {
        $this->startSession();
        
        // Check if user is logged in
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }
        
        $userId = $_SESSION['user']['id'];
        
        try {
            // Check if service has appointments
            $checkAppointmentsQuery = "SELECT COUNT(*) as count FROM appointments 
                                     WHERE service_id = ? AND user_id = ?";
            $checkStmt = $this->db->prepare($checkAppointmentsQuery);
            $checkStmt->execute([$id, $userId]);
            $appointmentCount = $checkStmt->fetch()['count'];
            
            if ($appointmentCount > 0) {
                $_SESSION['error'] = 'This service cannot be deleted because it has ' . 
                                    $appointmentCount . ' appointment(s) associated with it.';
                header('Location: /services');
                exit;
            }
            
            // Delete service
            $query = "DELETE FROM services WHERE id = ? AND user_id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$id, $userId]);
            
            // Check if any service was actually deleted
            if ($stmt->rowCount() === 0) {
                $_SESSION['error'] = 'Service not found';
                header('Location: /services');
                exit;
            }
            
            $_SESSION['success'] = 'Service deleted successfully';
            header('Location: /services');
            exit;
            
        } catch (PDOException $e) {
            error_log("Failed to delete service: " . $e->getMessage());
            $_SESSION['error'] = 'Failed to delete service. Please try again.';
            header('Location: /services');
            exit;
        }
    }
    
    // Get service statistics
    private function getServiceStatistics($userId) {
        try {
            // Total services
            $query = "SELECT COUNT(*) as total FROM services WHERE user_id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$userId]);
            $total = $stmt->fetch()['total'];
            
            // Number of categories
            $query = "SELECT COUNT(DISTINCT category) as categories 
                     FROM services 
                     WHERE user_id = ? AND category IS NOT NULL AND category != ''";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$userId]);
            $categories = $stmt->fetch()['categories'];
            
            // Most popular service (by appointments)
            $query = "SELECT s.id, s.name, COUNT(a.id) as appointment_count 
                     FROM services s
                     JOIN appointments a ON s.id = a.service_id
                     WHERE s.user_id = ?
                     GROUP BY s.id, s.name
                     ORDER BY appointment_count DESC, s.name ASC
                     LIMIT 1";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$userId]);
            $popular = $stmt->fetch();
            
            // Service with longest duration
            $query = "SELECT id, name, duration 
                     FROM services 
                     WHERE user_id = ?
                     ORDER BY duration DESC, name ASC
                     LIMIT 1";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$userId]);
            $longest = $stmt->fetch();
            
            return [
                'total' => $total,
                'categories' => $categories,
                'popular' => $popular,
                'longest' => $longest
            ];
        } catch (PDOException $e) {
            error_log("Error getting service statistics: " . $e->getMessage());
            return [
                'total' => 0,
                'categories' => 0,
                'popular' => null,
                'longest' => null
            ];
        }
    }
}