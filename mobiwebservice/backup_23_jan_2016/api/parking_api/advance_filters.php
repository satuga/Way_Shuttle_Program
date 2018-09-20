<?php
error_reporting(0);
include ('config.php');
include ('function.php');

//----- Required parameters which are required to create the query for searching ------//
$lat = $_REQUEST['lat'];
$long = $_REQUEST['long'];

$pricing = $_REQUEST['pricing'];
$dt2 = date('Y-m-d H:i:s');
 
$checkinDate = $_REQUEST['checkin_date'];
$checkoutDate = $_REQUEST['checkout_date'];

//----- check if the main parameters lat , long are present or not ------//
if($lat == '' || $long == ''  )
{
	$content = array("status" => "0","response" => ERROR, "message" => PARAMETER_MSG);
    echo json_encode($content);
    exit;
}
else
{
	$query_ext = '';
	if($checkinDate !='' && $checkoutDate !='')
	{
		$query_ext = " AND tbl_parking.P_ID IN (SELECT P_ID FROM `tbl_parkingavailability` WHERE $checkinDate > P_fromDate and $checkoutDate < PA_toDate and `PA_No_Spaces` > 0 GROUP BY P_ID)";
		//$query_ext = " AND P_ID IN (SELECT P_ID FROM `tbl_parkingavailability`)";
	}
		//echo $query_ext;exit();
	//echo "SELECT P_ID FROM `tbl_parkingavailability` WHERE '".$checkinDate."' > P_fromDate and '".$checkoutDate."' < PA_toDate and `PA_No_Spaces` > 0 GROUP BY P_ID";exit();
	
		// ----------- main query for search according to the latitude and longitude ----//
		$query = "SELECT tbl_parkinglocations.lat AS Lattitude,tbl_parkinglocations.lon AS Longitude,tbl_parking.P_ID,tbl_parking.Average_Price,tbl_parking.P_Lot_Type,tbl_parking.P_Parkingattributes,tbl_parking.P_Parkingextras,tbl_parking.P_Pricingtype,tbl_parking.P_Daily_Price_Type,tbl_parking.clicks,tbl_parking.views,tbl_parking.Todaysbookings,tbl_parking.average_reviews,tbl_parking.total_reviews,tbl_parking.P_Created,tbl_parking.P_FAmt,tbl_parkinglocations.Park_Name,tbl_parkinglocations.Park_Logo,tbl_parkinglocations.Park_Address ,tbl_parkinglocations.Park_City,tbl_parkinglocations.Park_State,tbl_parkinglocations.Park_Zip,tbl_parkinglocations.P_Airport_Distance,tbl_parkinglocations.Park_Image,tbl_parkinglocations.Airport_Near_Address,tbl_parkinglocations.Airport_Near_Address_ID,tbl_parkinglocations.Park_Typeoflocation,tbl_parkinglocations.Park_AirportVenue,tbl_parkinglocations.P_Parkingattributes,tbl_parkinglocations.P_Shuttleother,tbl_parkinglocations.P_Shuttledesc,tbl_registeration.firstname,tbl_registeration.display_name,3956 * 2 * ASIN(SQRT(POWER(SIN(('".$lat."' - abs(tbl_parkinglocations.lat)) * pi()/180 / 2), 2) + COS('".$lat."' * pi()/180 ) * COS(abs(tbl_parkinglocations.lat) * pi()/180) * POWER(SIN(('".$long."'-tbl_parkinglocations.lon) * pi()/180 / 2), 2) )) as distance,1 AS Main,tbl_parkinglocations.P_Parkingextras AS P_LocationParkingextras,tbl_parkinglocations.P_Parkingattributes AS P_LocationParkingattributes,tbl_parking.min_reservation  FROM tbl_parking 
			INNER JOIN tbl_parkinglocations ON tbl_parkinglocations.Park_ID=tbl_parking.P_Location
			INNER JOIN tbl_registeration ON tbl_registeration.id=tbl_parking.P_UserID
			WHERE tbl_parking.P_Status=1 AND tbl_registeration.parking_control=1 AND Park_Location_Status=1 AND P_Pricingtype<>'event' '".$query_ext."' AND (3956 * 2 * ASIN(SQRT(POWER(SIN(('".$lat."' - abs(tbl_parkinglocations.lat)) * pi()/180 / 2), 2) + COS('".$lat."' * pi()/180 ) * COS(abs(tbl_parkinglocations.lat) * pi()/180) * POWER(SIN(('".$long."'-tbl_parkinglocations.lon) * pi()/180 / 2), 2) )))<=500";
		//echo $query;exit();
		/*$query = "SELECT tbl_parkinglocations.lat AS Lattitude,tbl_parkinglocations.lon AS Longitude,tbl_parking.P_ID,tbl_parking.Average_Price,tbl_parking.P_Lot_Type,tbl_parking.P_Parkingattributes,tbl_parking.P_Parkingextras,tbl_parking.P_Pricingtype,tbl_parking.P_Daily_Price_Type,tbl_parking.clicks,tbl_parking.views,tbl_parking.Todaysbookings,tbl_parking.average_reviews,tbl_parking.total_reviews,tbl_parking.P_Created,tbl_parking.P_FAmt,tbl_parkinglocations.Park_Name,tbl_parkinglocations.Park_Logo,tbl_parkinglocations.Park_Address ,tbl_parkinglocations.Park_City,tbl_parkinglocations.Park_State,tbl_parkinglocations.Park_Zip,tbl_parkinglocations.P_Airport_Distance,tbl_parkinglocations.Park_Image,tbl_parkinglocations.Airport_Near_Address,tbl_parkinglocations.Airport_Near_Address_ID,tbl_parkinglocations.Park_Typeoflocation,tbl_parkinglocations.Park_AirportVenue,tbl_parkinglocations.P_Parkingattributes,tbl_parkinglocations.P_Shuttleother,tbl_parkinglocations.P_Shuttledesc,tbl_registeration.firstname,tbl_registeration.display_name,3956 * 2 * ASIN(SQRT(POWER(SIN(('".$lat."' - abs(tbl_parkinglocations.lat)) * pi()/180 / 2), 2) + COS('".$lat."' * pi()/180 ) * COS(abs(tbl_parkinglocations.lat) * pi()/180) * POWER(SIN(('".$long."'-tbl_parkinglocations.lon) * pi()/180 / 2), 2) )) as distance,1 AS Main,tbl_parkinglocations.P_Parkingextras AS P_LocationParkingextras,tbl_parkinglocations.P_Parkingattributes AS P_LocationParkingattributes,tbl_parking.min_reservation  FROM tbl_parking 
			INNER JOIN tbl_parkinglocations ON tbl_parkinglocations.Park_ID=tbl_parking.P_Location
			INNER JOIN tbl_registeration ON tbl_registeration.id=tbl_parking.P_UserID
			WHERE tbl_parking.P_Status=1 AND tbl_registeration.parking_control=1 AND Park_Location_Status=1 AND P_Pricingtype<>'event' AND (3956 * 2 * ASIN(SQRT(POWER(SIN(('".$lat."' - abs(tbl_parkinglocations.lat)) * pi()/180 / 2), 2) + COS('".$lat."' * pi()/180 ) * COS(abs(tbl_parkinglocations.lat) * pi()/180) * POWER(SIN(('".$long."'-tbl_parkinglocations.lon) * pi()/180 / 2), 2) )))<=1000";
		*/	
		if(!$query){
			echo mysql_error();
			exit();
		}
	
	
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