<?php
//logout
session_start();
//unset users to explore if they logout (looking now dno if needed but too late for this shit)
unset($_SESSION['users_to_explore']);
session_unset();
session_destroy();

header("Location: index.php");