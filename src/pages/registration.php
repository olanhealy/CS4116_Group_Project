<?php
// Process #4 checks if the Account already exists in the account table of db
function isAccountFound($email, $password)
{
    global $conn;
    $count = 0;
    $sql_is_account_found = "SELECT COUNT(*) FROM account WHERE email = ? AND password_hash = ?";
    $account = $conn->prepare($sql_is_account_found);
    $account->bind_param('ss', $email, $password);
    $account->execute();
    $account->bind_result($count);
    $account->fetch();
    $account->close();

    return $count > 0;
}

// Process #8 verifyStudentEmail verifies by making sure the email ends with '@studentmail.ul.ie'
function verifyStudentEmail($email)
{
    return substr($email, -18) === "@studentmail.ul.ie";
}

// Process #9 to set the Users password in the account in db
function setPassword($password, $user_id)
{
    global $conn;

    // Hash the password for security 
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Update the password in the Accounts table
    $sql_set_password = "UPDATE account SET password_hash = ? WHERE user_id = ?";
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

// Process #15 to set the user_id based on the users student number which is inputted with the ul student email
function setUserId($email)
{
    $user_id_string = explode("@", $email)[0]; // Extract the portion before @
    $user_id = (int) $user_id_string; // Convert to integer
    return $user_id;
}

include "db_connection.php";
include "helper.php";


//Initalise array of erros which can then be displayed from the html
$errors = [];

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate inputs
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $first_name = htmlspecialchars($_POST['first_name']);
    $last_name = htmlspecialchars($_POST['last_name']);

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }

    // Check if email is a UL student email using the verifyStudentEmail function. Uses #Process 9, verifyStudentMail by checking if output
    // is false it will end
    if (!verifyStudentEmail($email)) {
        $errors[] = "Email must end with '@studentmail.ul.ie' (Are you sure you are a UL Student?)";
    }

    // Check if password is between 8 and 20 characters long
    if (strlen($password) < 8 || strlen($password) > 20) {
        $errors[] = "Password must be between 8 and 20 characters long";
    }

    // Check for at least one capital letter and at least one special character with the inputted password
    if (!preg_match('/[A-Z]/', $password) || !preg_match('/[^a-zA-Z0-9]/', $password)) {
        $errors[] = "Password must contain at least one capital letter and one special character";
    }

    // Check if the inputted and repeated passwords both match
    if ($_POST['password'] !== $_POST['password-repeat']) {
        $errors[] = "The password and repeated password do not match";
    }

    //If erros are empty. then procede with Inserting new account into account table of db and setting the  attributes
    if (empty($errors)) {

        // Call setUserId function
        $user_id = setUserId($email);

        // Insert the newly successful registered email and the set userID into account table of database
        $sql_insert_account = "INSERT INTO account (user_id, email) VALUES (?,?)";
        $insert_account_statement = $conn->prepare($sql_insert_account);
        $insert_account_statement->bind_param('is', $user_id, $email);
        $insert_account_statement->execute();

        // Check if the insertion was successful
        if ($insert_account_statement->affected_rows > 0) {

            // Get the user id of this registered user

            // Using the setter methods from above
            setPassword($password, $user_id);
            setName($first_name, $last_name, $user_id);

            if(session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['email'] = $email;
            $_SESSION['id'] = $user_id;

            header("Location: edit_profile.php");
        } else {
            $errors[] = "Error occurred during registration";
        }

        // Close insert of new account as the HTTP request has been posted to db
        $insert_account_statement->close();
    }
}

// Include the HTML content from registration.html
include "registration.html";

?>