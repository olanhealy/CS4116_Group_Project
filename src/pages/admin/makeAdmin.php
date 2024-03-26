<?php

include "adminHelperFunctions.php";
$target_id = $_POST['target_id'];
setUserRole($target_id, "admin");