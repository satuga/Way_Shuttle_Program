<?php 
include ('config.php');
include ('function.php');

$data = json_decode(file_get_contents('php://input'), TRUE);
$order_id = sanitize($data['data']['order_id']);

if($order_id != '')
{
	$Query1="select orderStatus from orders where id = '".$order_id."'";
	$status_query = mysqli_query($con,$Query1);
	$row = mysqli_fetch_assoc($status_query);
	
	$count = mysqli_num_rows($status_query);
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
