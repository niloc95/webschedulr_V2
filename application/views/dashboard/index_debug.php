<?php
// Debug header - place this at the very top of your index.php view file
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check which variables are defined
echo '<div style="background: #f8d7da; border: 1px solid #f5c6cb; padding: 10px; margin-bottom: 20px; border-radius: 5px;">';
echo '<strong>Debug Information:</strong><br>';
echo 'Stats variable exists: ' . (isset($stats) ? 'Yes' : 'No') . '<br>';
echo 'UpcomingAppointments variable exists: ' . (isset($upcomingAppointments) ? 'Yes' : 'No') . '<br>';
echo 'CalendarData variable exists: ' . (isset($calendarData) ? 'Yes' : 'No') . '<br>';
echo 'AppointmentsByDay variable exists: ' . (isset($appointmentsByDay) ? 'Yes' : 'No') . '<br>';
echo 'RecentActivities variable exists: ' . (isset($recentActivities) ? 'Yes' : 'No') . '<br>';
echo '</div>';

// Continue with the rest of your view
$active = 'dashboard';
$title = 'Dashboard';

// Include header
include __DIR__ . '/../layouts/header.php';
?>