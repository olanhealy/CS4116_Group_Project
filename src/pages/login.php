<?php

    session_start(); 

    include "db_connection.php";

    //validate input
    if (isset($_POST['email']) && isset($_POST['password'])) {

        function validate($data){
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    
    }

    //get values passed in
    $email = $_POST['email'];
    $pass = $_POST['password'];

    //error check email 
    if (empty($email)) {
        header("Location: index.php?error=Email is required");
        exit();

    }else if(empty($pass)){
        header("Location: index.php?error=Password is required");
        exit();

    }

    //sql query
    $sql = "SELECT * FROM Account WHERE email='$email' AND password_hash='$pass'";
    $result = mysqli_query($conn, $sql);

    //check if user exists
    if (mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);

        //if user exists, log them in
        if ($row['email'] === $email && $row['password_hash'] === $pass) {
            echo "Logged in!";

            $_SESSION['email'] = $row['email'];
            $_SESSION['id'] = $row['user_id'];

            header("Location: home.php");

            exit();
            }

        //if user does not exist, return to login page
        }else{
            header("Location: index.php?error=Incorect User name or password");
            exit();
        }
    
}else{
    header("Location: index.php");
    exit();
}