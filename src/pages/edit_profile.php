<?php

//include database connection
include "db_connection.php";

// Start the session
session_start();


// Fetch user details from the database
$user_id = $_SESSION['id'];
$sql = "SELECT age, gender FROM profile WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($age, $gender);
$stmt->fetch();

$stmt->close();

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize input
    $age = intval($_POST['age']);
    $gender = htmlspecialchars($_POST['gender']);
    $hobbies = htmlspecialchars($_POST['hobbies']);
    $bio = htmlspecialchars($_POST['bio']);
    $pursuing = htmlspecialchars($_POST['pursuing']);
    $college_year = htmlspecialchars($_POST['college_year']);
    $course = htmlspecialchars($_POST['course']);
    $looking_for = htmlspecialchars($_POST['looking_for']);

    // Handle profile picture upload
    $profile_pic_filename = ""; 
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == UPLOAD_ERR_OK) {
        $upload_dir = "uploads/"; 
        $profile_pic_filename = $upload_dir . basename($_FILES['profile_pic']['name']);

        move_uploaded_file($_FILES['profile_pic']['tmp_name'], $profile_pic_filename);
    }

    // Update the user currently logged in  profile  table in the database
    $user_id = $_SESSION['id']; //we use this from being logged in

    $update_sql = "UPDATE profile SET age=?, gender=?, hobbies=?, bio=?, profile_pic=?, pursuing=?, college_year=?, course=?, looking_for=? WHERE user_id=?";
    $update_statement = $conn->prepare($update_sql);
    $update_statement->bind_param('issssssssi', $age, $gender, $hobbies, $bio, $profile_pic_filename, $pursuing, $college_year, $course, $looking_for, $user_id);
    $update_statement->execute();

    if ($update_statement->affected_rows > 0) {
        // If profile table is updated like it should, site should send you back to homepage
        header("Location: home.php"); 
        exit();
    } else {
        echo "Error updating profile: " . $update_statement->error;
    }

    $update_statement->close();
}

// Including edit profile html file 
include "edit_profile.html";
?>
