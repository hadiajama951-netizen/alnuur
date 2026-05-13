<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "alnuur"; // Hubi in magacani la mid yahay kan phpMyAdmin-kaaga

$conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
    die("Xidhiidhku wuu fashilmay: " . mysqli_connect_error());
}
?>