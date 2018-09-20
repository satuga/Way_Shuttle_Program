<?php
include ('config.php');
include ('function.php');

$data = json_decode(file_get_contents('php://input'), TRUE);
$list_id = $data['data']['listid'];
$lat = $data['data']['lat'];
$long = $data['data']['long'];

if($list_id == '')
{
	$content = array("status" => "0","response" => ERROR, "data" => PARAMETER_MSG);
    echo json_encode($content);
    exit;
}
else
{
	$query = "SELECT tbl_parkinglocations.lat AS Lattitude,tbl_parkinglocations.lon AS Longitude,tbl_parking.P_ID,tbl_parking.Average_Price,tbl_parking.P_Lot_Type,tbl_parking.P_Parkingattributes,tbl_parking.P_Parkingextras,tbl_parking.P_Pricingtype,tbl_parking.P_Daily_Price_Type,tbl_parking.clicks,tbl_parking.views,tbl_parking.Todaysbookings,tbl_parking.average_reviews,tbl_parking.total_reviews,tbl_parking.P_Created,tbl_parking.P_FAmt,tbl_parkinglocations.Park_Name,tbl_parkinglocations.Park_Logo,tbl_parkinglocations.Park_Address,tbl_parkinglocations.Park_Howtofind,tbl_parkinglocations.Park_Locdesc,tbl_parkinglocations.Park_SpecialInstructions ,tbl_parkinglocations.Park_City,tbl_parkinglocations.Park_State,tbl_parkinglocations.Park_Zip,tbl_parkinglocations.P_Airport_Distance,tbl_parkinglocations.Park_Image1,tbl_parkinglocations.Park_Image2,tbl_parkinglocations.Park_Image3,tbl_parkinglocations.Park_Image4,tbl_parkinglocations.Park_Image5,tbl_parkinglocations.Airport_Near_Address,tbl_parkinglocations.Airport_Near_Address_ID,tbl_parkinglocations.Park_Typeoflocation,tbl_parkinglocations.Park_AirportVenue,tbl_parkinglocations.P_Parkingattributes,tbl_parkinglocations.P_Shuttleother,tbl_parkinglocations.P_Shuttledesc,tbl_registeration.firstname,tbl_registeration.display_name,3956 * 2 * ASIN(SQRT(POWER(SIN(('".$lat."' - abs(tbl_parkinglocations.lat)) * pi()/180 / 2), 2) + COS('".$lat."' * pi()/180 ) * COS(abs(tbl_parkinglocations.lat) * pi()/180) * POWER(SIN(('".$long."'-tbl_parkinglocations.lon) * pi()/180 / 2), 2) )) as distance,1 AS Main,tbl_parkinglocations.P_Parkingextras AS P_LocationParkingextras,tbl_parkinglocations.P_Parkingattributes AS P_LocationParkingattributes,tbl_parking.min_reservation,tbl_parking.smart_lot  FROM tbl_parking
		INNER JOIN tbl_parkinglocations ON tbl_parkinglocations.Park_ID=tbl_parking.P_Location
		INNER JOIN tbl_registeration ON tbl_registeration.id=tbl_parking.P_UserID
		WHERE tbl_parking.P_Status=1 AND tbl_parking.Deleted=0 AND tbl_parking.P_ID='".$list_id."'";
  $res = mysql_query($query);
	$count = mysql_num_rows($res);
	if ($count > 0)
    {
		while($info = mysql_fetch_assoc($res))
		{
			$all[] =  array_map('utf8_encode', $info);
		}

    array_walk_recursive($all, function(&$item, $key) {
			if(is_string($item)) {
				$item = @strip_tags($item);
			}
		});


		$lat = $all[0]['Lattitude'];
		$long = $all[0]['Longitude'];

		$similar = "SELECT tbl_parkinglocations.lat AS Lattitude,tbl_parkinglocations.lon AS Longitude,tbl_parking.P_ID,tbl_parking.Average_Price,tbl_parking.P_Lot_Type,tbl_parking.P_Parkingattributes,tbl_parking.P_Parkingextras,tbl_parking.P_Pricingtype,tbl_parking.P_Daily_Price_Type,tbl_parking.clicks,tbl_parking.views,tbl_parking.Todaysbookings,tbl_parking.average_reviews,tbl_parking.total_reviews,tbl_parking.P_Created,tbl_parking.P_FAmt,tbl_parkinglocations.Park_Name,tbl_parkinglocations.Park_Logo,tbl_parkinglocations.Park_Address,tbl_parkinglocations.Park_Howtofind,tbl_parkinglocations.Park_Locdesc,tbl_parkinglocations.Park_SpecialInstructions ,tbl_parkinglocations.Park_City,tbl_parkinglocations.Park_State,tbl_parkinglocations.Park_Zip,tbl_parkinglocations.P_Airport_Distance,tbl_parkinglocations.Park_Image1,tbl_parkinglocations.Airport_Near_Address,tbl_parkinglocations.Airport_Near_Address_ID,tbl_parkinglocations.Park_Typeoflocation,tbl_parkinglocations.Park_AirportVenue,tbl_parkinglocations.P_Parkingattributes,tbl_parkinglocations.P_Shuttleother,tbl_parkinglocations.P_Shuttledesc,tbl_registeration.firstname,tbl_registeration.display_name,3956 * 2 * ASIN(SQRT(POWER(SIN(('".$lat."' - abs(tbl_parkinglocations.lat)) * pi()/180 / 2), 2) + COS('".$lat."' * pi()/180 ) * COS(abs(tbl_parkinglocations.lat) * pi()/180) * POWER(SIN(('".$long."'-tbl_parkinglocations.lon) * pi()/180 / 2), 2) )) as distance,1 AS Main,tbl_parkinglocations.P_Parkingextras AS P_LocationParkingextras,tbl_parkinglocations.P_Parkingattributes AS P_LocationParkingattributes,tbl_parking.min_reservation,tbl_parking.smart_lot  FROM tbl_parking
			INNER JOIN tbl_parkinglocations ON tbl_parkinglocations.Park_ID=tbl_parking.P_Location
			INNER JOIN tbl_registeration ON tbl_registeration.id=tbl_parking.P_UserID
			WHERE tbl_parking.P_Status=1 AND tbl_registeration.parking_control=1 AND Park_Location_Status=1 AND P_Pricingtype<>'event' '".$query_ext."' AND (3956 * 2 * ASIN(SQRT(POWER(SIN(('".$lat."' - abs(tbl_parkinglocations.lat)) * pi()/180 / 2), 2) + COS('".$lat."' * pi()/180 ) * COS(abs(tbl_parkinglocations.lat) * pi()/180) * POWER(SIN(('".$long."'-tbl_parkinglocations.lon) * pi()/180 / 2), 2) )))<=500 and tbl_parking.P_ID !='".$list_id."' limit 0,3";
		$res1 = mysql_query($similar);
		$count1 = mysql_num_rows($res1);
		if ($count1 > 0)
		{
			while($info1 = mysql_fetch_assoc($res1))
			{
				$all1[] = array_map('utf8_encode', $info1);
			}


			array_walk_recursive($all1, function(&$item, $key) {
				if(is_string($item)) {
					$item = @strip_tags($item);
				}
			});
			$all[0]['similar_listing'] = $all1;
		}
		else
		{
			$all[0]['similar_listing'] = '';
		}


		$query2 = "select reg.firstname,reg.lastname,reg.logo,rev.* from tbl_reviews as rev left join tbl_registeration as reg on rev.user_id = reg.id where rev.list_id ='".$list_id."' ORDER BY rev.Review_ID DESC limit 0,1";
		$res2 = mysql_query($query2);
		$count2 = mysql_num_rows($res2);
		if ($count2 > 0)
		{
			while($info2 = mysql_fetch_assoc($res2))
			{
				$all2[] = $info2;
			}
			$all[0]['reviews'] = $all2;
		}
		else
		{
			$all[0]['reviews'] = '';
		}



		$all[0]['encrypt_password'] = '';

		// for($i = 0; $i < count($all); $i++){
		// 	$response[$i]['P_ID'] = $all[$i]['P_ID'];
		// 	$response[$i]['P_Lot_Type'] = $all[$i]['P_Lot_Type'];
		// 	$response[$i]['P_Parkingattributes'] = $all[$i]['P_Parkingattributes'];
		// 	$response[$i]['P_Parkingextras'] = $all[$i]['P_Parkingextras'];
		// 	$response[$i]['P_Instructions'] = $all[$i]['P_Instructions'];
		// 	$response[$i]['Average_Price'] = $all[$i]['Average_Price'];
		// 	$response[$i]['Park_ID'] = $all[$i]['Park_ID'];
		// 	$response[$i]['Park_UserID'] = $all[$i]['Park_UserID'];
		// 	$response[$i]['Park_Name'] = $all[$i]['Park_Name'];
		// 	$response[$i]['Park_Logo'] = $all[$i]['Park_Logo'];
		// 	$response[$i]['Park_Address'] = $all[$i]['Park_Address'];
		// 	$response[$i]['Park_City'] = $all[$i]['Park_City'];
		// 	$response[$i]['Park_State'] = $all[$i]['Park_State'];
		// 	$response[$i]['Park_Zip'] = $all[$i]['Park_Zip'];
		// 	$response[$i]['Lattitude'] = $all[$i]['lat'];
		// 	$response[$i]['Longitude'] = $all[$i]['lon'];
		// 	$response[$i]['Park_Howtofind'] = $all[$i]['Park_Howtofind'];
		// 	$response[$i]['Park_Locdesc'] = $all[$i]['Park_Locdesc'];
		// 	$response[$i]['Park_SpecialInstructions'] = $all[$i]['Park_SpecialInstructions'];
		// 	$response[$i]['P_Shuttledesc'] = $all[$i]['P_Shuttledesc'];
		// 	$response[$i]['Park_Image1'] = $all[$i]['Park_Image1'];
		// 	$response[$i]['Park_Image2'] = $all[$i]['Park_Image2'];
		// 	$response[$i]['Park_Image3'] = $all[$i]['Park_Image3'];
		// 	$response[$i]['Park_Image4'] = $all[$i]['Park_Image4'];
		// 	$response[$i]['Park_Image5'] = $all[$i]['Park_Image5'];
		// 	$response[$i]['similar_listing'] = $all[$i]['similar_listing'];
		// 	$response[$i]['reviews'] = $all[$i]['reviews'];
		// 	$response[$i]['average_reviews'] = $all[$i]['average_reviews'];
		// 	$response[$i]['total_reviews'] = $all[$i]['total_reviews'];
		// 	$response[$i]['Park_Image5'] = $all[$i]['Park_Image5'];
		// }

		function remove_tags($str)
		{
			//$encoded_string = htmlentities($str);
			//$decoded_string = html_entity_decode($encoded_string);
		    return @strip_tags($str);
		}


		$new_all_data = array_map("remove_tags", $all);

		$content = array("status" => "1", "data" => $new_all_data);

		$content = array("status" => "1", "data" => $all);
		echo $json = json_encode($content);
        exit;
	}
	else
	{
		$content = array("response" => ERROR, "message" => 'No Records Found');
		echo json_encode($content);
		exit;
	}
}
?>
