<?php
/* WebSchedulr Client Controller */

class ClientController {
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

    // List clients with optional search/filter - FIXED VERSION
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
        $page = max(1, intval($_GET['page'] ?? 1));
        $perPage = 10;
        $offset = ($page - 1) * $perPage;
        
        try {
            // Get total count for pagination - SIMPLIFIED APPROACH
            if (empty($search)) {
                // No search terms - simple count
                $countQuery = "SELECT COUNT(*) as total FROM clients WHERE user_id = ?";
                $stmt = $this->db->prepare($countQuery);
                $stmt->execute([$userId]);
            } else {
                // With search term
                $searchTerm = "%{$search}%";
                $countQuery = "SELECT COUNT(*) as total FROM clients 
                              WHERE user_id = ? 
                              AND (name LIKE ? OR email LIKE ? OR phone LIKE ?)";
                $stmt = $this->db->prepare($countQuery);
                $stmt->execute([$userId, $searchTerm, $searchTerm, $searchTerm]);
            }
            $total = $stmt->fetch()['total'];
            
            // Get clients for current page - SIMPLIFIED APPROACH
            if (empty($search)) {
                // No search terms
                $query = "SELECT * FROM clients 
                         WHERE user_id = ? 
                         ORDER BY name ASC 
                         LIMIT ?, ?";
                $stmt = $this->db->prepare($query);
                $stmt->execute([$userId, $offset, $perPage]);
            } else {
                // With search term
                $searchTerm = "%{$search}%";
                $query = "SELECT * FROM clients 
                         WHERE user_id = ? 
                         AND (name LIKE ? OR email LIKE ? OR phone LIKE ?) 
                         ORDER BY name ASC 
                         LIMIT ?, ?";
                $stmt = $this->db->prepare($query);
                $stmt->execute([$userId, $searchTerm, $searchTerm, $searchTerm, $offset, $perPage]);
            }
            $clients = $stmt->fetchAll();
            
            // Calculate pagination values
            $totalPages = ceil($total / $perPage);
            $pagination = [
                'current' => $page,
                'total' => $totalPages,
                'hasNext' => $page < $totalPages,
                'hasPrev' => $page > 1,
                'nextPage' => $page + 1,
                'prevPage' => $page - 1,
                'total_records' => $total
            ];
    
            // Get client statistics
            $stats = $this->getClientStatistics($userId);
            
        } catch (PDOException $e) {
            // Log error and provide empty results
            error_log("Error in client search: " . $e->getMessage());
            $clients = [];
            $pagination = [
                'current' => 1,
                'total' => 1,
                'hasNext' => false,
                'hasPrev' => false,
                'nextPage' => 1,
                'prevPage' => 1,
                'total_records' => 0
            ];
            $stats = [
                'total' => 0,
                'recent' => 0,
                'with_appointments' => 0,
                'active' => 0
            ];
        }
        
        include __DIR__ . '/../views/clients/index.php';
    }
    
    // Display client creation form
    public function create() {
        $this->startSession();
        
        // Check if user is logged in
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }
        
        include __DIR__ . '/../views/clients/create.php';
    }

    /**
 * Create a client via AJAX and return JSON response
 */
public function ajaxCreate() {
    $this->startSession();
    
    // Debug log
    error_log("ajaxCreate method called");
    
    if (!isset($_SESSION['user'])) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        return;
    }
    
    // Set JSON content type
    header('Content-Type: application/json');
    
    $userId = $_SESSION['user']['id'];
    
    // Get input data - handle both JSON and form data
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input && !empty($_POST)) {
        $input = $_POST;
    }
    
    error_log("Input data: " . print_r($input, true));
    
    // Validate inputs
    $name = trim($input['name'] ?? '');
    $email = trim($input['email'] ?? '');
    $phone = trim($input['phone'] ?? '');
    $notes = trim($input['notes'] ?? '');
    
    // Name is required
    if (empty($name)) {
        echo json_encode(['success' => false, 'message' => 'Name is required']);
        return;
    }
    
    try {
        // Check for duplicate email if provided
        if (!empty($email)) {
            $stmt = $this->db->prepare("SELECT id FROM clients WHERE email = ? AND user_id = ?");
            $stmt->execute([$email, $userId]);
            
            if ($stmt->fetch()) {
                echo json_encode(['success' => false, 'message' => 'A client with this email already exists']);
                return;
            }
        }
        
        // Insert new client
        $query = "INSERT INTO clients (user_id, name, email, phone, notes, created_at) VALUES (?, ?, ?, ?, ?, NOW())";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$userId, $name, $email, $phone, $notes]);
        
        // Get the new client ID
        $clientId = $this->db->lastInsertId();
        
        // Return success with client data
        echo json_encode([
            'success' => true, 
            'message' => 'Client created successfully',
            'client' => [
                'id' => $clientId,
                'name' => $name,
                'email' => $email,
                'phone' => $phone
            ]
        ]);
        
        error_log("Client created with ID: $clientId");
        
    } catch (PDOException $e) {
        error_log("Error creating client via AJAX: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Database error occurred: ' . $e->getMessage()]);
    }
}
    
    // Store new client
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
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $notes = trim($_POST['notes'] ?? '');
        $errors = [];
        
        // Validation rules
        if (empty($name)) {
            $errors['name'] = 'Client name is required';
        }
        
        if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Invalid email format';
        }
        
        // If validation fails, return to form with errors
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $_POST;
            header('Location: /clients/create');
            exit;
        }
        
        // Insert client
        try {
            // Dump values for debugging (comment out in production)
            if (Config::DEBUG_MODE) {
                error_log("Trying to insert client with values: " . 
                    "userId=$userId, name=$name, email=$email, phone=$phone");
            }
            
            $query = "INSERT INTO clients (user_id, name, email, phone, address, notes, created_at) 
                     VALUES (:userId, :name, :email, :phone, :address, :notes, NOW())";
                     
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':userId', $userId);
            $stmt->bindValue(':name', $name);
            $stmt->bindValue(':email', $email);
            $stmt->bindValue(':phone', $phone);
            $stmt->bindValue(':address', $address);
            $stmt->bindValue(':notes', $notes);
            $stmt->execute();
            
            $clientId = $this->db->lastInsertId();
            
            $_SESSION['success'] = 'Client created successfully';
            header('Location: /clients');
            exit;
            
        } catch (PDOException $e) {
            // Log the detailed error
            error_log("Database error creating client: " . $e->getMessage());
            
            // Check for specific error conditions
            $errorCode = $e->getCode();
            
            // Provide more specific error messages based on error code
            if ($errorCode == '23000' && strpos($e->getMessage(), 'Duplicate entry') !== false) {
                $_SESSION['errors'] = ['email' => 'A client with this email already exists.'];
            } else if ($errorCode == '42S02') {
                $_SESSION['errors'] = ['db_error' => 'The clients table does not exist. Database setup may be incomplete.'];
            } else {
                $_SESSION['errors'] = ['db_error' => 'Failed to create client: ' . (Config::DEBUG_MODE ? $e->getMessage() : 'Database error')];
            }
            
            $_SESSION['old'] = $_POST;
            header('Location: /clients/create');
            exit;
        }
    }
    
    // Show client details
    public function show($id) {
        $this->startSession();
        
        // Check if user is logged in
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }
        
        $userId = $_SESSION['user']['id'];
        
        // Get client details
        $query = "SELECT * FROM clients WHERE id = :id AND user_id = :userId";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':userId', $userId);
        $stmt->execute();
        $client = $stmt->fetch();
        
        // If client not found or doesn't belong to user
        if (!$client) {
            $_SESSION['error'] = 'Client not found';
            header('Location: /clients');
            exit;
        }
        
        // Get client's appointments
        $query = "SELECT a.*, s.name as service_name, s.color 
                 FROM appointments a 
                 JOIN services s ON a.service_id = s.id
                 WHERE a.client_id = :clientId AND a.user_id = :userId
                 ORDER BY a.start_time DESC";
                 
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':clientId', $id, PDO::PARAM_INT);
        $stmt->bindValue(':userId', $userId);
        $stmt->execute();
        $appointments = $stmt->fetchAll();
        
        include __DIR__ . '/../views/clients/show.php';
    }
    
    // Display client edit form
    public function edit($id) {
        $this->startSession();
        
        // Check if user is logged in
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }
        
        $userId = $_SESSION['user']['id'];
        
        // Get client details
        $query = "SELECT * FROM clients WHERE id = :id AND user_id = :userId";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':userId', $userId);
        $stmt->execute();
        $client = $stmt->fetch();
        
        // If client not found or doesn't belong to user
        if (!$client) {
            $_SESSION['error'] = 'Client not found';
            header('Location: /clients');
            exit;
        }
        
        include __DIR__ . '/../views/clients/edit.php';
    }
    
    // Update client
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
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $notes = trim($_POST['notes'] ?? '');
        $errors = [];
        
        // Validation rules
        if (empty($name)) {
            $errors['name'] = 'Client name is required';
        }
        
        if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Invalid email format';
        }
        
        // If validation fails, return to form with errors
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $_POST;
            header("Location: /clients/edit/{$id}");
            exit;
        }
        
        try {
            // Check if client exists and belongs to user
            $checkQuery = "SELECT id FROM clients WHERE id = :id AND user_id = :userId";
            $checkStmt = $this->db->prepare($checkQuery);
            $checkStmt->bindValue(':id', $id, PDO::PARAM_INT);
            $checkStmt->bindValue(':userId', $userId);
            $checkStmt->execute();
            
            if (!$checkStmt->fetch()) {
                $_SESSION['error'] = 'Client not found';
                header('Location: /clients');
                exit;
            }
            
            // Update client
            $query = "UPDATE clients 
                     SET name = :name, email = :email, phone = :phone, address = :address, 
                         notes = :notes, updated_at = NOW()
                     WHERE id = :id AND user_id = :userId";
                     
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':name', $name);
            $stmt->bindValue(':email', $email);
            $stmt->bindValue(':phone', $phone);
            $stmt->bindValue(':address', $address);
            $stmt->bindValue(':notes', $notes);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->bindValue(':userId', $userId);
            $stmt->execute();
            
            $_SESSION['success'] = 'Client updated successfully';
            header("Location: /clients/show/{$id}");
            exit;
            
        } catch (PDOException $e) {
            $_SESSION['errors'] = ['db_error' => 'Failed to update client: ' . (Config::DEBUG_MODE ? $e->getMessage() : 'Database error')];
            $_SESSION['old'] = $_POST;
            error_log("Failed to update client: " . $e->getMessage());
            header("Location: /clients/edit/{$id}");
            exit;
        }
    }
    
    // Delete client
    public function delete($id) {
        $this->startSession();
        
        // Check if user is logged in
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }
        
        $userId = $_SESSION['user']['id'];
        
        try {
            // Start transaction to handle deleting appointments first then client
            $this->db->beginTransaction();
            
            // Delete related appointments first (respecting foreign key constraints)
            $query = "DELETE FROM appointments WHERE client_id = :clientId AND user_id = :userId";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':clientId', $id, PDO::PARAM_INT);
            $stmt->bindValue(':userId', $userId);
            $stmt->execute();
            
            // Delete client
            $query = "DELETE FROM clients WHERE id = :id AND user_id = :userId";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->bindValue(':userId', $userId);
            $stmt->execute();
            
            // Check if any client was actually deleted
            if ($stmt->rowCount() === 0) {
                $this->db->rollBack();
                $_SESSION['error'] = 'Client not found';
                header('Location: /clients');
                exit;
            }
            
            // Commit transaction
            $this->db->commit();
            
            $_SESSION['success'] = 'Client deleted successfully';
            header('Location: /clients');
            exit;
            
        } catch (PDOException $e) {
            $this->db->rollBack();
            $_SESSION['error'] = 'Failed to delete client. Please try again.';
            error_log("Failed to delete client: " . $e->getMessage());
            header('Location: /clients');
            exit;
        }
    }
    
    // Get client statistics
    private function getClientStatistics($userId) {
        try {
            // Total clients
            $query = "SELECT COUNT(*) as total FROM clients WHERE user_id = :userId";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':userId', $userId);
            $stmt->execute();
            $total = $stmt->fetch()['total'];
            
            // Recent clients (added in last 30 days)
            $query = "SELECT COUNT(*) as recent FROM clients WHERE user_id = :userId AND created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':userId', $userId);
            $stmt->execute();
            $recent = $stmt->fetch()['recent'];
            
            // Clients with appointments
            $query = "SELECT COUNT(DISTINCT c.id) as with_appointments 
                     FROM clients c
                     JOIN appointments a ON c.id = a.client_id
                     WHERE c.user_id = :userId";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':userId', $userId);
            $stmt->execute();
            $withAppointments = $stmt->fetch()['with_appointments'];
            
            // Recent activity (appointments in last 30 days)
            $query = "SELECT COUNT(DISTINCT c.id) as active 
                     FROM clients c
                     JOIN appointments a ON c.id = a.client_id
                     WHERE c.user_id = :userId 
                     AND a.start_time >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':userId', $userId);
            $stmt->execute();
            $active = $stmt->fetch()['active'];
            
            return [
                'total' => $total,
                'recent' => $recent,
                'with_appointments' => $withAppointments,
                'active' => $active
            ];
        } catch (PDOException $e) {
            error_log("Error getting client statistics: " . $e->getMessage());
            return [
                'total' => 0,
                'recent' => 0,
                'with_appointments' => 0,
                'active' => 0
            ];
        }
    }
}