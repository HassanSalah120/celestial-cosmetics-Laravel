<?php
// Simple file to test webhook configuration
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Dump request information
echo "<h1>Webhook Test</h1>";
echo "<h2>Request Method: " . $_SERVER['REQUEST_METHOD'] . "</h2>";
echo "<h2>Request Headers:</h2>";
echo "<pre>";
$headers = getallheaders();
foreach ($headers as $name => $value) {
    echo htmlspecialchars($name) . ": " . htmlspecialchars($value) . "\n";
}
echo "</pre>";

echo "<h2>Request Body:</h2>";
echo "<pre>";
$body = file_get_contents('php://input');
echo htmlspecialchars($body);
echo "</pre>"; 