<?php

include "db_connection.php";
include "helperFunctions.php";
include "admin/adminHelperFunctions.php";

accessCheck();

echo $_POST['action'];
if ($_POST['action'] == "adore_user") {
    $targetUserId = $_POST['target_user_id'];
    adoreUser($_SESSION['user_id'], $targetUserId);

    var_dump($_POST);
    echo "Adored user with ID: " . $targetUserId;
    echo $_SESSION['user_id'];
    if (isItAMatch($_SESSION['user_id'], $targetUserId)) {
    // if it is a match, add to matches table
        addMatch($targetUserId, $_SESSION['user_id']);
    }
}

header("Location: searchPage.html");

