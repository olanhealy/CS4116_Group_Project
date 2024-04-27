<?php

// Process #8 verifyStudentEmail verifies by making sure the email ends with '@studentmail.ul.ie'
function verifyStudentEmail($email) {
    // Check if the email ends with '@studentmail.ul.ie'
    if (substr($email, -18) !== "@studentmail.ul.ie") {
        return false;
    }

    // get the student number part of the email
    $studentId = substr($email, 0, strpos($email, '@'));

    // Check if it is a student id (i.e. only numbers)  and does not exceed 8 digits (length of a studentId)
    return preg_match('/^\d{1,8}$/', $studentId);
}


// Process #15 to set the user_id based on the users student number which is inputted with the ul student email
function setUserId($email){
    $userIdString = explode("@", $email)[0]; // Extract the portion before @
    $userId = (int) $userIdString; // Convert to integer
    return $userId;
}

include "../helpers/db_connection.php";
include "../helpers/helperFunctions.php";

//Initalise array of errors which can then be displayed from the html
$errors = [];

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate inputs
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $firstName = htmlspecialchars($_POST['first_name']);
    $lastName = htmlspecialchars($_POST['last_name']);

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }

    // Check if email is a UL student email using the verifyStudentEmail function. Uses #Process 9, verifyStudentMail by checking if output
    // is false it will end
    if (!verifyStudentEmail($email)) {
        $errors[] = "Email must be a valid UL student email and the student number should not exceed 8 digits.";
    }

    // Check if password is between 8 and 20 characters long
    if (strlen($password) < 8 || strlen($password) > 20) {
        $errors[] = "Password must be between 8 and 20 characters long";
    }

    // Check for at least one capital letter and at least one special character with the inputted password
    if (!preg_match('/[A-Z]/', $password) || !preg_match('/[^a-zA-Z0-9]/', $password)) {
        $errors[] = "Password must be minimum 8 characters long, contain at least one capital letter and one special character";
    }

    // Check if the inputted and repeated passwords both match
    if ($_POST['password'] !== $_POST['password-repeat']) {
        $errors[] = "The password and repeated password do not match";
    }

    // Check if first name only contains letters, apostrophes, hyphens, and spaces
    if (!preg_match('/^[A-Za-z\'\-\s]+$/', $firstName)) {
    $errors[] = "First name can only contain letters, apostrophes, hyphens, and spaces.";
    }

    // Check if last name only contains Letters, apostrophes, hyphens, and spaces
    if (!preg_match('/^[A-Za-z\'\-\s]+$/', $lastName)) {
    $errors[] = "Last name can only contain letters, apostrophes, hyphens, and spaces.";
    }
    //Included apostrophes and hyphens in the regex for names as could have names such as Gary O'Brien or Mary-Anne

    //If erros are empty. then procede with Inserting new account into account table of db and setting the  attributes
    if (empty($errors)) {

        // Call setUserId function
        $userId = setUserId($email);

        
    //check if userId already excists in db
    $sqlCheckUserIdExists = "SELECT user_id FROM account WHERE user_id = ?";
    $checkUserId = $conn->prepare($sqlCheckUserIdExists);
    $checkUserId->bind_param('i', $userId);
    $checkUserId->execute();
    $checkUserId->store_result();

    // If the userId already exists, add error message to array of errors
    if ($checkUserId->num_rows > 0) {
        $errors[] = "An account with this student ID already exists.";
        } else {
    
            // Insert the newly successful registered email and the set userID into account table of database
            $query = "INSERT INTO account (user_id, email) VALUES (?,?)";
            $insertAccountStatement = $conn->prepare($query);
            $insertAccountStatement->bind_param('is', $userId, $email);
            $insertAccountStatement->execute();

            // Check if the insertion was successful
            if ($insertAccountStatement->affected_rows > 0) {

                // Start the session and set the email and id of the user
                if(session_status() === PHP_SESSION_NONE) {
                    session_start();
                }

                $_SESSION['email'] = $email;
                $_SESSION['user_id'] = $userId;

                // Using the setter methods from above
                setPassword($password, $userId);
                setName($firstName, $lastName, $userId);


                // Redirect to the edit profile page
                header("Location: ../editprofile/editProfile.php");
            } else {
                $errors[] = "Error occurred during registration";
            }

        // Close insert of new account as the HTTP request has been posted to db
        $insertAccountStatement->close();
            }
        // Close checkUserId statement
        $checkUserId->close();
    }
}

// Include HTML content from registration.html
include "registration.html";