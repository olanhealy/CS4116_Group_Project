<?php

include "adminHelperFunctions.php";
include "../db_connection.php";

adminAccessCheck();

//assigns the targetId to the value of the post request
$targetId = $_POST['makeAdminTargetId'];
setUserRole($targetId, "admin");

header("Location: usersListAdmin.php");