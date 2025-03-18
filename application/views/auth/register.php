<?php
// Set title
$title = 'Register';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?> - WebSchedulr</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/css/app.css">
</head>
<body>
    <div class="auth-page">
        <div class="auth-card card">
            <div class="card-header">
                <div class="text-center mb-3">
                <img src="/assets/images/logo_black.png" alt="WebSchedulr" class="app-logo">
                </div>
                <h3 class="auth-title">Create an Account</h3>
                <p class="auth-subtitle">Register to start scheduling appointments</p>
            </div>
            
            <div class="card-body">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>
                
                <form method="POST" action="<?= site_url('register') ?>" class="auth-form">
                    <div class="form-group">
                        <label for="name" class="form-label">Full Name</label>
                        <input type="text" name="name" id="name" class="form-control" value="<?= $_POST['name'] ?? '' ?>" required autofocus>
                    </div>
                    
                    <div class="form-group">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" name="email" id="email" class="form-control" value="<?= $_POST['email'] ?? '' ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" name="password" id="password" class="form-control" required>
                        <small class="form-text text-muted">Must be at least 6 characters</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password" class="form-label">Confirm Password</label>
                        <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary submit-btn">Register</button>
                    </div>
                </form>
                
                <div class="auth-footer">
                    <p>Already have an account? <a href="<?= site_url('login') ?>">Log in</a></p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>