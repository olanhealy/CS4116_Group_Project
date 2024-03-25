<?php

// Include database connection
include "db_connection.php";

//Requiring the "helper.php" cllass which contains getters and setters
require "helper.php";

// Start the session
session_start();

// Fetch user details from the database
$user_id = $_SESSION['id'];


//Call getter method so if the user has registered and navitage to edit_profile page, they will see their previous inpts
//Moved all the getter methods into "helper.php" for reuseability  
$bio = getBio($user_id);
$hobbies = getHobbies($user_id);
$gender = getGender($user_id);
$age = getAge($user_id);
$college_year = getCollegeYear($user_id);
$pursuing = getPursuing($user_id);
$profile_pic_filename = getProfilePicture($user_id);
$course = getCourse($user_id);
$looking_for = getLookingFor($user_id);

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize input
    $bio = htmlspecialchars($_POST['bio']);
    $gender = htmlspecialchars($_POST['gender']);
    $age = intval($_POST['age']);
    $college_year = htmlspecialchars($_POST['college_year']);
    $pursuing = htmlspecialchars($_POST['pursuing']);
    $course = htmlspecialchars($_POST['course']);
    $hobbies = htmlspecialchars($_POST['hobbies']);
    $looking_for = htmlspecialchars($_POST['looking_for']);

    // Update the user currently logged in profile table in the database
    $user_id = $_SESSION['id']; //we use this from being logged in

    // Call setter methods to make the updates in db
    setBio($user_id, $bio);
    setGender($user_id, $gender);
    setAge($age, $user_id);
    setCollegeYear($user_id, $college_year);
    setPursuing($user_id, $pursuing);
    setProfilePic($user_id, $profile_pic_filename);
    setCourse($user_id, $course);
    setHobbies($user_id, $hobbies);
    setLookingFor($user_id, $looking_for);

    header("Location: home.php");
}

// Including edit profile html file 
include "edit_profile.html";
?>