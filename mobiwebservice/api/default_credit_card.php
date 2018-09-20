<?php
header("Content-Type: application/json");
include('config.php');
include('function.php');

$data = json_decode(file_get_contents('php://input'), TRUE);
$user_id = sanitize($data['data']['user_id']);
$card_id = sanitize($data['data']['card_id']);
$default=sanitize($data['data']['default_val'])!='' ? sanitize($data['data']['default_val']):'1';
if($user_id != '' && $card_id != '')
{
	$sql="Select * From tbl_creditcarddetails WHERE Card_User_ID=".$user_id." AND Card_ID=".$card_id; 
	$res=mysqli_query($con,$sql);
	if(mysqli_num_rows($res)>0)
	{
		
		$query="UPDATE tbl_creditcarddetails SET Card_Default = '0' WHERE Card_User_ID=".$user_id;
		mysqli_query($con,$query);
		$sql="UPDATE tbl_creditcarddetails SET Card_Default = '".$default."' WHERE Card_User_ID=".$user_id." AND Card_ID=".$card_id; //die;
		mysqli_query($con,$sql);
		$output=array("status"=>"1","data"=>"Successfully set default card");
		echo json_encode($output);exit;
	}
	else
	{
		$output=array("status"=>"0","data"=>"Record not found");
		echo json_encode($output);exit;

	}
}
else 
{
	$output=array("status"=>"0","data"=>"Missing parameters");
	echo json_encode($output);exit;
}
?>
