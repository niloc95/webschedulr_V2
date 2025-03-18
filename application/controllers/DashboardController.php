<?php
class DashboardController {
    private $db;
    private $isConnected = false;
    
    public function __construct() {
        // Load the main config file that contains DB credentials
        require_once __DIR__ . '/../../config.php';
        
        // Connect to database using the Config class from the root config file
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
            $this->isConnected = true;
        } catch (PDOException $e) {
            // Just log the error and continue - view will use defaults
            error_log("Database connection error: " . $e->getMessage());
        }
    }
    
    private function startSession() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    public function index() {
        $this->startSession();
        
        // Check if user is logged in
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }
        
        $userId = $_SESSION['user']['id'];
        $today = date('Y-m-d');
        $tomorrow = date('Y-m-d', strtotime('+1 day'));
        
        // Initialize all variables with default values
        $stats = [
            'total_appointments' => 0,
            'upcoming_appointments' => 0,
            'total_clients' => 0,
            'total_services' => 0,
            'today_appointments' => 0,
            'tomorrow_appointments' => 0
        ];
        
        $upcomingAppointments = [];
        $todaysAppointments = [];
        $appointmentsByDay = [
            'labels' => ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
            'data' => [0, 0, 0, 0, 0, 0, 0]
        ];
        $statusStats = [
            'labels' => [],
            'data' => [],
            'backgrounds' => []
        ];
        $recentActivities = [];
        $recentClients = [];
        
        // Only try to get real data if DB connection succeeded
        if ($this->isConnected) {
            try {
                // Get basic stats
                $stats['total_appointments'] = $this->getTotalAppointments($userId);
                $stats['upcoming_appointments'] = $this->getUpcomingAppointmentsCount($userId, $today);
                $stats['total_clients'] = $this->getTotalClients($userId);
                $stats['total_services'] = $this->getTotalServices($userId);
                $stats['today_appointments'] = $this->getTodayAppointmentsCount($userId, $today);
                $stats['tomorrow_appointments'] = $this->getTodayAppointmentsCount($userId, $tomorrow);
                
                // Get detailed data
                $upcomingAppointments = $this->getUpcomingAppointments($userId, $today, 7);
                $todaysAppointments = $this->getTodaysAppointments($userId, $today);
                $appointmentsByDay = $this->getAppointmentDistribution($userId);
                $statusStats = $this->getAppointmentStatusStats($userId);
                $recentActivities = $this->getRecentActivities($userId, 10);
                $recentClients = $this->getRecentClients($userId, 5);
            } catch (Exception $e) {
                // Log any errors but continue with defaults
                error_log("Error retrieving dashboard data: " . $e->getMessage());
            }
        }
        
        // Calendar data doesn't depend on DB
        $calendarData = $this->getCalendarData();
        
        // Include the dashboard view
        include __DIR__ . '/../views/dashboard/index.php';
    }
    
    // Get total appointments for a user
    private function getTotalAppointments($userId) {
        if (!$this->isConnected) return 0;
        
        $query = "SELECT COUNT(*) as count FROM appointments WHERE user_id = :userId";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        
        return (int) $stmt->fetch()['count'];
    }
    
    // Get count of upcoming appointments
    private function getUpcomingAppointmentsCount($userId, $today) {
        if (!$this->isConnected) return 0;
        
        $query = "SELECT COUNT(*) as count FROM appointments 
                 WHERE user_id = :userId 
                 AND start_time >= :today
                 AND status != 'cancelled'";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':today', $today);
        $stmt->execute();
        
        return (int) $stmt->fetch()['count'];
    }
    
    // Get count of today's appointments
    private function getTodayAppointmentsCount($userId, $date) {
        if (!$this->isConnected) return 0;
        
        $query = "SELECT COUNT(*) as count FROM appointments 
                 WHERE user_id = :userId 
                 AND DATE(start_time) = :date
                 AND status != 'cancelled'";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':date', $date);
        $stmt->execute();
        
        return (int) $stmt->fetch()['count'];
    }
    
    // Get total clients
    private function getTotalClients($userId) {
        if (!$this->isConnected) return 0;
        
        $query = "SELECT COUNT(*) as count FROM clients WHERE user_id = :userId";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        
        return (int) $stmt->fetch()['count'];
    }
    
    // Get total services
    private function getTotalServices($userId) {
        if (!$this->isConnected) return 0;
        
        $query = "SELECT COUNT(*) as count FROM services WHERE user_id = :userId";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        
        return (int) $stmt->fetch()['count'];
    }
    
    // Calculate calendar data for mini calendar (no DB dependency)
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
    
    // Get upcoming appointments
    private function getUpcomingAppointments($userId, $today, $days = 7) {
        if (!$this->isConnected) return [];
        
        $endDate = date('Y-m-d', strtotime($today . ' + ' . $days . ' days'));
        
        $query = "SELECT a.*, c.name as client_name, c.email as client_email, s.name as service_name, s.duration, s.color
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
    
    // Get today's appointments
    private function getTodaysAppointments($userId, $today) {
        if (!$this->isConnected) return [];
        
        $query = "SELECT a.*, c.name as client_name, c.email as client_email, s.name as service_name, s.duration, s.color
                 FROM appointments a
                 JOIN clients c ON a.client_id = c.id
                 JOIN services s ON a.service_id = s.id
                 WHERE a.user_id = :userId
                 AND DATE(a.start_time) = :today
                 ORDER BY a.start_time ASC";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':today', $today);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    // Get appointment distribution by day of week
    private function getAppointmentDistribution($userId) {
        if (!$this->isConnected) return [
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
        
        // Convert to format suitable for charts
        $chartData = [
            'labels' => array_values($days),
            'data' => array_values($distribution)
        ];
        
        return $chartData;
    }
    
    // Get appointments by status for pie chart
    private function getAppointmentStatusStats($userId) {
        if (!$this->isConnected) return [
            'labels' => [],
            'data' => [],
            'backgrounds' => []
        ];
        
        $query = "SELECT 
                   status,
                   COUNT(*) as count
                 FROM appointments
                 WHERE user_id = :userId
                 GROUP BY status
                 ORDER BY status";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        
        $results = $stmt->fetchAll();
        
        // Define status colors
        $statusColors = [
            'pending' => '#ffc107',    // warning
            'confirmed' => '#28a745', // success
            'completed' => '#007bff', // primary
            'cancelled' => '#dc3545'  // danger
        ];
        
        $labels = [];
        $data = [];
        $backgrounds = [];
        
        foreach ($results as $row) {
            $labels[] = ucfirst($row['status']);
            $data[] = (int)$row['count'];
            $backgrounds[] = $statusColors[$row['status']] ?? '#6c757d'; // default gray
        }
        
        return [
            'labels' => $labels,
            'data' => $data,
            'backgrounds' => $backgrounds
        ];
    }
    
    // Get recent activities
    private function getRecentActivities($userId, $limit = 10) {
        if (!$this->isConnected) return [];
        
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
    
    // Get recent clients
    private function getRecentClients($userId, $limit = 5) {
        if (!$this->isConnected) return [];
        
        $query = "SELECT *
                 FROM clients
                 WHERE user_id = :userId
                 ORDER BY created_at DESC
                 LIMIT :limit";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
}