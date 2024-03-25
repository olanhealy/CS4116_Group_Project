<?php
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

// Process #14 to get the user's bio if they already exist in the profiile table of db
function getBio($user_id) {
    global $conn; 
    $bio = "";

    $sql_get_bio = "SELECT bio FROM profile WHERE user_id = ?";
    $get_bio = $conn->prepare($sql_get_bio);
    $get_bio->bind_param("i", $user_id);
    $get_bio->execute();
    $get_bio->store_result();

    if ($get_bio->num_rows > 0) {
        $get_bio->bind_result($bio);
        $get_bio->fetch();
    }

    $get_bio->close();
    return $bio;

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

// Process #38 to get the user's gender if they already exist in the profiile table of db
function getGender($user_id) {
    global $conn;
    $gender = "";

    $sql_get_gender = "SELECT gender FROM profile WHERE user_id = ?";
    $get_gender = $conn->prepare($sql_get_gender);
    $get_gender->bind_param("i", $user_id);
    $get_gender->execute();
    $get_gender->store_result();

    if ($get_gender->num_rows > 0) {
        $get_gender->bind_result($gender);
        $get_gender->fetch();
    }

    $get_gender->close();
    return $gender;

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

// Process #20 to get the user's age if they already exist in the profiile table of db
function getAge($user_id) {
    global $conn;
    $age = "";

    $sql_get_age = "SELECT age FROM profile WHERE user_id = ?";
    $get_age = $conn->prepare($sql_get_age);
    $get_age->bind_param("i", $user_id);
    $get_age->execute();
    $get_age->store_result();

    if ( $get_age->num_rows > 0) {
        $get_age->bind_result($age);
        $get_age->fetch();
    }

    $get_age->close();
    return $age;

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

// Process #22 to get the user's college year if they already exist in the profile table of the database
function getCollegeYear($user_id) {
    global $conn;
    $college_year = "";

    $sql_get_college_year = "SELECT college_year FROM profile WHERE user_id = ?";
    $get_college_year = $conn->prepare($sql_get_college_year);
    $get_college_year->bind_param("i", $user_id);
    $get_college_year->execute();
    $get_college_year->store_result();

    if ($get_college_year->num_rows > 0) {
        $get_college_year->bind_result($college_year);
        $get_college_year->fetch();
    }

    $get_college_year->close();
    return $college_year;
}

// Process #23 to set the user's pursuing status in the profile table of the db
function setPursuing($user_id, $pursuing) {
    global $conn;

    $sql_set_pursuing = "UPDATE profile SET pursuing = ? WHERE user_id = ?";
    $set_pursuing = $conn->prepare($sql_set_pursuing);
    $set_pursuing->bind_param("si", $pursuing, $user_id);
    $set_pursuing->execute();

    if ($set_pursuing->affected_rows > 0) {
        echo "Pursuing set successfully";
    } else {
        echo "Error setting Pursuing";
    }

    $set_pursuing->close();
}

// Process #22 to get the user's pursuing status if they already exist in the profile table of the database
function getPursuing($user_id) {
    global $conn;
    $pursuing = "";

    $sql_get_pursuing = "SELECT pursuing FROM profile WHERE user_id = ?";
    $get_pursuing = $conn->prepare($sql_get_pursuing);
    $get_pursuing->bind_param("i", $user_id);
    $get_pursuing->execute();
    $get_pursuing->store_result();

    if ($get_pursuing->num_rows > 0) {
        $get_pursuing->bind_result($pursuing);
        $get_pursuing->fetch();
    }

    $get_pursuing->close();
    return $pursuing;
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

// Process #26 to get the user's profile picture from the profile table of the db
function getProfilePicture($user_id) {
    global $conn;
    $profile_pic_filename = "";

    $sql_get_profile_pic = "SELECT profile_pic FROM profile WHERE user_id = ?";
    $get_profile_pic = $conn->prepare($sql_get_profile_pic);
    $get_profile_pic->bind_param("i", $user_id);
    $get_profile_pic->execute();
    $get_profile_pic->store_result();

    if ($get_profile_pic->num_rows > 0) {
        $get_profile_pic->bind_result($profile_pic_filename);
        $get_profile_pic->fetch();
    }

    $get_profile_pic->close();
    return $profile_pic_filename;
}

// Process #29 to set the user's course of study in the profile table of the db
function setCourse($user_id, $course) {
    global $conn;

    $sql_set_course = "UPDATE profile SET course = ? WHERE user_id = ?";
    $set_course = $conn->prepare($sql_set_course);
    $set_course->bind_param("si", $course, $user_id); 
    $set_course->execute();

    if ($set_course->affected_rows > 0) {
        echo "Course set successfully";
    } else {
        echo "Error setting course";
    }
}

// Process #30 to  get the user's course of study from the profile table of the db
function getCourse($user_id) {
    global $conn;
    $course= "";

    $sql_get_course = "SELECT course  FROM profile WHERE user_id = ?";
    $get_course  = $conn->prepare($sql_get_course );
    $get_course ->bind_param("i", $user_id);
    $get_course ->execute();
    $get_course ->store_result();

    if ($get_course ->num_rows > 0) {
        $get_course ->bind_result($course );
        $get_course ->fetch();
    }

    $get_course ->close();
    return $course;
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

// Process #32 to get the user's hobbies if they already exist in the profiile table of db (incomplete for html side)
function getHobbies($user_id) {
    global $conn;
    $hobbies = "";

    $sql_get_hobbies = "SELECT hobbies FROM profile WHERE user_id = ?";
    $get_hobbies = $conn->prepare($sql_get_hobbies);
    $get_hobbies->bind_param("i", $user_id);
    $get_hobbies->execute();
    $get_hobbies->store_result();

    if ($get_hobbies->num_rows > 0) {
        $get_hobbies->bind_result($hobbies);
        $get_hobbies->fetch();
    }

    $get_hobbies->close();
    return $hobbies;
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

// Process #34 to get the user's looking for status if they already exist in the profiile table of db
function getLookingFor($user_id) {
    global $conn;
    $looking_for = "";
    
    $sql_get_looking_for = "SELECT looking_for FROM profile WHERE user_id = ?";
    $get_looking_for = $conn->prepare($sql_get_looking_for);
    $get_looking_for->bind_param("i", $user_id);
    $get_looking_for->execute();
    $get_looking_for->store_result();

    if ($get_looking_for->num_rows > 0) {
        $get_looking_for->bind_result($looking_for);
        $get_looking_for->fetch();
    }
    
    $get_looking_for->close();
    return $looking_for;
}
?>