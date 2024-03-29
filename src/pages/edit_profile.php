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
$name = getName($user_id);

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {


    // Validate inputs
    $bio = htmlspecialchars($_POST['bio']);
    $gender = htmlspecialchars($_POST['gender']);
    $age = intval($_POST['age']);
    $college_year = htmlspecialchars($_POST['college_year']);
    $pursuing = htmlspecialchars($_POST['pursuing']);
    $course = htmlspecialchars($_POST['course']);
    $hobbies = htmlspecialchars($_POST['hobbies']);
    $looking_for = htmlspecialchars($_POST['looking_for']);
    $profile_pic_filename = htmlspecialchars($_FILES['profile_pic']['name']);

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


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>

    <!-- @frontend made v basic html page for edit profile. styling and making it look nice should be done here -->
</head>
<body>
    <h1>Edit Profile</h1>

        <form action="edit_profile.php" method="post" enctype="multipart/form-data">

       <!-- Name -->
        <label for="name">Name:</label>
        <span id="name"><?php echo htmlspecialchars($name); ?></span>
        <!-- Age -->
        <label for="age">Age:</label>
        <input type="number" id="age" name="age" <?php if(isset($age)) echo "value='$age'"; ?> <?php if(isset($age)) echo "readonly"; ?> required>

        <!-- Gender -->
        <label for="gender">Gender:</label>
        <select id="gender" name="gender" <?php if(isset($gender)) echo "disabled"; ?> required>
            <option value="Male" <?php if(isset($gender) && $gender == "Male") echo "selected"; ?>>Male</option>
            <option value="Female" <?php if(isset($gender) && $gender == "Female") echo "selected"; ?>>Female</option>
            <option value="Other" <?php if(isset($gender) && $gender == "Other") echo "selected"; ?>>Other</option>
        </select>

        <!-- Hobbies (May need to update this as text for it is meh)-->
        <label for="hobbies">Hobbies:</label>
        <input type="text" id="hobbies" name="hobbies" required value="<?php echo htmlspecialchars($hobbies); ?>">

        <!-- Bio -->
        <label for="bio">Bio:</label>
        <textarea id="bio" name="bio" required><?php echo htmlspecialchars($bio); ?></textarea>

        <!-- Profile Picture-->
        <label for="profile_pic">Profile Picture:</label>
        <input type="file" id="profile_pic" name="profile_pic">
        <img src="<?php echo $profile_pic_filename ? '/' . htmlspecialchars($profile_pic_filename) : '/path/to/default/image.png'; ?>" alt="Profile Picture" style="max-width:200px;">

        
        <!--  pursuing -->
        <label for="pursuing">Pursuing:</label>
        <select id="pursuing" name="pursuing" required>
            <option value="Male" <?php if ($pursuing == "Male") echo "selected"; ?>>Male</option>
            <option value="Female" <?php if ($pursuing == "Female") echo "selected"; ?>>Female</option>
            <option value="Other"<?php if ($pursuing == "Other") echo "selected"; ?>>Other</option>
        </select>

        <!-- College Year -->
        <label for="college_year">College Year:</label>
        <select id="college_year" name="college_year" required>
            <option value="Undergrad" <?php if ($college_year == "Undergrad") echo "selected"; ?>>Undergrad</option>
            <option value="Masters" <?php if ($college_year == "Masters") echo "selected"; ?>>Masters</option>
            <option value="PhD" <?php if ($college_year == "PhD") echo "selected"; ?>>PhD</option>
        </select>

        <!-- COurse-->
        <label for="course">Course:</label>
        <input type="text" id="course" name="course" required value="<?php echo htmlspecialchars($course); ?>">

        <!-- Looking for-->
        <label for="looking_for">Looking For:</label>
        <select id="looking_for" name="looking_for" required>
            <option value="Short-term" <?php if ($looking_for == "Short-term") echo "selected"; ?>>Short-Term</option>
            <option value="Long-term"<?php if ($looking_for == "Long-term") echo "selected"; ?>>Long-Term</option>
            <option value="Unsure" <?php if ($looking_for == "Unsure") echo "selected"; ?>>Unsure</option>
        </select>

        <!-- Button to just update changes made in db -->
        <button type="submit">Save Changes</button>
    </form>

    

</body>
</html>
