<?php
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
	$check_card = mysql_query("select Card_ID from tbl_creditcarddetails where Card_Number = '".$card_number."' and Card_User_ID = '".$user_id."'");
	$count = mysql_num_rows($check_card);
	if($count > 0)
	{
		$output=array("status"=>"0","data"=>"Card Already Exist");
		echo json_encode($output);exit;
	}
	else
	{
		/*$sql="INSERT INTO tbl_creditcarddetails(Card_User_ID,Card_Type,Card_Number,Card_Exp_Month,Card_Exp_Year,Card_FirstName,Card_Street,Card_City,Card_State,Card_Zip,Card_Created)VALUES(".$user_id.",'".$card_type."',AES_ENCRYPT('".mysql_real_escape_string($card_number)."','".$EncryptKey."'),'".mysql_real_escape_string($exp_month)."','".mysql_real_escape_string($exp_year)."','".mysql_real_escape_string($card_name)."','".mysql_real_escape_string($street)."','".mysql_real_escape_string($city)."','".mysql_real_escape_string($state)."','".mysql_real_escape_string($zip)."',Now())";*/
		$sql="INSERT INTO tbl_creditcarddetails(Card_User_ID,Card_Type,Card_Number,Card_Exp_Month,Card_Exp_Year,Card_FirstName,Card_Street,Card_City,Card_State,Card_Zip,Card_Created)VALUES(".$user_id.",'".$card_type."','".$card_number."','".mysql_real_escape_string($exp_month)."','".mysql_real_escape_string($exp_year)."','".mysql_real_escape_string($card_name)."','".mysql_real_escape_string($street)."','".mysql_real_escape_string($city)."','".mysql_real_escape_string($state)."','".mysql_real_escape_string($zip)."',Now())";
		$exeCC=mysql_query($sql);
		$Card_ID=mysql_insert_id();
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
		$sql="UPDATE tbl_creditcarddetails SET Card_Type='".mysql_real_escape_string($card_type)."',Card_Number='".mysql_real_escape_string($card_number)."',Card_Exp_Month='".mysql_real_escape_string($exp_month)."',Card_Exp_Year='".mysql_real_escape_string($exp_year)."',Card_FirstName='".mysql_real_escape_string($card_name)."',Card_Street='".mysql_real_escape_string($street)."',Card_State='".mysql_real_escape_string($state)."',Card_City='".mysql_real_escape_string($city)."',Card_Zip='".mysql_real_escape_string($zip)."' WHERE Card_ID=".$card_id;
		$exeCC=mysql_query($sql);
		$output=array("status"=>"1","data"=>"Card Updated Successfully");
		echo json_encode($output);exit;
	}
}
?>
