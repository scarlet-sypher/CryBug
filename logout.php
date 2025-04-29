<?php
session_start();


session_start();
session_unset();     // clear all session variables
session_destroy();
header("Location: index.php");
exit;
?>