<?php
require_once __DIR__ . '/../application/controllers/CalendarController.php';
$controller = new CalendarController();

// Replace 1 with a valid appointment ID
$controller->edit(2);
?>