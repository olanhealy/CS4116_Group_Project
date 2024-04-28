<?php
include "../helpers/db_connection.php";
require_once '../helpers/helperFunctions.php';
include "../admin/adminHelperFunctions.php";

accessCheck(); 

$userId = $_SESSION['user_id'];

//Sets up Header & Dropdown
setupHeader();

// Include html file
include "searchPage.html";

//Sets up Footer
setupFooter();

?>