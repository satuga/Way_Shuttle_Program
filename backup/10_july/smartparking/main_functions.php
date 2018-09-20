<?php

include("DataObjectLayer/GateParkingTransactionManagerDO.php");
 

$object = new GateParkingTransactionManagerDO();

$object->GarageID = 'bar';


echo "test";
echo "<br>GarageID :" .$object->GarageID;


?>
