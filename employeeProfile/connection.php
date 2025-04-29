<?php

$servername = "localhost";
$username = "root";
$password = "kousei arima";
$dbname = "crybug";

$con = mysqli_connect($servername, $username, $password, $dbname);

if (!$con) die("Connection failed: " . mysqli_connect_error());

// else echo "<h1>connected</h1>" ;


// mysqli_close($con);
?>
