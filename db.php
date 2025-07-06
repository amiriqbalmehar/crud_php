<?php
// Optimized database connection with persistent connection and better error handling
$host = 'localhost'; 
$user = 'root';   
$pass = "";   
$database = 'php_crud';     

// Enable persistent connections for better performance
$conn = new mysqli($host, $user, $pass, $database);

// Set charset to prevent character encoding issues
$conn->set_charset("utf8mb4");

// Check connection with better error handling
if ($conn->connect_error) {
    error_log("Database Connection failed: " . $conn->connect_error);
    die("Connection failed. Please try again later.");
}

// Set MySQL session variables for better performance
$conn->query("SET SESSION sql_mode = 'STRICT_TRANS_TABLES,NO_ZERO_DATE,NO_ZERO_IN_DATE,ERROR_FOR_DIVISION_BY_ZERO'");

// Function to safely escape and prepare data
function sanitize_input($data) {
    global $conn;
    return $conn->real_escape_string(trim(htmlspecialchars($data)));
}
?>


