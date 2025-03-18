<?php

/* ----------------------------------------------------------------------------
 * @webSchedulr - Online Appointment Scheduler
 *
 * @package     @webSchedulr
 * @author      N.N Cara <nilo.cara@frontend.co.za>
 * @copyright   Copyright (c) Nilesh Cara
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://webschedulr.co.za
 * @since       v1.0.0
 * ---------------------------------------------------------------------------- */

require_once __DIR__ . '/BaseController.php';

class AppointmentController extends BaseController {
    
    public function index() {
        $this->startSession();
        
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }
        
        $userId = $_SESSION['user']['id'];
        
        // Get filter parameters
        $clientId = isset($_GET['client_id']) ? (int)$_GET['client_id'] : null;
        $serviceId = isset($_GET['service_id']) ? (int)$_GET['service_id'] : null;
        $status = isset($_GET['status']) ? $_GET['status'] : null;
        $startDate = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-d', strtotime('-7 days'));
        $endDate = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d', strtotime('+30 days'));
        
        try {
            // Initialize arrays for conditions and parameters
            $conditions = ['a.user_id = ?'];
            $params = [$userId];
            
            if ($clientId) {
                $conditions[] = 'a.client_id = ?';
                $params[] = $clientId;
            }
            
            if ($serviceId) {
                $conditions[] = 'a.service_id = ?';
                $params[] = $serviceId;
            }
            
            if ($status) {
                $conditions[] = 'a.status = ?';
                $params[] = $status;
            }
            
            // Use DATE() function to extract just the date portion from start_time
            if ($startDate) {
                $conditions[] = "DATE(a.start_time) >= ?";
                $params[] = $startDate;
            }
            
            if ($endDate) {
                $conditions[] = "DATE(a.start_time) <= ?";
                $params[] = $endDate;
            }
            
            // Build WHERE clause
            $whereClause = implode(' AND ', $conditions);
            
            // Get appointments
            $query = "
                SELECT a.*, 
                       c.name as client_name, 
                       s.name as service_name, 
                       s.duration, 
                       s.color
                FROM appointments a
                LEFT JOIN clients c ON a.client_id = c.id
                LEFT JOIN services s ON a.service_id = s.id
                WHERE $whereClause
                ORDER BY a.start_time ASC
            ";
            
            error_log("Appointments query: $query with params: " . print_r($params, true));
            
            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            $appointments = $stmt->fetchAll();
            
            // Get clients for filter dropdown
            $stmt = $this->db->prepare("SELECT id, name FROM clients WHERE user_id = ? ORDER BY name ASC");
            $stmt->execute([$userId]);
            $clients = $stmt->fetchAll();
            
            // Get services for filter dropdown
            $stmt = $this->db->prepare("SELECT id, name FROM services WHERE user_id = ? ORDER BY name ASC");
            $stmt->execute([$userId]);
            $services = $stmt->fetchAll();
            
            // Render view
            include __DIR__ . '/../views/appointment/index.php';
            
        } catch (PDOException $e) {
            // Log error
            error_log("Error fetching appointments: " . $e->getMessage());
            
            // Show error page with detailed message
            $error = "There was a problem retrieving appointments. Error: " . $e->getMessage();
            include __DIR__ . '/../views/error.php';
        }
    }

    /**
     * Display direct database schema information for debugging
     */
    public function showSchema() {
        $this->startSession();
        
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }
        
        $title = 'Database Schema';
        
        try {
            // Get appointments table structure
            $stmt = $this->db->query("DESCRIBE appointments");
            $appointmentsColumns = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Get sample data
            $stmt = $this->db->prepare("SELECT * FROM appointments WHERE user_id = ? LIMIT 1");
            $stmt->execute([$_SESSION['user']['id']]);
            $sampleAppointment = $stmt->fetch(PDO::FETCH_ASSOC);
            
            include __DIR__ . '/../views/appointment/schema.php';
            
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
            include __DIR__ . '/../views/error.php';
        }
    }
}