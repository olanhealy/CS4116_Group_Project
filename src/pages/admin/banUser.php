<?php

include "../helpers/db_connection.php";
include_once "adminHelperFunctions.php";
include "../helpers/helperFunctions.php";

adminAccessCheck();


// Retrieve the user ID
$userId = $_SESSION['user_id'];

//Checks for post request
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    //set variables
    $reason = $_POST['reason'];
    $dateOfUnban = $_POST['dateOfUnban'];
    $permaBan = $_POST['permaBan'];

    if ($permaBan == "1") {
        $dateOfUnban = "0000-00-00";
    }

    if (isset($_SESSION['targetId'])) {
        $targetId = $_SESSION['targetId'];
    } else {
        echo "Target ID is not set.";
        exit();
    }

    //create ban
    $sqlInsert = "INSERT INTO banned (user_id, banned_by, reason, dateOfUnban) VALUES (?, ?, ?, ?)";
    $insertNewBanned = $conn->prepare($sqlInsert);
    $insertNewBanned->bind_param('ssss', $targetId, $userId, $reason, $dateOfUnban);
    $insertNewBanned->execute();

    //call setBanned from adminHelperFunctions.php
    setBanned("$targetId", 1);
    echo "$targetId banned successfully";
    // Redirect to userListAdmin.php
    header("Location: usersListAdmin.php");
    exit();
} else {
    echo "No request made";
}
?>