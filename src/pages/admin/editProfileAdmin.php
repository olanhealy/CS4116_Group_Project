<?php

if(session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Include the helper.php file
include_once '../helper.php';
include '../db_connection.php';

//var_dump($_SESSION);
if (isset($_SESSION['targetId'])) {
    $targetId = $_SESSION['targetId'];

    // Fetch existing profile information
    $_SESSION['existingBio'] = getBio($targetId);
    $_SESSION['existingHobbies'] = getHobbies($targetId);
    $_SESSION['existingCourse'] = getCourse($targetId);
    // added in to get the existing profile picture
    $_SESSION['existingProfilePic'] = getProfilePicture($targetId);


    include "editProfileAdmin.html";
    // Check if form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Process form data

        // Example: Updating user's bio
        if (isset($_POST['bio'])) {
            $bio = $_POST['bio'];
            setBio($targetId, $bio);
        }

        // Example: Updating user's profile picture
        if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == UPLOAD_ERR_OK) {
            $profilePicFilename = $_FILES['profile_pic']['name'];
            setProfilePic($targetId, $profilePicFilename);
        }

        // Example: Updating user's hobbies
        if (isset($_POST['hobbies'])) {
            $hobbies = $_POST['hobbies'];
            setHobbies($targetId, $hobbies);
        }

        // Example: Updating user's course
        if (isset($_POST['course'])) {
            $course = $_POST['course'];
            setCourse($targetId, $course);
        }
    }


} else {
    echo "Target ID is not set.";
    exit();
}