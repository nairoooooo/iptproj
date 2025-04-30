<?php
session_start();

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

// Check if user is already logged in
if(isset($_SESSION['user_id'])) {
    if($_SESSION['user_role'] == 'teacher') {
        header("Location: dashboard.php");
    } else {
        header("Location: student_dashboard.php");
    }
    exit;
}

$error = "";

// Process login form
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $role = $_POST['role'];
    
    // Get user from database based on role
    $table = ($role == 'teacher') ? 'teachers' : 'students';
    $sql = "SELECT * FROM $table WHERE email = '$email'";
    $result = mysqli_query($conn, $sql);
    
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        
        // Verify password
        if (password_verify($password, $row['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['user_name'] = $row['name'];
            $_SESSION['user_email'] = $row['email'];
            $_SESSION['user_role'] = $role;
            
            // Redirect based on role
            if ($role == 'teacher') {
                header("Location: dashboard.php");
            } else {
                header("Location: student_dashboard.php");
            }
            exit;
        } else {
            $error = "Invalid password";
        }
    } else {
        $error = "User not found";
    }
}

// Process registration form
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    $name = mysqli_real_escape_string($conn, $_POST['reg_name']);
    $email = mysqli_real_escape_string($conn, $_POST['reg_email']);
    $phone = mysqli_real_escape_string($conn, $_POST['reg_phone']);
    $password = $_POST['reg_password'];
    $confirm_password = $_POST['confirm_password'];
    $role = $_POST['reg_role'];
    
    // Validate input
    if ($password != $confirm_password) {
        $error = "Passwords do not match";
    } else {
        // Check if email already exists in respective table
        $table = ($role == 'teacher') ? 'teachers' : 'students';
        $sql = "SELECT * FROM $table WHERE email = '$email'";
        $result = mysqli_query($conn, $sql);
        
        if (mysqli_num_rows($result) > 0) {
            $error = "Email already exists";
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Add course field for students
            $course_field = "";
            $course_value = "";
            if ($role == 'student') {
                $course = mysqli_real_escape_string($conn, $_POST['reg_course']);
                $course_field = ", course";
                $course_value = ", '$course'";
            }
            
            // Insert new user
            $sql = "INSERT INTO $table (name, email, phone, password$course_field) 
                    VALUES ('$name', '$email', '$phone', '$hashed_password'$course_value)";
            
            if (mysqli_query($conn, $sql)) {
                // Set session variables
                $_SESSION['user_id'] = mysqli_insert_id($conn);
                $_SESSION['user_name'] = $name;
                $_SESSION['user_email'] = $email;
                $_SESSION['user_role'] = $role;
                
                // Redirect based on role
                if ($role == 'teacher') {
                    header("Location: dashboard.php");
                } else {
                    header("Location: student_dashboard.php");
                }
                exit;
            } else {
                $error = "Registration failed: " . mysqli_error($conn);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/login.css">
    <title>Login - Student Attendance System</title>
 
</head>
<body>
    <div class="container">
        <h1>NAIRO</h1>
        
        <?php if (!empty($error)): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <div class="form-container">
            <div class="tabs">
                <div class="tab active" onclick="switchTab('login')">Login</div>
                <div class="tab" onclick="switchTab('register')">Register</div>
            </div>
            
            <div id="login" class="tab-content active">
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <div class="role-toggle">
                        <div class="role-option active" onclick="selectRole('teacher', this)">Teacher</div>
                        <div class="role-option" onclick="selectRole('student', this)">Student</div>
                        <input type="hidden" id="role" name="role" value="teacher">
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    
                    <button type="submit" name="login" class="btn">Login</button>
                </form>
            </div>
            
            <div id="register" class="tab-content">
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <div class="role-toggle">
                        <div class="role-option active" onclick="selectRegRole('teacher', this)">Teacher</div>
                        <div class="role-option" onclick="selectRegRole('student', this)">Student</div>
                        <input type="hidden" id="reg_role" name="reg_role" value="teacher">
                    </div>
                    
                    <div class="form-group">
                        <label for="reg_name">Full Name</label>
                        <input type="text" id="reg_name" name="reg_name" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="reg_email">Email</label>
                        <input type="email" id="reg_email" name="reg_email" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="reg_phone">Phone</label>
                        <input type="text" id="reg_phone" name="reg_phone" required>
                    </div>
                    
                    <div class="form-group student-fields" id="course-field">
                        <label for="reg_course">Course</label>
                        <input type="text" id="reg_course" name="reg_course">
                    </div>
                    
                    <div class="form-group">
                        <label for="reg_password">Password</label>
                        <input type="password" id="reg_password" name="reg_password" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password">Confirm Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" required>
                    </div>
                    
                    <button type="submit" name="register" class="btn">Register</button>
                </form>
            </div>
        </div>
    </div>
    
    <script>
        function switchTab(tab) {
            // Hide all tab contents
            var tabContents = document.getElementsByClassName('tab-content');
            for (var i = 0; i < tabContents.length; i++) {
                tabContents[i].classList.remove('active');
            }
            
            // Deactivate all tabs
            var tabs = document.getElementsByClassName('tab');
            for (var i = 0; i < tabs.length; i++) {
                tabs[i].classList.remove('active');
            }
            
            // Activate selected tab and content
            document.getElementById(tab).classList.add('active');
            event.currentTarget.classList.add('active');
        }
        
        function selectRole(role, element) {
            // Update hidden input
            document.getElementById('role').value = role;
            
            // Update UI
            var options = document.querySelectorAll('#login .role-option');
            options.forEach(function(option) {
                option.classList.remove('active');
            });
            element.classList.add('active');
        }
        
        function selectRegRole(role, element) {
            // Update hidden input
            document.getElementById('reg_role').value = role;
            
            // Update UI
            var options = document.querySelectorAll('#register .role-option');
            options.forEach(function(option) {
                option.classList.remove('active');
            });
            element.classList.add('active');
            
            // Show/hide student-specific fields
            var studentFields = document.querySelectorAll('.student-fields');
            studentFields.forEach(function(field) {
                if (role === 'student') {
                    field.style.display = 'block';
                    field.querySelector('input').required = true;
                } else {
                    field.style.display = 'none';
                    field.querySelector('input').required = false;
                }
            });
        }
    </script>
</body>
</html>