<?php

if(session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in using the session variable
if(isset($_SESSION['user_id']) && isset($_SESSION['email'])){
    include_once("home.html");
} 
else {
    header("Location: index.php");
    exit();
}
?>