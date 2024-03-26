<?php
    
    include_once "adminHelperFunctions.php";
    $target_id = $_POST['target_id'];
    deleteUser($target_id);