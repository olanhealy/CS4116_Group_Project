<?php

require "db_connection.php";
require_once "helperFunctions.php";

// Start session securely
if(session_status() === PHP_SESSION_NONE) {
    session_start();
}

// get user id of user currently accessing the explore page
$userLoggedInId = $_SESSION['user_id'];

// delete ignores older than 3 minutes keeping as 3 for testing purposes (kinda works kind of doesnt idk something to do with timezones or some bs)
//$cleanup_sql = "DELETE FROM `ignore` WHERE `date` < NOW() - INTERVAL 3 MINUTE";
//$conn->query($cleanup_sql);

// initialide user's exploration state if not already set or if a different user is logged in
if (!isset($_SESSION['explore_state'][$userLoggedInId])) {
    $_SESSION['explore_state'][$userLoggedInId] = [
        'users_to_explore' => [],
        'current_position' => 0, // tracks the current position in the explore list
    ];
}
//Use the getters from helperFunctions.php only of the details we require from profile table of db for the user logged in
$userLoggedInHobbies = getHobbies($userLoggedInId);
$userLoggedInGender = getGender($userLoggedInId);
$userLoggedInAge = getAge($userLoggedInId);
$userLoggedInCollegeYear = getCollegeYear($userLoggedInId);
$userLoggedInPursuing = getPursuing($userLoggedInId);
$userLoggedInCourse = getCourse($userLoggedInId);
$userLoggedInLookingFor = getLookingFor($userLoggedInId);

//Fucntion for how we are going to differ the weights to display matches. We are going to have a few factors
function calcMatchWeight($targetUserId)
{
    global $userLoggedInId, $userLoggedInAge, $userLoggedInHobbies, $userLoggedInCollegeYear, $userLoggedInCourse, $userLoggedInLookingFor;

    // we will get the target users relevant details here as only now do we need them as the userid of target user has passed the "gender check"
    $targetUserIdHobbies = getHobbies($targetUserId);
    $targetUserIdAge = getAge($targetUserId);
    $targetUserIdCollegeYear = getCollegeYear($targetUserId);
    $targetUserIdCourse = getCourse($targetUserId);
    $targetUserIdLookingFor = getLookingFor($targetUserId);


    //weight array were key represents the % in decimal form of how much influence it will have on which user is displayed to current user
    $weight = array(
        'age' => 1.1, //10% increase
        'hobbies' => 1.4, //40% increase
        'college_year' => 1.15, //15% increase
        'course' => 1.15, //15% increase
        'looking_for' => 1.20 //20% increase

    );

    //variable to store the score meter. This will help determine which user is a 'best fit' for current user based of certain criteria 
    $weightScore = 0;

    foreach ($weight as $key => $weight) {
        switch ($key) {
            //case for age
            case 'age':
                $ageDifference = abs($userLoggedInAge - $targetUserIdAge);

                // different age difference constraints to give a score
                if ($ageDifference <= 3) {
                    $score = 100;
                } elseif ($ageDifference <= 6) {
                    $score = 50;
                } else {
                    $score = 0;
                }
                break;
            //case for hobbies    
            case 'hobbies':
                $userHobbies = explode(',', $userLoggedInHobbies);
                $targetUserHobbies = explode(',', $targetUserIdHobbies);
                $matchingHobbies = array_intersect($userHobbies, $targetUserHobbies);

                // If hobbies match, for the amount that 2 multply it by 50 score
                $score = count($matchingHobbies) * 50; // each matching hobby contributes 10 to the score
                break;
            //case for college year    
            case 'college_year':
                if ($userLoggedInCollegeYear === $targetUserIdCollegeYear) {
                    $score = 100; // same college year
                } else {
                    $score = 0; // different college year
                }
                break;
            //case for course    
            case 'course':

                if ($userLoggedInCourse === $targetUserIdCourse) {
                    $score = 100; // same course
                } else {
                    $score = 0; // different course
                }
                break;
            //case for looking_for
            case 'looking_for':
                if ($userLoggedInLookingFor === $targetUserIdLookingFor) {
                    $score = 100; // same looking for status
                } else {
                    $score = 0; // different looking for status
                }
                break;
            default:
                // set score to 0 if no criteria matches
                $score = 0;
                break;
        }

        // add this score to toal score
        $weightScore += $weight * $score;
    }
    return $weightScore;
}

function getUsersForExplore($userLoggedInId, $adoredUsers, $ignoredUsers)
{
    /*
    if no users to explore are set in session, we will then use this method to get them
    this helps as if a user leaves the page, its state will be remember and will nott have to recalculate the users to explore. Also checks to explore state
    which is postion in array
    */
    if (empty($_SESSION['explore_state'][$userLoggedInId]['users_to_explore'])) {
        global $conn, $userLoggedInGender, $userLoggedInPursuing;

        //initalise array to explore users that will eventually pass the criteria made for user logged in 
        $usersToExplore = array();

        // for getting all users, is a process so placeholder for now
        $sqlUsersToExplore = "SELECT user_id FROM profile WHERE user_id != ?";

        // check if the the user adores other user, if they do exclude them from being displayed
        if (!empty($adoredUsers)) {
            $placeholder = implode(",", array_fill(0, count($adoredUsers), "?"));
            $sqlUsersToExplore .= " AND user_id NOT IN ($placeholder)";
        }

        // check if the user ignores other users, if they do exclude them from being displayed
        if (!empty($ignoredUsers)) {
            $placeholders = implode(",", array_fill(0, count($ignoredUsers), "?"));
            $sqlUsersToExplore .= " AND user_id NOT IN ($placeholders)";
        }

        /*
        prepares our users_to_explore but excluding the adored and ignored users.
        str_repeat built in function and gets the total some of adored and ignored users and then adds 1 to account for the user logged in
        array_merge built in fucntion to merge the user logged in id with the adored and ignored users
        basically makes sure no users skip through 
        */
        $getAllUsersToExplore = $conn->prepare($sqlUsersToExplore);
        $types = str_repeat("i", count($adoredUsers) + count($ignoredUsers) + 1);
        $values = array_merge([$userLoggedInId], $adoredUsers, $ignoredUsers);
        $getAllUsersToExplore->bind_param($types, ...$values);
        $getAllUsersToExplore->execute();
        $resultAllUsersToExplore = $getAllUsersToExplore->get_result();


        while ($users_row = $resultAllUsersToExplore->fetch_assoc()) {
            $targetUserId = $users_row['user_id'];

            /*
            The first check needed is to make sure the user logged in is can only 'explore' the gender they are pursuing and that the displayed users are only
            exploring the gender of the user logged in. i.e if the user logged in is a male, who is pursuing a female, the displayed user must a female who is pursuing a male
            */

            $targetUserIdGender = getGender($targetUserId);
            $targetUserIdPursuing = getPursuing($targetUserId);

            if ($targetUserIdGender !== $userLoggedInPursuing || $targetUserIdPursuing !== $userLoggedInGender) {
                continue;
                /*
                so if the target (female) does not equal the user logged in pursuing (female) it would pass, and if the targer user pursuing (male) does not equal the gender of
                the user logged in (male). In this case they both equal so statement wouldnt proceed and function would move on. If this condition is true, we use continue
                too essentially just cut this user id and move onto the next one    
                */
            }

            // calculate match weight for the user if it passes the gender check 
            $weightScore = calcMatchWeight($targetUserId);

            //get target user details to display
            $targetUserIdProfilePicFilename = getProfilePicture($targetUserId);
            $targetUserIdName = getName($targetUserId);
            $targetUserIdAge = getAge($targetUserId);
            $targetUserIdCollegeYear = getCollegeYear($targetUserId);
            $targetUserIdCourse = getCourse($targetUserId);
            $targetUserIdLookingFor = getLookingFor($targetUserId);
            $targetUserIdBio = getBio($targetUserId);
            $targetUserIdHobbies = getHobbies($targetUserId);

            // store user id of a user that makes it thus for and their subsuqenet match score, now display details from mockup
            $usersToExplore[] = array(
                'user_id' => $targetUserId,
                'profile_pic_filename' => $targetUserIdProfilePicFilename,
                'name' => $targetUserIdName,
                'age' => $targetUserIdAge,
                'gender' => $targetUserIdGender,
                'college_year' => $targetUserIdCollegeYear,
                'course' => $targetUserIdCourse,
                'pursuing' => $targetUserIdPursuing,
                'looking_for' => $targetUserIdLookingFor,
                'bio' => $targetUserIdBio,
                'hobbies' => $targetUserIdHobbies,
                'weight_score' => $weightScore
            );
        }

        // sort users from match score in descending order
        usort($usersToExplore, function ($a, $b) {
            return $b['weight_score'] - $a['weight_score'];
        });


        $_SESSION['explore_state'][$userLoggedInId]['users_to_explore'] = $usersToExplore;
    }

    /*
    return users to explore which give an array of the user id matched with their "match score"
    now have it set as session so can actually update
    */
    return $_SESSION['explore_state'][$userLoggedInId]['users_to_explore'];
}

// GET request for the action which is assigned to adore and ignore.
if (isset($_GET['action'])) {
    // get the current position from the session
    $currentPosition = $_SESSION['explore_state'][$userLoggedInId]['current_position'];

    // Get the current based on the current position before incrementing for the next user so we can then use it to add to db for an adore or ignore action
    $currentUser = $_SESSION['explore_state'][$userLoggedInId]['users_to_explore'][$currentPosition] ?? null;

    if ($currentUser !== null) {
        if ($_GET['action'] === 'adore') {
            adoreUser($userLoggedInId, $currentUser['user_id']);
            if (isItAMatch($userLoggedInId, $currentUser['user_id'])) {
                // if it is a match, add to matches table
                addMatch($currentUser['user_id'], $userLoggedInId);
            }
        } elseif ($_GET['action'] === 'ignore') {
            ignoreUser($userLoggedInId, $currentUser['user_id']);
        }
    }

    // Increment the current position after handling any action (adore or ignore)
    $_SESSION['explore_state'][$userLoggedInId]['current_position']++;
}

// Process #45, fucntion to get all adores of the user currently logged in
function getAllAdores($userLoggedInId)
{
    global $conn;
    $adoredUsers = [];
    $sql = "SELECT adored_user_id FROM adore WHERE user_id = ?";
    $adores = $conn->prepare($sql);
    $adores->bind_param("i", $userLoggedInId);
    $adores->execute();
    $adoresResult = $adores->get_result();
    while ($row = $adoresResult->fetch_assoc()) {
        $adoredUsers[] = $row["adored_user_id"];
    }
    return $adoredUsers;
}

// function to get all ignores of user currently logged in
function getAllIgnores($userLoggedInId)
{
    global $conn;
    $ignoredUsers = [];
    $sql = "SELECT ignored_user_id FROM `ignore` WHERE user_id = ?";
    $ignores = $conn->prepare($sql);
    $ignores->bind_param("i", $userLoggedInId);
    $ignores->execute();
    $ignoresResult = $ignores->get_result();
    while ($row = $ignoresResult->fetch_assoc()) {
        $ignoredUsers[] = $row["ignored_user_id"];
    }
    return $ignoredUsers;
}

// call these functions to get users adores and ignores currently so we dont redisplay users. important to call before usersToExplore
$adoredUsers = getAllAdores($userLoggedInId);
$ignoredUsers = getAllIgnores($userLoggedInId);

// Call the users to explore method
$usersToExplore = getUsersForExplore($userLoggedInId, $adoredUsers, $ignoredUsers);

// Attempt to get the next user based on current position in users_to_explore 
$currentPosition = $_SESSION['explore_state'][$userLoggedInId]['current_position'];
$nextUser = $usersToExplore[$currentPosition] ?? null;

//initialise variables to null first so then if no users to explore it will display no users to explore
$nextUserId = null;
$nextUserScore = null;
$nextUserProfilePicFilename = null;
$nextUserName = null;
$nextUserAge = null;
$nextUserGender = null;
$nextUserCollegeYear = null;
$nextUserCourse = null;
$nextUserPursuing = null;
$nextUserLookingFor = null;
$nextUserBio = null;
$nextUserHobbies = null;

//set display user to false
$displayUser = false;

if ($nextUser) {
    $nextUserId = $nextUser['user_id'];
    $nextUserScore = $nextUser['weight_score'];
    $nextUserProfilePicFilename = $nextUser['profile_pic_filename'];
    $nextUserName = $nextUser['name'];
    $nextUserAge = $nextUser['age'];
    $nextUserGender = $nextUser['gender'];
    $nextUserCollegeYear = $nextUser['college_year'];
    $nextUserCourse = $nextUser['course'];
    $nextUserPursuing = $nextUser['pursuing'];
    $nextUserLookingFor = $nextUser['looking_for'];
    $nextUserBio = $nextUser['bio'];
    $nextUserHobbies = $nextUser['hobbies'];
    $displayUser = true;
} else {
    $displayUser = false; // if no users left to explore, reset to false
}

//include the HTML file
include "explore.html";
?>