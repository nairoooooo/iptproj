<?php
session_start();

// Check if user is logged in and is a teacher
if(!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'teacher') {
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

// Use the custom QR code image provided by you
$qr_code_image = "attendanceqr.png";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SAMS - Admin Dashboard</title>
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
        .main-content {
            flex: 1;
            background-color: #a73535;
            padding: 20px;
            color: white;
        }
        .qr-container {
            text-align: center;
            margin-top: 30px;
            margin-bottom: 30px;
            background: white;
            padding: 20px;
            border-radius: 10px;
            display: inline-block;
        }
        .download-btn {
            background-color: #3498db;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            text-decoration: none;
            display: inline-block;
            margin-top: 15px;
            cursor: pointer;
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
        .refresh-section {
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .refresh-btn {
            background-color: #27ae60;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <h1>NAIRO</h1>
            <ul>
                <li class="active"><a href="dashboard.php">DASHBOARD</a></li>
                <li><a href="profile.php">PROFILE</a></li>
                <li class="logout"><a href="logout.php">LOGOUT</a></li>
            </ul>
        </div>
        <div class="main-content">
            <h2>DASHBOARD</h2>
            
            <div class="qr-container">
                <h3>Student Attendance QR Code</h3>
                <p>Students can scan this QR code to access the attendance form</p>
                <img src="<?php echo $qr_code_image; ?>" alt="Attendance QR Code" width="200">
                <div>
                    <a href="<?php echo $qr_code_image; ?>" download class="download-btn">Download QR Code</a>
                </div>
            </div>
            
            <div class="refresh-section">
                <h3>Recent Attendance</h3>
                <a href="dashboard.php" class="refresh-btn">Refresh Data</a>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>NAME</th>
                        <th>EMAIL</th>
                        <th>COURSE</th>
                        <th>TIME IN</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT * FROM attendance ORDER BY time_in DESC";
                    $result = mysqli_query($conn, $sql);
                    $count = 1;
                    
                    if (mysqli_num_rows($result) > 0) {
                        while($row = mysqli_fetch_assoc($result)) {
                            echo "<tr>";
                            echo "<td>" . $count . "</td>";
                            echo "<td>" . $row['name'] . "</td>";
                            echo "<td>" . $row['email'] . "</td>";
                            echo "<td>" . $row['course'] . "</td>";
                            echo "<td>" . $row['time_in'] . "</td>";
                            echo "</tr>";
                            $count++;
                        }
                    } else {
                        echo "<tr><td colspan='5'>No attendance records found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>