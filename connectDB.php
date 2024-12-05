<?php
$servername = "localhost";
$dBUsername = "root"; // or your database username
$dBPassword = ""; // or your database password
$dBName = "rfidattendance";

$conn = mysqli_connect($servername, $dBUsername, $dBPassword, $dBName);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>