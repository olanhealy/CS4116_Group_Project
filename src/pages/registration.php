<?php

// Include database connection
include "db_connection.php";


// Process #9 to set the Users password in the account in db
function setPassword($password, $user_id) {
    global $conn;

    // Hash the password for security 
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Update the password in the Accounts table
    $sql_set_password = "UPDATE Account SET password_hash = ? WHERE user_id = ?";
    $set_password = $conn->prepare($sql_set_password);
    $set_password->bind_param('si', $hashed_password, $user_id);
    $set_password->execute();

    if ($set_password->affected_rows > 0) {
        echo "Password set successfully";
    } else {
        echo "Error setting password";
    }

    $set_password->close();
}

//Process #11 to set the First name and Last name in the account table in db
function setName($first_name, $last_name, $user_id) {
    global $conn;

    $sql_set_name = "UPDATE Account SET first_name = ?, last_name = ? WHERE user_id = ?";

    $set_name = $conn->prepare($sql_set_name);
    $set_name->bind_param("sss", $first_name, $last_name, $user_id);
    $set_name->execute();

    if ($set_name-> affected_rows > 0) {
        echo "First and Last Name set successfully";
    } else {
        echo "Error setting First and Last Name";
    }
   
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize input
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $first_name = htmlspecialchars($_POST['first_name']);
    $last_name = htmlspecialchars($_POST['last_name']);

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Invalid email format');</script>";
        exit;
    }

    if (isset($_POST['submitted'])) {
        // Check if email already exists in account table
        $sql_check_email = "SELECT Email FROM Account WHERE email = ?";
        $successful_account_creation = $conn->prepare($sql_check_email);
        $successful_account_creation->bind_param('s', $email);
        $successful_account_creation->execute();
        $result_check_email = $successful_account_creation->get_result();

        while ($row = $result_check_email->fetch_assoc()) {
            if ($_POST['email'] == $row['email']) {
                echo "<script>alert('Email already exists');</script>";
                $successful_account_creation->close();  
                exit;
            }
        }
 
        // Process #8 verifyStudentEmail
        function verifyStudentEmail($email) {
            return substr($email, -18) === "@studentmail.ul.ie";
        }

        // Check if email is a UL student email using the verifyStudentEmail function
        if (!verifyStudentEmail($email)) {
            echo "<script>alert('Email must end with '@studentmail.ul.ie' (Are you sure you are a UL Student?)');</script>";
            $successful_account_creation->close();  
            exit;
        }

        // Check if password is between 8 and 20 characters long
        if (strlen($password) < 8 || strlen($password) > 20) {
            echo "<script>alert('Password must be between 8 and 20 characters long')</script>";
            $successful_account_creation->close();  
            exit;
        }

        // Check for at least one capital letter and at least one special character with the inputted password
        if (!preg_match('/[A-Z]/', $password) || !preg_match('/[^a-zA-Z0-9]/', $password)) {
            echo "<script>alert('Password must contain at least one capital letter and one special character')</script>";
            $successful_account_creation->close();  
            exit;
        }

        // Check if the inputted and repeated passwords both match
        if ($_POST['password'] !== $_POST['password-repeat']) {
            echo "<script>alert('The password and repeated password do not match.')</script>";
            $successful_account_creation->close();
            exit;
        }

        // Make sure all conditions met
        if ($_POST['email'] !== "" and isset($_POST['agreed']) and $_POST['password'] == $_POST['password-repeat'] and strlen($_POST['password']) > 5) {
            // Insert the newly successful registered account into the database account table
            
            $sql_insert = "INSERT INTO Account (email ) VALUES (?)";
            $insert_new_account = $conn->prepare($sql_insert);
            $insert_new_account->bind_param('s', $email);
            $insert_new_account->execute();

            if ($insert_new_account->affected_rows > 0) {
                // Get the user id of this registered user
                $user_id = $insert_new_account->insert_id;

                setPassword($password, $user_id);
                setName($first_name, $last_name, $user_id);

                // Insert it into profile table, also inserting the full name of user
                $sql_insert_profile = "INSERT INTO profile (user_id, name ) VALUES (?, ?)";
                $insert_new_profile = $conn->prepare($sql_insert_profile);
                $full_name = $first_name . " " . $last_name;
                $insert_new_profile->bind_param('is', $user_id, $full_name);
                $insert_new_profile->execute();

                if ($insert_new_profile->affected_rows <= 0) {
                    echo "Error inserting into the Profile table";
                }

                $insert_new_profile->close();

                session_start();
                $_SESSION['email'] = $email;
                $_SESSION['id'] = $user_id;
                header("Location: edit_profile.php");
            } else {
                echo "<script>alert('Error occurred during registration');</script>";
            }

            // Close insert of new account as the HTTP request has been posted to db
            $insert_new_account->close();
        }

        // Close as the account has now been created successfully
        $successful_account_creation->close();
    }
}

// Include the HTML content from registration.html
include "registration.html";
?>