<?php
    
    include_once "adminHelperFunctions.php";
    include_once "../db_connection.php";
    
    $targetId = $_POST['deleteTargetId'];
    deleteUser($targetId);