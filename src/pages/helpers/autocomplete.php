<?php
// Include database connection
include "db_connection.php";
include "helperFunctions.php";
include "../admin/adminHelperFunctions.php";

accessCheck();

$userName = getName($_SESSION['user_id']);

// Fetch user names matching the input
if(isset($_GET['term'])){
    $searchName = $_GET['term'] . '%';

    // Prepare SQL query
    $query = "SELECT * FROM profile WHERE name LIKE ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $searchName);
    $stmt->execute();
    $result = $stmt->get_result();

    // Fetch and return matching names as JSON
    $names = array();
    while ($row = $result->fetch_assoc()) {

        $targetUserId = $row['user_id'];
        $userGender = getGender($_SESSION['user_id']);

        if (($targetUserId == $_SESSION['user_id'] || getUserRole($targetUserId) == "admin" ) ){
            continue;
        }

        $showingAdoreButton = false;
        if ((getPursuing($_SESSION['user_id']) === getGender($targetUserId) && getPursuing($targetUserId) === $userGender) && !isUserAdored($_SESSION['user_id'], $targetUserId)) {
            $showingAdoreButton = true;
        }

        // Build the link for each name
        $link = "<a href='../profilecard/profileCard.php?target_user_id=" . $targetUserId . "&showingAdoreButton=" . $showingAdoreButton . "'>" . $row['name'];
        $names[] = $link;
    }
    echo json_encode($names);
}
?>
