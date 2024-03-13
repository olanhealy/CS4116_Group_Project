<?php

include_once "adminHelperFunctions.php";

if($_SERVER["REQUEST_METHOD"] == "POST") {
    if(isset($_POST['user_id'])){

        //target_id set from post
        $target_id = $_POST['user_id'];

        //if ban, sets ban. If unban sets unban
        if($_POST['action'] == 'unban') {
            setBanned($target_id, 0);
        } elseif($_POST["action"] == "ban") {
            setBanned($target_id, 1);
        }

        // Redirect to userListAdmin.php
        header("Location: usersListAdmin.php");
        exit();
    } else {
        echo "No user_id found";
    }
}