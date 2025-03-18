<?php

namespace WebSchedulr\Controllers;

use WebSchedulr\Models\UserModel;

class AuthController {
    private $userModel;
    
    public function __construct() {
        $this->userModel = new UserModel();
    }
    
    public function login() {
        session_start();
        
        // If user is already logged in, redirect to dashboard
        if (isset($_SESSION['user'])) {
            redirect('dashboard');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            
            if (empty($email) || empty($password)) {
                $error = "Email and password are required";
            } else {
                $user = $this->userModel->authenticate($email, $password);
                
                if ($user) {
                    // Store user data in session
                    $_SESSION['user'] = [
                        'id' => $user['id'],
                        'name' => $user['name'],
                        'email' => $user['email'],
                        'role' => $user['role']
                    ];
                    
                    redirect('dashboard');
                } else {
                    $error = "Invalid email or password";
                }
            }
        }
        
        // Load login view
        include __DIR__ . '/../views/auth/login.php';
    }
    
    public function register() {
        session_start();
        
        // If user is already logged in, redirect to dashboard
        if (isset($_SESSION['user'])) {
            redirect('dashboard');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';
            
            // Validate input
            if (empty($name) || empty($email) || empty($password)) {
                $error = "All fields are required";
            } elseif ($password !== $confirmPassword) {
                $error = "Passwords do not match";
            } elseif (strlen($password) < 6) {
                $error = "Password must be at least 6 characters";
            } elseif ($this->userModel->findByEmail($email)) {
                $error = "Email already exists";
            } else {
                // Create new user
                $userId = $this->userModel->create([
                    'name' => $name,
                    'email' => $email,
                    'password' => $password,
                    'role' => 'user',
                    'created_at' => date('Y-m-d H:i:s')
                ]);
                
                if ($userId) {
                    // Set success message and redirect to login
                    $_SESSION['success'] = "Registration successful. Please login.";
                    redirect('login');
                } else {
                    $error = "Registration failed. Please try again.";
                }
            }
        }
        
        // Load register view
        include __DIR__ . '/../views/auth/register.php';
    }
    
    public function logout() {
        session_start();
        session_destroy();
        redirect('login');
    }
}