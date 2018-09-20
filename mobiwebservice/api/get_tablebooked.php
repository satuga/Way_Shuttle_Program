<?php
include('config.php');
include('function.php');

$data = json_decode(file_get_contents('php://input'), TRUE);
$id = sanitize($data['data']['user_id']);

$query = "select m.merchantName,m.contactAddress,m.postalCode,m.telephone,m.email as merchantemail,m.geoLat,m.geoLong,b.* from tbl_tablebooking_bookings as b left join merchant as m ON m.id = b.Book_Restaurant where b.Book_UserID = '".$id."' order by b.Book_ID desc";
$res = mysqli_query($con,$query);
$count = mysqli_num_rows($res);

	if($count>0)
	{
		while($allinfo = mysqli_fetch_assoc($res))
		{
			$info[] = $allinfo;
		}
		$output=array("status"=>"1","data"=>$info);
		echo json_encode($output);exit;
	}
    else
	{
		$output=array("status"=>"0","data"=>"No Records found");
		echo json_encode($output);exit;
    }
	
	
?>
