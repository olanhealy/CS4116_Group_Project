<?php
session_start();

include "db_connection.php";

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate input
    if (isset($_POST['email']) && isset($_POST['password'])) {
        // Trim and sanitize user input
        $email = trim($_POST['email']);
        $pass = trim($_POST['password']);

        // Error check email and password
        if (empty($email) || empty($pass)) {
            // Redirect with error message appended to the URL
            header("Location: login.html?error=Please provide email and password");
            exit();
        }

        // SQL query to retrieve user information based on email
        $sql = "SELECT * FROM account WHERE email='$email'";
        $result = mysqli_query($conn, $sql);

        // Check if user exists
        if (mysqli_num_rows($result) === 1) {
            $row = mysqli_fetch_assoc($result);

            // Verify password and check banned status
            if (password_verify($pass, $row['password_hash']) && $row['banned'] == '0') {
                // User authenticated, set session variables
                $_SESSION['email'] = $row['email'];
                $_SESSION['id'] = $row['user_id'];

                // Redirect to home page
                header("Location: home.php");
                exit();
            } else if ($row['banned'] == '1') {
                // User is banned, redirect with error message
                header("Location: login.html?error=User banned. Please contact support.");
                exit();
            } else {
                // Incorrect username or password, redirect with error message
                header("Location: login.html?error=Incorrect username or password");
                exit();
            }
        } else {
            // User does not exist, redirect with error message
            header("Location: login.html?error=User does not exist");
            exit();
        }
    } else {
        // Invalid form submission, redirect with error message
        header("Location: login.html?error=Invalid form submission");
        exit();
    }
} else {
    // Redirect to login.html if the form was not submitted
    header("Location: login.html");
    exit();
}
?>
