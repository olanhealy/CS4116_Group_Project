<?php

include_once "../helpers/db_connection.php";
include "../helpers/helperFunctions.php";
include "../admin/adminHelperFunctions.php";

accessCheck(); 

// Get the user ID from the URL
$targetUserId = $_GET['target_user_id'];

global $showingAdoreButton;
$showingAdoreButton = false;
if(isset($_GET['showingAdoreButton'])){
    $showingAdoreButton = $_GET['showingAdoreButton'];
}

//show the profile card of the user with the given user ID
showProfileCard($targetUserId);