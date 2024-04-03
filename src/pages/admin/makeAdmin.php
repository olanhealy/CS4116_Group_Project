<?php
if(session_status() === PHP_SESSION_NONE) {
    session_start();
}
include "adminHelperFunctions.php";

//assigns the targetId to the value of the post request
$targetId = $_POST['makeAdminTargetId'];
setUserRole($targetId, "admin");

header("Location: usersListAdmin.php");