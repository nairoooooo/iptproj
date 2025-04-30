<?php
// Start the session
session_start();

// Check if action is confirmed
if(isset($_GET['confirm']) && $_GET['confirm'] == 'yes') {
    // Unset all session variables
    $_SESSION = array();
    
    // Destroy the session
    session_destroy();
    
    // Redirect to login page
    header("Location: login.php");
    exit;
}

// If we reach here without the confirm=yes parameter, redirect to dashboard
header("Location: dashboard.php");
exit;
?>