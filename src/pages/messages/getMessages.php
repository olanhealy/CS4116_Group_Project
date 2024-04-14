<?php
//Include connection and helper files
include "../db_connection.php";
include "../helperFunctions.php";

if(session_status() === PHP_SESSION_NONE) {
    session_start();
}

// sending out JSON data so set the content type to JSON
header('Content-Type: application/json');

// Check if the match_id GET request has been set to getMessages.php. Indicates which conversation to fetch messages for

if (isset($_GET['match_id'])) {
    $matchId = $_GET['match_id'];
    $userId = $_SESSION['user_id'];

    // Call the getMessagesByMatchId function from helper and outpt  the result as a JSON response: 
    /* eg:
    [
    {
        "message_id": 4,
        "match_id": 2,
        "receiver_id": 598345435,
        "sender_id": 594320,
        "message_content": "Hello world",
        "date": "2024-04-14 17:21:10",
        "read_status": "delivered"
    },
    {
        "message_id": 7,
        "match_id": 2,
        "receiver_id": 598345435,
        "sender_id": 594320,
        "message_content": "Hi",
        "date": "2024-04-14 17:28:53",
        "read_status": "delivered"
    }
]
    */
    $messages = getMessagesByMatchId($matchId);
    echo json_encode($messages);
} else {
    echo json_encode(["error" => "No match_id "]);
}
?>