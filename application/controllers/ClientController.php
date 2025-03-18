<?php
/* WebSchedulr Client Controller */
require_once __DIR__ . '/BaseController.php';

class ClientController extends BaseController {
    
    public function __construct() {
        // Call parent constructor to set up database connection
        parent::__construct();
        
        // Any ClientController specific initialization can go here
    }
    
    // List clients with optional search/filter - FIXED VERSION
    public function index() {
        $this->startSession();
        
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
            
            // Get client statistics - THIS LINE WAS MISSING
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
        
        // Include the view
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


// Add this method to your ClientController class

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
        echo json_encode(['success' => false, 'error' => 'Client name is required']);
        exit;
    }
    
    try {
        $stmt = $this->db->prepare("
            INSERT INTO clients (user_id, name, email, phone, created_at)
            VALUES (?, ?, ?, ?, NOW())
        ");
        
        $stmt->execute([
            $userId,
            $_POST['name'],
            $_POST['email'] ?? null,
            $_POST['phone'] ?? null
        ]);
        
        $clientId = $this->db->lastInsertId();
        
        // Return success with new client data
        echo json_encode([
            'success' => true,
            'client' => [
                'id' => $clientId,
                'name' => $_POST['name'],
                'email' => $_POST['email'] ?? null,
                'phone' => $_POST['phone'] ?? null
            ]
        ]);
        
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
    }
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

    public function profile($id) {
        $this->startSession();
        
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }
        
        $userId = $_SESSION['user']['id'];
        
        try {
            // Get client details
            $stmt = $this->db->prepare("
                SELECT * FROM clients 
                WHERE id = ? AND user_id = ?
            ");
            $stmt->execute([$id, $userId]);
            $client = $stmt->fetch();
            
            if (!$client) {
                $_SESSION['error'] = 'Client not found';
                header('Location: /clients');
                exit;
            }
            
            // Get client's appointments with service details
            $appointmentsQuery = "
                SELECT a.*, s.name as service_name, s.color
                FROM appointments a
                JOIN services s ON a.service_id = s.id
                WHERE a.client_id = ? AND a.user_id = ?
                ORDER BY a.start_time DESC
            ";
            $appointmentsStmt = $this->db->prepare($appointmentsQuery);
            $appointmentsStmt->execute([$id, $userId]);
            $appointments = $appointmentsStmt->fetchAll();
            
            // Get client metrics
            $metricsQuery = "
                SELECT 
                    COUNT(*) as total_appointments,
                    SUM(s.price) as total_spent,
                    MIN(a.start_time) as first_visit,
                    MAX(a.start_time) as last_visit
                FROM appointments a
                JOIN services s ON a.service_id = s.id
                WHERE a.client_id = ? AND a.user_id = ?
            ";
            $metricsStmt = $this->db->prepare($metricsQuery);
            $metricsStmt->execute([$id, $userId]);
            $metrics = $metricsStmt->fetch();
            
            include __DIR__ . '/../views/clients/profile.php';
            
        } catch (PDOException $e) {
            error_log("Error in client profile: " . $e->getMessage());
            $_SESSION['error'] = 'Database error occurred';
            header('Location: /clients');
            exit;
        }
    }

    // Simple direct query function for debugging
    public function debug() {
        $this->startSession();
        
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }
        
        $userId = $_SESSION['user']['id'];
        
        try {
            // Direct, simple query
            $stmt = $this->db->prepare("SELECT * FROM clients WHERE user_id = ?");
            $stmt->execute([$userId]);
            $clients = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo "<h2>Debug Client List</h2>";
            
            if (count($clients) === 0) {
                echo "<p>No clients found. Adding sample client...</p>";
                
                // Add a sample client
                $this->db->prepare("
                    INSERT INTO clients (user_id, name, email, phone, created_at) 
                    VALUES (?, 'Debug Client', 'debug@example.com', '555-1234', NOW())
                ")->execute([$userId]);
                
                echo "<p>Sample client added. <a href='/clients/debug'>Refresh</a></p>";
            } else {
                echo "<table border='1'>
                    <tr><th>ID</th><th>Name</th><th>Email</th><th>Phone</th></tr>";
                
                foreach ($clients as $client) {
                    echo "<tr>
                        <td>{$client['id']}</td>
                        <td>{$client['name']}</td>
                        <td>{$client['email']}</td>
                        <td>{$client['phone']}</td>
                    </tr>";
                }
                
                echo "</table>";
            }
            
        } catch (PDOException $e) {
            echo "<h2>Database Error</h2>";
            echo "<p>Error: " . $e->getMessage() . "</p>";
        }
        
        exit;
    }
}