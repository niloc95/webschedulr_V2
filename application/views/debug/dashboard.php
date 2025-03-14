<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Debug - WebSchedulr</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { padding: 20px; }
        .debug-section { margin-bottom: 30px; }
        .error { color: #dc3545; }
        .success { color: #198754; }
        pre { background: #f8f9fa; padding: 10px; border-radius: 4px; }
        .debug-header { background: #f0f0f0; padding: 10px; margin-bottom: 20px; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="debug-header">
            <h1>WebSchedulr Dashboard Debug</h1>
            <p>PHP Version: <?= phpversion() ?> | Current Time: <?= date('Y-m-d H:i:s') ?></p>
            <p>Session ID: <?= session_id() ?> | User: <?= isset($_SESSION['user']) ? htmlspecialchars($_SESSION['user']['name']) : 'Not logged in' ?></p>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="debug-section">
                    <h3>Log Messages</h3>
                    <ul class="list-group">
                        <?php foreach($this->messages as $msg): ?>
                            <li class="list-group-item <?= strpos($msg, 'ERROR') !== false ? 'list-group-item-danger' : '' ?>">
                                <?= htmlspecialchars($msg) ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                
                <?php if (!empty($this->errors)): ?>
                <div class="debug-section">
                    <h3>Errors</h3>
                    <div class="alert alert-danger">
                        <ul>
                            <?php foreach($this->errors as $error): ?>
                                <li><?= htmlspecialchars($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
                <?php endif; ?>
                
                <div class="debug-section">
                    <h3>Dashboard Variables Status</h3>
                    <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Stats
                            <span class="badge bg-<?= isset($stats) ? 'success' : 'danger' ?>"><?= isset($stats) ? 'Defined' : 'Undefined' ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Upcoming Appointments
                            <span class="badge bg-<?= isset($upcomingAppointments) ? 'success' : 'danger' ?>"><?= isset($upcomingAppointments) ? 'Defined' : 'Undefined' ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Calendar Data
                            <span class="badge bg-<?= isset($calendarData) ? 'success' : 'danger' ?>"><?= isset($calendarData) ? 'Defined' : 'Undefined' ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            AppointmentsByDay
                            <span class="badge bg-<?= isset($appointmentsByDay) ? 'success' : 'danger' ?>"><?= isset($appointmentsByDay) ? 'Defined' : 'Undefined' ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Recent Activities
                            <span class="badge bg-<?= isset($recentActivities) ? 'success' : 'danger' ?>"><?= isset($recentActivities) ? 'Defined' : 'Undefined' ?></span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="debug-section">
                    <h3>Stats Data</h3>
                    <pre><?php print_r($stats); ?></pre>
                </div>
                
                <div class="debug-section">
                    <h3>Calendar Data</h3>
                    <pre><?php print_r($calendarData); ?></pre>
                </div>
                
                <div class="debug-section">
                    <h3>Sample Appointments</h3>
                    <?php if (!empty($upcomingAppointments)): ?>
                        <pre><?php print_r(array_slice($upcomingAppointments, 0, 2)); ?></pre>
                    <?php else: ?>
                        <p>No appointments found</p>
                    <?php endif; ?>
                </div>
                
                <div class="debug-section">
                    <h3>Chart Data</h3>
                    <pre><?php print_r($appointmentsByDay); ?></pre>
                </div>
            </div>
        </div>
        
        <div class="debug-section">
            <h3>Next Steps</h3>
            <div class="alert alert-info">
                <p><strong>Try one of these:</strong></p>
                <ul>
                    <li>If there are database connection errors, check your config.php file and make sure the database credentials are correct</li>
                    <li>If tables are missing, make sure you've run the setup SQL files</li>
                    <li>If no errors but no data, make sure you have sample data in the database</li>
                    <li>Once everything looks good here, try the <a href="/dashboard">regular dashboard</a> again</li>
                </ul>
            </div>
        </div>
    </div>
</body>
</html>