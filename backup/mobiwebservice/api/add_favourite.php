<?php 
include ('config.php');
include ('function.php');

$data = json_decode(file_get_contents('php://input'), TRUE);
$user_id = $data['data']['user_id'];
$dine_id = $data['data']['dine_id'];
$time  = date('Y-m-d H:i:s');

if($user_id != '' && $dine_id != '')
{
	$chk = "select * from tbl_favorite where Dine_ID = '".$dine_id."' and user_id = '".$user_id."'";
	$chk_query = mysqli_query($con,$chk);
	$c_count = mysqli_num_rows($chk_query);
	if($c_count > 0 )
	{
		$dd = mysqli_fetch_array($chk_query);
		if($dd['status'] == '1')
		{
			$Query2="update tbl_favorite set status = '0' where user_id = '".$user_id."' and Dine_ID = '".$dine_id."'";
			mysqli_query($con,$Query2);
			$output=array("status"=>"1","data"=>"Favorite Removed");
			echo json_encode($output);exit;
		}
		else
		{
			$Query3="update tbl_favorite set status = '1' where user_id = '".$user_id."' and Dine_ID = '".$dine_id."'";
			mysqli_query($con,$Query3);
			$output=array("status"=>"1","data"=>"Favorite Added");
			echo json_encode($output);exit;
		}
	}
	else
	{
		$Query1="INSERT INTO tbl_favorite(Dine_ID,user_id,cdate,status) VALUES('".$dine_id."','".$user_id."','".$time."','1')";
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
