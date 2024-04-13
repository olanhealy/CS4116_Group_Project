<?php
// Include necessary files and functions
include 'db_connection.php';
include 'helperFunctions.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$userId = $_SESSION['user_id'];

// Call the setVerified function to update verification status
setVerified($userId, 1);
?>