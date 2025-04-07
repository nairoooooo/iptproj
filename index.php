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
if (!mysqli_query($conn, $sql)) {
    echo "Error creating database: " . mysqli_error($conn);
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

if (!mysqli_query($conn, $sql)) {
    echo "Error creating table: " . mysqli_error($conn);
}

$submit_success = false;
$student_name = "";

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $course = mysqli_real_escape_string($conn, $_POST['course']);
    
    $sql = "INSERT INTO attendance (name, email, course) VALUES ('$student_name', '$email', '$course')";
    
    if (mysqli_query($conn, $sql)) {
        $submit_success = true;
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SAMS - Student Attendance</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }
        body {
            background-color: #a73535;
            color: white;
            min-height: 100vh;
        }
        .container {
            padding: 20px;
            max-width: 100%;
            margin: 0 auto;
        }
        .status-bar {
            display: flex;
            justify-content: space-between;
            padding: 5px 10px;
            background-color: white;
            color: black;
            font-size: 12px;
        }
        .status-bar-left, .status-bar-right {
            display: flex;
            align-items: center;
        }
        .signal, .wifi, .battery {
            margin-left: 5px;
        }
        h1 {
            text-align: center;
            margin: 40px 0;
            font-size: 24px;
            border-bottom: 2px solid white;
            padding-bottom: 10px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 14px;
        }
        input, select {
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 4px;
            background-color: white;
        }
        .submit-btn {
            background-color: white;
            color: #a73535;
            border: none;
            padding: 8px 20px;
            border-radius: 4px;
            font-weight: bold;
            float: right;
            cursor: pointer;
        }
        .success-page {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 80vh;
            text-align: center;
        }
        .checkmark {
            width: 80px;
            height: 80px;
            margin-bottom: 30px;
        }
        .checkmark .circle {
            stroke-dasharray: 166;
            stroke-dashoffset: 166;
            stroke-width: 2;
            stroke: white;
            fill: none;
            animation: stroke 0.6s cubic-bezier(0.65, 0, 0.45, 1) forwards;
        }
        .checkmark .check {
            stroke-dasharray: 48;
            stroke-dashoffset: 48;
            stroke-width: 3;
            stroke: white;
            fill: none;
            animation: stroke 0.3s cubic-bezier(0.65, 0, 0.45, 1) 0.8s forwards;
        }
        @keyframes stroke {
            100% {
                stroke-dashoffset: 0;
            }
        }
        .success-message {
            font-size: 24px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    

    <div class="container">
        <?php if (!$submit_success): ?>
            <!-- Form Page -->
            <h1>WELCOME TO SAMS</h1>
            
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <div class="form-group">
                    <label for="name">Full Name :</label>
                    <input type="text" id="name" name="name" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email :</label>
                    <input type="email" id="email" name="email" required>
                </div>
                
                <div class="form-group">
                    <label for="course">Course :</label>
                    <input type="text" id="course" name="course" required>
                </div>
                
                <button type="submit" class="submit-btn">SUBMIT</button>
            </form>
            
        <?php else: ?>
            <!-- Success Page -->
            <div class="success-page">
                <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
                    <circle class="circle" cx="26" cy="26" r="25" fill="none"/>
                    <path class="check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
                </svg>
                
                <div class="success-message">GOODJOB "<?php echo htmlspecialchars($student_name); ?>"</div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>