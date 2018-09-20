<?php
include 'security.php';

$value = "1NprUJ1G9O80i/WRIn3JPQOKIjSGIXC4jnT0IvLi6a8=";
$key = "L0M35r1j3mqcntZL"; //16 Character Key
echo Security::decrypt($value,$key);

?>