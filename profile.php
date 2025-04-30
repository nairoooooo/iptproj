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

// Process form submission
$message = '';
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_profile'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    
    $profile_image = '';
    
    // Handle file upload if a new image was submitted
    if(isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
        $allowed = array('jpg', 'jpeg', 'png', 'gif');
        $filename = $_FILES['profile_image']['name'];
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        
        if(in_array(strtolower($ext), $allowed)) {
            $new_filename = 'profile_' . $_SESSION['user_id'] . '_' . time() . '.' . $ext;
            $upload_path = 'uploads/' . $new_filename;
            
            // Create uploads directory if it doesn't exist
            if (!file_exists('uploads')) {
                mkdir('uploads', 0777, true);
            }
            
            if(move_uploaded_file($_FILES['profile_image']['tmp_name'], $upload_path)) {
                $profile_image = $upload_path;
            }
        }
    }
    
    // Update teacher information
    $teacher_id = $_SESSION['user_id'];
    
    if($profile_image != '') {
        $sql = "UPDATE teachers SET name='$name', phone='$phone', email='$email', profile_image='$profile_image' WHERE id=$teacher_id";
    } else {
        $sql = "UPDATE teachers SET name='$name', phone='$phone', email='$email' WHERE id=$teacher_id";
    }
    
    if(mysqli_query($conn, $sql)) {
        $message = '<div class="alert success">Profile updated successfully!</div>';
    } else {
        $message = '<div class="alert error">Error updating profile: ' . mysqli_error($conn) . '</div>';
    }
}

// Get teacher information from database based on logged-in user ID
$teacher_id = $_SESSION['user_id'];
$sql = "SELECT * FROM teachers WHERE id = $teacher_id";
$result = mysqli_query($conn, $sql);
$teacher = mysqli_fetch_assoc($result);

// If teacher data couldn't be found, use session data as fallback
if (!$teacher) {
    $teacher = [
        'name' => $_SESSION['user_name'] ?? 'Unknown Teacher',
        'phone' => '',
        'email' => '',
        'profile_image' => ''
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/profile.css">
    <link rel="stylesheet" href="css/logout_modal.css">
    <title>Teacher Profile - Attendance System</title>
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <h1>NAIRO</h1>
            <ul>
                <li><a href="dashboard.php">DASHBOARD</a></li>
                <li class="active"><a href="profile.php">PROFILE</a></li>
                <li class="logout"><a href="logout.php?confirm=yes">LOGOUT</a></li>
            </ul>
        </div>
        <div class="main-content">
            <h2>HELLO <?php echo strtoupper(explode(' ', $teacher['name'])[0]); ?> !</h2>
            
            <?php echo $message; ?>
            
            <form method="post" action="profile.php" enctype="multipart/form-data">
                <div class="profile-container">
                    <div class="profile-pic">
                        <?php if(!empty($teacher['profile_image']) && file_exists($teacher['profile_image'])): ?>
                            <img src="<?php echo htmlspecialchars($teacher['profile_image']); ?>" alt="Profile Picture">
                        <?php else: ?>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        <?php endif; ?>
                        <div class="change-photo" onclick="document.getElementById('profile_image').click()">
                            Change Photo
                        </div>
                        <input type="file" id="profile_image" name="profile_image" class="file-input" accept="image/*">
                        <div id="photoError"></div>
                    </div>
                    
                    <div class="admin-info">
                        <div class="input-group">
                            <label for="name">Full Name</label>
                            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($teacher['name']); ?>" required>
                            <svg class="edit-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                            </svg>
                        </div>
                        
                        <div class="input-group">
                            <label for="phone">Phone Number</label>
                            <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($teacher['phone'] ?? ''); ?>">
                            <svg class="edit-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                            </svg>
                        </div>
                        
                        <div class="input-group">
                            <label for="email">Email Address</label>
                            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($teacher['email'] ?? ''); ?>">
                            <svg class="edit-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                            </svg>
                        </div>
                        
                        <div class="buttons">
                            <button type="submit" name="update_profile" class="btn btn-save">Save Changes</button>
                            <a href="dashboard.php" class="btn btn-cancel">Cancel</a>
                        </div>
                    </div>
                </div>
            </form>
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
    <script src="js/profile.js"></script>
</body>
</html>