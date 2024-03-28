<?php
    
    include_once "adminHelperFunctions.php";
    $targetId = $_POST['deleteTargetId'];
    deleteUser($targetId);