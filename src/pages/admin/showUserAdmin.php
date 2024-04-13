<?php

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
            echo '<div class="linkContainer">';
            include "makeAdmin.html";
            echo '<a href="banUser.html" id="banUserLink">Ban User</a>';
        }else if(getUserRole($targetId) == "admin") {
            //include page for viewing admin account
            include "viewAdminAccount.html";
        }
        //if the user is banned just show delete button
        include "deleteUser.html";
        echo '</div>';
        // always include footer 
        include "adminFooter.html"; 
    }

    ?>

<html>
    <link rel="stylesheet" type="text/css" href="../../assets/css/editProfileAdmin.css">
</html>
