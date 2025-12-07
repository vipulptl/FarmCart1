<?php
// Database Configuration
$host = "localhost";      // Database Host
$user = "root";           // Database Username
$pass = "";               // Database Password
$dbname = "farm";     // Database Name
$port = 3307;             // Custom MySQL Port

// ✅ Create Database Connection with Port
$conn = new mysqli($host, $user, $pass, $dbname, $port);

// ✅ Check Connection
if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}

// ✅ Set character set to UTF-8
$conn->set_charset("utf8");
?>
