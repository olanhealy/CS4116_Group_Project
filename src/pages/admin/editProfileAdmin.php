<?php
session_start();

// Include the helper.php file
include '../helper.php';
include '../db_connection.php';

if (isset($_SESSION['targetId'])) {
    $target_id = $_SESSION['targetId'];

    // Fetch existing profile information
    $existing_bio = getBio($target_id);
    $existing_hobbies = getHobbies($target_id);
    $existing_course = getCourse($target_id);

    // Check if form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Process form data

        // Example: Updating user's bio
        if (isset($_POST['bio'])) {
            $bio = $_POST['bio'];
            setBio($target_id, $bio);
        }

        // Example: Updating user's profile picture
        if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == UPLOAD_ERR_OK) {
            $profile_pic_filename = $_FILES['profile_pic']['name'];
            setProfilePic($target_id, $profile_pic_filename);
        }

        // Example: Updating user's hobbies
        if (isset($_POST['hobbies'])) {
            $hobbies = $_POST['hobbies'];
            setHobbies($target_id, $hobbies);
        }

        // Example: Updating user's course
        if (isset($_POST['course'])) {
            $course = $_POST['course'];
            setCourse($target_id, $course);
        }

    }

    ?>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Edit Profile</title>
    </head>

    <body>
        <h1>Edit Profile</h1>

        <!-- Display existing profile information -->
        <h2>Existing Profile Information</h2>
        <p><strong>Bio:</strong>
            <?php echo htmlspecialchars($existing_bio); ?>
        </p>
        <p><strong>Hobbies:</strong>
            <?php echo htmlspecialchars($existing_hobbies); ?>
        </p>
        <p><strong>Course:</strong>
            <?php echo htmlspecialchars($existing_course); ?>
        </p>

        <hr>

        <!-- Form for editing profile information -->
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
            <label for="bio">Bio:</label><br>
            <textarea id="bio" name="bio"><?php echo htmlspecialchars($existing_bio); ?></textarea><br>

            <label for="profile_pic">Profile Picture:</label><br>
            <input type="file" id="profile_pic" name="profile_pic"><br>

            <label for="hobbies">Hobbies:</label><br>
            <input type="text" id="hobbies" name="hobbies" value="<?php echo htmlspecialchars($existing_hobbies); ?>"><br>

            <label for="course">Course:</label><br>
            <input type="text" id="course" name="course" value="<?php echo htmlspecialchars($existing_course); ?>"><br>

            <input type="submit" value="Submit">
        </form>
    </body>

    </html>

    <?php
    
} else {
    echo "Target ID is not set.";
    exit();
}

?>