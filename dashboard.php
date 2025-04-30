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

// Get teacher information
$teacher_id = $_SESSION['user_id'];
$sql = "SELECT * FROM teachers WHERE id = $teacher_id";
$result = mysqli_query($conn, $sql);
$teacher = mysqli_fetch_assoc($result);

// If teacher data couldn't be found, use session data as fallback
if (!$teacher) {
    $teacher = [
        'name' => $_SESSION['user_name'] ?? 'Unknown Teacher',
        'phone' => '',
        'email' => ''
    ];
}

// Use the custom QR code image provided by you
$qr_code_image = "attendanceqr.png";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/logout_modal.css">
    <title>SAMS - Teacher Dashboard</title>
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <h1>NAIRO</h1>
            <ul>
                <li class="active"><a href="dashboard.php">DASHBOARD</a></li>
                <li><a href="profile.php">PROFILE</a></li>
                <li class="logout"><a href="logout.php?confirm=yes">LOGOUT</a></li>
            </ul>
        </div>
        <div class="main-content">
            <div class="welcome-box">
                <h2>Welcome, <?php echo htmlspecialchars($teacher['name']); ?>!</h2>
                <p>Teacher Dashboard</p>
            </div>
            
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
                            echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['course']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['time_in']) . "</td>";
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
    
    <!-- Logout Modal -->
    <div id="logoutModal" class="modal">
        <div class="modal-content">
            <h2>Logout Confirmation</h2>
            <p>Are you sure you want to log out?</p>
            <div class="logout-actions">
                <a href="logout.php?confirm=yes" class="btn btn-yes">Yes, Log out</a>
                <a href="#" class="btn btn-no">No, Stay logged in</a>
            </div>
        </div>
    </div>
    
    <script src="js/logout.js"></script>
</body>
</html>