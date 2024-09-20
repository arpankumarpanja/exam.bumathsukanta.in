<?php
$servername = "localhost";
$username = "root";
$password = ""; // Replace with your own password
$dbname = "exam_system";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
