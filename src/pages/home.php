<?php
include_once("helperFunctions.php");
include_once("db_connection.php");
if(session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in using the session variable
if(isset($_SESSION['user_id']) && isset($_SESSION['email'])){
    $userId = $_SESSION['user_id'];
} 
else {
    header("Location: index.php");
    exit();
}

$notifications = fetchNotifications($userId);
$totalNotifications = $notifications['messages'] + $notifications['matches'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Main Stylesheet -->
    <link rel="stylesheet" type="text/css" href="../assets/css/home.css">

    <!-- Bootstrap Icon -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

</head>
<body>
<?php 
    setupHeader();
    ?>

   
    <br>

    <!-- Main Container -->
    <div class="container-fluid mx-auto border border-5 col-md-12 col-lg-12 col-sm-12" id="outline">
        <div class="row">
            <div class="col-lg-6" id="leftContainer">
                <div class="container col-lg-12 col-md-12 col-sm-12" style="text-align: center;">
                    <a id="title"> 💚 UL Singles 💚 </a>
                </div>
    
                <div class="container col-lg-12 col-md-12 col-sm-12" style="text-align: center;">
                    <img id="deal" class="img-fluid" src="../assets/images/stablesDeal.png" alt="Stables Deal">
                </div>
    
                <div class="col-lg-12 col-md-12 col-sm-12" style="text-align: center; margin-bottom: 10px;">     
                    <a href="home.php" id="link"> View Events for the Week </a>
                </div> 
            </div>
            
            <div class="col-lg-6 d-lg-flex justify-content-center align-items-center order-lg-2" style="text-align: center">
                <img id="picture" class="img-fluid" src="../assets/images/holdinghands.jpg" alt="homepage_image">
            </div>
        </div>
    </div>
    
    


    <br>

    <!-- Slogan -->
    <section class="slogan">
        <div class="container-fluid text-center d-none d-md-block">
            <div class="col-sm">
                Explore. Adore. Ignore.
            </div>
        </div> 
    </section>

    <br>

    <!-- Footer -->
    <footer class="p-2">
        © 2024 Copyright UL Singles. All Rights Reserved
    </footer>

</body>
</html>