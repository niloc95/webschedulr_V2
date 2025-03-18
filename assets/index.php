<?php
/**
 * WebSchedulr Application Entry Point
 */

// Load bootstrap file
require_once __DIR__ . '/../bootstrap.php';

// For testing purposes - display a simple page
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WebSchedulr</title>
    <link rel="stylesheet" href="<?= asset_url('css/app.css') ?>">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">WebSchedulr Modernization</h4>
                    </div>
                    <div class="card-body">
                        <h5>Installation Complete!</h5>
                        <p>Your modernized WebSchedulr application has been set up successfully.</p>
                        
                        <h6 class="mt-4">Environment Information:</h6>
                        <ul>
                            <li>PHP Version: <?= phpversion() ?></li>
                            <li>Base URL: <?= Config::BASE_URL ?></li>
                            <li>Debug Mode: <?= Config::DEBUG_MODE ? 'Enabled' : 'Disabled' ?></li>
                        </ul>
                        
                        <div class="mt-4">
                            <a href="#" class="btn btn-primary">Dashboard</a>
                            <button class="btn btn-outline-secondary ms-2" data-bs-toggle="tooltip" title="This is a tooltip example">Hover Me</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="<?= asset_url('js/main.js') ?>"></script>
</body>
</html>
