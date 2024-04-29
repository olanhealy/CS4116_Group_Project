<?php
function adminAccessCheck()
{

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_SESSION['user_id']) || getUserRole($_SESSION['user_id']) != "admin") {
        header("Location: /src/pages/errors/unauthorisedaccess.html");
        exit();
    }
}

//show all the accounts in a list
function showAccounts()
{
    global $conn;
    //get all account information
    $query = "SELECT * FROM account ORDER BY user_id ASC";

    //check the result is not empty
    $result = $conn->query($query);
    if ($result->num_rows > 0) {
        // Output data of each row with a form to ban/unban
        while ($row = $result->fetch_assoc()) {
            //getting each user_id from the query
            $targetId = $row['user_id'];

            include "userListAdmin.html";
        }
    } else {
        //error
        echo "0 results found";
    }
}

function updateUserDetails()
{

}

//function to get user role enum(admin, standard)
//returns user role
function getUserRole($userId)
{
    global $conn;

    //query to return role of the user_id
    $query = "SELECT user_role FROM account WHERE user_id = ?";

    //process the statement and store it
    $getUserRoleStmt = $conn->prepare($query);
    $getUserRoleStmt->bind_param("i", $userId);
    $getUserRoleStmt->execute();
    $getUserRoleStmt->store_result();

    //set userRole to the value of getUserRoleStmt
    $userRole = null;
    if ($getUserRoleStmt->num_rows > 0) {
        $getUserRoleStmt->bind_result($userRole);
        $getUserRoleStmt->fetch();
    }

    //close the statement
    $getUserRoleStmt->close();

    //returns user role
    return $userRole;
}

//function to delete a user
function deleteUser($targetId)
{
    global $conn;

    //an array of tables that have user_d as a foreign key
    $tables = ['adore', 'banned', '`ignore`', '`profile`'];

    //iterate through tables
    foreach ($tables as $table) {

        //gets all where user_id matches the passed in target_id
        $query = "SELECT * FROM $table WHERE user_id = $targetId";
        if ($table == 'adore') {
            $query = "SELECT * FROM $table WHERE user_id = $targetId OR adored_user_id = $targetId";
        } elseif ($table == '`ignore`') {
            $query = "SELECT * FROM $table WHERE user_id = $targetId OR ignored_user_id = $targetId";
        }

        $result = $conn->query($query);
        //checks the result is recieved
        if ($result !== false) {
            //checks the result isn't empty
            if ($result->num_rows > 0) {

                //delete all the value from $table where user_id is used
                $query = "DELETE FROM $table WHERE user_id = ?";
                if ($table == 'adore') {
                    $query = "DELETE FROM $table WHERE user_id = ? OR adored_user_id = ?";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("ii", $targetId, $targetId);
                } elseif ($table == '`ignore`') {
                    $query = "DELETE FROM $table WHERE user_id = ? OR ignored_user_id = ?";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("ii", $targetId, $targetId);
                } else {
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("i", $targetId);
                }

                $stmt->execute();

                //checks the result isn't empty
                if ($stmt->affected_rows !== 0) {

                    echo "User deleted successfully from $table" . "<br>";
                }
            } else {
                //error
                echo "User $targetId has no data relating to them in table $table" . "<br>";
            }
        } else {
            //error
            die("Error in SQL query for table $table: " . $conn->error . "<br>");
        }
    }

    deleteMessages($targetId);
    deleteMatches($targetId);

    //select from account table where user_id
    $query = "SELECT * FROM account WHERE user_id = $targetId";

    $result = $conn->query($query);
    if ($result !== false) {
        if ($result->num_rows > 0) {

            //delete user from accounts
            $query = "DELETE FROM account WHERE user_id = ?";
            $stmt = $conn->prepare($query);

            //excute statement
            $stmt->bind_param("i", $targetId);
            $stmt->execute();

            //checks if row is removed
            if ($stmt->affected_rows !== 0) {
                echo "User deleted successfully from account" . "<br>";
            }

        } else {
            //error
            echo "User $targetId has no data relating to them in table account" . "<br>";
        }
    } else {
        //error
        die("Error in SQL query for table account: " . $conn->error . "<br>");
    }

    // Redirect to userListAdmin.php
    header("Location: usersListAdmin.php");
    exit();

}

function deleteMessages($targetId)
{

    global $conn;

    //select from messages where sender_id or receiver_id is user_id
    $query = "SELECT * FROM messages WHERE sender_id = $targetId OR receiver_id = $targetId";

    //check the result is not empty
    $result = $conn->query($query);
    if ($result !== false) {

        if ($result->num_rows > 0) {
            //delete from messages where sender_id or receiver_id is user_id
            $query = "DELETE FROM messages WHERE sender_id = ? OR receiver_id = ?";
            $stmt = $conn->prepare($query);

            //checks if statement is empty

            $stmt->bind_param("ii", $targetId, $targetId);
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                echo "User deleted successfully from messages" . "<br>";
            }


        } else {
            echo "User $targetId has no data relating to them in table messages" . "<br>";
        }
    } else {
        die("Error in SQL query for table messages: " . $conn->error . "<br>");
    }
}

function deleteMatches($targetId)
{

    global $conn;

    //select from matches where initiator_id or target_id is user_id
    $query = "SELECT * FROM matches WHERE initiator_id = $targetId OR target_id = $targetId";

    //check the result is not empty
    $result = $conn->query($query);
    if ($result !== false) {
        //checks the result isn't empty
        if ($result->num_rows > 0) {

            //delete from matches where initiator_id or target_id is user_id
            $query = "DELETE FROM matches WHERE initiator_id = ? OR target_id = ?";
            $stmt = $conn->prepare($query);


            $stmt->bind_param("ii", $targetId, $targetId);
            $stmt->execute();

            //checks if row is removed
            if ($stmt->affected_rows > 0) {
                echo "User deleted successfully from matches" . "<br>";
            }

        } else {
            echo "User $targetId has no data relating to them in table matches" . "<br>";
        }
    } else {
        die("Error in SQL query for table matches: " . $conn->error . "<br>");
    }
}

// Function to check if a account is banned
// Returns 1 if banned, 0 if not
function isAccountBanned($userId)
{
    global $conn;

    //set banned to false
    $banned = false;

    //sql query for banned id
    $query = "SELECT banned FROM account WHERE user_id= ? ";

    //prepare the statement
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->store_result();

    //set userRole to the value of getUserRoleStmt
    $result = null;
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($result);
        $stmt->fetch();

        //gets value of banned
        return $result;
    } else {
        //error logging
        echo "Error get data from banned table";
        return 0;
    }
}

//Sets a user ban status in account and if unbanned deletes the ban info from the banned table
function setBanned($userId, $newBannedStatus)
{
    global $conn;

    //set banned status in account table
    $query = "UPDATE account SET banned = ? WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $newBannedStatus, $userId);
    $stmt->execute();

    //if unbanned delete ban info from table
    if ($newBannedStatus == 0) {
        $query = "DELETE FROM banned WHERE user_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
    }
}

//set user role 
function setUserRole($userId, $role)
{
    global $conn;

    //query to update user role
    $query = "UPDATE account SET user_role = ? WHERE user_id = ?";
    $setQuery = $conn->prepare($query);
    $setQuery->bind_param("si", $role, $userId);
    $setQuery->execute();

    //check if the user role has been set
    if ($setQuery->affected_rows > 0) {
        echo "User set to Admin successfully";
    } else {
        echo "Error setting Admin";
    }
}