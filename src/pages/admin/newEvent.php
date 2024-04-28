<?php
include "../db_connection.php";
include "adminHelperFunctions.php";
adminAccessCheck();

$message = '';

if(isset($_POST['submit'])){
    // Check if a file was uploaded
    if(isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../../assets/images/';
        $uploadFile = $uploadDir . 'event.jpeg'; // Rename uploaded file to 'event.jpeg'
        $imageFileType = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

        // Check if the file is an actual image
        $check = getimagesize($_FILES['image']['tmp_name']);
        if($check !== false) {
            // Check file size
            if ($_FILES['image']['size'] > 500000) {
                $message = 'Sorry, your file is too large.';
            } else {
                // Allow certain file formats
                if($imageFileType != 'jpg' && $imageFileType != 'png' && $imageFileType != 'jpeg') {
                    $message = 'Sorry, only JPG, JPEG, and PNG files are allowed.';
                } else {
                    // Upload file
                    if(move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
                        $message = 'The file '. htmlspecialchars( basename( $_FILES['image']['name'])). ' has been uploaded and replaced the existing image. This may take a few minutes to update on the system.';
                    } else {
                        $message = 'Sorry, there was an error uploading your file.';
                    }
                }
            }
        } else {
            $message = 'File is not an image.';
        }
    } else {
        $message = 'No file uploaded.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin</title>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap Stylesheet -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <!-- Main Stylesheet -->
    <link rel="stylesheet" type="text/css" href="../../assets/css/newEventAdmin.css">

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
        <button type="button" id="logoutbutton" class="btn button d-none d-md-block"
            onclick="location.href='../logout.php'">Log Out</button>
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

    <!-- Main Container -->
    <div class="container-fluid">
        <div class="container-l" style="text-align: center;">
            <h1 id="header">Change Current Event on Home Page</h1>
            <br><br>

            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" enctype="multipart/form-data">
                <!-- Choose File -->
                <label for="image" class="fileUploadBtn">Choose File</label>
                <input type="file" name="image" id="image">

                <!-- Save Changes -->
                <button type="submit" name="submit" class="btn btn-secondary mt-2 mb-4 saveChangesBtn">Upload Image</button>
            </form>

            <p><?php echo $message; ?></p>
        </div>
    </div>



    <!-- Footer -->
    <footer class="p-2">
        © 2024 Copyright UL Singles. All Rights Reserved
    </footer>

</body>
</html>