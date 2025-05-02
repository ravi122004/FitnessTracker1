<?php
$host = "localhost";
$dbname = 'abc_db';  // Corrected to match the variable
$user = "root";
$pass = "";  // your MySQL password
$port = 3308;

$conn = new mysqli($host, $user, $pass, $dbname, $port);  // Use $dbname instead of $db

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
