<?php
// This is a diagnostic tool - remove after debugging
echo '<h1>Route Debugging</h1>';

echo '<h2>Test Direct Links:</h2>';
echo '<ul>';
echo '<li><a href="/calendar/edit/1" target="_blank">Test Calendar Edit: ID 1</a></li>';
echo '<li><a href="/services/edit/1" target="_blank">Test Service Edit: ID 1</a></li>';
echo '</ul>';

echo '<h2>Current Request Information:</h2>';
echo '<pre>';
echo 'REQUEST_URI: ' . $_SERVER['REQUEST_URI'] . "\n";
echo 'SCRIPT_NAME: ' . $_SERVER['SCRIPT_NAME'] . "\n"; 
echo 'PHP_SELF: ' . $_SERVER['PHP_SELF'] . "\n";
echo '</pre>';

echo '<h2>All Server Variables:</h2>';
echo '<pre>';
print_r($_SERVER);
echo '</pre>';
?>