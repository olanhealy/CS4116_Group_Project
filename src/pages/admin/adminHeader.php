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
    <link rel="stylesheet" type="text/css" href="../../assets/css/admin.css">

    <!-- Bootstrap Icon -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

</head>
<body>

    <!-- Start of Navbar -->
    <nav class="navbar navbar-fixed-top" id="navbar">

        <!-- Images -->
        <div class="images">
          <img class="header-img d-none d-md-block" src="../../assets/images/ul_logo.png" alt="ul_logo">
          <div class="line"></div>
          <img class="header-img" src="../../assets/images/ulSinglesTrasparent.png" alt="ulSingles_logo">
        </div>

        <!-- Buttons -->
        <div class="btn-group ms-auto" role="group">
            <button type="button" id="adminbutton" class="btn button d-none d-md-block" onclick="location.href='admin.html'">Admin</button>
            <button type="button" id="logoutbutton" class="btn button d-none d-md-block" onclick="location.href='logout.php'">Log Out</button>
        </div>

        <!-- Profile Icon Dropdown -->
        <div class="dropdown">
            <button class="btn btn-secondary" id="iconbutton" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <svg xmlns="http://www.w3.org/2000/svg" width="45" height="40" fill="currentColor" class="bi bi-person-circle" viewBox="0 0 16 16">
                    <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0"/>
                    <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1"/>
                </svg>
            </button>
            <ul class="dropdown-menu d-md-none" aria-labelledby="iconbutton" id="profiledropdown">
                <li><a class="dropdown-item-profile d-md-none" href="admin.html" id="admindropdown">Admin</a></li>
                <li><hr class="dropdown-divider d-md-none"></li>
                <li><a class="dropdown-item-profile d-md-none" href="../logout.php" id="logoutdropdown">Log Out</a></li>
            </ul>
        </div>

    </nav>
    <hr>
    <!-- End of Navbar -->
</body>
</html>