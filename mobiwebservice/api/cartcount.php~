<?php
header('Content-Type: application/json');
error_reporting(1);
include ('config.php');
$data = json_decode(file_get_contents('php://input'), TRUE);

$user_id= $data['data']['user_id'];
$user_id =  ctype_digit($user_id)? $user_id : '';
$device_id = $data['data']['device_id'];

if($user_id != '' || $device_id != '')
{
	if($user_id !="")
		$sql="select count(*) as total_cart from tbl_cart where Cart_Type in('Dine','Parking') and  Cart_UserID='".($user_id)."'";
     else
		$sql="select count(*) as total_cart from tbl_cart where Cart_Type in('Dine','Parking') and  Sess_ID='".($device_id)."'";
    $query_count=mysqli_query($con,$sql);
	if (mysqli_num_rows($query_count))
	{
		$counts= mysqli_fetch_array($query_count);
        $total_cart=$counts['total_cart'];
        if($total_cart>0 )
		{
			$output = array("status" => 1, 'total_cart'=>$total_cart);
			echo json_encode($output);
			exit;
		}
		else
		{
			$output = array("status" => 0, "message"=> "Your Cart is empty");
			echo json_encode($output);
			exit;
		}
	}
	else
	{
		$output = array("status" => 0, "message"=> "Your Cart is empty");
		echo json_encode($output);
		exit;
	}
}
else
{
    $output=array("status"=>"0","response"=>"error","message"=>"Parameter Missing");
    echo json_encode($output);exit;
}

	?>
