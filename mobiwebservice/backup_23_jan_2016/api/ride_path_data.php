<?php
include 'config.php';
include ('function.php');

$data = json_decode(file_get_contents('php://input'), TRUE);
$cuisines = array();

$order_id = $data['data']['order_id'];
if($order_id != '')
{
	$query = "select * from driver_ride_path where order_id = '".$order_id."'";
	$result = mysql_query($query) or die(mysql_error());
	$count = mysql_num_rows($result);
	if($count > 0) 
	{
	    while ($row = mysql_fetch_assoc($result)) 
	    {
	        $info[] = $row;
	    }
	    $output=array("status"=>"1","data"=>$info);
	    echo json_encode($output);
		exit;
	}
	else
	{
		$output=array("status"=>"0","data"=>"No data found");
		echo json_encode($output);exit;
	}
}
else
{
	$output=array("status"=>"0","data"=>"Please Enter Correct Data");
	echo json_encode($output);exit;
}