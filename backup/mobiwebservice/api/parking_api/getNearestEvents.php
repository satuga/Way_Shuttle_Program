<?php
error_reporting(0);
header('Content-Type: application/json');
include ('config.php');
include ('function.php');

//----- Required parameters which are required to create the query for searching ------//
$lat = $_REQUEST['lat'];
$long = $_REQUEST['long'];
$airport = $_REQUEST['airport'];
$city = $_REQUEST['city'];

$min = $_REQUEST['min'];
$max = $_REQUEST['max'];

$pricing = $_REQUEST['pricing'];
$dt2 = date('Y-m-d H:i:s');
$event = 1;
$checkinDate = $_REQUEST['checkinDate'];
$checkoutDate = $_REQUEST['checkoutDate'];
$checkinTime = $_REQUEST['checkinTime'];
$checkoutTime = $_REQUEST['checkoutTime'];


//----- check if the main parameters lat , long are present or not ------//
if($lat == '' || $long == '')
{
	$content = array("status" => "0","response" => ERROR, "message" => PARAMETER_MSG);
    echo json_encode($content);
    exit;
}
else
{
	$query_ext = '';
	$join_ext = '';
	// ----- check if the airport is selected -------//
	if($airport !='' && $airport == 1)
	{
		$query_ext .= ' AND Park_AirportVenue=1';
	}
	// ------ check if the city is selected --------//
	if($city !='' && $city == 1)
	{
		$query_ext .= ' AND Park_AirportVenue=0';
	}

	// ------ check if the range is selected or not ----//
	if($min != '' && $max !='')
	{
		$query_ext.= " AND (tbl_parking.Average_Price >= '".$min."' AND tbl_parking.Average_Price<='".$max."')";
	}

	//----- checking the pricing for the parkings -------//
	if($pricing !='')
	{
		$query_ext.= " AND P_Pricingtype IN (".$pricing.")";
	}

	if($checkinDate !='')
	{
		if($checkinTime != ""){
			$checkinDate = $checkinDate." ".$checkinTime;
		}
		if($checkoutTime != ""){
			$checkoutDate = $checkoutDate." ".$checkoutTime;
		}
		if($checkoutDate ==''){
			$checkoutDate = $checkinDate;
		}
		$query_ext = " AND tbl_parkinglocations.Park_ID IN (SELECT Park_ID FROM `tbl_parkingavailability` WHERE (P_fromDate Between '".$checkinDate."' and '".$checkoutDate."' or PA_toDate Between '".$checkinDate."' and '".$checkoutDate."') and `PA_No_Spaces` > 0)";
		//$query_ext = " AND P_ID IN (SELECT P_ID FROM `tbl_parkingavailability`)";
	}

	if($event =='' && $event != 1)
	{
		// ----------- main query for search according to the latitude and longitude ----//
		$query = "SELECT tbl_parkinglocations.lat AS Lattitude,tbl_parkinglocations.lon AS Longitude,tbl_parking.P_ID,tbl_parking.Average_Price,tbl_parking.P_Pricingtype,tbl_parking.P_Daily_Price_Type,tbl_parkinglocations.Park_City,tbl_parkinglocations.Park_State,tbl_parkinglocations.Park_Zip,3956 * 2 * ASIN(SQRT(POWER(SIN(('".$lat."' - abs(tbl_parkinglocations.lat)) * pi()/180 / 2), 2) + COS('".$lat."' * pi()/180 ) * COS(abs(tbl_parkinglocations.lat) * pi()/180) * POWER(SIN(('".$long."'-tbl_parkinglocations.lon) * pi()/180 / 2), 2) )) as distance,tbl_parkingevents.*  FROM tbl_parking
			INNER JOIN tbl_parkinglocations ON tbl_parkinglocations.Park_ID=tbl_parking.P_Location
			INNER JOIN tbl_registeration ON tbl_registeration.id=tbl_parking.P_UserID
			WHERE tbl_parking.P_Status=1 AND tbl_registeration.parking_control=1 AND Park_Location_Status=1 AND P_Pricingtype<>'event' ".$query_ext." AND (3956 * 2 * ASIN(SQRT(POWER(SIN(('".$lat."' - abs(tbl_parkinglocations.lat)) * pi()/180 / 2), 2) + COS('".$lat."' * pi()/180 ) * COS(abs(tbl_parkinglocations.lat) * pi()/180) * POWER(SIN(('".$long."'-tbl_parkinglocations.lon) * pi()/180 / 2), 2) )))<=500";
	}
	else
	{
		$query = "SELECT tbl_parkinglocations.lat AS Lattitude,tbl_parkinglocations.lon AS Longitude,tbl_parking.P_ID,tbl_parking.Average_Price,tbl_parking.P_Pricingtype,tbl_parking.P_Daily_Price_Type,tbl_parkinglocations.Park_City,tbl_parkinglocations.Park_State,tbl_parkinglocations.Park_Zip,3956 * 2 * ASIN(SQRT(POWER(SIN(('".$lat."' - abs(tbl_parkinglocations.lat)) * pi()/180 / 2), 2) + COS('".$lat."' * pi()/180 ) * COS(abs(tbl_parkinglocations.lat) * pi()/180) * POWER(SIN(('".$long."'-tbl_parkinglocations.lon) * pi()/180 / 2), 2) )) as distance,tbl_parkingevents.*  FROM tbl_parking
			INNER JOIN tbl_parkinglocations ON tbl_parkinglocations.Park_ID=tbl_parking.P_Location
			INNER JOIN tbl_registeration ON tbl_registeration.id=tbl_parking.P_UserID
			RIGHT JOIN tbl_parkingevents ON tbl_parkingevents.PE_ParkID=tbl_parking.P_ID
			WHERE tbl_parking.P_Status=1 AND tbl_registeration.parking_control=1 AND Park_Location_Status=1 AND tbl_parkingevents.PE_End>'".$dt2."' AND P_Pricingtype='event' AND (3956 * 2 * ASIN(SQRT(POWER(SIN(('".$lat."' - abs(tbl_parkinglocations.lat)) * pi()/180 / 2), 2) + COS('".$lat."' * pi()/180 ) * COS(abs(tbl_parkinglocations.lat) * pi()/180) * POWER(SIN(('".$long."'-tbl_parkinglocations.lon) * pi()/180 / 2), 2) )))<=500";
	}

	//echo $query; exit;
	$res = mysql_query($query);
	$count = mysql_num_rows($res);
	if ($count > 0)
    {
		while($info = mysql_fetch_assoc($res))
		{
			$all[] = array_map('utf8_encode', $info);
		}

		/*array_walk_recursive($all, function(&$item, $key) {
			if(is_string($item)) {
				$item = htmlentities($item);
			}
		});*/

		$content = array("status" => 1,"data" => $all);
        echo json_encode($content);
        exit;
	}
	else
	{
		$content = array("status" => 0,"response" => ERROR, "message" => 'No Records Found');
		echo json_encode($content);
		exit;
	}
}
?>
