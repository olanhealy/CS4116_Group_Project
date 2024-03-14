<?php

// Include database connection
include "db_connection.php";

// Start the session
session_start();

// Fetch user details from the database
$user_id = $_SESSION['id'];

// Process #13 to set the user's bio in the profile table of the db
function setBio($user_id, $bio) {
    global $conn;

    $sql_set_bio = "UPDATE profile SET bio = ? WHERE user_id = ?";
    $set_bio = $conn->prepare($sql_set_bio);
    $set_bio->bind_param("si", $bio, $user_id);
    $set_bio->execute();

    if ($set_bio->affected_rows > 0) {
        echo "Bio set successfully";
    } else {
        echo "Error setting bio";
    }
}

// Process #17 to set the user's gender in the profile table of the db
function setGender($user_id, $gender) {
    global $conn;

    $sql_set_gender = "UPDATE profile SET gender = ? WHERE user_id = ?";
    $set_gender = $conn->prepare($sql_set_gender);
    $set_gender->bind_param("si", $gender, $user_id);
    $set_gender->execute();

    if ($set_gender->affected_rows > 0) {
        echo "Gender set successfully";
    } else {
        echo "Error setting Gender";
    }

    $set_gender->close();
}

// Process #19 to set the user's age in the profile table of the db
function setAge($age, $user_id) {
    global $conn;

    $sql_set_age = "UPDATE profile SET age = ? WHERE user_id = ?";
    $set_age = $conn->prepare($sql_set_age);
    $set_age->bind_param("ii", $age, $user_id);
    $set_age->execute();

    if ($set_age->affected_rows > 0) {
        echo "Age set successfully";
    } else {
        echo "Error setting Age";
    }

    $set_age->close();
}

// Process #21 to set the user's college year in the profile table of the db
function setCollegeYear($user_id, $college_year) {
    global $conn;

    $sql_set_college_year = "UPDATE profile SET college_year = ? WHERE user_id = ?";
    $set_college_year = $conn->prepare($sql_set_college_year);
    $set_college_year->bind_param("si", $college_year, $user_id);
    $set_college_year->execute();

    if ($set_college_year->affected_rows > 0) {
        echo "College Year set successfully";
    } else {
        echo "Error setting College Year";
    }

    $set_college_year->close();
}

// Process #23 to set the user's pursuing status in the profile table of the db
function setPursuing($user_id, $pursuing) {
    global $conn;

    $sql_set_pursuing = "UPDATE profile SET pursuing = ? WHERE user_id = ?";
    $set_pursuing = $conn->prepare($sql_set_pursuing);
    $set_pursuing->bind_param("si", $pursuing, $user_id);
    $set_pursuing->execute();

    if ($set_pursuing->affected_rows > 0) {
        echo "College Year set successfully";
    } else {
        echo "Error setting College Year";
    }

    $set_pursuing->close();
}

// Process #25 to set the user's profile picture in the profile table of the db
function setProfilePic($user_id, $profile_pic_filename) {
    global $conn;

    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == UPLOAD_ERR_OK) {
        $upload_dir = "uploads/";
        $profile_pic_filename = $upload_dir . basename($_FILES['profile_pic']['name']);

        if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $profile_pic_filename)) {
            // If file is uploaded successfully, set profile pic in database
            setProfilePic($user_id, $profile_pic_filename);
        } else {
            echo "Error uploading profile picture";
        }
    }

    $sql_set_profile_pic = "UPDATE profile SET profile_pic = ? WHERE user_id = ?";
    $set_profile_pic = $conn->prepare($sql_set_profile_pic);
    $set_profile_pic->bind_param("si", $profile_pic_filename, $user_id);
    $set_profile_pic->execute();

    if ($set_profile_pic->affected_rows > 0) {
        echo "Profile picture set successfully";
    } else {
        echo "Error setting profile picture";
    }
}

// Process #29 to set the user's course of study in the profile table of the db
function setCourse($user_id, $course) {
    global $conn;

    $sql_set_course = "UPDATE profile SET course = ? WHERE user_id = ?";
    $set_course = $conn->prepare($sql_set_course);
    $set_course->bind_param("si", $course, $user_id); // Corrected parameter binding
    $set_course->execute();

    if ($set_course->affected_rows > 0) {
        echo "Course set successfully";
    } else {
        echo "Error setting course";
    }
}

// Process #31 to set the user's hobbies in the profile table of the db
function setHobbies($user_id, $hobbies) {
    global $conn;

    $sql_set_hobbies = "UPDATE profile SET hobbies = ? WHERE user_id = ?";
    $set_hobbies = $conn->prepare($sql_set_hobbies);
    $set_hobbies->bind_param("si", $hobbies, $user_id);
    $set_hobbies->execute();

    if ($set_hobbies->affected_rows > 0) {
        echo "Hobbies set successfully";
    } else {
        echo "Error setting hobbies";
    }
}

// Process #33 to set the user's looking for status in the profile table of the db
function setLookingFor($user_id, $looking_for) {
    global $conn;

    $sql_set_looking_for = "UPDATE profile SET looking_for = ? WHERE user_id = ?";
    $set_looking_for = $conn->prepare($sql_set_looking_for);
    $set_looking_for->bind_param("si", $looking_for, $user_id);
    $set_looking_for->execute();

    if ($set_looking_for->affected_rows > 0) {
        echo "Looking for set successfully";
    } else {
        echo "Error setting looking for";
    }
}

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