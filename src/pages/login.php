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
            $error = "Please provide email and password";
        } else {
            // SQL query to retrieve user information based on email
            $sql = "SELECT * FROM account WHERE email='$email'";
            $result = mysqli_query($conn, $sql);

            // Check if user exists
            if (mysqli_num_rows($result) === 1) {
                $row = mysqli_fetch_assoc($result);

                if ($row['banned'] == '1') {
                    $error = "User banned. Please contact support.";
                } else if (password_verify($pass, $row['password_hash'])) {
                    // User authenticated, set session variables
                    $_SESSION['email'] = $row['email'];
                    $_SESSION['id'] = $row['user_id'];

                    if ($row['user_role'] == 'admin') {
                        header("Location: admin/admin.html");
                        exit();
                    }
                    // Redirect to home page
                    header("Location: home.php");
                    exit();
                } else {
                    $error = "Incorrect username or password";
                }
            } else {
                $error = "User does not exist";
            }
        }
    } else {
        $error = "Invalid form submission";
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Login</title>

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

    <link rel="stylesheet" type="text/css" href="../assets/css/login.css">

</head>

<body>

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
            <div class="col-12 login">
                <!-- login form  -->
                <form action="" method="post">
                    <img class="mt-4 mb-4 img-fluid" src="../assets/images/ulSinglesSymbolTransparent.png" height="200"
                        alt="ulSingles_symbol">
                    <h1 class="h3 mb-3 font-weight-normal">Log In</h1>

                    <?php if(isset($error)): ?>
                        <div class="alert alert-danger" role="alert">
                            <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>
                    
                    <input type="text" name="email" class="form-control" placeholder="Email" required autofocus><br>
                    <input type="password" name="password" placeholder="Password" class="form-control"><br>
                    <button type="submit" class="btn btn-secondary mb-4 custom-btn">Log in</button>

                    <!-- Link to Registration page via button -->
                    <p>Don't have an account? <a href="registration.php">Register here</a></p>
                </form>

            </div>
        </div>

    </div>

    <footer class="p-2">
        © 2024 Copyright UL Singles. All Rights Reserved
    </footer>

</body>

</html>
