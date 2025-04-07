<?php
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

// Demo admin data - in a real application, this would come from authentication
$admin_name = "DENMARK ELPA";
$admin_phone = "+6396 2566 7525";
$admin_email = "denmark@univ.edu";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Profile - Attendance System</title>
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
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        .profile-container {
            text-align: center;
        }
        .profile-pic {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background-color: #000;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .profile-pic svg {
            width: 60px;
            height: 60px;
            color: white;
        }
        .admin-info {
            margin-bottom: 30px;
        }
        .admin-info h3 {
            font-size: 24px;
            margin-bottom: 20px;
        }
        .admin-info p {
            margin: 10px 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .admin-info svg {
            margin-left: 10px;
            width: 18px;
            height: 18px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <h1>NAIRO</h1>
            <ul>
                <li><a href="dashboard.php">DASHBOARD</a></li>
                <li class="active"><a href="profile.php">PROFILE</a></li>
            </ul>
        </div>
        <div class="main-content">
            <h2>HELLO DENMARK !</h2>
            
            <div class="profile-container">
                <div class="profile-pic">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                
                <div class="admin-info">
                    <h3>DENMARK ELPA</h3>
                    <p>
                        <?php echo $admin_phone; ?>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                        </svg>
                    </p>
                    <p>
                        <?php echo $admin_email; ?>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                        </svg>
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>