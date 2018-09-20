<?php
require_once('config.php');
$query = "update tbl_shuttle_online_detail set SOD_status = 0";
$res = mysqli_query($con, $query) or die("Error");
?>
