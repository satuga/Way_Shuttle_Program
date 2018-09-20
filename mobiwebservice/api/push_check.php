<?php
include ('config.php'); 
include("function.php"); 

$token = "APA91bE8TqIjcT0SP0IBgKGvo9Ed-51jNBHKEdHl_Q6Bnhxtpw_vyLh0D4i2eN-LijQfjFb0yBfVYNgUfWIfNlhcIRR0TU4SwMkxmcd6oIwm21cSgWLwLZw";
$msg = "hi dhruv";

//IOS_notification($token,$msg);
android_notification($token,$msg);
?>