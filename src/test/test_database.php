<?php

include "../pages/db_connection.php";

// Define your SQL query
$sql = "SELECT * FROM Account";

// Execute the SQL query
$result = mysqli_query($conn, $sql);

// Check if the query was successful
if ($result) {
    // Check if there are any rows returned
    if (mysqli_num_rows($result) > 0) {
        // Output data of each row
        while ($row = mysqli_fetch_assoc($result)) {
            echo "ID: " . $row["id"] . " - email: " . $row["email"] . "<br>";
        }
    } else {
        echo "No records found";
    }
} else {
    echo "Error: " . mysqli_error($conn);
}

// Close the database connection
mysqli_close($conn);
?>