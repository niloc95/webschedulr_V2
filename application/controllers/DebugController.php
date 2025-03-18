<?php
class DebugController {
    private $db = null;
    private $dbSuccess = false;
    private $errors = [];
    private $messages = [];
    
    public function __construct() {
        $this->logMessage("DebugController initialized");
        
        // First, check if session is working
        $this->startSession();
        $this->logMessage("Session started. Session ID: " . session_id());
        
        // Try to load both config files
        $rootConfigExists = file_exists(__DIR__ . '/../../config.php');
        $appConfigExists = file_exists(__DIR__ . '/../config/config.php');
        
        $this->logMessage("Root config.php exists: " . ($rootConfigExists ? 'Yes' : 'No'));
        $this->logMessage("App config/config.php exists: " . ($appConfigExists ? 'Yes' : 'No'));
        
        // Load the main config file
        if ($rootConfigExists) {
            require_once __DIR__ . '/../../config.php';
            $this->logMessage("Root config loaded. DB: " . Config::DB_HOST . "/" . Config::DB_NAME);
        } else {
            $this->logError("Root config file not found!");
        }
        
        // Try DB connection
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
            $this->dbSuccess = true;
            $this->logMessage("Database connection successful");
        } catch (PDOException $e) {
            $this->logError("Database connection failed: " . $e->getMessage());
        }
    }
    
    private function logMessage($msg) {
        $this->messages[] = $msg;
    }
    
    private function logError($msg) {
        $this->errors[] = $msg;
        $this->logMessage("ERROR: " . $msg);
    }
    
    private function startSession() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    public function testDashboard() {
        $this->logMessage("Running dashboard tests");
        
        // Check user authentication
        if (!isset($_SESSION['user'])) {
            $this->logError("No user in session. Using test user ID 1");
            $userId = 1;
        } else {
            $userId = $_SESSION['user']['id'];
            $this->logMessage("User found in session: ID " . $userId . ", Name: " . $_SESSION['user']['name']);
        }
        
        $today = date('Y-m-d');
        $this->logMessage("Today's date: " . $today);
        
        // Test database tables existence
        $this->testTable('users');
        $this->testTable('clients');
        $this->testTable('services');
        $this->testTable('appointments');
        
        // Test data retrieval functions
        $stats = [];
        $upcomingAppointments = [];
        $appointmentsByDay = [];
        $recentActivities = [];
        
        if ($this->dbSuccess) {
            try {
                $stats['total_appointments'] = $this->getTotalAppointments($userId);
                $this->logMessage("Total appointments: " . $stats['total_appointments']);
                
                $stats['upcoming_appointments'] = $this->getUpcomingAppointmentsCount($userId, $today);
                $this->logMessage("Upcoming appointments: " . $stats['upcoming_appointments']);
                
                $stats['total_clients'] = $this->getTotalClients($userId);
                $this->logMessage("Total clients: " . $stats['total_clients']);
                
                $stats['total_services'] = $this->getTotalServices($userId);
                $this->logMessage("Total services: " . $stats['total_services']);
                
                $upcomingAppointments = $this->getUpcomingAppointments($userId, $today, 7);
                $this->logMessage("Upcoming appointments data: " . count($upcomingAppointments) . " records");
                
                $appointmentsByDay = $this->getAppointmentDistribution($userId);
                $this->logMessage("Appointments by day loaded");
                
                $recentActivities = $this->getRecentActivities($userId, 10);
                $this->logMessage("Recent activities: " . count($recentActivities) . " records");
            } catch (Exception $e) {
                $this->logError("Error retrieving dashboard data: " . $e->getMessage());
            }
        }
        
        // Calculate calendar data
        $calendarData = $this->getCalendarData();
        $this->logMessage("Calendar data generated for " . $calendarData['month']);
        
        // Render debug view
        include __DIR__ . '/../views/debug/dashboard.php';
    }
    
    // Test if a table exists and has records
    private function testTable($tableName) {
        if (!$this->dbSuccess) return;
        
        try {
            // Check if table exists
            $stmt = $this->db->query("SHOW TABLES LIKE '$tableName'");
            $tableExists = $stmt->rowCount() > 0;
            
            if ($tableExists) {
                $this->logMessage("Table '$tableName' exists");
                
                // Check for records
                $stmt = $this->db->query("SELECT COUNT(*) as count FROM $tableName");
                $count = $stmt->fetch()['count'];
                $this->logMessage("Table '$tableName' has $count records");
            } else {
                $this->logError("Table '$tableName' does not exist");
            }
        } catch (Exception $e) {
            $this->logError("Error checking table '$tableName': " . $e->getMessage());
        }
    }
    
    // Calendar data calculation (no DB dependency)
    private function getCalendarData() {
        $firstDay = mktime(0, 0, 0, date('n'), 1, date('Y'));
        $numDays = date('t');
        $firstDayOfWeek = date('w', $firstDay);
        
        return [
            'month' => date('F Y'),
            'firstDay' => $firstDay,
            'numDays' => $numDays,
            'firstDayOfWeek' => $firstDayOfWeek
        ];
    }
    
    // Get total appointments for a user
    private function getTotalAppointments($userId) {
        if (!$this->dbSuccess) return 0;
        
        $query = "SELECT COUNT(*) as count FROM appointments WHERE user_id = :userId";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch()['count'];
    }
    
    // Get count of upcoming appointments
    private function getUpcomingAppointmentsCount($userId, $today) {
        if (!$this->dbSuccess) return 0;
        
        $query = "SELECT COUNT(*) as count FROM appointments 
                 WHERE user_id = :userId 
                 AND start_time >= :today
                 AND status != 'cancelled'";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':today', $today);
        $stmt->execute();
        
        return $stmt->fetch()['count'];
    }
    
    // Get total clients
    private function getTotalClients($userId) {
        if (!$this->dbSuccess) return 0;
        
        $query = "SELECT COUNT(*) as count FROM clients WHERE user_id = :userId";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch()['count'];
    }
    
    // Get total services
    private function getTotalServices($userId) {
        if (!$this->dbSuccess) return 0;
        
        $query = "SELECT COUNT(*) as count FROM services WHERE user_id = :userId";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch()['count'];
    }
    
    // Get upcoming appointments
    private function getUpcomingAppointments($userId, $today, $days = 7) {
        if (!$this->dbSuccess) return [];
        
        $endDate = date('Y-m-d', strtotime($today . ' + ' . $days . ' days'));
        
        $query = "SELECT a.*, c.name as client_name, c.email as client_email, s.name as service_name, s.duration
                 FROM appointments a
                 JOIN clients c ON a.client_id = c.id
                 JOIN services s ON a.service_id = s.id
                 WHERE a.user_id = :userId
                 AND DATE(a.start_time) BETWEEN :today AND :endDate
                 AND a.status != 'cancelled'
                 ORDER BY a.start_time ASC";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':today', $today);
        $stmt->bindValue(':endDate', $endDate);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    // Get appointment distribution by day of week
    private function getAppointmentDistribution($userId) {
        if (!$this->dbSuccess) return [
            'labels' => ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
            'data' => [0, 0, 0, 0, 0, 0, 0]
        ];
        
        $query = "SELECT 
                   DAYOFWEEK(start_time) as day_of_week,
                   COUNT(*) as count
                 FROM appointments
                 WHERE user_id = :userId
                 AND start_time >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                 GROUP BY DAYOFWEEK(start_time)
                 ORDER BY day_of_week";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        
        $results = $stmt->fetchAll();
        
        // Initialize all days with 0 count
        $days = [
            1 => 'Sunday',
            2 => 'Monday',
            3 => 'Tuesday',
            4 => 'Wednesday',
            5 => 'Thursday',
            6 => 'Friday',
            7 => 'Saturday'
        ];
        
        $distribution = array_fill_keys(array_keys($days), 0);
        
        // Fill in the actual counts
        foreach ($results as $row) {
            $distribution[$row['day_of_week']] = (int)$row['count'];
        }
        
        return [
            'labels' => array_values($days),
            'data' => array_values($distribution)
        ];
    }
    
    // Get recent activities
    private function getRecentActivities($userId, $limit = 10) {
        if (!$this->dbSuccess) return [];
        
        $query = "SELECT 
                   a.id, 
                   a.start_time,
                   a.status,
                   a.created_at,
                   c.name as client_name,
                   s.name as service_name,
                   'appointment' as type
                 FROM appointments a
                 JOIN clients c ON a.client_id = c.id
                 JOIN services s ON a.service_id = s.id
                 WHERE a.user_id = :userId
                 ORDER BY a.created_at DESC
                 LIMIT :limit";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
}