<?php
// Base controller class with common functionality for all controllers

class BaseController {
    protected $db;
    
    public function __construct() {
        try {
            // Create database connection with FETCH_ASSOC as default
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