<?php


include "db_connection.php";

//Initalise array of erros which can then be displayed from the html
$errors = [];

// Process #4 checks if the Account already exists in the account table of db
function isAccountFound($email, $password)
{
    global $conn;

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

// Process #11 to set the First name and Last name in the account table in db
function setName($first_name, $last_name, $user_id)
{
    global $conn;

    $sql_set_name = "UPDATE account SET first_name = ?, last_name = ? WHERE user_id = ?";

    $set_name = $conn->prepare($sql_set_name);
    $set_name->bind_param("sss", $first_name, $last_name, $user_id);
    $set_name->execute();

    if ($set_name->affected_rows > 0) {
        echo "First and Last Name set successfully";
    } else {
        echo "Error setting First and Last Name";
    }
}

// Process #15 to set the user_id based on the users student number which is inputted with the ul student email
function setUserId($email)
{
    $user_id = explode("@", $email)[0]; // Extract the portion before @
    return $user_id;
}

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
        isAccountFound($email, $password);

        // Insert the newly successful registered email and the set userID into account table of database
        $sql_insert_account = "INSERT INTO account (user_id, email) VALUES (?,?)";
        $insert_account_statement = $conn->prepare($sql_insert_account);
        $insert_account_statement->bind_param('is', $user_id, $email);
        $insert_account_statement->execute();

        // Check if the insertion was successful
        if ($insert_account_statement->affected_rows > 0) {

            // Get the user id of this registered user
            $user_id = $insert_account_statement->insert_id;

            // Using the setter methods from above
            setPassword($password, $user_id);
            setName($first_name, $last_name, $user_id);

            // Insert the user id  into profile table, also inserting the full name of user
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
            $errors[] = "Error occurred during registration";
        }

        // Close insert of new account as the HTTP request has been posted to db
        $insert_new_account->close();
    }
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <title>Registration</title>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap Stylesheet -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>

    <!-- Main Stylesheet -->
    <link rel="stylesheet" href="css/main.css">

    <!-- Bootstrap Icon -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link rel="stylesheet" type="text/css" href="../assets/css/registration.css">
</head>

<body>


    <?php
    // In edit_profile.php, any errors that may be encountered while registering are stored
    // In an array. If there are erros, display them as a list here
    if (!empty($errors)) {
        echo "<ul>";
        foreach ($errors as $error) {
            echo "<li>$error</li>";
        }
        echo "</ul>";
    }
    ?>

    <nav class="navbar navbar-fixed-top">
        <div class="images">
            <img class="header-img" src="../assets/images/ul_logo.png" alt="ul_logo">
            <div class="line"></div>
            <img class="header-img" src="../assets/images/ulSinglesTrasparent.png" alt="ulSingles_logo">
        </div>

        <div class="btn-group buttons d-none d-lg-block" role="group">
            <button type="button" class="btn button" onclick="location.href='login.php'">Log In</button>
            <button type="button" class="btn button" onclick="location.href='registration.php'">Sign Up</button>
        </div>

    </nav>

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6 register-left">
                <img src="../assets/images/ulSinglesTrasparent.png" class="img-fluid image" alt="UL Singles Logo">
            </div>

            <div class="col-md-6 register-right">

                <h1 class="h3 mb-3 font-weight-normal text-center mb-4 mx-1 mt-4">Create an account</h1>

                <form class="mx-1 mx-md-4" action="registration.php" method="post">

                    <div class="form-input mb-4">
                        <!-- Email  -->
                        <input type="email" class="form-control" placeholder="Email" name="email" required>
                    </div>


                    <div class="form-input mb-4">
                        <!-- Password  -->
                        <input type="password" class="form-control" placeholder="Password" name="password" required>
                    </div>

                    <div class="form-input mb-4">
                        <!-- Repeated Password  -->
                        <input type="password" class="form-control" placeholder="Confirm Password"
                            name="password-repeat" required>
                    </div>

                    <div class="form-input mb-4">
                        <!-- First Name  -->
                        <input type="text" class="form-control" placeholder="First Name" name="first_name" required>
                    </div>

                    <div class="form-input mb-4">
                        <!-- Last Name  -->
                        <input type="text" class="form-control" placeholder="Last Name" name="last_name" required>
                    </div>

                    <div class="form-check d-flex justify-content-center mb-4">
                        <!-- Terms and Conditions checkbox  -->
                        <input class="form-check-input me-2" type="checkbox" name="agreed" required>
                        <label class="form-check-label" for="agreed">I agree to the terms and conditions</label>
                    </div>

                    <div class="d-flex justify-content-center mb-4">
                        <input type="hidden" name="submitted" value="1">
                        <button class="btn btn-secondary custom-btn" type="submit">Sign Up</button>
                    </div>

                    <p class="d-flex justify-content-center">Already have an account? <a href="login.php">Log in
                            here</a></p>

                </form>

            </div>
        </div>
    </div>

    <footer class="p-2">
        © 2024 Copyright UL Singles. All Rights Reserved
    </footer>

</body>

</html>