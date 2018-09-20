<?php
header('Content-Type: application/json');
include ('config.php');
// include ('function.php');

$data = json_decode(file_get_contents('php://input'), TRUE);
$user_id = $data['data']['user_id'];

if($user_id != '')
{
	$user_query = "select * from tbl_deliveryaddress where DE_userID ='".$user_id."'";
	$res = mysqli_query($con,$user_query);
	$count = mysqli_num_rows($res);
	if($count > 0)
	{
		while($aRow=mysqli_fetch_assoc($res))
		{
			$info[] = $aRow;
		}
		$output=array("status"=>"1","data"=>$info);
		header('Content-Type: application/json; charset=utf-8');
		echo json_encode($output);
		exit;
	}
	else
	{
		$output=array("status"=>"0","data"=>"No records found");
		header('Content-Type: application/json; charset=utf-8');
		echo json_encode($output);exit;
	}
}
else
{
	$output=array("status"=>"0","data"=>"Please add correct data");
	header('Content-Type: application/json; charset=utf-8');
	echo json_encode($output);exit;
}
