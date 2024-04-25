<?php
//Include connection and helper files
include "../db_connection.php";
include "../helperFunctions.php";
include_once("../admin/adminHelperFunctions.php");

accessCheck();
// sending out JSON data so set the content type to JSON
header('Content-Type: application/json');


//check if the messageContent and matchId POST requests have been set to sendMessage.php (form for sending a message from html)
if (isset($_POST['message_content']) && isset($_POST['match_id'])) {
    $userId = $_SESSION['user_id'];
    $messageContent = $_POST['message_content'];
    $matchId = $_POST['match_id'];

   
    //call the sendMessage function from helper and assign the boolean result to success
    $success = sendMessage($userId, $matchId, $messageContent);

    //if the message sent successfully, return a JSON response with success as true. else it didnt send so return error. can see in netwrok tab
    if ($success) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["error" => "Message could not be sent"]);
    }
} else {
    echo json_encode(["error" => "Invalid request"]);
}
?>