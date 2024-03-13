<?php
    //get session info
    session_start();

    include "../db_connection.php";
    include_once "adminHelperFunctions.php";


    // Check if the user is logged in
    if(isset($_SESSION['id'])) {
        // Retrieve the user ID
        $user_id = $_SESSION['id'];
        
    } else {
        // Redirect the user to the login page or display an error message
        echo "User is not logged in.";
    }

    //Checks for post request
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if(isset($_POST['email'])){

            //set variables
            $email = $_POST['email'];
            $reason = $_POST['reason'];
            $duration = $_POST['duration'];

            //get account details from the email (user_id)
            $sql = "SELECT * FROM account WHERE email='$email'";
            $result = mysqli_query($conn, $sql);

            //get to-be-banned id
            if ($result && mysqli_num_rows($result) == 1) {
                $row = mysqli_fetch_assoc($result);
                $target_id = $row["user_id"];
            }

            //create ban
            $sql_insert = "INSERT INTO banned (user_id, banned_by, reason, duration) VALUES (?, ?, ?, ?)";
            $insert_new_banned = $conn->prepare($sql_insert);
            $insert_new_banned->bind_param('ssss', $target_id, $user_id, $reason, $duration);
            $insert_new_banned->execute();

            //call setBanned from adminHelperFunctions.php
            setBanned("$target_id", 1);
        }
    }

    //html
    include "banUser.html";