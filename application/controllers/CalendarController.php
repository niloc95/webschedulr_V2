<?php

class CalendarController {
    private $db;
    
    public function __construct() {
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
            die("Database connection failed: " . $e->getMessage());
        }
    }
    
    // Helper method to safely start a session
    private function startSession() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    // Calendar monthly view
    public function index() {
        $this->startSession();
        
        // Check if user is logged in
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }
        
        $userId = $_SESSION['user']['id'];
        
        // Get month and year from query params or use current month/year
        $month = isset($_GET['month']) ? intval($_GET['month']) : intval(date('n'));
        $year = isset($_GET['year']) ? intval($_GET['year']) : intval(date('Y'));
        
        // Make sure month is valid
        if ($month < 1 || $month > 12) {
            $month = date('n');
        }
        
        // Calculate first and last day of month
        $firstDayOfMonth = mktime(0, 0, 0, $month, 1, $year);
        $numDays = date('t', $firstDayOfMonth);
        $lastDayOfMonth = mktime(0, 0, 0, $month, $numDays, $year);
        
        // Get first day of the month (0 = Sunday, 1 = Monday, etc)
        $firstDayOfWeek = date('w', $firstDayOfMonth);
        
        // Get appointments for this month
        $startDate = date('Y-m-d', $firstDayOfMonth);
        $endDate = date('Y-m-d', $lastDayOfMonth);
        $appointments = $this->getAppointmentsByDateRange($userId, $startDate, $endDate);
        
        // Group appointments by date
        $appointmentsByDate = [];
        foreach ($appointments as $appointment) {
            $date = date('Y-m-d', strtotime($appointment['start_time']));
            if (!isset($appointmentsByDate[$date])) {
                $appointmentsByDate[$date] = [];
            }
            $appointmentsByDate[$date][] = $appointment;
        }
        
        // Include the view
        include __DIR__ . '/../views/calendar/month.php';
    }
    
    // Calendar day view
    public function day() {
        $this->startSession();
        
        // Check if user is logged in
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }
        
        $userId = $_SESSION['user']['id'];
        
        // Get date from query params or use current date
        $date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
        
        // Validate date format
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            $date = date('Y-m-d');
        }
        
        // Get appointments for this day
        $appointments = $this->getAppointmentsByDate($userId, $date);
        
        // Get services for dropdown in appointment form
        $services = $this->getServices($userId);
        
        // Get clients for dropdown in appointment form
        $clients = $this->getClients($userId);
        
        // Include the view
        include __DIR__ . '/../views/calendar/day.php';
    }
    
    // Create appointment form/handler
    public function createAppointment() {
        $this->startSession();
        
        // Check if user is logged in
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }
        
        $userId = $_SESSION['user']['id'];
        $errors = [];
        
        // Get services and clients for form
        $services = $this->getServices($userId);
        $clients = $this->getClients($userId);
        
        // Handle form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $clientId = isset($_POST['client_id']) ? intval($_POST['client_id']) : 0;
            $serviceId = isset($_POST['service_id']) ? intval($_POST['service_id']) : 0;
            $date = isset($_POST['date']) ? $_POST['date'] : '';
            $time = isset($_POST['time']) ? $_POST['time'] : '';
            $notes = isset($_POST['notes']) ? $_POST['notes'] : '';
            
            // Validate form data
            if ($clientId <= 0) {
                $errors[] = "Please select a client";
            }
            
            if ($serviceId <= 0) {
                $errors[] = "Please select a service";
            }
            
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
                $errors[] = "Invalid date format";
            }
            
            if (!preg_match('/^\d{2}:\d{2}$/', $time)) {
                $errors[] = "Invalid time format";
            }
            
            if (empty($errors)) {
                // Get service duration to calculate end time
                $service = $this->getService($serviceId);
                $duration = $service ? intval($service['duration']) : 60; // Default to 60 min
                
                $startDateTime = $date . ' ' . $time . ':00';
                $endDateTime = date('Y-m-d H:i:s', strtotime($startDateTime . ' +' . $duration . ' minutes'));
                
                // Check if time slot is available
                if ($this->isTimeSlotAvailable($userId, $startDateTime, $endDateTime)) {
                    // Create appointment
                    $result = $this->createAppointmentInDb(
                        $userId, 
                        $clientId, 
                        $serviceId, 
                        $startDateTime, 
                        $endDateTime, 
                        $notes
                    );
                    
                    if ($result) {
                        // Redirect to day view
                        header('Location: /calendar/day?date=' . $date . '&success=1');
                        exit;
                    } else {
                        $errors[] = "Failed to create appointment. Please try again.";
                    }
                } else {
                    $errors[] = "This time slot is not available. Please select another time.";
                }
            }
        }
        
        // If we get here, either the form hasn't been submitted or there were errors
        include __DIR__ . '/../views/calendar/create.php';
    }
    
    // Edit appointment
    public function editAppointment($id) {
        $this->startSession();
        
        // Check if user is logged in
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }
        
        $userId = $_SESSION['user']['id'];
        $errors = [];
        
        // Get appointment details
        $appointment = $this->getAppointmentById($id, $userId);
        if (!$appointment) {
            header('Location: /calendar');
            exit;
        }
        
        // Get services and clients for form
        $services = $this->getServices($userId);
        $clients = $this->getClients($userId);
        
        // Handle form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $clientId = isset($_POST['client_id']) ? intval($_POST['client_id']) : 0;
            $serviceId = isset($_POST['service_id']) ? intval($_POST['service_id']) : 0;
            $date = isset($_POST['date']) ? $_POST['date'] : '';
            $time = isset($_POST['time']) ? $_POST['time'] : '';
            $notes = isset($_POST['notes']) ? $_POST['notes'] : '';
            
            // Validate form data
            if ($clientId <= 0) {
                $errors[] = "Please select a client";
            }
            
            if ($serviceId <= 0) {
                $errors[] = "Please select a service";
            }
            
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
                $errors[] = "Invalid date format";
            }
            
            if (!preg_match('/^\d{2}:\d{2}$/', $time)) {
                $errors[] = "Invalid time format";
            }
            
            if (empty($errors)) {
                // Get service duration to calculate end time
                $service = $this->getService($serviceId);
                $duration = $service ? intval($service['duration']) : 60; // Default to 60 min
                
                $startDateTime = $date . ' ' . $time . ':00';
                $endDateTime = date('Y-m-d H:i:s', strtotime($startDateTime . ' +' . $duration . ' minutes'));
                
                // Check if time slot is available (excluding current appointment)
                if ($this->isTimeSlotAvailable($userId, $startDateTime, $endDateTime, $id)) {
                    // Update appointment
                    $result = $this->updateAppointmentInDb(
                        $id,
                        $userId, 
                        $clientId, 
                        $serviceId, 
                        $startDateTime, 
                        $endDateTime, 
                        $notes
                    );
                    
                    if ($result) {
                        // Redirect to day view
                        header('Location: /calendar/day?date=' . $date . '&success=1');
                        exit;
                    } else {
                        $errors[] = "Failed to update appointment. Please try again.";
                    }
                } else {
                    $errors[] = "This time slot is not available. Please select another time.";
                }
            }
        }
        
        // If we get here, either the form hasn't been submitted or there were errors
        include __DIR__ . '/../views/calendar/edit.php';
    }
    
    // Delete appointment
    public function deleteAppointment($id) {
        $this->startSession();
        
        // Check if user is logged in
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }
        
        $userId = $_SESSION['user']['id'];
        
        // Delete appointment from database
        $result = $this->deleteAppointmentInDb($id, $userId);
        
        if ($result) {
            header('Location: /calendar?success=1');
            exit;
        } else {
            header('Location: /calendar?error=1');
            exit;
        }
    }
    
    // Helper - Get appointments for date range
    private function getAppointmentsByDateRange($userId, $startDate, $endDate) {
        $query = "SELECT a.*, c.name as client_name, s.name as service_name, s.duration as duration
                 FROM appointments a
                 JOIN clients c ON a.client_id = c.id
                 JOIN services s ON a.service_id = s.id
                 WHERE a.user_id = :userId
                 AND DATE(a.start_time) BETWEEN :startDate AND :endDate
                 ORDER BY a.start_time ASC";
                 
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':startDate', $startDate);
        $stmt->bindValue(':endDate', $endDate);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    // Helper - Get appointments for a specific date
    private function getAppointmentsByDate($userId, $date) {
        return $this->getAppointmentsByDateRange($userId, $date, $date);
    }
    
    // Helper - Get services for a user
    private function getServices($userId) {
        $query = "SELECT * FROM services WHERE user_id = :userId OR user_id = 0 ORDER BY name ASC";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    // Helper - Get specific service
    private function getService($serviceId) {
        $query = "SELECT * FROM services WHERE id = :serviceId";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':serviceId', $serviceId, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch();
    }
    
    // Helper - Get clients for a user
    private function getClients($userId) {
        $query = "SELECT * FROM clients WHERE user_id = :userId ORDER BY name ASC";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    // Helper - Check if time slot is available
    private function isTimeSlotAvailable($userId, $startTime, $endTime, $excludeAppointmentId = null) {
        $query = "SELECT COUNT(*) as count FROM appointments 
                 WHERE user_id = :userId 
                 AND status != 'cancelled'
                 AND (
                     (start_time <= :startTime AND end_time > :startTime) OR
                     (start_time < :endTime AND end_time >= :endTime) OR
                     (start_time >= :startTime AND end_time <= :endTime)
                 )";
                 
        if ($excludeAppointmentId) {
            $query .= " AND id != :excludeId";
        }
        
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':startTime', $startTime);
        $stmt->bindValue(':endTime', $endTime);
        
        if ($excludeAppointmentId) {
            $stmt->bindValue(':excludeId', $excludeAppointmentId, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        $result = $stmt->fetch();
        
        return $result['count'] == 0;
    }
    
    // Helper - Create appointment in database
    private function createAppointmentInDb($userId, $clientId, $serviceId, $startTime, $endTime, $notes) {
        $query = "INSERT INTO appointments 
                 (user_id, client_id, service_id, start_time, end_time, status, notes, created_at)
                 VALUES 
                 (:userId, :clientId, :serviceId, :startTime, :endTime, :status, :notes, NOW())";
                 
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':clientId', $clientId, PDO::PARAM_INT);
        $stmt->bindValue(':serviceId', $serviceId, PDO::PARAM_INT);
        $stmt->bindValue(':startTime', $startTime);
        $stmt->bindValue(':endTime', $endTime);
        $stmt->bindValue(':status', 'confirmed');
        $stmt->bindValue(':notes', $notes);
        
        return $stmt->execute();
    }
    
    // Helper - Get appointment by ID
    private function getAppointmentById($id, $userId) {
        $query = "SELECT * FROM appointments WHERE id = :id AND user_id = :userId";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch();
    }
    
    // Helper - Update appointment in database
    private function updateAppointmentInDb($id, $userId, $clientId, $serviceId, $startTime, $endTime, $notes) {
        $query = "UPDATE appointments 
                 SET client_id = :clientId, 
                     service_id = :serviceId, 
                     start_time = :startTime, 
                     end_time = :endTime, 
                     notes = :notes 
                 WHERE id = :id AND user_id = :userId";
                 
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':clientId', $clientId, PDO::PARAM_INT);
        $stmt->bindValue(':serviceId', $serviceId, PDO::PARAM_INT);
        $stmt->bindValue(':startTime', $startTime);
        $stmt->bindValue(':endTime', $endTime);
        $stmt->bindValue(':notes', $notes);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
    
    // Helper - Delete appointment from database
    private function deleteAppointmentInDb($id, $userId) {
        $query = "DELETE FROM appointments WHERE id = :id AND user_id = :userId";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
}