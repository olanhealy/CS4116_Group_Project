<?php
include 'db_connection.php';
include 'helperFunctions.php';
include "admin/adminHelperFunctions.php";

accessCheck();

// let data be json data sent from the client side
$data = json_decode(file_get_contents('php://input'), true);

// Check if tthe data has been recieved
if (isset($data['type']) && isset($data['user_id'])) {
    $userId = intval($data['user_id']);
    //of the type is messages, clear the message notifications, else clear the match notifications
    if ($data['type'] == 'messages') {
        clearMessageNotifications($userId);
    } else if ($data['type'] == 'matches') {
        clearMatchNotifications($userId);
    }
    // Update session with new notification count.
    $_SESSION['notifications'] = fetchNotifications($userId);

    // header is json
    header('Content-Type: application/json');
    // Return new notification count as a JSON response.
    echo json_encode($_SESSION['notifications']);
} else {
    //Send a HTTP response code 400 if invalid
    http_response_code(400);
    echo json_encode(array("error" => "Invalid request. Type and user ID required."));
}
?>