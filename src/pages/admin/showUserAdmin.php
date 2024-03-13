<?php

    include_once "adminHelperFunctions.php";
    
    session_start();

    //targetID set from GET sent from userListAdmin.html
    $targetId =  $_GET['targetId'];
    //transfer targetId to a SESSION variable
    $_SESSION['targetId'] = $targetId;

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
            include "banUser.html";
        }
        //if the user is banned just show delete button
        include "deleteUser.html";
    }