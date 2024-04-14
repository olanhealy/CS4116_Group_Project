<?php
// Process #13 to set the user's bio in the profile table of the db

// Process #4 checks if the Account already exists in the account table of db
function isAccountFound($email, $password)
{
    global $conn;
    $count = 0;
    $sql_is_account_found = "SELECT COUNT(*) FROM account WHERE email = ? AND password_hash = ?";
    $account = $conn->prepare($sql_is_account_found);
    $account->bind_param('ss', $email, $password);
    $account->execute();
    $account->bind_result($count);
    $account->fetch();
    $account->close();

    return $count > 0;
}

function setVerified($user_id, $verified){
    global $conn;

    $sql_set_verified = "UPDATE `profile` SET verified = ? WHERE user_id = ?";
    $set_verified = $conn->prepare($sql_set_verified);
    $set_verified->bind_param("ii", $verified, $user_id);
    $set_verified->execute();

    if ($set_verified->affected_rows > 0) {
        echo "Verified set successfully";
    } else {
        echo "Error setting Verified";
    }

    $set_verified->close();
}

function getVerified($user_id){
    global $conn;
    $verified = 0;

    $sql_get_verified = "SELECT verified FROM profile WHERE user_id = ?";
    $get_verified = $conn->prepare($sql_get_verified);
    $get_verified->bind_param("i", $user_id);
    $get_verified->execute();
    $get_verified->store_result();

    if ($get_verified->num_rows > 0) {
        $get_verified->bind_result($verified);
        $get_verified->fetch();
    }

    $get_verified->close();
    return $verified;
}

function setPassword($password, $user_id)
{
    global $conn;

    // Hash the password for security 
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Update the password in the Accounts table
    $sql_set_password = "UPDATE account SET password_hash = ? WHERE user_id = ?";
    $set_password = $conn->prepare($sql_set_password);
    $set_password->bind_param('si', $hashed_password, $user_id);
    $set_password->execute();

    if ($set_password->affected_rows > 0) {
        echo "Password set successfully";
    } else {
        echo "Error setting password";
    }

    $set_password->close();
}

function setFirstName($user_id, $first_name){
    global $conn;

    $sql_set_name = "UPDATE account SET first_name = ? WHERE user_id = ?";

    $set_name = $conn->prepare($sql_set_name);
    $set_name->bind_param("si", $first_name, $user_id);
    $set_name->execute();

    if ($set_name->affected_rows > 0) {
        echo "First name set successfully in account";
    } else {
        //TODO: backend: fix messaging for error when jsut editted name
        //echo "Error setting First and Last Name";
    }
}

function setLastName($user_id, $last_Name){
    global $conn;

    $sql_set_name = "UPDATE account SET last_name = ? WHERE user_id = ?";

    $set_name = $conn->prepare($sql_set_name);
    $set_name->bind_param("si", $last_Name, $user_id);
    $set_name->execute();

    if ($set_name->affected_rows > 0) {
        echo "Last name set successfully in account";
    } else {
        //TODO: backend: fix messaging for error when jsut editted name
        //echo "Error setting First and Last Name";
    }
}

// Process #11 to set the First name and Last name in the account table in db
function setName($first_name, $last_name, $user_id)
{
    global $conn;

    setFirstName($user_id, $first_name);
    setLastName($user_id, $last_name);

    if (getName($user_id) === "") {
        // Insert the user id  into profile table, also inserting the full name of user
        $sql_insert_profile = "INSERT INTO `profile` (user_id, `name` ) VALUES (?, ?)";
        $insert_new_profile = $conn->prepare($sql_insert_profile);
        $full_name = $first_name . " " . $last_name;
        $insert_new_profile->bind_param('is', $user_id, $full_name);
        $insert_new_profile->execute();

        //TODO: backend: fix messaging for error when jsut editted name
        // if ($insert_new_profile->affected_rows <= 0) {
        //     echo "Error inserting into the Profile table";
        // }

        $insert_new_profile->close();
    }else{
        $full_name = $first_name . " " . $last_name;
        $sql_update_profile = "UPDATE `profile` SET `name` = ? WHERE user_id = ?";
        $update_profile = $conn->prepare($sql_update_profile);
        $update_profile->bind_param("si", $full_name, $user_id);
        $update_profile->execute();

        //TODO: backend: fix messaging for error when jsut editted name
        // if ($update_profile->affected_rows > 0) {
        //     echo "Name set successfully";
        // } else {
        //     echo "Error setting Name";
        // }
    
    }
}

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

    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
        // gets the file extension (i.e png etc ) and converts to lowercase for consistency in comparision
        $file_extension = strtolower(pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION));
        //the only allowed type of files to be upload
        $allowed_extensions = array('png', 'jpeg', 'jpg');

        //checks if the file extension is in the allowed extensions
        if (in_array($file_extension, $allowed_extensions)) {
           
            /*
            overwrite the uplaoded file name to be the users id, followed by "_profile_pic" and the file extension so we can ensure no 2 users
            have the same profile pic name
            */

            $new_filename = $user_id . "_profile_pic." . $file_extension;
            // path for saving is in uploads folder, we get the servers document root and then look for uploads
            $upload_dir = $_SERVER['DOCUMENT_ROOT'] . "/uploads/";
            $upload_file = $upload_dir . $new_filename;

            // Attempt to move the uploaded file
            if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $upload_file)) {
                // update the profile table in db 
                $sql_set_profile_pic = "UPDATE profile SET profile_pic = ? WHERE user_id = ?";
                $set_profile_pic = $conn->prepare($sql_set_profile_pic);
                $relative_path = "uploads/" . $new_filename;
                $set_profile_pic->bind_param("si", $relative_path, $user_id);
                $set_profile_pic->execute();
                if ($set_profile_pic->affected_rows > 0) {
                    // Return the new path for immediate use
                    return $relative_path;
                } else {
                    echo "Error setting profile picture.";
                    return false;
                }
            } else {
                echo "Error uploading file.";
                return false;
            }
        } else {
            echo "Invalid file type. Only PNG, JPEG, and JPG are allowed.";
            return false;
        }
    }

    // return excisting photo if no new photo is uploade || invalid file type uploaded (no way to display this error without javascript)
    return $profile_pic_filename;
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

// function to get name used in explore.php
function getName($user_id) {
    global $conn;
    $name = "";

    $sql_get_name = "SELECT name FROM profile WHERE user_id = ?";
    $get_name = $conn->prepare($sql_get_name);
    $get_name->bind_param("i", $user_id);
    $get_name->execute();
    $get_name->store_result();

    if ($get_name->num_rows > 0) {
        $get_name->bind_result($name);
        $get_name->fetch();
    }

    $get_name->close();
    return $name;
}

function getMatch($userId, $targetId)
{
    global $conn;

    $query = "SELECT * FROM matches WHERE initiator_id = ? AND target_id = ? OR initiator_id = ? AND target_id = ?";
    $stmt = $conn->prepare($query);
    if ($stmt !== false) {
        $stmt->bind_param("iiii", $userId, $targetId, $targetId, $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        if ($result->num_rows > 0) {
            return true;
        }
    }
    return false;
}

function addMatch($initiatorId, $targetId)
{

    global $conn;

    $query = "INSERT INTO matches (initiator_id, target_id) VALUES (?, ?)";
    $stmt = $conn->prepare($query);
    if ($stmt !== false) {
        $stmt->bind_param("ii", $initiatorId, $targetId);
        $stmt->execute();
    } else {
        die("Error in SQL query: " . $conn->error . "<br>");
    }
}

function isItAMatch($initiatorId, $targetId)
{
    global $conn;

    $query = "SELECT * FROM adore WHERE user_id = ? AND adored_user_id = ?";

    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("ii", $targetId , $initiatorId);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        if ($result->num_rows > 0) {
            // The current user has previously adored the logged in user
            return true;
        }
    }
    // The current user has not previously adored the logged in user
    return false;
}

function removeMatch($userId, $targetId)
{

    global $conn;

    $query = "DELETE FROM matches WHERE initiator_id = ? AND target_id = ? OR initiator_id = ? AND target_id = ?";
    $stmt = $conn->prepare($query);

    if ($stmt !== false) {
        $stmt->bind_param("iiii", $userId, $targetId, $targetId, $userId);
        $stmt->execute();
    } else {
        die("Error in SQL query: " . $conn->error . "<br>");
    }
}

function getAllMatches($userId)
{
    global $conn;
    //get all account information
    $query = "SELECT 
        CASE 
            WHEN initiator_id = $userId THEN target_id 
            ELSE initiator_id 
        END AS other_user_id 
    FROM matches 
    WHERE initiator_id = $userId OR target_id = $userId 
    ORDER BY response_date DESC";
    ;

    //check the result is not empty
    $result = $conn->query($query);
    if ($result->num_rows > 0) {
        // Output data of each row with a form to ban/unban
        while ($row = $result->fetch_assoc()) {

            $targetId = $row['other_user_id'];
            
            $name = getName($targetId);
            $profilePicture = getProfilePicture($targetId);

            //include the user list html for each row
            include "match.html";
        }
    } else {
        //error
        echo "0 results found";
    }
}

function showProfileCard($user_id){

    $targetUserId = $user_id;
    $name = getName($user_id);
    $profilePicture = getProfilePicture($user_id);
    $bio = getBio($user_id);
    $gender = getGender($user_id);
    $age = getAge($user_id);
    $collegeYear = getCollegeYear($user_id);
    $pursuing = getPursuing($user_id);
    $course = getCourse($user_id);
    $hobbies = getHobbies($user_id);
    $lookingFor = getLookingFor($user_id);

    include "profileCard.html";
}

//process #43, functon to handle adore action 
function adoreUser($userLoggedInId, $currentUserId)
{
    global $conn;
    $sqlAdore = "INSERT INTO adore (user_id, adored_user_id, date) VALUES (?, ?, NOW())";
    if ($adore = $conn->prepare($sqlAdore)) {
        $adore->bind_param("ii", $userLoggedInId, $currentUserId);
        $adore->execute();
        $adore->close();
    }
}

//fucntion to handle ignore action
function ignoreUser($userLoggedInId, $currentUserId)
{
    global $conn;
    $sqlIgnore = "INSERT INTO `ignore` (user_id, ignored_user_id, date) VALUES (?, ?, NOW())";
    if ($ignore = $conn->prepare($sqlIgnore)) {
        $ignore->bind_param("ii", $userLoggedInId, $currentUserId);
        $ignore->execute();
        $ignore->close();
    }
}

function isUserAdored($userId, $targetId){

    global $conn;

    $query = "SELECT * FROM adore WHERE user_id = ? AND adored_user_id = ?";

    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("ii", $userId, $targetId);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        if ($result->num_rows > 0) {
            // The current user has previously adored the logged in user
            return true;
        }
    }
    // The current user has not previously adored the logged in user
    return false;
}

function getMessagesByMatchId($matchId) {
    global $conn;
    $sqlGetMessagesByMatchId = $conn->prepare("SELECT * FROM messages WHERE match_id = ? ORDER BY date ASC");
    $sqlGetMessagesByMatchId->bind_param("i", $matchId);
    $sqlGetMessagesByMatchId->execute();
    $resultSqlGetMessagesByMatchId = $sqlGetMessagesByMatchId->get_result();
    $messages = $resultSqlGetMessagesByMatchId->fetch_all(MYSQLI_ASSOC);
    $sqlGetMessagesByMatchId->close();
    return $messages;
}


// process #47 to send a message to target user
function sendMessage($userId, $matchId, $messageContent) {
    global $conn;
    // Determine receiver_id based on match_id
    $receiverId = getReceiverIdByMatchId($matchId, $userId);
    //return false if no receiver_id is found (no match id or invalid one)
    if (!$receiverId) {
        
        return false;
    }
    //Insert message content into the db
    $sqlSendMessage = $conn->prepare("INSERT INTO messages (match_id, receiver_id, sender_id, message_content, read_status) VALUES (?, ?, ?, ?, 'delivered')");
    $sqlSendMessage ->bind_param("iiis", $matchId, $receiverId, $userId, $messageContent);
    $executeSuccess = $sqlSendMessage ->execute();
    $sqlSendMessage ->close();

    return $executeSuccess; // Return the success status of the execute (true or false)
}

// Function to get receiver_id by match_id
function getReceiverIdByMatchId($matchId, $senderId) {
    global $conn;
    //Get the initiator_id and target_id from the matches table
    $sqlGetReceiverIdByMatchId = $conn->prepare("SELECT initiator_id, target_id FROM matches WHERE match_id = ?");
    $sqlGetReceiverIdByMatchId->bind_param("i", $matchId);
    $sqlGetReceiverIdByMatchId->execute();
    $sqlGetReceiverIdByMatchId->bind_result($initiatorId, $targetId);

    if ($sqlGetReceiverIdByMatchId->fetch()) {
        // Get the receiver_id based on the match_id
        $receiverId = ($initiatorId === $senderId) ? $targetId : $initiatorId;
    } else {
        $receiverId = null; //if no match excists
    }
    $sqlGetReceiverIdByMatchId->close();
    return $receiverId;
}

// process #46 to get messages for a user between their specific target
function getMessages($userId) {
    global $conn;
    $conversations = [];
    // get all matchIds where the user is either the initiator or the target
    $sqlGetMessages = $conn->prepare("SELECT match_id FROM matches WHERE initiator_id = ? OR target_id = ?");
    $sqlGetMessages->bind_param("ii", $userId, $userId);
    $sqlGetMessages->execute();
    $resultSqlGetMessages = $sqlGetMessages->get_result();
    while($row = $resultSqlGetMessages->fetch_assoc()) {
        $conversations[] = $row['match_id'];
    }
    $sqlGetMessages->close();
    return $conversations;
}
?>