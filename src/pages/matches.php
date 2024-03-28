<?php

function itsAMatch($initiatorId, $targetId)
{

    global $conn;

    $query = "INSERT INTO matches (initiator_id, target_id, status) VALUES (?, ?, 'Matched')";
    $stmt = $conn->prepare($query);
    if ($stmt !== false) {
        $stmt->bind_param("ii", $initiatorId, $targetId);
        $stmt->execute();
    } else {
        die("Error in SQL query: " . $conn->error . "<br>");
    }
}

function startAMatch($initiatorId, $targetId)
{

    global $conn;

    $query = "INSERT INTO matches (initiator_id, target_id, status) VALUES (?, ?, 'Pending')";
    $stmt = $conn->prepare($query);

    if ($stmt !== false) {
        $stmt->bind_param("ii", $initiatorId, $targetId);
        $stmt->execute();
    } else {
        die("Error in SQL query: " . $conn->error . "<br>");
    }
}

function removeMatches($userId, $targetId)
{

    global $conn;

    $query = "DELETE FROM matches WHERE initiator_id = ? AND target_id = ? OR initiator_id = ? AND target_id = ?";
    $stmt = $conn->prepare($query);

    if ($stmt !== false) {
        $stmt->bind_param("iiii", $userId, $targetId, $targetId, $userId);
        $stmt->execute();
    } else {
        die("Error in SQL query: " . $conn->error . "<br>");
    }
}

function getAllMatches($userId)
{
    global $conn;

    //get all account information
    $query = "SELECT * FROM matches WHERE initiator_id = $userId OR target_id = $userId";
    $result = $conn->query($query);
    $recipientId = 0;
    $_SESSION['recipient_id'] = 00000000;

    if ($result->num_rows > 0) {
        // Output data of each row with a form to ban/unban
        while ($row = $result->fetch_assoc()) {

            if($row['initiator_id'] == $_SESSION['id']){

                //user is initiator
                $recipientId = $row['target_id'];
                $_SESSION['recipient_id'] = $recipientId;

            }elseif(($row['target_id'] == $_SESSION['id'])){

                //user is target
                $recipientId = $row['initiator_id'];
                $_SESSION['recipient_id'] = $recipientId;
            }
            //getting each user_id from the query
            $matchId = $row['match_id'];
            //include the user list html for each row
            include "match.html";
        }
    } else {
        //error
        echo "You've No matches";
    }
}

include "db_connection.php";

session_start();

$user = "66666666";
getAllMatches($user);
?>