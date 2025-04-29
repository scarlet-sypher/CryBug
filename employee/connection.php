<?php

$servername = "localhost";
$username = "root";
$password = "kousei arima";
$dbname = "crybug";

$con = mysqli_connect($servername, $username, $password, $dbname);

if (!$con) die("Connection failed: " . mysqli_connect_error());

// else  echo "<p style='color:green;'>Database connected successfully</p>" ; 


// mysqli_close($con);
?>