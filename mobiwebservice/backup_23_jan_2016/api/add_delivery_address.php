<?php
include('config.php');
include('function.php');

$data = json_decode(file_get_contents('php://input'), TRUE);

$user_id = $data['data']['user_id'];
$name = $data['data']['user_name'];
$address = $data['data']['address'];
$building = $data['data']['building'];
$state = $data['data']['state'];
$city = $data['data']['city'];
$zipcode = $data['data']['zipcode'];
$phone = $data['data']['phone'];
$add_id = $data['data']['address_id'];
$type = $data['data']['type'];

$lat = $data['data']['lat'];
$long = $data['data']['long'];
$default = '0';

/*if($default == '1')
{
	$sql="UPDATE tbl_deliveryaddress SET DE_default = '0' WHERE DE_UserID=".$user_id;
	mysql_query($sql);
}*/

if($user_id != '')
{
	if($type == 'add')
	{	
		$sql="INSERT INTO tbl_deliveryaddress(DE_UserID,DE_Name,DE_Address,BLDG_No,DE_State,DE_City,DE_Zipcode,DE_Phone,DE_lat,DE_long,DE_default,DE_Created) VALUES('".$user_id."','".$name."','".$address."','".$building."','".$state."','".$city."','".$zipcode."','".$phone."','".$lat."','".$long."','".$default."',Now())";
		
		$exeCC=mysql_query($sql);
		$last_id=mysql_insert_id();
		if($last_id != '')
		{
			$output=array("status"=>"1","data"=>"Address Added Successfully","new_id"=>$last_id);
			echo json_encode($output);
			exit;
		}
		else
		{
			$output=array("status"=>"0","data"=>"Address Not Added");
			echo json_encode($output);exit;
		}
	}
	else
	{
		$sql="UPDATE tbl_deliveryaddress SET DE_Name='".$name."',DE_Address='".$address."',BLDG_No='".mysql_real_escape_string($building)."',DE_State='".mysql_real_escape_string($state)."',DE_City='".mysql_real_escape_string($city)."',DE_Zipcode='".mysql_real_escape_string($zipcode)."',DE_Phone='".mysql_real_escape_string($phone)."',DE_lat = '".$lat."',DE_long = '".$long."',DE_default = '".$default."' WHERE DE_ID=".$add_id;
		mysql_query($sql);
		$output=array("status"=>"1","data"=>"Address Updated Successfully");
		echo json_encode($output);exit;
		
	}
}
else 
{
	$output=array("status"=>"0","data"=>"Please enter correct Data");
	echo json_encode($output);exit;
}
?>
