<?php
session_start();


session_start();
session_unset();     // clear all session variables
session_destroy();
// header("Location: dashboard.php");
header("Location: ../leaders/manager-Login.php");
exit;
?>