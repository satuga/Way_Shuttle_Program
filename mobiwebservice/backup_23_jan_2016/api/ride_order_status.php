<?php 
include ('config.php');
include ('function.php');

$data = json_decode(file_get_contents('php://input'), TRUE);
$order_id = $data['data']['order_id'];

if($order_id != '')
{
	$Query1="select orderStatus from orders where id = '".$order_id."'";
	$status_query = mysql_query($Query1);
	$row = mysql_fetch_assoc($status_query);
	
	$count = mysql_num_rows($status_query);
	if($count > 0)
	{
		$output=array("status"=>"1","data"=>$row['orderStatus']);
		echo json_encode($output);exit;
	}
	else 
	{
		$output=array("status"=>"0","data"=>"Order not found");
		echo json_encode($output);exit;
	}
}
else 
{
	$output=array("status"=>"0","data"=>"Please enter proper data");
	echo json_encode($output);exit;
}
