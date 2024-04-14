<?php

include_once "db_connection.php";
include_once "helperFunctions.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_POST['action'] == "report_user") {
    $targetId = $_POST['target_user_id'];
    $userId = $_SESSION['user_id'];

    // Update the report in the Accounts table
    $query = "UPDATE account SET number_of_reports = number_of_reports + 1 WHERE user_id = ?";
    $set_report = $conn->prepare($query);
    $set_report->bind_param('i', $targetId); // Assuming $user_id is already defined
    $set_report->execute();

    //check if the user has been reported successfully
    if ($set_report->affected_rows > 0) {
        echo "User has been reported successfully.";
    } else {
        echo "User could not be reported.";
    }

    $set_report->close();

    removeMatch($userId, $targetId);
}