<?php 
include ('config.php');
include ('function.php');

$data = json_decode(file_get_contents('php://input'), TRUE);
$driver_id = $data['data']['driver_id'];
$user_id = $data['data']['user_id'];
$order_id = $data['data']['order_id'];
$latitude = $data['data']['driver_lat'];
$longitude = $data['data']['driver_long'];
$time  = date('Y-m-d H:i:s');
$status = "1";


if($user_id != '' && $driver_id != '')
{
	$chk = "select orderStatus from orders where id = '".$order_id."' and orderStatus = 'Confirmed'";
	$query_chk = mysqli_query($con,$chk);
	$rows = mysqli_num_rows($query_chk);
	if($rows > 0)
	{
		$Query1="update orders set orderStatus = 'Accepted' where id = '".$order_id."'";
		mysqli_query($con,$Query1);
		
		$Query2="INSERT INTO driver_accept_ride(driver_id,user_id,order_id,driver_lat,driver_long,status,created_at) VALUES ('".$driver_id."','".$user_id."','".$order_id."','".$latitude."','".$longitude."','".$status."','".$time."')";
		mysqli_query($con,$Query2);
		
		$output=array("status"=>"1","data"=>"Driver Accepted Order");
		echo json_encode($output);exit;
	}
	else 
	{
		$output=array("status"=>"0","data"=>"Order is already accpeted");
		echo json_encode($output);
		exit;
	}
}
else 
{
	$output=array("status"=>"0","data"=>"No Records found");
	echo json_encode($output);exit;
}
?>