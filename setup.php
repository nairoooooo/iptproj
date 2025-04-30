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
    echo "Attendance table created successfully or already exists<br>";
} else {
    echo "Error creating attendance table: " . mysqli_error($conn) . "<br>";
}

// Create Teachers Table with profile_image field
$sql = "CREATE TABLE IF NOT EXISTS teachers (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(20) NOT NULL,
    password VARCHAR(255) NOT NULL,
    profile_image VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if (mysqli_query($conn, $sql)) {
    echo "Teachers table created successfully or already exists<br>";
} else {
    echo "Error creating teachers table: " . mysqli_error($conn) . "<br>";
}

// Add profile_image column if it doesn't exist
$result = mysqli_query($conn, "SHOW COLUMNS FROM teachers LIKE 'profile_image'");
if (mysqli_num_rows($result) == 0) {
    $sql = "ALTER TABLE teachers ADD COLUMN profile_image VARCHAR(255) DEFAULT NULL";
    if (mysqli_query($conn, $sql)) {
        echo "Profile image column added to teachers table<br>";
    } else {
        echo "Error adding profile image column: " . mysqli_error($conn) . "<br>";
    }
}

// Create Students Table
$sql = "CREATE TABLE IF NOT EXISTS students (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(20) NOT NULL,
    course VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if (mysqli_query($conn, $sql)) {
    echo "Students table created successfully or already exists<br>";
} else {
    echo "Error creating students table: " . mysqli_error($conn) . "<br>";
}

// Create a default teacher account if none exists
$sql = "SELECT COUNT(*) AS teacher_count FROM teachers";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);

if ($row['teacher_count'] == 0) {
    // Create default teacher: teacher@example.com / password123
    $default_name = "Default Teacher";
    $default_email = "teacher@example.com";
    $default_phone = "+123456789";
    $default_password = password_hash("password123", PASSWORD_DEFAULT);
    
    $sql = "INSERT INTO teachers (name, email, phone, password) 
            VALUES ('$default_name', '$default_email', '$default_phone', '$default_password')";
    
    if (mysqli_query($conn, $sql)) {
        echo "Default teacher account created: teacher@example.com / password123<br>";
    } else {
        echo "Error creating default teacher account: " . mysqli_error($conn) . "<br>";
    }
}

// Create a default student account if none exists
$sql = "SELECT COUNT(*) AS student_count FROM students";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);

if ($row['student_count'] == 0) {
    // Create default student: student@example.com / password123
    $default_name = "Default Student";
    $default_email = "student@example.com";
    $default_phone = "+123456789";
    $default_course = "Computer Science";
    $default_password = password_hash("password123", PASSWORD_DEFAULT);
    
    $sql = "INSERT INTO students (name, email, phone, course, password) 
            VALUES ('$default_name', '$default_email', '$default_phone', '$default_course', '$default_password')";
    
    if (mysqli_query($conn, $sql)) {
        echo "Default student account created: student@example.com / password123<br>";
    } else {
        echo "Error creating default student account: " . mysqli_error($conn) . "<br>";
    }
}

// Create uploads directory if it doesn't exist
if (!file_exists('uploads')) {
    if (mkdir('uploads', 0777, true)) {
        echo "Uploads directory created successfully<br>";
    } else {
        echo "Error creating uploads directory<br>";
    }
}

echo "<br><a href='login.php'>Go to Login Page</a>";
?>