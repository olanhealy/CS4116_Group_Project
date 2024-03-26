<?php

//TODO: Standardise how SQL statements are written

function showAccounts(){
    include("../db_connection.php");
    $query = "SELECT * FROM account";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        // Output data of each row with a form to ban/unban
        while($row = $result->fetch_assoc()) {
            global $target_id;
            $target_id = $row['user_id'];
            include "userListAdmin.html"; // Include HTML file for each row
        }
    } else {
        echo "0 results found";
    }
}

function updateUserDetails(){

}

function getUserRole($user_id){
    include("../db_connection.php");

    $sqlGetUserRole = "SELECT user_role FROM account WHERE user_id = ?";
    $getUserRole = $conn->prepare($sqlGetUserRole);
    $getUserRole->bind_param("i", $user_id);
    $getUserRole->execute();
    $getUserRole->store_result();

    if ($getUserRole->num_rows > 0) {
        $getUserRole->bind_result($userRole);
        $getUserRole->fetch();
    }

    $getUserRole->close();
    return $userRole;
}

function deleteUser($user_id){
    include("../db_connection.php");

    $query = "DELETE FROM account, adore, banned, `ignore` , matches, messages, `profile` USING account INNER JOIN adore INNER JOIN banned INNER JOIN `ignore` INNER JOIN matches INNER JOIN messages INNER JOIN `profile` WHERE account.user_id=? ON account.user_id=adore.user_id AND adore.user_id=banned.user_id AND banned.user_id=`ignore`.user_id AND `ignore`.user_id=matches.user_id AND matches.user_id=messages.user_id AND messages.user_id=`profile`.user_id;";

    $set_query = $conn->prepare($query);
    $set_query->bind_param("i", $user_id);
    $set_query->execute();

    if ($set_query->affected_rows > 0) {
        echo "User deleted successfully";
    } else {
        echo "Error deleting user";
    }


}


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

//set Admin as admin
function setUserRole($user_id, $role){
    include("../db_connection.php");

    $query = "UPDATE account SET user_role = ? WHERE user_id = ?";
    $set_query = $conn->prepare($query);
    $set_query->bind_param("si", $role, $user_id);
    $set_query->execute();

    if ($set_query->affected_rows > 0) {
        echo "User set to Admin successfully";
    } else {
        echo "Error setting Admin";
    }
}