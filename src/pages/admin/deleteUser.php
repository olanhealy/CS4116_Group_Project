<?php
    
    include_once "adminHelperFunctions.php";
    $targetId = $_POST['delete_targetId'];
    deleteUser($targetId);