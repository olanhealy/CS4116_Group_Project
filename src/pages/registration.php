<?php


//include database connection
include "db_connection.php";

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
        $sql_check_email = "SELECT email FROM account WHERE email = ?";
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

        // Check if email inputted ends with "@studentmail.ul.ie"
        $email_domain = explode('@', $email)[1];
        if ($email_domain !== 'studentmail.ul.ie') {
            echo "<script>alert('Email must end with '@studentmail.ul.ie' ( Are u sure you are a UL Student?)');</script>";
            $successful_account_creation->close();  
            exit;
        }


         // Check if password is between 8 and 20 characters long
        if (strlen($password) < 8 || strlen($password) > 20) {
            echo "<script>alert{'Password must be between 8 and 20 characters long')</script>";
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

        // Hash the password for security 
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // Make sure all conditions met
        if ($_POST['email'] !== "" and isset($_POST['agreed']) and $_POST['password'] == $_POST['password-repeat'] and strlen($_POST['password']) > 5) {
            // Insert the newly succesful registered account into the database
            $sql_insert = "INSERT INTO account (email, password_hash, first_name, last_name) VALUES (?, ?, ?, ?)";
            $insert_new_account = $conn->prepare($sql_insert);
            $insert_new_account->bind_param('ssss', $email, $hashed_password, $first_name, $last_name);
            $insert_new_account->execute();

            if ($insert_new_account->affected_rows > 0) {
                session_start();
                $_SESSION['email'] = $email;
                $_SESSION['id'] = $insert_new_account->insert_id; 
                header("Location: home.php");
            } else {
                echo "<script>alert('Error occurred during registration')</script>";
            }

            // Close insert of new account as http request has been posted to db
            $insert_new_account->close();
        }

        // Close as the account has now been created successfully
        $successful_account_creation->close();
    }
}

// Include the HTML content from registration.html
include "registration.html";
?>
