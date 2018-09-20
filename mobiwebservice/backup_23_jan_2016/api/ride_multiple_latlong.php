<?php 
include ('config.php');
include ('function.php');

$data = json_decode(file_get_contents('php://input'), TRUE);
$driver_id = $data['data']['driver_id'];
$ids = $data['data']['order_id'];
$count  = count($data['data']['latlong']);
if($driver_id != '')
{
	$ord = explode(",",$ids);
	if($count > 0)
	{
		for($i=0;$i<sizeof($ord);$i++)
		{
			for($y=0;$y<$count;$y++)
			{
				$latitude = $data['data']['latlong'][$y]['driver_lat'];
				$longitude = $data['data']['latlong'][$y]['driver_long'];
				
				$order_id = $ord[$i];
				$Query1="INSERT INTO driver_ride_path(driver_id,order_id,driver_lat,driver_long) VALUES ('".$driver_id."','".$order_id."',".$latitude.",".$longitude.")";
				mysql_query($Query1);
			}
		}
		$output=array("status"=>"1","data"=>"latitude longitude added");
		echo json_encode($output);exit;
	}
	else 
	{
		$output=array("status"=>"0","data"=>"No Lat Long found");
		echo json_encode($output);exit;
	}
}
else 
{
	$output=array("status"=>"0","data"=>"No Records found");
	echo json_encode($output);exit;
}

?>