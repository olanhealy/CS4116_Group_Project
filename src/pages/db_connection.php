<?php

//used for connecting to the database on server
 $sname= "sql313.infinityfree.com";
 $unmae= "if0_36038205";
 $password = "cs41162024";
 $db_name = "if0_36038205_ulsingles";


//used for connecting to the database on local machine
// $sname= "localhost";
// $unmae= "root";
// $password = "";
// $db_name = "ulsingles";

//connect to the database
$conn = mysqli_connect($sname, $unmae, $password, $db_name);


if (!$conn) {
    $error_message = "Connection failed: " . mysqli_connect_error();
    error_log($error_message, 3, "error.log");
    die($error_message);
}