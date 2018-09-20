<?php
header('Content-Type: application/json');
include('config.php');
include('function.php');
$EncryptKey = ENCRYPTKEY;

$data = json_decode(file_get_contents('php://input'), TRUE);
$user_id = $data['data']['user_id'];
$card_type = $data['data']['card_type'];
$card_number = $data['data']['card_number'];
$card_name = $data['data']['card_name'];
$exp_month = $data['data']['exp_month'];
$exp_year = $data['data']['exp_year'];

$street = $data['data']['street'];
$city = $data['data']['city'];
$state = $data['data']['state'];
$zip = $data['data']['zip'];

$type = $data['data']['type'];
$card_id = $data['data']['card_id'];

if($type == 'add')
{
	$check_card = mysqli_query($con,"select Card_ID from tbl_creditcarddetails where Card_Number = '".$card_number."' and Card_User_ID = '".$user_id."'");
	$count = mysqli_num_rows($check_card);
	if($count > 0)
	{
		$output=array("status"=>"0","data"=>"Card Already Exist");
		echo json_encode($output);exit;
	}
	else
	{
		/*$sql="INSERT INTO tbl_creditcarddetails(Card_User_ID,Card_Type,Card_Number,Card_Exp_Month,Card_Exp_Year,Card_FirstName,Card_Street,Card_City,Card_State,Card_Zip,Card_Created)VALUES(".$user_id.",'".$card_type."',AES_ENCRYPT('".mysqli_real_escape_string($con,$card_number)."','".$EncryptKey."'),'".mysqli_real_escape_string($con,$exp_month)."','".mysqli_real_escape_string($con,$exp_year)."','".mysqli_real_escape_string($con,$card_name)."','".mysqli_real_escape_string($con,$street)."','".mysqli_real_escape_string($con,$city)."','".mysqli_real_escape_string($con,$state)."','".mysqli_real_escape_string($con,$zip)."',Now())";*/
		$sql="INSERT INTO tbl_creditcarddetails(Card_User_ID,Card_Type,Card_Number,Card_Exp_Month,Card_Exp_Year,Card_FirstName,Card_Street,Card_City,Card_State,Card_Zip,Card_Created)VALUES(".$user_id.",'".$card_type."','".$card_number."','".mysqli_real_escape_string($con,$exp_month)."','".mysqli_real_escape_string($con,$exp_year)."','".mysqli_real_escape_string($con,$card_name)."','".mysqli_real_escape_string($con,$street)."','".mysqli_real_escape_string($con,$city)."','".mysqli_real_escape_string($con,$state)."','".mysqli_real_escape_string($con,$zip)."',Now())";
		$exeCC=mysqli_query($con,$sql);
		$Card_ID=mysqli_insert_id($con);
		if($Card_ID != '')
		{
			$output=array("status"=>"1","data"=>"Card Added Successfully","card_id"=>$Card_ID);
			echo json_encode($output);exit;
		}
		else
		{
			$output=array("status"=>"0","data"=>"Card Not Added");
			echo json_encode($output);exit;
		}
	}
}
else
{
	if($card_id == '')
	{
		$output=array("status"=>"0","data"=>"Please enter card id");
		echo json_encode($output);exit;
	}
	else
	{
		$sql="UPDATE tbl_creditcarddetails SET Card_Type='".mysqli_real_escape_string($con,$card_type)."',Card_Number='".mysqli_real_escape_string($con,$card_number)."',Card_Exp_Month='".mysqli_real_escape_string($con,$exp_month)."',Card_Exp_Year='".mysqli_real_escape_string($con,$exp_year)."',Card_FirstName='".mysqli_real_escape_string($con,$card_name)."',Card_Street='".mysqli_real_escape_string($con,$street)."',Card_State='".mysqli_real_escape_string($con,$state)."',Card_City='".mysqli_real_escape_string($con,$city)."',Card_Zip='".mysqli_real_escape_string($con,$zip)."' WHERE Card_ID=".$card_id;
		$exeCC=mysqli_query($con,$sql);
		$output=array("status"=>"1","data"=>"Card Updated Successfully");
		echo json_encode($output);exit;
	}
}
?>
