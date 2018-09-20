<?php
include ('config.php');
include ('function.php');

$data = json_decode(file_get_contents('php://input'), TRUE);
$user_id = $data['data']['user_id'];
$parking_id = $data['data']['parking_id'];
$time  = date('Y-m-d H:i:s');

if($user_id != '' && $parking_id != '')
{
	$chk = "select * from tbl_favorite where Parking_ID = '".$parking_id."' and user_id = '".$user_id."'";
	$chk_query = mysql_query($chk);
	$c_count = mysql_num_rows($chk_query);
	if($c_count > 0 )
	{
		$dd = mysql_fetch_array($chk_query);
		if($dd['status'] == '1')
		{
			$Query2="update tbl_favorite set status = '0' where user_id = '".$user_id."' and Parking_ID = '".$parking_id."'";
			mysql_query($Query2);
			$output=array("status"=>"1","data"=>"Favorite Removed");
			echo json_encode($output);exit;
		}
		else
		{
			$Query3="update tbl_favorite set status = '1' where user_id = '".$user_id."' and Parking_ID = '".$parking_id."'";
			mysql_query($Query3);
			$output=array("status"=>"1","data"=>"Favorite Added");
			echo json_encode($output);exit;
		}
	}
	else
	{
		$Query1="INSERT INTO tbl_favorite(Parking_ID,user_id,cdate,status) VALUES('".$parking_id."','".$user_id."','".$time."','1')";
		mysql_query($Query1);
		$output=array("status"=>"1","data"=>"Favorite Added");
		echo json_encode($output);exit;
	}
}
else
{
	$output=array("status"=>"0","data"=>"No Records found");
	echo json_encode($output);exit;
}
