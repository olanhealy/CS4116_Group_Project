<!-- Matches Page -->

<?php
include "../helpers/db_connection.php";
require_once '../helpers/helperFunctions.php';
include "../admin/adminHelperFunctions.php";

accessCheck(); 

$userId = $_SESSION['user_id'];

// If the user wants to remove a match
if (isset($_POST['action']) && $_POST['action'] === 'removeMatch') {
    removeMatch($_POST['userId'], $_POST['targetId']);
}

// Header & Dropdown Menu
setupHeader();

// Get the users next matches
$matches = getNextMatches($userId);

if ($matches === null) {
    exit();
}

// Include match.html file
include "match.html";

//Footer
setupFooter();

?>
