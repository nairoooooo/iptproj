<?php
session_start();

// Check if user is logged in and is a student
if(!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'student') {
    header("Location: login.php");
    exit;
}

// Database Configuration
$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "student_attendance";

// Connect to Database
$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Get student information
$student_id = $_SESSION['user_id'];
$sql = "SELECT * FROM students WHERE id = $student_id";
$result = mysqli_query($conn, $sql);
$student = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - Attendance System</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .container {
            display: flex;
            min-height: 100vh;
        }
        .sidebar {
            width: 250px;
            background-color: #222;
            color: #fff;
            padding: 20px 0;
        }
        .sidebar h1 {
            color: #e74c3c;
            text-align: center;
            margin-bottom: 40px;
        }
        .sidebar ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .sidebar li {
            padding: 15px 30px;
        }
        .sidebar li.active {
            background-color: #333;
            border-left: 4px solid #e74c3c;
        }
        .sidebar a {
            color: #fff;
            text-decoration: none;
        }
        .sidebar .logout {
            margin-top: 40px;
            border-top: 1px solid #444;
            padding-top: 15px;
        }
        .sidebar .logout a {
            color: #e74c3c;
            display: flex;
            align-items: center;
        }
        .sidebar .logout a:before {
            content: '↩';
            margin-right: 10px;
            font-size: 18px;
        }
        .main-content {
            flex: 1;
            background-color: #a73535;
            padding: 20px;
            color: white;
        }
        .welcome-box {
            background-color: rgba(0, 0, 0, 0.2);
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        .welcome-box h2 {
            margin-top: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: rgba(255, 255, 255, 0.1);
        }
        table th, table td {    
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }
        table th {
            background-color: rgba(0, 0, 0, 0.2);
        }
        .attendance-summary {
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
        }
        .summary-card {
            flex: 1;
            background-color: rgba(0, 0, 0, 0.2);
            padding: 20px;
            border-radius: 8px;
            text-align: center;
        }
        .summary-card h3 {
            margin-top: 0;
            margin-bottom: 10px;
        }
        .summary-card .count {
            font-size: 32px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <h1>NAIRO</h1>
            <ul>
                <li class="active"><a href="student_dashboard.php">DASHBOARD</a></li>
                <li><a href="student_profile.php">PROFILE</a></li>
                <li class="logout"><a href="logout.php">LOGOUT</a></li>
            </ul>
        </div>
        
        <div class="main-content">
            <div class="welcome-box">
                <h2>Welcome, <?php echo htmlspecialchars($student['name']); ?>!</h2>
                <p>Course: <?php echo htmlspecialchars($student['course']); ?></p>
            </div>
            
            <h3>Your Attendance History</h3>
            
            <?php
            // Get attendance stats
            $sql = "SELECT COUNT(*) as total FROM attendance WHERE email = '" . $student['email'] . "'";
            $result = mysqli_query($conn, $sql);
            $attendance_count = mysqli_fetch_assoc($result)['total'];
            ?>
            
            <div class="attendance-summary">
                <div class="summary-card">
                    <h3>Total Attendance</h3>
                    <div class="count"><?php echo $attendance_count; ?></div>
                </div>
                <div class="summary-card">
                    <h3>This Month</h3>
                    <?php
                    $sql = "SELECT COUNT(*) as monthly FROM attendance 
                            WHERE email = '" . $student['email'] . "' 
                            AND MONTH(time_in) = MONTH(CURRENT_DATE()) 
                            AND YEAR(time_in) = YEAR(CURRENT_DATE())";
                    $result = mysqli_query($conn, $sql);
                    $monthly_count = mysqli_fetch_assoc($result)['monthly'];
                    ?>
                    <div class="count"><?php echo $monthly_count; ?></div>
                </div>
                <div class="summary-card">
                    <h3>This Week</h3>
                    <?php
                    $sql = "SELECT COUNT(*) as weekly FROM attendance 
                            WHERE email = '" . $student['email'] . "' 
                            AND WEEK(time_in) = WEEK(CURRENT_DATE()) 
                            AND YEAR(time_in) = YEAR(CURRENT_DATE())";
                    $result = mysqli_query($conn, $sql);
                    $weekly_count = mysqli_fetch_assoc($result)['weekly'];
                    ?>
                    <div class="count"><?php echo $weekly_count; ?></div>
                </div>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>DATE</th>
                        <th>TIME</th>
                        <th>COURSE</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT * FROM attendance 
                            WHERE email = '" . $student['email'] . "' 
                            ORDER BY time_in DESC";
                    $result = mysqli_query($conn, $sql);
                    $count = 1;
                    
                    if (mysqli_num_rows($result) > 0) {
                        while($row = mysqli_fetch_assoc($result)) {
                            $time_in = new DateTime($row['time_in']);
                            
                            echo "<tr>";
                            echo "<td>" . $count . "</td>";
                            echo "<td>" . $time_in->format('Y-m-d') . "</td>";
                            echo "<td>" . $time_in->format('H:i:s') . "</td>";
                            echo "<td>" . $row['course'] . "</td>";
                            echo "</tr>";
                            $count++;
                        }
                    } else {
                        echo "<tr><td colspan='4'>No attendance records found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>