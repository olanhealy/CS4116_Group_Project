<?php

include "db_connection.php";
require_once 'helperFunctions.php';
include "admin/adminHelperFunctions.php";

accessCheck(); 

$userId = $_SESSION['user_id'];
?>

<!--TODO: frontend: Tidy up this Home Link -- Maybe Header?-->

<a href="home.php">Home</a>
<br>

<?php

// Get all matches for the user
getAllMatches($userId);

// If the user wants to remove a match
if (isset($_POST['action']) && $_POST['action'] === 'removeMatch') {
    removeMatch($_POST['userId'], $_POST['targetId']);
    header("Location: matches.php");
}
?>