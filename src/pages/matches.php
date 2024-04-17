<?php

include "db_connection.php";
require_once 'helperFunctions.php';

if(session_status() === PHP_SESSION_NONE) {
    session_start();
}

$userId = $_SESSION['user_id'];

//sets up the header and dropdown
setupHeader();

//include 'outline.html';

// Get all matches for the user
getAllMatches($userId);

// If the user wants to remove a match
if (isset($_POST['action']) && $_POST['action'] === 'removeMatch') {
    removeMatch($_POST['userId'], $_POST['targetId']);
    header("Location: matches.php");
}

//set up the footer
setupFooter();