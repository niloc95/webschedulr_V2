<?php

require_once __DIR__ . '/BaseController.php';

class CalendarController extends BaseController {
    
    public function __construct() {
        parent::__construct();
        // Add debugging to track controller instantiation
        error_log("CalendarController initialized");
    }
    
    // Calendar Month View
    public function index() {
        $this->startSession();
        
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }
        
        $userId = $_SESSION['user']['id'];
        
        // Get current month/year or use query parameters
        $month = isset($_GET['month']) ? (int) $_GET['month'] : (int) date('n');
        $year = isset($_GET['year']) ? (int) $_GET['year'] : (int) date('Y');
        
        // Validate month/year
        if ($month < 1 || $month > 12) $month = date('n');
        if ($year < 2020 || $year > 2050) $year = date('Y');
        
        // Calculate previous and next month
        $prevMonth = $month - 1;
        $prevYear = $year;
        if ($prevMonth < 1) {
            $prevMonth = 12;
            $prevYear--;
        }
        
        $nextMonth = $month + 1;
        $nextYear = $year;
        if ($nextMonth > 12) {
            $nextMonth = 1;
            $nextYear++;
        }
        
        // Calculate first and last day of month
        $firstDayOfMonth = mktime(0, 0, 0, $month, 1, $year);
        $numDaysInMonth = date('t', $firstDayOfMonth);
        $dateString = date('F Y', $firstDayOfMonth);
        
        // Calculate starting day of week (0 = Sunday, 6 = Saturday)
        $startingDayOfWeek = date('w', $firstDayOfMonth);
        
        // Get appointments for the month
        try {
            $startDate = date('Y-m-d 00:00:00', $firstDayOfMonth);
            $endDate = date('Y-m-d 23:59:59', mktime(0, 0, 0, $month, $numDaysInMonth, $year));
            
            $query = "
                SELECT a.*, c.name AS client_name, s.name AS service_name, s.color 
                FROM appointments a 
                JOIN clients c ON a.client_id = c.id 
                JOIN services s ON a.service_id = s.id 
                WHERE a.user_id = ? 
                  AND a.start_time BETWEEN ? AND ? 
                ORDER BY a.start_time
            ";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute([$userId, $startDate, $endDate]);
            $appointments = $stmt->fetchAll();
            
            // Organize appointments by day number (not full date)
            $appointmentsByDay = [];
            foreach ($appointments as $appointment) {
                $day = date('j', strtotime($appointment['start_time'])); // Just the day number
                if (!isset($appointmentsByDay[$day])) {
                    $appointmentsByDay[$day] = [];
                }
                $appointmentsByDay[$day][] = $appointment;
            }
            
        } catch (PDOException $e) {
            error_log("Error fetching appointments: " . $e->getMessage());
            $appointmentsByDay = [];
        }
        
        // Structure data exactly as the template expects it
        $calendarData = [
            'currentMonth' => $month,
            'currentYear' => $year,
            'prevMonth' => $prevMonth,
            'prevYear' => $prevYear,
            'nextMonth' => $nextMonth,
            'nextYear' => $nextYear,
            'dateString' => $dateString,
            'startingDayOfWeek' => $startingDayOfWeek,
            'numDaysInMonth' => $numDaysInMonth,
            'appointmentsByDay' => $appointmentsByDay
        ];
        
        include __DIR__ . '/../views/calendar/index.php';
    }
    
    // Calendar Day View - Updated to support both URL formats
    public function day($dateParam = null) {
        $this->startSession();
        
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }
        
        $userId = $_SESSION['user']['id'];
        
        // Check if we received a date in YYYY-MM-DD format as a direct parameter
        if ($dateParam && preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateParam)) {
            error_log("CalendarController::day - Using direct date parameter: $dateParam");
            $year = (int)substr($dateParam, 0, 4);
            $month = (int)substr($dateParam, 5, 2);
            $day = (int)substr($dateParam, 8, 2);
        } else {
            // Otherwise, use query parameters or defaults
            error_log("CalendarController::day - Using query parameters");
            $day = isset($_GET['day']) ? (int) $_GET['day'] : (int) date('j');
            $month = isset($_GET['month']) ? (int) $_GET['month'] : (int) date('n');
            $year = isset($_GET['year']) ? (int) $_GET['year'] : (int) date('Y');
        }
        
        // Validate date
        if (!checkdate($month, $day, $year)) {
            error_log("Invalid date: $year-$month-$day - Using current date instead");
            $day = (int) date('j');
            $month = (int) date('n');
            $year = (int) date('Y');
        }
        
        // Format date for display
        $date = mktime(0, 0, 0, $month, $day, $year);
        $dateString = date('l, F j, Y', $date);
        $dateFormatted = date('Y-m-d', $date);
        
        // Get previous and next day
        $prevDate = strtotime('-1 day', $date);
        $nextDate = strtotime('+1 day', $date);
        
        // Create URLs for previous and next day using the new format
        $prevDateFormatted = date('Y-m-d', $prevDate);
        $nextDateFormatted = date('Y-m-d', $nextDate);
        
        try {
            // Get day's appointments
            $startDateTime = date('Y-m-d 00:00:00', $date);
            $endDateTime = date('Y-m-d 23:59:59', $date);
            
            $query = "
                SELECT a.*, c.name AS client_name, c.email AS client_email, c.phone AS client_phone,
                       s.name AS service_name, s.duration, s.color
                FROM appointments a 
                JOIN clients c ON a.client_id = c.id 
                JOIN services s ON a.service_id = s.id 
                WHERE a.user_id = ? 
                  AND a.start_time BETWEEN ? AND ? 
                ORDER BY a.start_time
            ";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute([$userId, $startDateTime, $endDateTime]);
            $appointments = $stmt->fetchAll();
            
            // Get start/end times for display
            $businessStart = 8; // 8 AM
            $businessEnd = 18; // 6 PM
            $timeSlots = [];
            
            for ($hour = $businessStart; $hour <= $businessEnd; $hour++) {
                $formattedHour = date('g:i A', mktime($hour, 0, 0));
                $timeSlots[] = $formattedHour;
            }
            
        } catch (PDOException $e) {
            error_log("Error fetching daily appointments: " . $e->getMessage());
            $appointments = [];
            $timeSlots = [];
        }
        
        // Get clients and services for appointment creation
        try {
            $clientsQuery = "SELECT id, name FROM clients WHERE user_id = ? ORDER BY name";
            $clientsStmt = $this->db->prepare($clientsQuery);
            $clientsStmt->execute([$userId]);
            $clients = $clientsStmt->fetchAll();
            
            $servicesQuery = "SELECT id, name, duration FROM services WHERE user_id = ? ORDER BY name";
            $servicesStmt = $this->db->prepare($servicesQuery);
            $servicesStmt->execute([$userId]);
            $services = $servicesStmt->fetchAll();
            
        } catch (PDOException $e) {
            error_log("Error fetching clients/services: " . $e->getMessage());
            $clients = [];
            $services = [];
        }
        
        // Pass data to view
        $dayData = [
            'day' => $day,
            'month' => $month,
            'year' => $year,
            'dateString' => $dateString,
            'dateFormatted' => $dateFormatted,
            'prevDay' => date('j', $prevDate),
            'prevMonth' => date('n', $prevDate),
            'prevYear' => date('Y', $prevDate),
            'nextDay' => date('j', $nextDate),
            'nextMonth' => date('n', $nextDate),
            'nextYear' => date('Y', $nextDate),
            'prevDateFormatted' => $prevDateFormatted,  // Added for new URL format
            'nextDateFormatted' => $nextDateFormatted,  // Added for new URL format
            'appointments' => $appointments,
            'timeSlots' => $timeSlots,
            'clients' => $clients,
            'services' => $services
        ];
        
        include __DIR__ . '/../views/calendar/day.php';
    }
    
    // Create new appointment
    public function create() {
        $this->startSession();
        
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }
        
        $userId = $_SESSION['user']['id'];
        
        try {
            // Get clients for dropdown
            $clientsQuery = "SELECT id, name FROM clients WHERE user_id = ? ORDER BY name";
            $clientsStmt = $this->db->prepare($clientsQuery);
            $clientsStmt->execute([$userId]);
            $clients = $clientsStmt->fetchAll();
            
            // Get services for dropdown
            $servicesQuery = "SELECT id, name, duration FROM services WHERE user_id = ? ORDER BY name";
            $servicesStmt = $this->db->prepare($servicesQuery);
            $servicesStmt->execute([$userId]);
            $services = $servicesStmt->fetchAll();
            
        } catch (PDOException $e) {
            error_log("Error fetching clients/services: " . $e->getMessage());
            $clients = [];
            $services = [];
        }
        
        // Get selected date if available
        $selectedDate = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
        $selectedTime = isset($_GET['time']) ? $_GET['time'] : '09:00';
        
        include __DIR__ . '/../views/calendar/create.php';
    }

 /**
 * Update appointment status via AJAX
 */
public function updateStatus() {
    $this->startSession();
    
    if (!isset($_SESSION['user'])) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        return;
    }
    
    header('Content-Type: application/json');
    
    $userId = $_SESSION['user']['id'];
    
    // Get input data
    $input = json_decode(file_get_contents('php://input'), true);
    
    $id = $input['id'] ?? null;
    $status = $input['status'] ?? null;
    
    if (!$id || !$status) {
        echo json_encode(['success' => false, 'message' => 'Missing appointment ID or status']);
        return;
    }
    
    // Validate status
    $validStatuses = ['pending', 'confirmed', 'completed', 'cancelled'];
    if (!in_array($status, $validStatuses)) {
        echo json_encode(['success' => false, 'message' => 'Invalid status']);
        return;
    }
    
    try {
        // Update appointment status
        $query = "UPDATE appointments SET status = ? WHERE id = ? AND user_id = ?";
        $stmt = $this->db->prepare($query);
        $result = $stmt->execute([$status, $id, $userId]);
        
        if ($result && $stmt->rowCount() > 0) {
            echo json_encode(['success' => true, 'message' => 'Appointment status updated']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Appointment not found or no changes made']);
        }
        
    } catch (PDOException $e) {
        error_log("Error updating appointment status: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Database error occurred']);
    }
}
    
    // Store new appointment
    public function store() {
        $this->startSession();
        
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }
        
        $userId = $_SESSION['user']['id'];
        
        // Validate inputs
        $clientId = filter_input(INPUT_POST, 'client_id', FILTER_VALIDATE_INT);
        $serviceId = filter_input(INPUT_POST, 'service_id', FILTER_VALIDATE_INT);
        // Removed title validation
        $date = trim($_POST['date'] ?? '');
        $time = trim($_POST['time'] ?? '');
        $notes = trim($_POST['notes'] ?? '');
        $duration = filter_input(INPUT_POST, 'duration', FILTER_VALIDATE_INT);
        // Status defaults to 'pending' as per your schema
        $status = 'pending';
        
        $errors = [];
        
        if (!$clientId) $errors['client_id'] = 'Please select a client';
        if (!$serviceId) $errors['service_id'] = 'Please select a service';
        // Removed title validation
        if (empty($date)) $errors['date'] = 'Date is required';
        if (empty($time)) $errors['time'] = 'Time is required';
        if (!$duration || $duration <= 0) $errors['duration'] = 'Duration must be greater than 0';
        
        // If errors, redirect back with error messages
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $_POST;
            
            // Redirect back to form
            header('Location: /calendar/create');
            exit;
        }
        
        // Calculate start and end times
        $startTime = date('Y-m-d H:i:s', strtotime("$date $time"));
        $endTime = date('Y-m-d H:i:s', strtotime("$date $time + $duration minutes"));
        
        try {
            // Check for overlapping appointments
            $overlapCheck = $this->db->prepare("
                SELECT COUNT(*) FROM appointments 
                WHERE user_id = ? 
                  AND status != 'cancelled'
                  AND (
                      (start_time <= ? AND end_time > ?) OR
                      (start_time < ? AND end_time >= ?) OR
                      (start_time >= ? AND end_time <= ?)
                  )
            ");
            
            $overlapCheck->execute([
                $userId,
                $endTime, $startTime,
                $endTime, $endTime,
                $startTime, $endTime
            ]);
            
            if ($overlapCheck->fetchColumn() > 0) {
                $_SESSION['error'] = 'This time slot overlaps with an existing appointment';
                $_SESSION['old'] = $_POST;
                header('Location: /calendar/create');
                exit;
            }
            
            // Current time for created_at
            $currentTime = date('Y-m-d H:i:s');
            
            // Insert appointment - removed title field
            $query = "
                INSERT INTO appointments 
                (user_id, client_id, service_id, start_time, end_time, status, notes, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                $userId,
                $clientId,
                $serviceId,
                $startTime,
                $endTime,
                $status,
                $notes,
                $currentTime
            ]);
            
            $_SESSION['success'] = 'Appointment created successfully';
            
            // Get formatted date for new URL format redirect
            $formattedDate = date('Y-m-d', strtotime($date));
            header("Location: /calendar/day/{$formattedDate}");
            exit;
            
        } catch (PDOException $e) {
            error_log("Error creating appointment: " . $e->getMessage());
            $_SESSION['error'] = 'Failed to create appointment: ' . $e->getMessage();
            $_SESSION['old'] = $_POST;
            header('Location: /calendar/create');
            exit;
        }
    }
    
    // Edit appointment form
    public function edit($id) {
        $this->startSession();
        
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }
        
        $userId = $_SESSION['user']['id'];
        
        // Add critical debugging
        error_log("CalendarController::edit called with ID: $id");
        
        try {
            // Get appointment data
            $query = "
                SELECT a.*, c.name AS client_name, c.phone AS client_phone, s.name AS service_name, s.duration, s.color
                FROM appointments a
                JOIN clients c ON a.client_id = c.id
                JOIN services s ON a.service_id = s.id
                WHERE a.id = ? AND a.user_id = ?
            ";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$id, $userId]);
            $appointment = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$appointment) {
                error_log("Appointment not found: ID $id for user $userId");
                $_SESSION['error'] = 'Appointment not found';
                header('Location: /calendar');
                exit;
            }
            
            // Log that we found the appointment
            error_log("Found appointment: " . json_encode($appointment));
            
            // Get clients for dropdown
            $clientsQuery = "SELECT * FROM clients WHERE user_id = ? ORDER BY name";
            $clientsStmt = $this->db->prepare($clientsQuery);
            $clientsStmt->execute([$userId]);
            $clients = $clientsStmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Get services for dropdown
            $servicesQuery = "SELECT * FROM services WHERE user_id = ? ORDER BY name";
            $servicesStmt = $this->db->prepare($servicesQuery);
            $servicesStmt->execute([$userId]);
            $services = $servicesStmt->fetchAll(PDO::FETCH_ASSOC);
            
            // CRITICAL: Check if the calendar edit view exists before including it
            $viewPath = __DIR__ . '/../views/calendar/edit.php';
            if (!file_exists($viewPath)) {
                error_log("ERROR: Calendar edit view not found at: $viewPath");
                $_SESSION['error'] = "System error: View file not found";
                header('Location: /calendar');
                exit;
            }
            
            // Log before including the view
            error_log("Including calendar edit view from: $viewPath");
            
            // Display edit form
            include $viewPath;
            
        } catch (PDOException $e) {
            error_log("Database error in calendar edit: " . $e->getMessage());
            $_SESSION['error'] = 'Database error occurred';
            header('Location: /calendar');
            exit;
        }
    }
    
    // Update appointment
    public function update($id) {
        $this->startSession();
        
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }
        
        $userId = $_SESSION['user']['id'];
        
        // Validate inputs
        $clientId = filter_input(INPUT_POST, 'client_id', FILTER_VALIDATE_INT);
        $serviceId = filter_input(INPUT_POST, 'service_id', FILTER_VALIDATE_INT);
        // Remove the title field
        // $title = trim($_POST['title'] ?? '');
        $date = trim($_POST['date'] ?? '');
        $time = trim($_POST['time'] ?? '');
        $notes = trim($_POST['notes'] ?? '');
        $duration = filter_input(INPUT_POST, 'duration', FILTER_VALIDATE_INT);
        $status = trim($_POST['status'] ?? 'pending');
        
        $errors = [];
        
        if (!$clientId) $errors['client_id'] = 'Please select a client';
        if (!$serviceId) $errors['service_id'] = 'Please select a service';
        // Remove title validation
        // if (empty($title)) $errors['title'] = 'Title is required';
        if (empty($date)) $errors['date'] = 'Date is required';
        if (empty($time)) $errors['time'] = 'Time is required';
        if (!$duration || $duration <= 0) $errors['duration'] = 'Duration must be greater than 0';
        
        // Validate status against your schema values
        $validStatuses = ['pending', 'confirmed', 'cancelled', 'completed'];
        if (!in_array($status, $validStatuses)) {
            $errors['status'] = 'Invalid status selected';
        }
        
        // If errors, redirect back with error messages
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $_POST;
            header("Location: /calendar/edit/{$id}");
            exit;
        }
        
        try {
            // First check if appointment exists and belongs to user
            $checkQuery = "SELECT id FROM appointments WHERE id = ? AND user_id = ?";
            $checkStmt = $this->db->prepare($checkQuery);
            $checkStmt->execute([$id, $userId]);
            
            if (!$checkStmt->fetch()) {
                $_SESSION['error'] = 'Appointment not found';
                header('Location: /calendar');
                exit;
            }
            
            // Calculate start and end times
            $startTime = date('Y-m-d H:i:s', strtotime("$date $time"));
            $endTime = date('Y-m-d H:i:s', strtotime("$date $time + $duration minutes"));
            
            // Check for overlapping appointments (excluding this one)
            $overlapCheck = $this->db->prepare("
                SELECT COUNT(*) FROM appointments 
                WHERE user_id = ? 
                  AND id != ?
                  AND status != 'cancelled'
                  AND (
                      (start_time <= ? AND end_time > ?) OR
                      (start_time < ? AND end_time >= ?) OR
                      (start_time >= ? AND end_time <= ?)
                  )
            ");
            
            $overlapCheck->execute([
                $userId,
                $id,
                $endTime, $startTime,
                $endTime, $endTime,
                $startTime, $endTime
            ]);
            
            if ($overlapCheck->fetchColumn() > 0) {
                $_SESSION['error'] = 'This time slot overlaps with an existing appointment';
                $_SESSION['old'] = $_POST;
                header("Location: /calendar/edit/{$id}");
                exit;
            }
            
            // Update appointment - removed title field
            $query = "
                UPDATE appointments 
                SET client_id = ?, 
                    service_id = ?,
                    start_time = ?, 
                    end_time = ?, 
                    notes = ?,
                    status = ?
                WHERE id = ? AND user_id = ?
            ";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                $clientId,
                $serviceId,
                $startTime,
                $endTime,
                $notes,
                $status,
                $id,
                $userId
            ]);
            
            $_SESSION['success'] = 'Appointment updated successfully';
            
            // Format date for new URL format
            $formattedDate = date('Y-m-d', strtotime($date));
            header("Location: /calendar/day/{$formattedDate}");
            exit;
            
        } catch (PDOException $e) {
            error_log("Error updating appointment: " . $e->getMessage());
            $_SESSION['error'] = 'Failed to update appointment: Database error';
            $_SESSION['old'] = $_POST;
            header("Location: /calendar/edit/{$id}");
            exit;
        }
    }
    
    // Delete appointment
    public function delete($id) {
        $this->startSession();
        
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }
        
        $userId = $_SESSION['user']['id'];
        
        try {
            // First check if appointment exists and belongs to user
            $checkQuery = "SELECT id, start_time FROM appointments WHERE id = ? AND user_id = ?";
            $checkStmt = $this->db->prepare($checkQuery);
            $checkStmt->execute([$id, $userId]);
            $appointment = $checkStmt->fetch();
            
            if (!$appointment) {
                $_SESSION['error'] = 'Appointment not found';
                header('Location: /calendar');
                exit;
            }
            
            // Delete appointment
            $query = "DELETE FROM appointments WHERE id = ? AND user_id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$id, $userId]);
            
            $_SESSION['success'] = 'Appointment deleted successfully';
            
            // Get formatted date for redirection
            $appointmentDate = date('Y-m-d', strtotime($appointment['start_time']));
            
            // Determine where to redirect based on referrer or date
            $referrer = $_POST['referrer'] ?? "/calendar/day/{$appointmentDate}";
            header("Location: $referrer");
            exit;
            
        } catch (PDOException $e) {
            error_log("Error deleting appointment: " . $e->getMessage());
            $_SESSION['error'] = 'Failed to delete appointment: Database error';
            header('Location: /calendar');
            exit;
        }
    }
}