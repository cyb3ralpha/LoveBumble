<?php
// Include constants
require_once 'constants.php';

// Create MySQLi connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Set character set to UTF-8
$conn->set_charset("utf8");

// Example usage:
// $result = $conn->query("SELECT * FROM users");
// while($row = $result->fetch_assoc()) { echo $row['full_name']; }

?>
