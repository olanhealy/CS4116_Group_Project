<?php

include_once "../helpers/db_connection.php";
include_once "../helpers/helperFunctions.php";
include "../admin/adminHelperFunctions.php";

accessCheck();

if ($_POST['action'] == "report_user") {
    $targetId = $_POST['target_user_id'];
    $userId = $_SESSION['user_id'];

    // Update the report in the Accounts table
    $query = "UPDATE account SET number_of_reports = number_of_reports + 1 WHERE user_id = ?";
    $set_report = $conn->prepare($query);
    $set_report->bind_param('i', $targetId); // Assuming $user_id is already defined
    $set_report->execute();

    // Check if the user has been reported successfully
    if ($set_report->affected_rows > 0) {
        // User reported successfully, show success message in a popup
        echo "<script>alert('User has been reported successfully.');</script>";
        removeMatch($userId, $targetId);
    } else {
        // User could not be reported, show error message in a popup
        echo "<script>alert('User could not be reported.');</script>";
    }

    $set_report->close();

    // Redirect to matches.php
    echo "<script>window.location.href = '../matches/matches.php';</script>";

    exit();
}