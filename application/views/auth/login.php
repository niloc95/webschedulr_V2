<?php
// Set title
$title = 'Login';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?> - WebSchedulr</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/css/app.css">
</head>
<body>
    <div class="auth-page">
        <div class="auth-card card">
            <div class="card-header text-center">
                <div class="logo-container">
                    <img src="/assets/images/logo_black.png" alt="WebSchedulr" class="app-logo">
                </div>
                <div class="header-text">
                    <h3 class="auth-title fw-bold">Admin Login</h3>
                    
                </div>
            </div>
            
            <div class="card-body">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success"><?= $_SESSION['success'] ?></div>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>
                
                <form method="POST" action="<?= site_url('login') ?>" class="auth-form">
                    <div class="form-group">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" name="email" id="email" class="form-control w-100" required autofocus>
                    </div>
                    
                    <div class="form-group">
                        <div class="d-flex justify-content-between">
                            <label for="password" class="form-label">Password</label>
                        </div>
                        <input type="password" name="password" id="password" class="form-control w-100" required>
                    </div>
                    
                    <div class="form-group">
                        <div class="form-check">
                            <input type="checkbox" name="remember" id="remember" class="form-check-input">
                            <label for="remember" class="form-check-label">Remember me</label>
                            <a href="<?= site_url('forgot-password') ?>" class="form-text">Forgot password?</a>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary submit-btn">Login</button>
                    </div>
                </form>
                
                <div class="auth-footer">
                    <p>Don't have an account? <a href="<?= site_url('register') ?>">Sign up</a></p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>