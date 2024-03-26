<?php

//get session info
session_start();

include "../db_connection.php";
include_once "adminHelperFunctions.php";

// Check if the user is logged in
if (isset ($_SESSION['id'])) {
    // Retrieve the user ID
    $user_id = $_SESSION['id'];
    //Checks for post request
    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        //set variables
        $reason = $_POST['reason'];
        $dateOfUnban = $_POST['dateOfUnban'];

        if (isset($_SESSION['target_id'])) {
            $target_id = $_SESSION['target_id'];
        } else {
            echo "Target ID is not set.";
            exit();
        }

        //create ban
        $sql_insert = "INSERT INTO banned (user_id, banned_by, reason, dateOfUnban) VALUES (?, ?, ?, ?)";
        $insert_new_banned = $conn->prepare($sql_insert);
        $insert_new_banned->bind_param('ssss', $target_id, $user_id, $reason, $dateOfUnban);
        $insert_new_banned->execute();

        //call setBanned from adminHelperFunctions.php
        setBanned("$target_id", 1);
        echo "$target_id banned successfully"; 
        // Redirect to userListAdmin.php
        header("Location: usersListAdmin.php");
        exit();
    } else {
        echo "No request made";
    }
} else {
    // Redirect the user to the login page or display an error message
    echo "User is not logged in.";
}