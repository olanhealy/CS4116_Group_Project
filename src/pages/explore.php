<?php
//require db connection and the helper file as we will need to use the getters for displaying certain information about users
require "db_connection.php";
require "helper.php";
require "matches.php";

// Start session securely
session_start();

// get user id of user currently accessing the explore page
$user_logged_in_id = $_SESSION['id'];

// delete ignores older than 3 minutes keeping as 3 for testing purposes (kinda works kind of doesnt idk something to do with timezones or some bs)
//$cleanup_sql = "DELETE FROM `ignore` WHERE `date` < NOW() - INTERVAL 3 MINUTE";
//$conn->query($cleanup_sql);

// initialide user's exploration state if not already set or if a different user is logged in
if (!isset($_SESSION['explore_state'][$user_logged_in_id])) {
    $_SESSION['explore_state'][$user_logged_in_id] = [
        'users_to_explore' => [],
        'current_position' => 0, // tracks the current position in the explore list
    ];
}
//Use the getters from helper.php only of the details we require from profile table of db for the user logged in
$user_logged_in_hobbies = getHobbies($user_logged_in_id);
$user_logged_in_gender = getGender($user_logged_in_id);
$user_logged_in_age = getAge($user_logged_in_id);
$user_logged_in_college_year = getCollegeYear($user_logged_in_id);
$user_logged_in_pursuing = getPursuing($user_logged_in_id);
$user_logged_in_course = getCourse($user_logged_in_id);
$user_logged_in_looking_for = getLookingFor($user_logged_in_id);

//Fucntion for how we are going to differ the weights to display matches. We are going to have a few factors
function calcMatchWeight($target_user_id)
{
    global $user_logged_in_id, $user_logged_in_age, $user_logged_in_hobbies, $user_logged_in_college_year, $user_logged_in_course, $user_logged_in_looking_for;

    // we will get the target users relevant details here as only now do we need them as the userid of target user has passed the "gender check"
    $target_user_id_hobbies = getHobbies($target_user_id);
    $target_user_id_age = getAge($target_user_id);
    $target_user_id_college_year = getCollegeYear($target_user_id);
    $target_user_id_course = getCourse($target_user_id);
    $target_user_id_looking_for = getLookingFor($target_user_id);


    //weight array were key represents the % in decimal form of how much influence it will have on which user is displayed to current user
    $weight = array(
        'age' => 1.1, //10% increase
        'hobbies' => 1.4, //40% increase
        'college_year' => 1.15, //15% increase
        'course' => 1.15, //15% increase
        'looking_for' => 1.20 //20% increase

    );

    //variable to store the score meter. This will help determine which user is a 'best fit' for current user based of certain criteria 
    $weight_score = 0;

    foreach ($weight as $key => $weight) {
        switch ($key) {
            //case for age
            case 'age':
                $age_difference = abs($user_logged_in_age - $target_user_id_age);

                // different age difference constraints to give a score
                if ($age_difference <= 3) {
                    $score = 100;
                } elseif ($age_difference <= 6) {
                    $score = 50;
                } else {
                    $score = 0;
                }
                break;
            //case for hobbies    
            case 'hobbies':
                $user_hobbies = explode(',', $user_logged_in_hobbies);
                $target_user_hobbies = explode(',', $target_user_id_hobbies);
                $matching_hobbies = array_intersect($user_hobbies, $target_user_hobbies);

                // If hobbies match, for the amount that 2 multply it by 50 score
                $score = count($matching_hobbies) * 50; // each matching hobby contributes 10 to the score
                break;
            //case for college year    
            case 'college_year':
                if ($user_logged_in_college_year === $target_user_id_college_year) {
                    $score = 100; // same college year
                } else {
                    $score = 0; // different college year
                }
                break;
            //case for course    
            case 'course':

                if ($user_logged_in_course === $target_user_id_course) {
                    $score = 100; // same course
                } else {
                    $score = 0; // different course
                }
                break;
            //case for looking_for
            case 'looking_for':
                if ($user_logged_in_looking_for === $target_user_id_looking_for) {
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
        $weight_score += $weight * $score;
    }
    return $weight_score;
}

function getUsersForExplore($user_logged_in_id, $adored_users, $ignored_users)
{
    /*
    if no users to explore are set in session, we will then use this method to get them
    this helps as if a user leaves the page, its state will be remember and will nott have to recalculate the users to explore. Also checks to explore state
    which is postion in array
    */
    if (empty($_SESSION['explore_state'][$user_logged_in_id]['users_to_explore'])) {
        global $conn, $user_logged_in_gender, $user_logged_in_pursuing;

        //initalise array to explore users that will eventually pass the criteria made for user logged in 
        $users_to_explore = array();

        // for getting all users, is a process so placeholder for now
        $sql_users_to_explore = "SELECT user_id FROM profile WHERE user_id != ?";

        // check if the the user adores other user, if they do exclude them from being displayed
        if (!empty($adored_users)) {
            $placeholder = implode(",", array_fill(0, count($adored_users), "?"));
            $sql_users_to_explore .= " AND user_id NOT IN ($placeholder)";
        }

        // check if the user ignores other users, if they do exclude them from being displayed
        if (!empty($ignored_users)) {
            $placeholders = implode(",", array_fill(0, count($ignored_users), "?"));
            $sql_users_to_explore .= " AND user_id NOT IN ($placeholders)";
        }

        /*
        prepares our users_to_explore but excluding the adored and ignored users.
        str_repeat built in function and gets the total some of adored and ignored users and then adds 1 to account for the user logged in
        array_merge built in fucntion to merge the user logged in id with the adored and ignored users
        basically makes sure no users skip through 
        */
        $get_all_users_to_explore = $conn->prepare($sql_users_to_explore);
        $types = str_repeat("i", count($adored_users) + count($ignored_users) + 1);
        $values = array_merge([$user_logged_in_id], $adored_users, $ignored_users);
        $get_all_users_to_explore->bind_param($types, ...$values);
        $get_all_users_to_explore->execute();
        $result_all_users_to_explore = $get_all_users_to_explore->get_result();


        while ($users_row = $result_all_users_to_explore->fetch_assoc()) {
            $target_user_id = $users_row['user_id'];

            /*
            The first check needed is to make sure the user logged in is can only 'explore' the gender they are pursuing and that the displayed users are only
            exploring the gender of the user logged in. i.e if the user logged in is a male, who is pursuing a female, the displayed user must a female who is pursuing a male
            */

            $target_user_id_gender = getGender($target_user_id);
            $target_user_id_pursuing = getPursuing($target_user_id);

            if ($target_user_id_gender !== $user_logged_in_pursuing || $target_user_id_pursuing !== $user_logged_in_gender) {
                continue;
                /*
                so if the target (female) does not equal the user logged in pursuing (female) it would pass, and if the targer user pursuing (male) does not equal the gender of
                the user logged in (male). In this case they both equal so statement wouldnt proceed and function would move on. If this condition is true, we use continue
                too essentially just cut this user id and move onto the next one    
                */
            }

            // calculate match weight for the user if it passes the gender check 
            $weight_score = calcMatchWeight($target_user_id);

            //get target user details to display
            $target_user_id_profile_pic_filename = getProfilePicture($target_user_id);
            $target_user_id_name = getName($target_user_id);
            $target_user_id_age = getAge($target_user_id);
            $target_user_id_college_year = getCollegeYear($target_user_id);
            $target_user_id_course = getCourse($target_user_id);
            $target_user_id_looking_for = getLookingFor($target_user_id);
            $target_user_id_bio = getBio($target_user_id);
            $target_user_id_hobbies = getHobbies($target_user_id);

            // store user id of a user that makes it thus for and their subsuqenet match score, now display details from mockup
            $users_to_explore[] = array(
                'user_id' => $target_user_id,
                'profile_pic_filename' => $target_user_id_profile_pic_filename,
                'name' => $target_user_id_name,
                'age' => $target_user_id_age,
                'gender' => $target_user_id_gender,
                'college_year' => $target_user_id_college_year,
                'course' => $target_user_id_course,
                'pursuing' => $target_user_id_pursuing,
                'looking_for' => $target_user_id_looking_for,
                'bio' => $target_user_id_bio,
                'hobbies' => $target_user_id_hobbies,
                'weight_score' => $weight_score
            );
        }

        // sort users from match score in descending order
        usort($users_to_explore, function ($a, $b) {
            return $b['weight_score'] - $a['weight_score'];
        });


        $_SESSION['explore_state'][$user_logged_in_id]['users_to_explore'] = $users_to_explore;
    }

    /*
    return users to explore which give an array of the user id matched with their "match score"
    now have it set as session so can actually update
    */
    return $_SESSION['explore_state'][$user_logged_in_id]['users_to_explore'];
}

// GET request for the action which is assigned to adore and ignore.
if (isset($_GET['action'])) {
    // get the current position from the session
    $current_position = $_SESSION['explore_state'][$user_logged_in_id]['current_position'];

    // Get the current based on the current position before incrementing for the next user so we can then use it to add to db for an adore or ignore action
    $current_user = $_SESSION['explore_state'][$user_logged_in_id]['users_to_explore'][$current_position] ?? null;

    if ($current_user !== null) {
        if ($_GET['action'] === 'adore') {
            adoreUser($user_logged_in_id, $current_user['user_id']);
            if (isItAMatch($user_logged_in_id, $current_user['user_id'])) {
                // if it is a match, add to matches table
                addMatch($current_user['user_id'], $user_logged_in_id);
            }
        } elseif ($_GET['action'] === 'ignore') {
            ignoreUser($user_logged_in_id, $current_user['user_id']);
        }
    }

    // Increment the current position after handling any action (adore or ignore)
    $_SESSION['explore_state'][$user_logged_in_id]['current_position']++;
}
//process #43, functon to handle adore action 
function adoreUser($user_logged_in_id, $current_user_id)
{
    global $conn;
    $adore_sql = "INSERT INTO adore (user_id, adored_user_id, date) VALUES (?, ?, NOW())";
    if ($adore = $conn->prepare($adore_sql)) {
        $adore->bind_param("ii", $user_logged_in_id, $current_user_id);
        $adore->execute();
        $adore->close();
    }
}

//fucntion to handle ignore action
function ignoreUser($user_logged_in_id, $current_user_id)
{
    global $conn;
    $ignore_sql = "INSERT INTO `ignore` (user_id, ignored_user_id, date) VALUES (?, ?, NOW())";
    if ($ignore = $conn->prepare($ignore_sql)) {
        $ignore->bind_param("ii", $user_logged_in_id, $current_user_id);
        $ignore->execute();
        $ignore->close();
    }
}


// Process #45, fucntion to get all adores of the user currently logged in
function getAllAdores($user_logged_in_id)
{
    global $conn;
    $adored_users = [];
    $sql = "SELECT adored_user_id FROM adore WHERE user_id = ?";
    $adores = $conn->prepare($sql);
    $adores->bind_param("i", $user_logged_in_id);
    $adores->execute();
    $adores_result = $adores->get_result();
    while ($row = $adores_result->fetch_assoc()) {
        $adored_users[] = $row["adored_user_id"];
    }
    return $adored_users;
}

// function to get all ignores of user currently logged in
function getAllIgnores($user_logged_in_id)
{
    global $conn;
    $ignored_users = [];
    $sql = "SELECT ignored_user_id FROM `ignore` WHERE user_id = ?";
    $ignores = $conn->prepare($sql);
    $ignores->bind_param("i", $user_logged_in_id);
    $ignores->execute();
    $ignores_result = $ignores->get_result();
    while ($row = $ignores_result->fetch_assoc()) {
        $ignored_users[] = $row["ignored_user_id"];
    }
    return $ignored_users;
}

// call these functions to get users adores and ignores currently so we dont redisplay users. important to call before usersToExplore
$adored_users = getAllAdores($user_logged_in_id);
$ignored_users = getAllIgnores($user_logged_in_id);


// Call the users to explore method
$users_to_explore = getUsersForExplore($user_logged_in_id, $adored_users, $ignored_users);


// Attempt to get the next user based on current position in users_to_explore 
$current_position = $_SESSION['explore_state'][$user_logged_in_id]['current_position'];
$next_user = $users_to_explore[$current_position] ?? null;

//initialise variables to null first so then if no users to explore it will display no users to explore
$next_user_id = null;
$next_user_score = null;
$next_user_profile_pic_filename = null;
$next_user_name = null;
$next_user_age = null;
$next_user_gender = null;
$next_user_college_year = null;
$next_user_course = null;
$next_user_pursuing = null;
$next_user_looking_for = null;
$next_user_bio = null;
$next_user_hobbies = null;

//set display user to false
$displayUser = false;

if ($next_user) {
    $next_user_id = $next_user['user_id'];
    $next_user_score = $next_user['weight_score'];
    $next_user_profile_pic_filename = $next_user['profile_pic_filename'];
    $next_user_name = $next_user['name'];
    $next_user_age = $next_user['age'];
    $next_user_gender = $next_user['gender'];
    $next_user_college_year = $next_user['college_year'];
    $next_user_course = $next_user['course'];
    $next_user_pursuing = $next_user['pursuing'];
    $next_user_looking_for = $next_user['looking_for'];
    $next_user_bio = $next_user['bio'];
    $next_user_hobbies = $next_user['hobbies'];
    $displayUser = true;
} else {
    $displayUser = false; // if no users left to explore, reset to false
}

//include the HTML file
include "explore.html";
?>