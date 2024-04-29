<?php

include_once "../helpers/db_connection.php";
include_once "adminHelperFunctions.php";

adminAccessCheck();

//targetID set from GET sent from userListAdmin.html
if (isset($_GET['targetId'])) {
    $targetId = $_GET['targetId'];
} else {
    //if targetId is not set, check if it is set in SESSION
    if (isset($_SESSION['targetId'])) {
        $targetId = $_SESSION['targetId'];
    } else {
        //if targetId is not set in GET or SESSION, show error message
        echo "Target ID is not set.";
        exit();
    }

}
;

//transfer targetId to a SESSION variable
$_SESSION['targetId'] = $targetId;
//var_dump($_SESSION);
//if the account is banned show editProfile, unban, deleteUser
if (isAccountBanned($targetId)) {
    include "editProfileAdmin.php";
    echo '<div class="col-md-12 col-lg-12 col-lg-12" id="unbanContainer">';
    include "unban.html";
    include "deleteUser.html";
    echo '</div>';
    include "../helpers/footer.php";
} else {
    //if the account isn't banned
    //and the account is not an admin show editProfile, makeAdmin, banUser
    if (getUserRole($targetId) == "standard") {
        include "editProfileAdmin.php";
        //add buttons to div
        echo '<div class="col-md-12 col-lg-12 col-lg-12" id="linkContainer">';
        include "makeAdmin.html";
        echo '<button type="button" id="banUser" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#banUserModal">Ban User</button>';
    } else if (getUserRole($targetId) == 'admin') {
        //include page for viewing an admin account
        include "viewAdminAccount.html";
    }
    //if the user is banned just show delete button
    include "deleteUser.html";
    //close div
    echo '</div>';
    //always include footer on all pages
    include "../helpers/footer.php";
}
?>

<html>

<head>
    <!-- Style Sheet -->
    <link rel="stylesheet" type="text/css" href="../../assets/css/showUserAdmin.css">
    <link rel="icon" href="/ulSinglesSymbolTransparent.ico" type="image/x-icon">
</head>

<body>

    <!-- Ban User Modal -->
    <div class="modal fade" id="banUserModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header">
                    <h4 style="font-weight: bold;" class="modal-title">Ban User</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="banUser.php" method="post">
                    <div class="modal-body">

                        <!-- Text Box for Ban Reason -->
                        <label for="reason">Reason:</label>
                        <textarea id="banUserTextArea" name="reason" rows="4" cols="50" required></textarea><br><br>

                        <!-- Buttons for Ban Duration -->
                        <div class="row">
                            <label for="reason">Date of Unban:</label>

                            <div class="col-6" id="temporarydiv">
                                <input type="date" id="dateOfUnban" name="dateOfUnban"><br><br>
                            </div>

                            <div class="col-6" id="permanentdiv">
                                <!-- have this button to set unban date as year 9999 for perm-ban -->
                                <input type="hidden" id="permaBanInput" name="permaBan" value="0">

                                <input type="submit" id="permaBan" value="PermaBan">
                            </div>

                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <input type="submit" class="btn btn-danger" value="Ban User">
                    </div>
                </form>

            </div>
        </div>
    </div>
    <!-- Permanently Bans User -->
<!-- Permanently Bans User -->
<script>
    document.getElementById('banUser').addEventListener('click', function() {
        // Set the permaBan value to 0 when the "Ban User" button is clicked
        document.getElementById('permaBanInput').value = "0";
        // Ensure the dateOfUnban field is not required
        document.getElementById('dateOfUnban').removeAttribute('required');
    });

    document.getElementById('permaBan').addEventListener('click', function(event) {
        // Set the permaBan value to 1 when the "PermaBan" button is clicked
        document.getElementById('permaBanInput').value = "1";
        // Ensure the dateOfUnban field is not required
        document.getElementById('dateOfUnban').removeAttribute('required');
    });
</script>



    <!-- Doesn't allow the unban date to be before today's date -->
    <script>
        // Get today's date
        var today = new Date().toISOString().split('T')[0];

        // Set the minimum date for the input field
        document.getElementById("dateOfUnban").setAttribute("min", today);
    </script>

</body>

</html>