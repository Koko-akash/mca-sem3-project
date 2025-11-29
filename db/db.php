<?php
// DATABASE CONFIG (change only if needed)
$host = "localhost";
$username = "root";   // default XAMPP user
$password = "";       // default empty
$dbname = "study_predictor";

// Create connection
$conn = mysqli_connect($host, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("💥 Database Connection Failed: " . mysqli_connect_error());
}

// OPTIONAL: Set charset for better compatibility
mysqli_set_charset($conn, "utf8");

?>
