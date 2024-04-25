<?php
// Include connection and helper
include "../db_connection.php";
include "../helperFunctions.php";
include_once("../admin/adminHelperFunctions.php");

accessCheck();

header('Content-Type: application/json');

if (isset($_SESSION['user_id']) && isset($_POST['message_id'])) {
    //get userid and message ud of message to be deleted
    $userId = $_SESSION['user_id'];
    $messageId = $_POST['message_id'];

    //call deletemessage fucntion
    $success = deleteMessage($userId, $messageId);

    //handle the success or failure of it
    if ($success) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to delete message']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'No message ID provided or user not logged in']);
}