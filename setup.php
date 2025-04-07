<?php
// Database Configuration
$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "student_attendance";

// Connect to Database
$conn = mysqli_connect($db_host, $db_user, $db_pass);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Create Database if it doesn't exist
$sql = "CREATE DATABASE IF NOT EXISTS $db_name";
if (mysqli_query($conn, $sql)) {
    echo "Database created successfully or already exists<br>";
} else {
    echo "Error creating database: " . mysqli_error($conn) . "<br>";
}

// Select Database
mysqli_select_db($conn, $db_name);

// Create Attendance Table
$sql = "CREATE TABLE IF NOT EXISTS attendance (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    course VARCHAR(50) NOT NULL,
    time_in TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if (mysqli_query($conn, $sql)) {
    echo "Table created successfully or already exists<br>";
} else {
    echo "Error creating table: " . mysqli_error($conn) . "<br>";
}

// Just for initial setup, remove in production
echo "<a href='index.php'>Go to Main System</a>";
?>