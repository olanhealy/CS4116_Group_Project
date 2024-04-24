<?php

include "db_connection.php";
require "helperFunctions.php";

// Start the session
if (session_status() === PHP_SESSION_NONE) {
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


    if (isset($_POST['hobbies']) && !empty($_POST['hobbies'])) {
        $hobbies = array_slice($_POST['hobbies'], 0, 4); // Take only the first 4 hobbies
        $hobbies = implode(' ', $hobbies);
        setHobbies($userId, $hobbies); // Update hobbies in the database
    } else {
        $hobbies = getHobbies($userId); // Default to existing hobbies if none provided
    }
    
    if(isset($_POST['gender'])){
        $gender = htmlspecialchars($_POST['gender']);
    }

    
    if(isset($_POST['age']) && !empty($_POST['age'])){
        $age = intval($_POST['age']);
        setAge($userId, $age); 
    } else {
        $age = getAge($userId); 
    }
    
    //for displaying hobbies when set
    $selectedHobbiesArray = explode(' ', $hobbies);
    // Validate inputs
    $bio = htmlspecialchars($_POST['bio']);
    $collegeYear = htmlspecialchars($_POST['college_year']);
    $pursuing = htmlspecialchars($_POST['pursuing']);
    $course = htmlspecialchars($_POST['course']);
    $lookingFor = htmlspecialchars($_POST['looking_for']);
    $profilePicFilename = htmlspecialchars($_FILES['profile_pic']['name']);
    $password = htmlspecialchars($_POST['password']);

    // Check if password is between 8 and 20 characters long
    if (strlen($password) < 8 || strlen($password) > 20) {
        $passwordErrors[] = "Password must be between 8 and 20 characters long";
    }

    // Check for at least one capital letter and at least one special character with the inputted password
    if (!preg_match('/[A-Z]/', $password) || !preg_match('/[^a-zA-Z0-9]/', $password)) {
        $passwordErrors[] = "Password must contain at least one capital letter and one special character";
    }

    // Check if the inputted and repeated passwords both match
    if ($_POST['password'] !== $_POST['password-repeat']) {
        $errors[] = "The password and repeated password do not match";
    }

    // Check if at least one hobbie is inputted
    if (!isset($_POST['hobbies']) || empty($_POST['hobbies'])) {
        $errors[] = "Please select at least one hobby";
    }

    // Check if user uploaded a profile picture
    if (!isset($_FILES['profile_pic']) || $_FILES['profile_pic']['error'] == UPLOAD_ERR_NO_FILE) {
        $errors[] = "Please upload a profile picture";
    }
    

    // Update the user currently logged in profile table in the database
    $userId = $_SESSION['user_id']; //we use this from being logged in


    // Call setter methods to make the updates in db
   

    
    
    setBio($userId, $bio);
    setGender($userId, $gender);
    setCollegeYear($userId, $collegeYear);
    setPursuing($userId, $pursuing);
    setProfilePic($userId, $profilePicFilename);
    setCourse($userId, $course);
    setHobbies($userId, $hobbies);
    setLookingFor($userId, $lookingFor);
    
}

//set selectedHobbies to empty array for first time after register as will not have any hobbies yet
$selectedHobbiesArray = isset($selectedHobbiesArray) ? $selectedHobbiesArray : [];
?>

<!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset="UTF-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Edit Profile</title>
      <!-- Bootstrap Stylesheet -->
      <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
         integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
         integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
         crossorigin="anonymous"></script>
      <!-- Bootstrap Icon -->
      <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
      <!-- Scripts -->
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
      <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-beta.0/css/select2.min.css" rel="stylesheet">
      <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-beta.0/js/select2.min.js"></script>
      <script src="https://cdn.rawgit.com/harvesthq/chosen/gh-pages/chosen.jquery.min.js"></script>
      <link href="https://cdn.rawgit.com/harvesthq/chosen/gh-pages/chosen.min.css" rel="stylesheet" />
      <!-- External Stylesheet -->
      <link rel="stylesheet" type="text/css" href="../assets/css/edit_profile.css">
   </head>
   <body>
      <!-- CourseOfStudy Enum -->
      <?php include '../assets/enums/CourseOfStudy.php'; ?>
      <!-- hobbies  Enum -->
      <?php include '../assets/enums/hobbies.php'; ?>
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
            <?php if (isset($age)) { ?>
            <button type="button" id="explorebutton" class="btn button d-none d-md-block"
               onclick="location.href='explore.php'">Explore</button>
            <?php } ?>
            <button type="button" id="logoutbutton" class="btn button d-none d-md-block"
               onclick="location.href='logout.php'">Log Out</button>
         </div>
         <!-- Profile Icon -->
         <?php if (isset($age)) { ?>
         <div class="dropdown">
            <button class="btn-secondary" id="iconbutton" type="button" data-bs-toggle="dropdown" aria-expanded="false">
               <svg xmlns="http://www.w3.org/2000/svg" width="45" height="40" fill="currentColor"
                  class="bi bi-person-circle" viewBox="0 0 16 16">
                  <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0" />
                  <path fill-rule="evenodd"
                     d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1" />
               </svg>
            </button>
            <ul class="dropdown-menu" aria-labelledby="iconbutton" id="profiledropdown">
               <li><a class="dropdown-item-profile" href="edit_profile.php">Edit Profile</a></li>
               <li><a class="dropdown-item-profile d-md-none" href="logout.php">Log Out</a></li>
            </ul>
         </div>
         <?php } ?>
      </nav>
      <!-- End of Navbar -->
      <!-- Dropdown Menu Button as long as they have already provided their defails -->
      <?php if (areUserDetailsSet($userId)) { ?>
      <div class="container-fluid">
         <div class="row">
            <div class="col-12 dropdownBtn">
               <button class="btn btn-primary dropdown-toggle" type="button" id="menu-dropdown"
                  data-bs-toggle="dropdown">
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
      <?php } ?>
      <br>
      <!-- Form -->
      <div class="container-fluid border border-2 col-md-12 col-lg-12 col-sm-12" id="outline">
         <div class="row">
            <form class="container-fluid" action="editProfile.php" method="post" enctype="multipart/form-data">
               <div class="row">
                  <div class="col-lg-7 order-lg-2 col-md-12 info-box">
                     <!-- Top Row -- Name, Age, Gender -->
                     <div class="row inputField">
                        <div class="col-md-4 col-sm-12 col-lg-4">
                           <!-- Name -->
                           <label for="name" class="inputLabelText">Name</label> <br>
                           <span id="name"><?php echo htmlspecialchars($name); ?></span>
                        </div>
                        <?php if(isset($age)) { ?>
                        <div class="col-md-4 col-sm-12 col-lg-4">
                           <label for="age" class="inputLabelText">Age</label><br>
                           <span id="age"><?php echo htmlspecialchars($age); ?></span>
                        </div>
                        <?php } else { ?>
                        <div class="col-md-4 col-sm-12 col-lg-4">
                           <label for="age" class="inputLabelText">Age</label><br>
                           <input type="number" id="age" name="age" class="textInput" placeholder="Type here..." min="18" required>
                        </div>
                        <?php } ?>
                        <?php if(isset($gender)) { ?>
                        <div class="col-md-4 col-sm-12 col-lg-4">
                           <!-- Gender -->
                           <label for="gender" class="inputLabelText">Gender</label><br>
                           <span id="gender"><?php echo htmlspecialchars($gender); ?></span>
                        </div>
                        <?php } else { ?>
                        <div class="col-md-4 col-sm-12 col-lg-4">
                           <label for="gender" class="inputLabelText">Gender</label><br>
                           <select id="gender" name="gender" class="optionDropdown" required>
                              <option value="" disabled selected>Choose..</option>
                              <option value="Male" 
                                 <?php if (isset($gender) && $gender == "Male")
                                    echo "selected"; ?>>Male</option>
                              <option value="Female" <?php if (isset($gender) && $gender == "Female")
                                 echo "selected"; ?>>Female</option>
                              <option value="Other" <?php if (isset($gender) && $gender == "Other")
                                 echo "selected"; ?>>Other</option>
                           </select>
                        </div>
                        <?php } ?>
                     </div>
                     <!--Second Row -- College Year, Course of Study -->
                     <div class="row inputField">
                        <div class="col-md-6 col-sm-12 col-lg-6">
                           <!-- College Year -->
                           <label for="college_year" class="inputLabelText">College Year</label><br>
                           <select id="college_year" name="college_year" class="optionDropdown" required>
                              <option value="" disabled selected>Choose..</option>
                              <option value="Undergrad" <?php if ($collegeYear == "Undergrad")
                                 echo "selected"; ?>>
                                 Undergrad
                              </option>
                              <option value="Masters" <?php if ($collegeYear == "Masters")
                                 echo "selected"; ?>>
                                 Masters
                              </option>
                              <option value="PhD" <?php if ($collegeYear == "PhD")
                                 echo "selected"; ?>>PhD</option>
                           </select>
                        </div>
                        <div class="col-md-6 col-sm-12 col-lg-6">
                           <!-- Course-->
                           <label for="course" class="inputLabelText">Course of Study</label><br>
                           <select class="optionDropdown" style="width: 100%" id="course" name="course" required>
                              <option value="" selected disabled>Choose..</option>
                              <?php
                                 foreach (CourseOfStudy::cases() as $case) {
                                 $value = $case->value;
                                 // Check if the course is the same as the one in the database so can display previous choice
                                 $selected = ($course == $value) ? "selected" : "";
                                 echo "<option value=\"$value\" $selected>" . $value . "</option>";
                                 }
                                 ?>
                           </select>
                        </div>
                     </div>
                     <!--Third Row -- Pursuing, Looking For -->
                     <div class="row inputField">
                        <div class="col-md-6 col-sm-12 col-lg-6">
                           <!--  pursuing -->
                           <label for="pursuing" class="inputLabelText">Pursuing</label><br>
                           <select id="pursuing" name="pursuing" class="optionDropdown" required>
                              <option value="" disabled selected>Choose..</option>
                              <option value="Male" <?php if ($pursuing == "Male")
                                 echo "selected"; ?>>Male</option>
                              <option value="Female" <?php if ($pursuing == "Female")
                                 echo "selected"; ?>>Female
                              </option>
                              <option value="Other" <?php if ($pursuing == "Other")
                                 echo "selected"; ?>>Other
                              </option>
                           </select>
                        </div>
                        <div class="col-md-6 col-sm-12 col-lg-6">
                           <!-- Looking for-->
                           <label for="looking_for" class="inputLabelText">Looking For</label><br>
                           <select id="looking_for" name="looking_for" class="optionDropdown" required>
                              <option value="" disabled selected>Choose..</option>
                              <option value="Short-term" <?php if ($lookingFor == "Short-term")
                                 echo "selected"; ?>>
                                 Short-Term
                              </option>
                              <option value="Long-term" <?php if ($lookingFor == "Long-term")
                                 echo "selected"; ?>>
                                 Long-Term
                              </option>
                              <option value="Unsure" <?php if ($lookingFor == "Unsure")
                                 echo "selected"; ?>>Unsure
                              </option>
                           </select>
                        </div>
                     </div>
                     <!--Fourth Row -- Bio-->
                     <div class="row inputField">
                        <div class="col-md-12 col-sm-12 col-lg-12">
                           <!-- Bio -->
                           <label for="bio" class="inputLabelText">Bio</label><br>
                           <textarea id="bio" name="bio" class="textInput" placeholder="Type here..."
                              required><?php echo htmlspecialchars($bio); ?></textarea>
                        </div>
                     </div>
                     <div class="row inputField">
                        <div class="col-md-12 col-sm-12 col-lg-12">
                           <select data-placeholder="Hobbies..." multiple class="chosen-select" name="hobbies[]" id="hobbies">
                              <option value=""></option>
                              <?php
                                 foreach (Hobby::cases() as $case) {
                                     $value = $case->value;
                                     $selected = in_array($value, $selectedHobbiesArray) ? 'selected' : '';
                                     echo "<option value='$value' $selected>$value</option>";
                                     }
                                 ?>
                           </select>
                           <div id="hobby-limit-message" class="alert alert-warning" style="display: none;">
                              You can only select up to 4 hobbies.
                           </div>
                        </div>
                     </div>
                     <?php if (isset($age)) { ?>
                     <div class="row inputField">
                        <!--Sixth Row -- Password, verify-->
                        <div class="col-md-6 col-sm-12 col-lg-6">
                           <!-- Change Password button -->
                           <button type="button" id="changePasswordBtn" class="btn btn-secondary"
                              data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                           Change Password
                           </button>
                        </div>
                        <!-- Modal for changing password -->
                        <div class="modal fade" id="changePasswordModal" tabindex="-1"
                           aria-labelledby="changePasswordModalLabel" aria-hidden="true">
                           <div class="modal-dialog">
                              <div class="modal-content">
                                 <div class="modal-header">
                                    <h5 class="modal-title" id="changePasswordModalLabel">Change Password</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                       aria-label="Close"></button>
                                 </div>
<form id="changePasswordForm" action="editProfile.php" method="post">
    <div class="modal-body">
        <div class="mb-3">
            <label for="password" class="form-label">New Password</label>
            <input type="password" class="form-control" id="password" name="password">
        </div>
        <div class="mb-3">
            <label for="password-repeat" class="form-label">Confirm New
                Password</label>
            <input type="password" class="form-control" id="password-repeat" name="password-repeat">
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-secondary mt-1 mb-1 saveChangesBtn">Save
            Password</button>
    </div>
</form>
</div>
</div>
</div>
            <?php if (getVerified($userId) == 0) { ?>
            <div class="col-md-6 col-sm-12 col-lg-6">
            <!-- Button to verify email -->
            <button type="button" id="verifyEmailBtn" class="btn btn-primary">Verify Email</button>
            </div>
            <?php } ?>
            </div>
            <?php } ?>
            </div>
            <div class="col-lg-5 order-lg-1 col-md-12 imgContainer">
            <!-- Profile Picture-->
            <img class="profilePicture"
               src="<?php echo $profilePicFilename ? '/' . htmlspecialchars($profilePicFilename) : '/src/assets/images/defaultProfilePic.jpg'; ?>"
               alt="Profile Picture">
            <label for="profile_pic" class="fileUploadBtn">Upload/Change profile picture</label>
            <input type="file" id="profile_pic" name="profile_pic">
            <!-- Button to just update changes made in db -->
            <button type="submit" class="btn btn-secondary mt-2 mb-4 saveChangesBtn">Save
            Changes</button>
            </div>
            <div class="" id="outline">
            <!-- Error Messages -->
            <?php
               if (!empty($errors)) {
                   echo "<ul>";
                   foreach ($errors as $error) {
                       echo '<div class="alert alert-danger" role="alert">';
                       echo htmlspecialchars($error);
                       echo '</div>';
                       }
                   echo "</ul>";
                   }
               ?>
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

    <script>
        $(document).ready(function () {
            $('#verifyEmailBtn').click(function () {
                // AJAX request to update verification status
                $.ajax({
                    url: 'verifyEmail.php', // Update with the endpoint to handle email verification
                    type: 'POST',
                    data: { emailVerified: true }, // If needed, provide any data required by the PHP script
                    success: function (response) {
                        // Handle successful response here
                        alert('Email verification email sent');
                    },
                    error: function (xhr, status, error) {
                        // Handle error response here
                        alert('Error occurred while verifying email: ' + error);
                    }
                });
            });
        });
    </script>

    <script>
        // Function to clear input fields when modal is closed
        $('#changePasswordModal').on('hidden.bs.modal', function () {
            $(this).find('form').trigger('reset');
        });
    </script>

    <script>
        $(document).ready(function () {
            // Function to add or remove the required attribute based on modal state
            $('#changePasswordModal').on('show.bs.modal', function () {
                $('#password, #password-repeat').attr('required', true);
            });

            $('#changePasswordModal').on('hidden.bs.modal', function () {
                $('#password, #password-repeat').removeAttr('required');
            });

        });
    </script>

    <script>
        //Function to display whatever image is inputted in edit profile automatically
        document.getElementById('profile_pic').addEventListener('change', function (event) {
            if (event.target.files && event.target.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    // Get the data and render the image
                    document.querySelector('.profilePicture').src = e.target.result;
                };

                // Read the image file as a data URL.
                reader.readAsDataURL(event.target.files[0]);
            }
        });
    </script>

<script>
$(document).ready(function () {
    var maxHobbies = 4;
    $(".chosen-select").chosen({
        no_results_text: "Oops, nothing found!"
    }).on('change', function(evt, params) {
        var selectedHobbies = $(this).chosen().val();
        if (selectedHobbies.length > maxHobbies) {
            // Show message
            $('#hobby-limit-message').show();
            // Deselect the last hobby
            selectedHobbies.pop();
            $(this).val(selectedHobbies).trigger('chosen:updated');
        } else {
            // Hide message
            $('#hobby-limit-message').hide();
        }
    });
});
</script>

</body>

</html>