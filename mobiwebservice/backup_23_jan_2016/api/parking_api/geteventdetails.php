<?php
include ('config.php');
include ('function.php');

$data = json_decode(file_get_contents('php://input'), TRUE);
$PE_id = $data['data']['PE_id'];

if($PE_id == '')
{
	$content = array("status" => "0","response" => ERROR, "data" => PARAMETER_MSG);
    echo json_encode($content);
    exit;
}
else
{
	$query = "SELECT tbl_parkinglocations.lat AS Lattitude,tbl_parkinglocations.lon AS Longitude,tbl_parking.*,tbl_parkinglocations.*,tbl_parkingevents.*,tbl_registeration.*,3956 * 2 * ASIN(SQRT(POWER(SIN(('".$lat."' - abs(tbl_parkinglocations.lat)) * pi()/180 / 2), 2) + COS('".$lat."' * pi()/180 ) * COS(abs(tbl_parkinglocations.lat) * pi()/180) * POWER(SIN(('".$long."'-tbl_parkinglocations.lon) * pi()/180 / 2), 2) )) as distance,1 AS Main,tbl_parkinglocations.P_Parkingextras AS P_LocationParkingextras,tbl_parkinglocations.P_Parkingattributes AS P_LocationParkingattributes,map_duration  FROM tbl_parking 
			INNER JOIN tbl_parkinglocations ON tbl_parkinglocations.Park_ID=tbl_parking.P_Location
			INNER JOIN tbl_registeration ON tbl_registeration.id=tbl_parking.P_UserID
			RIGHT JOIN tbl_parkingevents ON tbl_parkingevents.PE_ParkID=tbl_parking.P_ID
			WHERE tbl_parking.P_Status=1 AND tbl_registeration.parking_control=1 AND Park_Location_Status=1 AND tbl_parkingevents.PE_ID = '".$PE_id."' AND P_Pricingtype='event'";
    $res = mysql_query($query);
	$count = mysql_num_rows($res);
	if ($count > 0)
    {
		while($info = mysql_fetch_assoc($res))
		{
			$all[] = $info;
		}
		
		
        array_walk_recursive($all, function(&$item, $key) {
			if(is_string($item)) {
				$item = htmlentities($item);
			}
		});
		
		$content = array("status" => "1","response" => SUCCESS, "data" => $all);
		echo json_encode($content);
        exit;
	}
	else
	{
		$content = array("status" => "0","response" => ERROR, "data" => 'No Records Found');
		echo json_encode($content);
		exit;
	}
}
?>