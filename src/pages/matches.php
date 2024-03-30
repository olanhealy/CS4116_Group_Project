<?php

function addMatch($initiatorId, $targetId)
{

    global $conn;

    $query = "INSERT INTO matches (initiator_id, target_id) VALUES (?, ?)";
    $stmt = $conn->prepare($query);
    if ($stmt !== false) {
        $stmt->bind_param("ii", $initiatorId, $targetId);
        $stmt->execute();
    } else {
        die("Error in SQL query: " . $conn->error . "<br>");
    }
}

function isItAMatch($initiatorId, $targetId)
{
    global $conn;

    $query = "SELECT * FROM adore WHERE user_id = ? AND adored_user_id = ?";

    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("ii", $targetId , $initiatorId);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        if ($result->num_rows > 0) {
            // The current user has previously adored the logged in user
            return true;
        }
    }
    // The current user has not previously adored the logged in user
    return false;
}

function removeMatch($userId, $targetId)
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
    $query = "SELECT 
        CASE 
            WHEN initiator_id = $userId THEN target_id 
            ELSE initiator_id 
        END AS other_user_id 
    FROM matches 
    WHERE initiator_id = $userId OR target_id = $userId 
    ORDER BY response_date DESC";
    ;

    //check the result is not empty
    $result = $conn->query($query);
    if ($result->num_rows > 0) {
        // Output data of each row with a form to ban/unban
        while ($row = $result->fetch_assoc()) {

            $targetId = $row['other_user_id'];
            
            $name = getName($targetId);
            $profilePicture = getProfilePicture($targetId);

            //include the user list html for each row
            include "match.html";
        }
    } else {
        //error
        echo "0 results found";
    }
}

include "db_connection.php";
require_once 'helper.php';

if(session_status() === PHP_SESSION_NONE) {
    session_start();
}

$userId = $_SESSION['id'];

?>

<a href="home.php">Home</a>
<br>
<?php
getAllMatches($userId);

if (isset($_POST['action']) && $_POST['action'] === 'removeMatch') {
    removeMatch($_POST['userId'], $_POST['targetId']);
    header("Location: matches.php");
}

?>