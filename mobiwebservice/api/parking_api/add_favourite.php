<?php
error_reporting(0);
header('Content-Type: application/json');
include ('../config.php');
include ('../function.php');

$data = json_decode(file_get_contents('php://input'), TRUE);
$user_id = sanitize($data['data']['user_id']);
$parking_id = sanitize($data['data']['parking_id']);
$time  = date('Y-m-d H:i:s');

if($user_id != '' && $parking_id != '')
{
	$chk = "select * from tbl_favorite where Parking_ID = '".$parking_id."' and user_id = '".$user_id."'";
	$chk_query = mysqli_query($con,$chk);
	$c_count = mysqli_num_rows($chk_query);
	if($c_count > 0 )
	{
		$Query2="DELETE FROM tbl_favorite  where Parking_ID = '".$parking_id."' and user_id = '".$user_id."'";
		mysqli_query($con,$Query2);
		$output=array("status"=>"1","data"=>"Favorite Removed");
		echo json_encode($output);exit;
	/*	$dd = mysqli_fetch_array($chk_query);
		if($dd['status'] == '1')
		{
			$Query2="update tbl_favorite set status = '0' where user_id = '".$user_id."' and Parking_ID = '".$parking_id."'";
			mysqli_query($con,$Query2);
			$output=array("status"=>"1","data"=>"Favorite Removed");
			echo json_encode($output);exit;
		}
		else
		{
			$Query3="update tbl_favorite set status = '1' where user_id = '".$user_id."' and Parking_ID = '".$parking_id."'";
			mysqli_query($con,$Query3);
			$output=array("status"=>"1","data"=>"Favorite Added");
			echo json_encode($output);exit;
		}
		*/
	}
	else
	{
		$Query1="INSERT INTO tbl_favorite(Parking_ID,user_id,cdate,status) VALUES('".$parking_id."','".$user_id."','".$time."','1')";
		mysqli_query($con,$Query1);
		$output=array("status"=>"1","data"=>"Favorite Added");
		echo json_encode($output);exit;
	}
}
else
{
	$output=array("status"=>"0","data"=>"No Records found");
	echo json_encode($output);exit;
}
