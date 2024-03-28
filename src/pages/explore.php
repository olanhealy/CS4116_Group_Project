<?php
//require db connection and the helper file as we will need to use the getters for displaying certain information about users
require "db_connection.php";
require "helper.php";

// Start session securely
session_start();

// Fetch user id of user currently accessing the explore page
$user_logged_in_id = $_SESSION['id'];

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
function calcMatchWeight($target_user_id) {
    global $user_logged_in_id, $user_logged_in_age, $user_logged_in_hobbies, $user_logged_in_college_year, $user_logged_in_course, $user_logged_in_looking_for;

    // we will get the target users relevant details here as only now do we need them as the userid of target user has passed the "gender check"
    $target_user_id_hobbies = getHobbies($target_user_id);
    $target_user_id_age = getAge($target_user_id);
    $target_user_id_college_year = getCollegeYear($target_user_id);
    $target_user_id_course = getCourse($target_user_id);
    $target_user_id_looking_for = getLookingFor($target_user_id);


    //weight array were key represents the % in decimal form of how much influence it will have on which user is displayed to current user
    $weight = array (
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

function getUsersForExplore($user_logged_in_id) {
    if (empty($_SESSION['explore_state'][$user_logged_in_id]['users_to_explore'])) { // if no users to explore are set in session, we will then use this method to get them
        // this helps as if a user leaves the page, its state will be remember and will nott have to recalculate the users to explore
    global $conn, $user_logged_in_gender, $user_logged_in_pursuing;
    
    //initalise array to explore users that will eventually pass the criteria made for user logged in 
    $users_to_explore = array();

    // for getting all users, is a process so placeholder for now
    $sql_all_users = "SELECT user_id FROM profile WHERE user_id != ?"; 

    $get_all_users = $conn->prepare($sql_all_users);
    $get_all_users->bind_param("i", $user_logged_in_id);
    $get_all_users->execute();
    $result_all_users = $get_all_users->get_result();

    while ($users_row = $result_all_users->fetch_assoc()) {
        $target_user_id = $users_row['user_id']; 

        /*
        The first check needed is to make sure the user logged in is can only 'explore' the gender they are pursuing and that the displayed users are only
        exploring the gender of the user logged in. i.e if the user logged in is a male, who is pursuing a female, the displayed user must a female who is pursuing a male
        */
    
        $target_user_id_gender = getGender($target_user_id);
        $target_user_id_pursuing = getPursuing($target_user_id);

        if ($target_user_id_gender !== $user_logged_in_pursuing || $target_user_id_pursuing !== $user_logged_in_gender) {
            continue; //so if the target (female) does not equal the user logged in pursuing (female) it would pass, and if the targer user pursuing (male) does not equal the gender of
                      //the user logged in (male). In this case they both equal so statement wouldnt proceed and function would move on. If this condition is true, we use continue
                      //too essentially just cut this user id and move onto the next one         
        }

        // calculate match weight for the user if it passes the gender check 
        $weight_score = calcMatchWeight($target_user_id);   

        // store user id of a user that makes it thus for and their subsuqenet match score
        $users_to_explore[] = array(
            'user_id' => $target_user_id,
            'weight_score' => $weight_score
        );
    }

    // sort users from match score in descending order
    usort($users_to_explore, function ($a, $b) {
        return $b['weight_score'] - $a['weight_score'];
    });

    
    $_SESSION['explore_state'][$user_logged_in_id]['users_to_explore'] = $users_to_explore;
    }
    
    // return users to explore which give an array of the user id matched with their "match score"
    // now have it set as session so can actually update
    return $_SESSION['explore_state'][$user_logged_in_id]['users_to_explore'] ;
}

// GET request for the action which is assigned to adore and ignore.
if (isset($_GET['action'])) {
    // get the current position from the session
    $current_position = $_SESSION['explore_state'][$user_logged_in_id]['current_position'];
    
    // gets the current based on the current position beofore incrementing for the next user so we can then use it to add to db for an adore or ignore action
    $current_user = $_SESSION['explore_state'][$user_logged_in_id]['users_to_explore'][$current_position] ?? null;

    if ($current_user !== null) {
        if ($_GET['action'] === 'adore') {
            $adore_sql = "INSERT INTO adore (user_id, adored_user_id, date) VALUES (?, ?, NOW())";
            if ($adore = $conn->prepare($adore_sql)) {
                $adore->bind_param("ii", $user_logged_in_id, $current_user['user_id']);
                $adore->execute();
                $adore->close();
            }
        } else if ($_GET['action'] === 'ignore') { // Correctly moved to a separate condition
            $ignore_sql = "INSERT INTO `ignore` (user_id, ignored_user_id, date) VALUES (?, ?, NOW())";
            if ($ignore = $conn->prepare($ignore_sql)) {
                $ignore->bind_param("ii", $user_logged_in_id, $current_user['user_id']);
                $ignore->execute();
                $ignore->close();
            }
        }
    }
    // increment the current position after handling any action
    $_SESSION['explore_state'][$user_logged_in_id]['current_position']++;
}


// Call the users to explore method
$users_to_explore = getUsersForExplore($user_logged_in_id);

// Attempt to get the next user based on current position in users_to_explore 
$current_position = $_SESSION['explore_state'][$user_logged_in_id]['current_position'];
$next_user = $users_to_explore[$current_position] ?? null;

//initialise variables to null first so then if no users to explore it will display no users to explore
$next_user_id = null;
$next_user_score = null;
$displayUser = false;

if ($next_user) {
    $next_user_id = $next_user['user_id'];
    $next_user_score = $next_user['weight_score'];
    $displayUser = true;
} else {
    $displayUser = false; // if no users left to explore, reset to false
}
    
//include the HTML file
include "explore.html";
?>