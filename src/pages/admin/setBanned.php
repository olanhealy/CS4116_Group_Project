<?php

include_once "adminHelperFunctions.php";

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset ($_SESSION['target_id'])) {
        $target_id = $_SESSION['target_id'];

        //if ban, sets ban. If unban sets unban
        if ($_POST['action'] == 'unban') {
            setBanned($target_id, 0);
        } elseif ($_POST["action"] == "ban") {
            setBanned($target_id, 1);
        }

        // Redirect to userListAdmin.php
        header("Location: usersListAdmin.php");
        exit();
    } else {
        echo "Target ID is not set.";
        exit();
    }
}