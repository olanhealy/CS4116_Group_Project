<?php
include "db_connection.php";
require_once 'helperFunctions.php';
include "admin/adminHelperFunctions.php";

accessCheck(); 

$userId = $_SESSION['user_id'];

// If the user wants to remove a match
if (isset($_POST['action']) && $_POST['action'] === 'removeMatch') {
    removeMatch($_POST['userId'], $_POST['targetId']);
}



//sets up the header and dropdown
setupHeader();


// Get the user's next matches
$matches = getNextMatches($userId);

if ($matches === null) {
    exit();
}
// Include match.html file
include "match.html";

//set up the footer
setupFooter();

?>
