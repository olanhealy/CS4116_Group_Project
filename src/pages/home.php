<?php
session_start();

if(isset($_SESSION['id']) && isset($_SESSION['email'])){
    include_once("home.html");
} 
else {
    header("Location: index.php");
    exit();
}
?>