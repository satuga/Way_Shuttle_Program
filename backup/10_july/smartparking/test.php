<?php
$to_time = strtotime("2008-12-13 10:42:00");
$from_time = strtotime("2008-12-13 10:40:00");
echo round(abs($to_time - $from_time) / 60,2). " minute";
?>