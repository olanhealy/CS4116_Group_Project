<a href="home.php">Home</a><br>
<a href="searchPage.html">Search Again?</a><br>
<?php

include "db_connection.php";
include "helperFunctions.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $searchAgeMin = $_POST['min_age'];
    $searchAgeMax = $_POST['max_age'];
    $searchGender = $_POST['gender'];
    $searchLookingFor = $_POST['looking_for'];
    $searchCollegeYear = $_POST['college_year'];

    // Build the SQL query using prepared statements to prevent SQL injection
    $query = "SELECT * FROM profile WHERE age BETWEEN ? AND ?";

    // Bind parameters for the age range
    $params = array();

    // Append conditions for gender, looking for, and college year if they are provided
    if ($searchGender !== "any") {
        $query .= " AND gender = ?";
        $params[] = $searchGender;
    }

    if ($searchLookingFor !== "any") {
        $query .= " AND looking_for = ?";
        $params[] = $searchLookingFor;
    }

    if ($searchCollegeYear !== "any") {
        $query .= " AND college_year = ?";
        $params[] = $searchCollegeYear;
    }

    // Determine the types of parameters for bind_param
    $types = 'ii'; // 'i' for integer, 's' for string

    // Append type and value pairs for each parameter
    foreach ($params as $param) {
        if (is_string($param)) {
            $types .= 's'; // Add a string type
        }
    }

    $stmt = $conn->prepare($query);
    // Create an array with all parameters including the age range parameters
    $allParams = array_merge(array($types), array($searchAgeMin, $searchAgeMax), $params);

    // Bind parameters
    $stmt->bind_param(...$allParams);

    // Execute the SQL query
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo '<div class="profile-cards-container">';
        // Output data of each row with a form to ban/unban
        while ($row = $result->fetch_assoc()) {
            $userId = $row['user_id'];
            showProfileCard($userId);
        }
        echo '</div>';
    } else {
        //error
        echo "0 results found";
    }

}