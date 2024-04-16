<?php

    include_once "../db_connection.php";
    include_once "adminHelperFunctions.php";
    
    if(session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    //targetID set from GET sent from userListAdmin.html
    if (isset($_GET['targetId'])){
        $targetId =  $_GET['targetId'];
    }else{
        //if targetId is not set, check if it is set in SESSION
        if (isset($_SESSION['targetId'])){
            $targetId = $_SESSION['targetId'];
        }else{
            //if targetId is not set in GET or SESSION, show error message
            echo "Target ID is not set.";
            exit();
    }
    
    };

    ?>

    <a href="usersListAdmin.php">Back to User List</a>

    <?php

    //transfer targetId to a SESSION variable
    $_SESSION['targetId'] = $targetId;
    //var_dump($_SESSION);
    //if the account is banned show editProfile, unban, deleteUser
    if(isAccountBanned($targetId)){
        include "editProfileAdmin.php";
        include "unban.html";
        include "deleteUser.html";
    }else{
    //if the account isn't banned
        //and the account is not an admin show editProfile, makeAdmin, banUser
        if(getUserRole($targetId) == "standard"){
            include "editProfileAdmin.php";
            include "makeAdmin.html";
            echo '<a href="banUser.html">Ban User</a>';
        }
        //if the user is banned just show delete button
        include "deleteUser.html";
    }