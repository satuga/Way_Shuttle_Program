<?php 
include ('config.php');
include ('function.php');

$data = json_decode(file_get_contents('php://input'), TRUE);
$driver_id = $data['data']['driver_id'];
$order_id = $data['data']['order_id'];
$user_id = $data['data']['user_id'];
$status = '2';
$picked = 'Picked Up';

if($driver_id != '' && $order_id != '')
{
	$Query1="update orders set orderStatus = '".$picked."' where id = '".$order_id."'";
	mysql_query($Query1);
	
	$Query2="update driver_accept_ride set status = '".$status."' where order_id = '".$order_id."' and driver_id ='".$driver_id."'";
	mysql_query($Query2);
	
	
 	$user_info = "select * from tbl_user_device where user_id='".$user_id."'";
 	$info_query = mysql_query($user_info);
 	while($row = mysql_fetch_assoc($info_query))
	{
		$res[] = $row;
	}
	$msg = "Your order has been picked up";
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
 	
 
	$output=array("status"=>"1","data"=>"Order Picked Up");
	echo json_encode($output);exit;
	
}
else 
{
	$output=array("status"=>"0","data"=>"No Records found");
	echo json_encode($output);exit;
}

?>