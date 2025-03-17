<?php
// Base controller class with common functionality for all controllers

class BaseController {
    protected $db;
    
    public function __construct() {
        // Include config file
        require_once __DIR__ . '/../../config.php';
        
        try {
            // Create database connection
            $this->db = new PDO(
                "mysql:host=" . Config::DB_HOST . ";dbname=" . Config::DB_NAME,
                Config::DB_USERNAME,
                Config::DB_PASSWORD,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
        } catch (PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage());
            die("Database connection failed. Check error logs for details.");
        }
    }
    
    protected function startSession() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }
}
?>