<?php
// Function to check if a account is banned
function isAccountBanned($user_id){
    include("../db_connection.php");
    $banned = false;

    //sql query for banned id
    $sql = "SELECT banned FROM account WHERE user_id='$user_id' ";
    $result = mysqli_query($conn, $sql);

    //Checks success retrieval of data
    if($result){
        if(mysqli_num_rows($result) > 0){
            //creates an array from result
            $row = mysqli_fetch_array($result);
            //gets value of banned
            return $row["banned"];
    }else{
        //error logging
        error_log(mysqli_error($conn));
        return 0;
    }
    }
}

//Sets a user ban status in account and banned table
function setBanned($user_id, $new_banned_status){
    include("../db_connection.php");

    //set account[banned] as value
    $sql = "UPDATE account SET banned = $new_banned_status WHERE user_id = $user_id";
    mysqli_query($conn, $sql);

    //if unbanned delete ban info from table
    if($new_banned_status == 0){
        $sql = "DELETE FROM banned WHERE user_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        mysqli_stmt_execute($stmt);
    }
}