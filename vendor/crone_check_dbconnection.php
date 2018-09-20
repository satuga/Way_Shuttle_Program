<?php 
$start_date = date('Y-m-d H:i:s');
//error_log("Start time : ".$start_date."\n",3,"E:/Apache24/htdocs/responsive/shuttletracking/nodejs/daemon/db_connection/db_connection_check.log");
$DBSERVER="localhost";
$DBUSER="root";
$DBPWD="";
$DBDATABASE="wayBI";

$con = mysqli_connect($DBSERVER,$DBUSER,$DBPWD);
if (mysqli_connect($DBSERVER,$DBUSER,$DBPWD)) { 
	$connection_db = mysqli_select_db($con,$DBDATABASE);
    if (!$connection_db) { echo 'not connected';  }

    $conn_date = date('Y-m-d H:i:s');  
  //  error_log("connect to database time : ".$conn_date."\n",3,"E:/Apache24/htdocs/responsive/shuttletracking/nodejs/daemon/db_connection/db_connection_check.log");
	
}else{

	$connection_db = mysqli_select_db($con,$DBDATABASE);
    if (!$connection_db) { echo 'not connected';  }

	$loss_date = date('Y-m-d H:i:s');
	//error_log("Failed to connect to database : ".$loss_date."\n",3,"E:/Apache24/htdocs/responsive/shuttletracking/nodejs/daemon/db_connection/db_connection_check.log");
}

