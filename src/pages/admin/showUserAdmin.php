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
            //add buttons to div
            echo '<div class="linkContainer">';
            include "makeAdmin.html";
            echo '<a href="banUser.html" id="banUser" class="btn btn-danger">Ban User</a>';
        }else if(getUserRole($targetId) == 'admin'){
            //include page for viewing an admin account
            include "viewAdminAccount.html";
        }
        //if the user is banned just show delete button
        include "deleteUser.html";
        //close div
        echo '</div>';
        //always include footer on all pages
        include "../footer.php";
    }
    ?>

    <html>
        <link rel="stylesheet" type="text/css" href="../../assets/css/editProfileAdmin.css">
    </html>