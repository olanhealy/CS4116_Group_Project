<?php


include "db_connection.php";
require_once 'helper.php';

if(session_status() === PHP_SESSION_NONE) {
    session_start();
}

$userId = $_SESSION['id'];

?>

<a href="home.php">Home</a>
<br>
<?php
getAllMatches($userId);

if (isset($_POST['action']) && $_POST['action'] === 'removeMatch') {
    removeMatch($_POST['userId'], $_POST['targetId']);
    header("Location: matches.php");
}

?>