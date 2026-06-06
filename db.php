<?php
$host     = "sql103.infinityfree.com";
$dbname   = "if0_42114629_nestway";
$username = "if0_42114629";
$password = "nOY7zlppS20";   // the password YOU set

$conn = mysqli_connect($host, $username, $password, $dbname);
if (!$conn) {
    die("Connection Failed: " . mysqli_connect_error());
}
mysqli_set_charset($conn, "utf8");
?>