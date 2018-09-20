<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "ln";
$dbname = "wayBI";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname, 3306, '/run/mysqld/mysqld.sock');
// Check connection
if ($conn->connect_error) {
die(sprintf("gege[%d] %s\n", mysqli_connect_errno(), mysqli_connect_error()));
die("Connection failed: " . $conn->connect_error);

}
$query = "update tbl_shuttle_online_detail set SOD_status = 0";
$res = mysqli_query($conn, $query) or die("Error");
?>
