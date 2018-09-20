<?php
include ('config.php');
include ('function.php');
header('Content-Type: application/json');

$user_id = $_REQUEST['id'];
$email = $_REQUEST['email'];
$about = $_REQUEST['about'];
$school = $_REQUEST['school'];
$gender = $_REQUEST['gender'];
$birthday = $_REQUEST['birthday'];
if(isset($_REQUEST['phone'])){
  $phone = $_REQUEST['phone'];
} else {
  $phone = "";
}
$city = $_REQUEST['city'];
$street = $_REQUEST['street'];
$state = $_REQUEST['state'];
$push = $_REQUEST['push_notification'];


$name = $_REQUEST['name'];
$building = $_REQUEST['building'];
$zipcode = $_REQUEST['zipcode'];

$lat = $_REQUEST['lat'];
$long = $_REQUEST['long'];
$default = '1';

if ($user_id == '' && $email == '')
{
    $content = array("status" => '0',"response" => ERROR, "message" => PARAMETER_MSG);
    echo json_encode($content);
    exit;
}
else
{
	$query = "update tbl_registeration set MessageBody = '".$about."',email_add = '".$email."',gender = '".$gender."',birthday = '".$birthday."',city = '".$city."',BLDG_No = '".$building."',state = '".$state."',street='".$street."',mobile_phone = '".$phone."',zipcode='".$zipcode."',school = '".$school."',push_notification = '".$push."' where id='".$user_id."'";
    $res = mysqli_query($con,$query);
	$count = mysqli_affected_rows();

    if ($count > 0)
	{
		if($zipcode != '' && $name != '' && $building != '')
		{
			$chk_delviery = "select * from tbl_deliveryaddress where DE_UserID = '".$user_id."'";
			$chk_query = mysqli_query($con,$chk_delviery);
			$chk_count = mysqli_num_rows($chk_query);
			if($chk_count > 0)
			{

			}
			else
			{
				$sql="INSERT INTO tbl_deliveryaddress(DE_UserID,DE_Name,DE_Address,BLDG_No,DE_State,DE_City,DE_Zipcode,DE_Phone,DE_lat,DE_long,DE_default,DE_Created) VALUES('".$user_id."','".$name."','".$street."','".$building."','".$state."','".$city."','".$zipcode."','".$phone."','".$lat."','".$long."','".$default."',Now())";
    			$res = mysqli_query($con,$sql);
			}
		}

		/*--------------- Getting the Default address of the user if there are any address ---------------*/
		$sql_add="select * from tbl_deliveryaddress where DE_UserID='".$user_id."' AND DE_default = '1'";
	    $exe_add = mysqli_query($con,$sql_add);
	    $num_add = mysqli_num_rows($exe_add);
        if($num_add > 0)
        {
        	while($add1 = mysqli_fetch_assoc($exe_add))
        	{
        		$address[] = $add1;
        	}
        	$address_all = array("DE_ID"=>$address[0]['DE_ID'],"DE_Name" => $address[0]['DE_Name'],"DE_Address"=>$address[0]['DE_Address'],"BLDG_No"=>$address[0]['BLDG_No'],"DE_State"=>$address[0]['DE_State'],"DE_City"=>$address[0]['DE_City'],"DE_Zipcode"=>$address[0]['DE_Zipcode'],"DE_Phone"=>$address[0]['DE_Phone'],"DE_lat"=>$address[0]['DE_lat'],"DE_long"=>$address[0]['DE_long'],"DE_default" =>$address[0]['DE_default']);
        }
	    else
	    {
	    	$address_all = "";
	    }
		/*------------------ End of the Delivery Address ----------------*/

		$content = array("status" => '1',"message" => "Profile Updated Successfully","default_address"=>$address_all);
        echo json_encode($content);
        exit;
    }
    else
    {
        $content = array("status" => '0',"message" => "Update Failed");
        echo json_encode($content);
        exit;
    }
}

?>
