<?php
include "../helpers/db_connection.php";
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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Image</title>
    <link rel="icon" href="/ulSinglesSymbolTransparent.ico" type="image/x-icon">
</head>
<body>
<a href="admin.html">Admin Home</a>
    <h2>Upload Image</h2>
    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" enctype="multipart/form-data">
        <input type="file" name="image" id="image">
        <input type="submit" value="Upload Image" name="submit">
    </form>
    <p><?php echo $message; ?></p>
</body>
</html>
