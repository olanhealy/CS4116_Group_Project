<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Include the helperFunctions.php file
include_once '../helperFunctions.php';
include '../db_connection.php';

//var_dump($_SESSION);
if (isset($_SESSION['targetId'])) {
    $targetId = $_SESSION['targetId'];

    // Fetch existing profile information
    $_SESSION['existingName'] = getName($targetId);
    $_SESSION['existingBio'] = getBio($targetId);
    $_SESSION['existingHobbies'] = getHobbies($targetId);
    $_SESSION['existingCourse'] = getCourse($targetId);
    // added in to get the existing profile picture
    $_SESSION['existingProfilePic'] = getProfilePicture($targetId);


    include "editProfileAdmin.html";
    // Check if form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Process form data

        if (isset($_POST['first_name']) || isset($_POST['last_name']) && $_POST['first_name'] !== explode(' ', $_SESSION['existingName'])[0]|| $_POST['last_name'] !== explode(' ', $_SESSION['existingName'])[1]) {


            if ($_POST['first_name'] === explode(' ', $_SESSION['existingName'])[0]) {
                $firstName = explode(' ', $_SESSION['existingName'])[0];
            } else {
                $firstName = $_POST['first_name'];
            }

            if ($_POST['last_name'] === explode(' ', $_SESSION['existingName'])[1]) {
                $lastName = explode(' ', $_SESSION['existingName'])[1];
            } else {
                $lastName = $_POST['last_name'];
            }

            setName($firstName, $lastName, $targetId);

        }

        // Example: Updating user's bio
        if (isset($_POST['bio']) && $_POST['bio'] !== $_SESSION['existingBio']) {
            $bio = $_POST['bio'];
            setBio($targetId, $bio);
        }

        if (isset($_FILES['profile_pic']) && $_FILES['profile_pic'] !== $_SESSION['existingProfilePic']) {
            $profilePicFilename = $_FILES['profile_pic']['name'];
            setProfilePic($targetId, $profilePicFilename);
        }

        if (isset($_POST['hobbies']) && $_POST['hobbies'] !== $_SESSION['existingHobbies']) {
            $hobbies = $_POST['hobbies'];
            setHobbies($targetId, $hobbies);
        }

        if (isset($_POST['course']) && $_POST['course'] !== $_SESSION['existingCourse']) {
            $course = $_POST['course'];
            setCourse($targetId, $course);
        }

        header("Location: showUserAdmin.php");
    }


} else {
    echo "Target ID is not set.";
    exit();
}