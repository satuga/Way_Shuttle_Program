<?php 
include ('config.php');
include ('function.php');

$data = json_decode(file_get_contents('php://input'), TRUE);
$driver_id = sanitize($data['data']['driver_id']);
$order_id = sanitize($data['data']['order_id']);
$user_id = sanitize($data['data']['user_id']);
$status = '3';
$picked = 'Delivered';

if($driver_id != '' && $order_id != '')
{
	$Query1="update orders set orderStatus = '".$picked."' where id = '".$order_id."'";
	mysqli_query($con,$Query1);
	
	$Query2="update driver_accept_ride set status = '".$status."' where order_id = '".$order_id."' and driver_id ='".$driver_id."'";
	mysqli_query($con,$Query2);
	
	
	$user_info = "select * from tbl_user_device where user_id='".$user_id."'";
 	$info_query = mysqli_query($con,$user_info);
 	while($row = mysqli_fetch_assoc($info_query))
	{
		$res[] = $row;
	}
	$msg = "Your order has been delivered";
	for($i=0;$i<sizeof($res);$i++)
	{
		if($res[$i]['device_type'] == '1')
		{
			IOS_notification($res[$i]['access_token'],$msg);
		}
		elseif($res[$i]['device_type'] == '2')
		{
			android_notification($res[$i]['access_token'],$msg);
		}
		else
		{
		}
	}

	$output=array("status"=>"1","data"=>"Order Delivered");
	echo json_encode($output);exit;
	
}
else 
{
	$output=array("status"=>"0","data"=>"No Records found");
	echo json_encode($output);exit;
}
?>