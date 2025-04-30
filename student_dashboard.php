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

// Process QR code scan submission
$scan_message = "";
if(isset($_POST['submit_attendance'])) {
    $qr_data = $_POST['qr_data'];
    // Validate QR data format (should be a valid token or session ID)
    if(!empty($qr_data)) {
        // Get student information
        $student_id = $_SESSION['user_id'];
        $sql = "SELECT * FROM students WHERE id = $student_id";
        $result = mysqli_query($conn, $sql);
        $student = mysqli_fetch_assoc($result);
        
        // Insert attendance record
        $name = mysqli_real_escape_string($conn, $student['name']);
        $email = mysqli_real_escape_string($conn, $student['email']);
        $course = mysqli_real_escape_string($conn, $student['course']);
        
        $sql = "INSERT INTO attendance (name, email, course) VALUES ('$name', '$email', '$course')";
        
        if(mysqli_query($conn, $sql)) {
            $scan_message = "Attendance successfully recorded!";
        } else {
            $scan_message = "Error recording attendance: " . mysqli_error($conn);
        }
    } else {
        $scan_message = "Invalid QR code data.";
    }
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
    <link rel="stylesheet" href="student_dashboard.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.4/html5-qrcode.min.js"></script>
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <h1>NAIRO</h1>
            <ul>
                <li class="active"><a href="student_dashboard.php">DASHBOARD</a></li>
                <li><a href="#" onclick="toggleQrScanner()">QR SCANNER</a></li>
                <li><a href="student_profile.php">PROFILE</a></li>
                <li class="logout"><a href="logout.php">LOGOUT</a></li>
            </ul>
        </div>
        
        <div class="main-content">
            <div class="welcome-box">
                <h2>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h2>
                <p>Course: <?php echo htmlspecialchars($student['course']); ?></p>
            </div>
            
            <?php if(!empty($scan_message)): ?>
                <div class="<?php echo strpos($scan_message, 'successfully') !== false ? 'success-message' : 'error-message'; ?>">
                    <?php echo $scan_message; ?>
                </div>
            <?php endif; ?>
            
            <!-- QR Scanner Section -->
            <div id="qr-section" class="qr-section">
                <h3>Scan Attendance QR Code</h3>
                <p>Please scan the QR code provided by your teacher to mark your attendance</p>
                
                <div id="qr-reader"></div>
                
                <div class="manual-entry">
                    <p>Or enter the code manually:</p>
                    <form method="post" action="">
                        <input type="text" name="qr_data" placeholder="Enter QR code data" required>
                        <button type="submit" name="submit_attendance">Submit</button>
                    </form>
                </div>
            </div>
            
            <!-- Regular Dashboard Content -->
            <div id="dashboard-content">
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
    </div>
    
    <script>
        function toggleQrScanner() {
            const qrSection = document.getElementById('qr-section');
            const dashboardContent = document.getElementById('dashboard-content');
            
            if (qrSection.classList.contains('active')) {
                qrSection.classList.remove('active');
                dashboardContent.style.display = 'block';
                // Stop QR scanning if it's running
                if (window.html5QrCode) {
                    window.html5QrCode.stop();
                }
            } else {
                qrSection.classList.add('active');
                dashboardContent.style.display = 'none';
                startQrScanner();
            }
        }
        
        function startQrScanner() {
            const html5QrCode = new Html5Qrcode("qr-reader");
            window.html5QrCode = html5QrCode;
            
            const qrCodeSuccessCallback = (decodedText, decodedResult) => {
                console.log(`QR Code detected: ${decodedText}`);
                html5QrCode.stop();
                
                // Submit the form with the QR code data
                document.querySelector('input[name="qr_data"]').value = decodedText;
                document.querySelector('button[name="submit_attendance"]').click();
            };
            
            const config = { fps: 10, qrbox: { width: 250, height: 250 } };
            
            html5QrCode.start({ facingMode: "environment" }, config, qrCodeSuccessCallback);
        }
    </script>
</body>
</html>