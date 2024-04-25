<?php
// Include necessary files and functions
include 'db_connection.php';
include 'helperFunctions.php';
include "admin/adminHelperFunctions.php";

accessCheck();

$userId = $_SESSION['user_id'];

// Call the setVerified function to update verification status
setVerified($userId, 1);
?>