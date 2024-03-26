<?php

//TODO: Standardise how SQL statements are written

function showAccounts()
{
    include ("../db_connection.php");
    $query = "SELECT * FROM account";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        // Output data of each row with a form to ban/unban
        while ($row = $result->fetch_assoc()) {
            global $target_id;
            $target_id = $row['user_id'];
            include "userListAdmin.html"; // Include HTML file for each row
        }
    } else {
        echo "0 results found";
    }
}

function updateUserDetails()
{

}

function getUserRole($user_id)
{
    include ("../db_connection.php");

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

function deleteUser($user_id)
{
    include ("../db_connection.php");

    $target_id = $user_id;
    $tables = ['adore', 'banned', '`ignore`', '`profile`'];

    foreach ($tables as $table) {

        $query = "SELECT * FROM $table WHERE user_id = $target_id";
        $result = $conn->query($query);

        if ($result === false) {
            die ("Error in SQL query for table $table: " . $conn->error . "<br>");
        }

        if ($result->num_rows > 0) {
            $query = "DELETE FROM $table WHERE user_id = ?";
            $stmt = $conn->prepare($query);

            if ($stmt === false) {
                die ("Error in SQL query: " . $conn->error . "<br>");
            }

            $stmt->bind_param("i", $target_id);
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                echo "User deleted successfully from $table" . "<br>";
            }
        } else {
            echo "$target_id has no data relating to them in table $table" . "<br>";
        }
    }

    deleteMessages($target_id);
    deleteMatches($target_id);

    $query = "SELECT * FROM account WHERE user_id = $target_id";

    $result = $conn->query($query);
    if ($result === false) {
        die ("Error in SQL query for table account: " . $conn->error . "<br>");
    }

    if ($result->num_rows > 0) {
        $query = "DELETE FROM account WHERE user_id = ?";
        $stmt = $conn->prepare($query);

        if ($stmt === false) {
            die ("Error in SQL query: " . $conn->error . "<br>");
        }

        $stmt->bind_param("i", $user_id);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            echo "User deleted successfully from account" . "<br>";
        }
    } else {
        echo "$user_id has no data relating to them in table account" . "<br>";
    }

}

function deleteMessages($user_id)
{

    include ("../db_connection.php");

    $query = "SELECT * FROM messages WHERE sender_id = $user_id OR receiver_id = $user_id";

    $result = $conn->query($query);
    if ($result === false) {
        die ("Error in SQL query for table messages: " . $conn->error . "<br>");
    }

    if ($result->num_rows > 0) {
        $query = "DELETE FROM messages WHERE sender_id = ? OR receiver_id = ?";
        $stmt = $conn->prepare($query);

        if ($stmt === false) {
            die ("Error in SQL query: " . $conn->error . "<br>");
        }

        $stmt->bind_param("ii", $user_id, $user_id);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            echo "User deleted successfully from messages" . "<br>";
        }
    } else {
        echo "$user_id has no data relating to them in table messages" . "<br>";
    }
}

function deleteMatches($user_id){

    include ("../db_connection.php");

    $query = "SELECT * FROM matches WHERE initiator_id = $user_id OR target_id = $user_id";

    $result = $conn->query($query);
    if ($result === false) {
        die ("Error in SQL query for table matches: " . $conn->error . "<br>");
    }

    if ($result->num_rows > 0) {
        $query = "DELETE FROM matches WHERE initiator_id = ? OR target_id = ?";
        $stmt = $conn->prepare($query);

        if ($stmt === false) {
            die ("Error in SQL query: " . $conn->error . "<br>");
        }

        $stmt->bind_param("ii", $user_id, $user_id);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            echo "User deleted successfully from matches" . "<br>";
        }
    } else {
        echo "$user_id has no data relating to them in table matches" . "<br>";
    }
}

// Function to check if a account is banned
function isAccountBanned($user_id)
{
    include ("../db_connection.php");
    $banned = false;

    //sql query for banned id
    $sql = "SELECT banned FROM account WHERE user_id='$user_id' ";
    $result = mysqli_query($conn, $sql);

    //Checks success retrieval of data
    if ($result) {
        if (mysqli_num_rows($result) > 0) {
            //creates an array from result
            $row = mysqli_fetch_array($result);
            //gets value of banned
            return $row["banned"];
        } else {
            //error logging
            error_log(mysqli_error($conn));
            return 0;
        }
    }
}

//Sets a user ban status in account and banned table
function setBanned($user_id, $new_banned_status)
{
    include ("../db_connection.php");

    //set account[banned] as value
    $sql = "UPDATE account SET banned = $new_banned_status WHERE user_id = $user_id";
    mysqli_query($conn, $sql);

    //if unbanned delete ban info from table
    if ($new_banned_status == 0) {
        $sql = "DELETE FROM banned WHERE user_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        mysqli_stmt_execute($stmt);
    }
}

//set Admin as admin
function setUserRole($user_id, $role)
{
    include ("../db_connection.php");

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