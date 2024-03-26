<?php
    //TODO: use Olan scripts once added to add user info
    //TODO add way to edit profile

    //name
    //id
    //banned
    //admin
    //button to ban xx
    //button to unban xx
    //button to make admin xx
    //button to edit profile
    //button to delete profile xx

    include "adminHelperFunctions.php";

    session_start();

    $target_id =  $_GET['target_id'];
    $_SESSION['target_id'] = $target_id;

    if(isAccountBanned($target_id)){
        include "unban.html";
        include "deleteUser.html";
    }else{
        if(getUserRole($target_id) == "standard"){
            include "makeAdmin.html";
            include "banUser.html";
        }
        include "deleteUser.html";
    }
?>