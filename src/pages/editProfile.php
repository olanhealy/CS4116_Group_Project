<?php

include "db_connection.php";
require "helperFunctions.php";

// Start the session
if(session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Fetch user details from the database
$userId = $_SESSION['user_id'];


//Call getter method so if the user has registered and navitage to edit_profile page, they will see their previous inpts
//Moved all the getter methods into "helperFunctions.php" for reuseability  
$bio = getBio($userId);
$hobbies = getHobbies($userId);
$gender = getGender($userId);
$age = getAge($userId);
$collegeYear = getCollegeYear($userId);
$pursuing = getPursuing($userId);
$profilePicFilename = getProfilePicture($userId);
$course = getCourse($userId);
$lookingFor = getLookingFor($userId);
$name = getName($userId);

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {


    // Validate inputs
    $bio = htmlspecialchars($_POST['bio']);
    $gender = htmlspecialchars($_POST['gender']);
    $age = intval($_POST['age']);
    $collegeYear = htmlspecialchars($_POST['college_year']);
    $pursuing = htmlspecialchars($_POST['pursuing']);
    $course = htmlspecialchars($_POST['course']);
    $hobbies = htmlspecialchars($_POST['hobbies']);
    $lookingFor = htmlspecialchars($_POST['looking_for']);
    $profilePicFilename = htmlspecialchars($_FILES['profile_pic']['name']);

    var_dump($_SESSION);
    // Update the user currently logged in profile table in the database
    $userId = $_SESSION['user_id']; //we use this from being logged in

    // Call setter methods to make the updates in db
    setBio($userId, $bio);
    setGender($userId, $gender);
    setAge($age, $userId);
    setCollegeYear($userId, $collegeYear);
    setPursuing($userId, $pursuing);
    setProfilePic($userId, $profilePicFilename);
    setCourse($userId, $course);
    setHobbies($userId, $hobbies);
    setLookingFor($userId, $lookingFor);

    header("Location: home.php");
}

//TODO: frontend: Edit profile formatted here
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

    <?php if(isset($age)){ ?>
        <a href="home.php">Home</a>
    <?php } ?>
    
    <h1>Edit Profile</h1>

        <form action="editProfile.php" method="post" enctype="multipart/form-data">

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
        <img src="<?php echo $profilePicFilename ? '/' . htmlspecialchars($profilePicFilename) : '/path/to/default/image.png'; ?>" alt="Profile Picture" style="max-width:200px;">

        
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
            <option value="Undergrad" <?php if ($collegeYear == "Undergrad") echo "selected"; ?>>Undergrad</option>
            <option value="Masters" <?php if ($collegeYear == "Masters") echo "selected"; ?>>Masters</option>
            <option value="PhD" <?php if ($collegeYear == "PhD") echo "selected"; ?>>PhD</option>
        </select>

        <!-- COurse-->
        <label for="course">Course:</label>
        <input type="text" id="course" name="course" required value="<?php echo htmlspecialchars($course); ?>">

        <!-- Looking for-->
        <label for="looking_for">Looking For:</label>
        <select id="looking_for" name="looking_for" required>
            <option value="Short-term" <?php if ($lookingFor == "Short-term") echo "selected"; ?>>Short-Term</option>
            <option value="Long-term"<?php if ($lookingFor == "Long-term") echo "selected"; ?>>Long-Term</option>
            <option value="Unsure" <?php if ($lookingFor == "Unsure") echo "selected"; ?>>Unsure</option>
        </select>

        <!-- Button to just update changes made in db -->
        <button type="submit">Save Changes</button>
    </form>

    

</body>
</html>
