<?php
    include "../db_connection.php";
    include "adminHelperFunctions.php";
    include "setBanned.php";

    if(session_status() === PHP_SESSION_NONE) {
        session_start();
    }

include "userListAdmin.php";