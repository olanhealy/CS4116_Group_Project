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

    <!-- Bootstrap Stylesheet -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <!-- Bootstrap Icon -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Scripts -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-beta.0/css/select2.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-beta.0/js/select2.min.js"></script>

    <!-- External Stylesheet -->
    <link rel="stylesheet" type="text/css" href="../assets/css/edit_profile.css">

</head>

<body>

    <!-- CourseOfStudy Enum -->
    <?php include '../assets/enums/CourseOfStudy.php'; ?>

    <!-- Start of Navbar -->
    <nav class="navbar navbar-fixed-top" id="navbar">

        <!-- Images -->
        <div class="images">
            <img class="header-img d-none d-md-block" src="../assets/images/ul_logo.png" alt="ul_logo">
            <div class="line d-none d-md-block"></div>
            <img class="header-img" src="../assets/images/ulSinglesTrasparent.png" alt="ulSingles_logo">
        </div>

        <!-- Buttons -->
        <div class="btn-group ms-auto" role="group">
            <button type="button" id="explorebutton" class="btn button d-none d-md-block" onclick="location.href='explore.php'">Explore</button>
            <button type="button" id="logoutbutton" class="btn button d-none d-md-block" onclick="location.href='logout.php'">Log Out</button>
        </div>

        <!-- Profile Icon -->
        <div class="dropdown">
            <button class="btn-secondary" id="iconbutton" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <svg xmlns="http://www.w3.org/2000/svg" width="45" height="40" fill="currentColor" class="bi bi-person-circle" viewBox="0 0 16 16">
                    <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0" />
                    <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1" />
                </svg>
            </button>
            <ul class="dropdown-menu" aria-labelledby="iconbutton" id="profiledropdown">
                <li><a class="dropdown-item-profile" href="edit_profile.php">Edit Profile</a></li>
                <li><a class="dropdown-item-profile d-md-none" href="logout.php">Log Out</a></li>
            </ul>
        </div>

    </nav>
    <!-- End of Navbar -->

    <!-- Dropdown Menu Button -->
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 dropdownBtn">
                <button class="btn btn-primary dropdown-toggle" type="button" id="menu-dropdown" data-bs-toggle="dropdown">
                    Edit Profile
                </button>

                <ul class="dropdown-menu" aria-labelledby="menu-dropdown" id="homedropdown">
                    <li><a class="dropdown-item" href="home.php">Home</a></li>
                    <li><a class="dropdown-item" href="explore.php">Explore</a></li>
                    <li><a class="dropdown-item" href="matches.php">Matches</a></li>
                    <li><a class="dropdown-item" href="messages.php">Messages</a></li>
                </ul>
            </div>
        </div>
    </div>

    <br>

    <!-- Form -->
    <div class="container-fluid border border-2 col-md-12 col-lg-12 col-sm-12" id="outline">
        <div class="row">
            <form class="container-fluid" action="edit_profile.php" method="post" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-lg-7 order-lg-2 col-md-12 info-box">

                        <div class="row inputField">
                            <div class="col-md-4 col-sm-12 col-lg-4">
                                <!-- Name -->
                                <label for="name" class="inputLabelText">Name</label> <br>
                                <span id="name"><?php echo htmlspecialchars($name); ?></span>
                            </div>

                            <div class="col-md-4 col-sm-12 col-lg-4">
                                <!-- Age -->
                                <label for="age" class="inputLabelText">Age</label><br>
                                <input type="number" id="age" name="age" class="textInput" placeholder="Type here..." <?php if (isset($age)) echo "value='$age'"; ?> <?php if (isset($age)) echo "readonly"; ?> required>
                            </div>

                            <div class="col-md-4 col-sm-12 col-lg-4">
                                <!-- Gender -->
                                <label for="gender" class="inputLabelText">Gender</label><br>
                                <select id="gender" name="gender" class="optionDropdown" <?php if (isset($gender)) echo "disabled"; ?> required>
                                    <option value="" disabled selected>Choose..</option>
                                    <option value="Male" <?php if (isset($gender) && $gender == "Male") echo "selected"; ?>>Male</option>
                                    <option value="Female" <?php if (isset($gender) && $gender == "Female") echo "selected"; ?>>Female</option>
                                    <option value="Other" <?php if (isset($gender) && $gender == "Other") echo "selected"; ?>>Other</option>
                                </select>
                            </div>
                        </div>

                        <div class="row inputField">
                            <div class="col-md-6 col-sm-12 col-lg-6">
                                <!-- College Year -->
                                <label for="college_year" class="inputLabelText">College Year</label><br>
                                <select id="college_year" name="college_year" class="optionDropdown" required>
                                    <option value="" disabled selected>Choose..</option>
                                    <option value="Undergrad" <?php if ($college_year == "Undergrad") echo "selected"; ?>>Undergrad</option>
                                    <option value="Masters" <?php if ($college_year == "Masters") echo "selected"; ?>>Masters</option>
                                    <option value="PhD" <?php if ($college_year == "PhD") echo "selected"; ?>>PhD</option>
                                </select>
                            </div>

                            <div class="col-md-6 col-sm-12 col-lg-6">
                                <!-- Course-->
                                <label for="course" class="inputLabelText">Course of Study</label><br>
                                <select class="optionDropdown" style="width: 100%" id="course" name="course" required>
                                    <option value="" selected disabled>Choose..</option>
                                    <?php echo $options; ?>
                                </select>
                            </div>
                        </div>

                        <div class="row inputField">
                            <div class="col-md-6 col-sm-12 col-lg-6">
                                <!--  pursuing -->
                                <label for="pursuing" class="inputLabelText">Pursuing</label><br>
                                <select id="pursuing" name="pursuing" class="optionDropdown" required>
                                    <option value="" disabled selected>Choose..</option>
                                    <option value="Male" <?php if ($pursuing == "Male") echo "selected"; ?>>Male</option>
                                    <option value="Female" <?php if ($pursuing == "Female") echo "selected"; ?>>Female</option>
                                    <option value="Other" <?php if ($pursuing == "Other") echo "selected"; ?>>Other</option>
                                </select>
                            </div>

                            <div class="col-md-6 col-sm-12 col-lg-6">
                                <!-- Looking for-->
                                <label for="looking_for" class="inputLabelText">Looking For</label><br>
                                <select id="looking_for" name="looking_for" class="optionDropdown" required>
                                    <option value="" disabled selected>Choose..</option>
                                    <option value="Short-term" <?php if ($looking_for == "Short-term") echo "selected"; ?>>Short-Term</option>
                                    <option value="Long-term" <?php if ($looking_for == "Long-term") echo "selected"; ?>>Long-Term</option>
                                    <option value="Unsure" <?php if ($looking_for == "Unsure") echo "selected"; ?>>Unsure</option>
                                </select>
                            </div>
                        </div>

                        <div class="row inputField">
                            <div class="col-md-12 col-sm-12 col-lg-12">
                                <!-- Bio -->
                                <label for="bio" class="inputLabelText">Bio</label><br>
                                <textarea id="bio" name="bio" class="textInput" placeholder="Type here..." required><?php echo htmlspecialchars($bio); ?></textarea>
                            </div>
                        </div>

                        <div class="row inputField">
                            <div class="col-md-12 col-sm-12 col-lg-12">
                                <!-- Hobbies (May need to update this as text for it is meh)-->
                                <label for="hobbies" class="inputLabelText">Hobbies</label><br>
                                <input type="text" id="hobbies" name="hobbies" class="textInput" placeholder="Type here..." required value="<?php echo htmlspecialchars($hobbies); ?>">
                            </div>
                        </div>
                    </div>


                    <div class="col-lg-5 order-lg-1 col-md-12 imgContainer">
                        <!-- Profile Picture-->
                        <img class="profilePicture" src="<?php echo $profile_pic_filename ? '/' . htmlspecialchars($profile_pic_filename) : '/src/assets/images/defaultProfilePic.jpg'; ?>" alt="Profile Picture">

                        <label for="profile_pic" class="fileUploadBtn">Upload/Change profile picture</label>
                        <input type="file" id="profile_pic" name="profile_pic">

                        <!-- Button to just update changes made in db -->
                        <button type="submit" class="btn btn-secondary mt-2 mb-4 saveChangesBtn">Save Changes</button>
                    </div>
                </div>
            </form>

        </div>
    </div>


    <br>

    <!-- Footer -->
    <footer class="p-2">
        © 2024 Copyright UL Singles. All Rights Reserved
    </footer>

    <!-- JavaScript code for course options -->
    <script>
        $('#course').select2({
            placeholder: "Choose..",
            allowClear: true
        });
    </script>

</body>

</html>