<?php
    
    include_once "adminHelperFunctions.php";
    include_once "../helpers/db_connection.php";
    
    adminAccessCheck();
    $targetId = $_POST['deleteTargetId'];
    deleteUser($targetId);