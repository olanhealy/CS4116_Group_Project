<?php
    include "../db_connection.php";
    include "adminHelperFunctions.php";
    include "setBanned.php";

    // Select users with standard role
    $sql = "SELECT * FROM account WHERE user_role='standard'";
    $result = mysqli_query($conn, $sql);

    if ($result->num_rows > 0) {
        // Output data of each row with a form to ban/unban
        while($row = $result->fetch_assoc()) {
            include "userListAdmin.html"; // Include HTML file for each row

            if (isAccountBanned($row["user_id"]) == 1) {
                $user_id = $row["user_id"];
                include "unban.html"; // Include HTML file for unban button
            }
        }
    } else {
        echo "0 results found";
    }

    mysqli_close($conn);