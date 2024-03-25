<?php
//require db connection and the helper file as we will need to use the getters for displaying certain information about users
require "db_connection.php";
require "helper.php";

//start session securly 
session_start();

// Fetch user id of user currently accessing the explore page
$user_logged_in_id = $_SESSION['id'];

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
        'looking_for' => .20 //20% increase

    );

    //variable to store the score meter. This will help determine which user is a 'best fit' for current user based of certain criteria 
    $weight_score = 0;

    foreach ($weight as $key => $weight) {
        switch ($key) {
            //case for age
            case 'age':
                $age_difference = abs($user_logged_in_age - $target_user_id_age);
                
                // Score set based of these 3 constraints
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
                $score = count($matching_hobbies) * 50; // Each matching hobby contributes 10 to the score
                break;
            //case for college year    
            case 'college_year':
                if ($user_logged_in_college_year === $target_user_id_college_year) {
                    $score = 100; // Same college year
                } else {
                    $score = 0; // Different college year
                }
                break;
            //case for course    
            case 'course':
                
                if ($user_logged_in_course === $target_user_id_course) {
                    $score = 100; // Same course
                } else {
                    $score = 0; // Different course
                }
                break;
            //case for looking_for
            case 'looking_for':
                if ($user_logged_in_looking_for === $target_user_id_looking_for) {
                    $score = 100; // Same looking for status
                } else {
                    $score = 0; // Different looking for status
                }
                break;
            default:
                // Default score if no criteria match
                $score = 0;
                break;
        }
        
        // Add the weighted score to the total score
        $weight_score += $weight * $score;
    }
    return $weight_score;
}

function getUsersForExplore($user_logged_in_id) {
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
        
        
      

        // Calculate match weight for the user if it passes the gender check 
        $weight_score = calcMatchWeight($target_user_id);
        

        // Store the user ID and match weight in the array
        $users_to_explore[] = array(
            'user_id' => $target_user_id,
            'weight_score' => $weight_score
        );
    }

    // Sort users based on their weight score (descending order)
    usort($users_to_explore, function ($a, $b) {
        return $b['weight_score'] - $a['weight_score'];
    });

    // Now $users_to_explore contains the user IDs sorted by their match weight
    return $users_to_explore;
        
    }


// Get users to explore
$users_to_explore = getUsersForExplore($user_logged_in_id);

// Check if there are users to explore
if (!empty($users_to_explore)) {
    // Display the first user with the highest match score
    $next_user = array_shift($users_to_explore);
    $next_user_id = $next_user['user_id'];
    $next_user_score = $next_user['weight_score'];

    // Include the HTML file
    include "explore.html";
} else {
    // No more users to explore
    echo "<p>No more users available</p>";
}
?>