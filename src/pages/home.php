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

    <!-- Bootstrap Stylesheet -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <!-- Main Stylesheet -->
    <link rel="stylesheet" type="text/css" href="../assets/css/home.css">

    <!-- Bootstrap Icon -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

</head>
<body>

    <!-- Start of Navbar -->
    <nav class="navbar navbar-fixed-top" id="navbar">

        <!-- Images -->
        <div class="images">
            <img class="header-img d-none d-md-block" src="../assets/images/ul_logo.png" alt="ul_logo">
            <div class="line d-none d-md-block"></div>
            <img class="header-img" src="../assets/images/ulSinglesTrasparent.png" alt="ulSingles_logo">
        </div>

        <!-- Buttons -->
        <div class="btn-group ms-auto" role="group">
            <button type="button" id="explorebutton" class="btn button d-none d-md-block"
                onclick="location.href='explore.php'">Explore</button>
            <button type="button" id="logoutbutton" class="btn button d-none d-md-block"
                onclick="location.href='logout.php'">Log Out</button>
        </div>

        <!-- Profile Icon -->
        <div class="dropdown">
            <button class="btn-secondary" id="iconbutton" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <svg xmlns="http://www.w3.org/2000/svg" width="45" height="40" fill="currentColor"
                    class="bi bi-person-circle" viewBox="0 0 16 16">
                    <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0" />
                    <path fill-rule="evenodd"
                        d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1" />
                </svg>
            </button><?php if ($totalNotifications > 0): ?>
            <span class="badge bg-danger"><?php echo $totalNotifications; ?></span>
        <?php endif; ?>
    </button>
    <ul class="dropdown-menu" aria-labelledby="iconbutton" id="profiledropdown">
        <!-- Dropdown menu items -->
        <li><a class="dropdown-item-profile" href="editProfile.php">Edit Profile</a></li>
        <li><a class="dropdown-item-profile d-md-none" href="logout.php">Log Out</a></li>
        <?php if ($notifications['messages'] > 0): ?>
            <li><a class="dropdown-item-profile" href="messages/messages.php" onclick="clearNotifications('messages', <?php echo $userId; ?>)">You have new messages</a></li>
        <?php endif; ?>
        <?php if ($notifications['matches'] > 0): ?>
            <li><a class="dropdown-item-profile" href="matches.php" onclick="clearNotifications('matches', <?php echo $userId; ?>)">You have new matches</a></li>
        <?php endif; ?>
    </ul>
</div>

    </nav>
    <!-- End of Navbar -->

    <!-- Dropdown Menu Button -->
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 dropdownBtn">
                <button class="btn btn-primary dropdown-toggle" type="button" id="menu-dropdown"
                    data-bs-toggle="dropdown">
                    Home
                </button>

                <ul class="dropdown-menu" aria-labelledby="menu-dropdown" id="homedropdown">
                    <li><a class="dropdown-item" href="home.php">Home</a></li>
                    <li><a class="dropdown-item" href="explore.php">Explore</a></li>
                    <li><a class="dropdown-item" href="matches.php">Matches</a></li>
                    <li><a class="dropdown-item" href="messages/messages.php">Messages</a></li>
                    <li><a class="dropdown-item" href="searchPage.html">Search</a></li>
                </ul>
            </div>
        </div>
    </div>

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
<script>
    //Js function to clear notifications if they should be cleared
function clearNotifications(type, userId) {
    // Create a new instance of XMLHttpRequest which is used to make an HTTP request to the server
    var xhr = new XMLHttpRequest();

    //Make a POST request to the server where "resetNotifications.php" is the server-side script that will handle the request
    xhr.open("POST", 'resetNotifications.php', true);

    //  Content type is set to JSON 
    xhr.setRequestHeader("Content-Type", "application/json;charset=UTF-8");

    // function to be called when the readyState property changes
    xhr.onreadystatechange = function () {
        // Checks if the request is complete and the response is ready
        if (xhr.readyState == 4 && xhr.status == 200) {
            // Parses the JSON response from the server
            var response = JSON.parse(xhr.responseText);
         
        }
    };

    // Send the request to the server
    xhr.send(JSON.stringify({ type: type, user_id: userId }));
}
</script>
</html>