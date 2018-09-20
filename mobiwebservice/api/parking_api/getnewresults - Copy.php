<?php
error_reporting(0);
header('Content-Type: application/json');
include ('../config.php');
include ('../function.php');
/*
  * Developer Name: Hitesh Tank
  * Date : 18-March-2016

  */
//----- Required parameters which are required to create the query for searching ------//
$lat = $_REQUEST['lat'];
$long = $_REQUEST['long'];
$user_id = $_REQUEST['user_id'];
$airport = $_REQUEST['airport'];
$city = $_REQUEST['city'];

$min = $_REQUEST['min'];
$max = $_REQUEST['max'];

$pricing = $_REQUEST['pricing'];
$dt2 = date('Y-m-d H:i:s');
$event = $_REQUEST['event'];
$checkinDate = $_REQUEST['checkinDate'];
$checkoutDate = $_REQUEST['checkoutDate'];
$checkinTime = $_REQUEST['checkinTime'];
$checkoutTime = $_REQUEST['checkoutTime'];


/* Added by Hitesh Tank on 4-Feb-2016 */
$limits = 50;
$distance=30;

if(isset($_REQUEST['distance']) && !empty($_REQUEST['distance'])){
    $distance=$_REQUEST['distance'];
}
/* Added by Hitesh Tank on 4-Feb-2016 */
// chaded by logictree start


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
        $query_ext.= " AND P_Pricingtype IN ('".$pricing."')";
    }

    if($checkinDate !='')
    {
        /* if($checkinTime != ""){
            $checkinDate = $checkinDate." ".$checkinTime;
        }
        if($checkoutTime != ""){
            $checkoutDate = $checkoutDate." ".$checkoutTime;
        } */
        if($checkoutDate ==''){
            $checkoutDate = $checkinDate;
        }
        $query_ext .= " and tbl_parkinglocations.Park_ID IN (SELECT Park_ID FROM `tbl_parkingweekdayavailability` WHERE (P_fromDate Between '".$checkinDate."' and '".$checkoutDate."') and `PA_No_Spaces` > 0)";
        //$query_ext = " AND P_ID IN (SELECT P_ID FROM `tbl_parkingavailability`)";
    }
    if(($airport !='' && $airport == 1) || ($city !='' && $city == 1))
    {
        // ----------- main query for search according to the latitude and longitude ----//
		//echo "123";
        $query = "SELECT DISTINCTROW tbl_parking.P_MaxAmt,tbl_parkinglocations.lat AS Lattitude,tbl_parkinglocations.lon AS Longitude,tbl_parking.P_Location as Park_ID,tbl_parking.P_ID,tbl_parking.P_UserID,tbl_parking.Average_Price,tbl_parking.P_Lot_Type,tbl_parking.P_Parkingattributes,tbl_parking.P_Parkingextras,tbl_parking.P_Pricingtype,tbl_parking.P_Daily_Price_Type,tbl_parking.clicks,tbl_parking.views,tbl_parking.Todaysbookings,tbl_parking.average_reviews,tbl_parking.total_reviews,tbl_parking.P_Created,tbl_parking.P_FAmt,tbl_parkinglocations.Park_Name,tbl_parkinglocations.Park_Logo,tbl_parkinglocations.Park_Address ,tbl_parkinglocations.Park_City,tbl_parkinglocations.Park_State,tbl_parkinglocations.Park_Zip,tbl_parkinglocations.P_Airport_Distance,tbl_parkinglocations.Park_Image,tbl_parkinglocations.Airport_Near_Address,tbl_parkinglocations.Airport_Near_Address_ID,tbl_parkinglocations.Park_Typeoflocation,tbl_parkinglocations.Park_AirportVenue,tbl_parkinglocations.P_Parkingattributes,tbl_parkinglocations.P_Shuttleother,tbl_parkinglocations.P_Shuttledesc,tbl_registeration.firstname,tbl_registeration.display_name,3956 * 2 * ASIN(SQRT(POWER(SIN(('".$lat."' - abs(tbl_parkinglocations.lat)) * pi()/180 / 2), 2) + COS('".$lat."' * pi()/180 ) * COS(abs(tbl_parkinglocations.lat) * pi()/180) * POWER(SIN(('".$long."'-tbl_parkinglocations.lon) * pi()/180 / 2), 2) )) as distance,1 AS Main,tbl_parkinglocations.P_Parkingextras AS P_LocationParkingextras,tbl_parkinglocations.P_Parkingattributes AS P_LocationParkingattributes,tbl_parking.min_reservation,tbl_parking.smart_lot,IF(tbl_favorite.user_id = '".$user_id."', tbl_favorite.status, 0) as fav_status, tbl_parking.P_GarageID, tbl_parking.P_GateID
        FROM tbl_parking
            INNER JOIN tbl_parkinglocations ON tbl_parkinglocations.Park_ID=tbl_parking.P_Location
            INNER JOIN tbl_registeration ON tbl_registeration.id=tbl_parking.P_UserID
            LEFT JOIN tbl_favorite ON tbl_favorite.Parking_ID=tbl_parking.P_ID
            WHERE tbl_parking.P_Status=1 AND tbl_registeration.parking_control=1 AND Park_Location_Status=1 AND P_Pricingtype<>'event' ".$query_ext." AND (3956 * 2 * ASIN(SQRT(POWER(SIN(('".$lat."' - abs(tbl_parkinglocations.lat)) * pi()/180 / 2), 2) + COS('".$lat."' * pi()/180 ) * COS(abs(tbl_parkinglocations.lat) * pi()/180) * POWER(SIN(('".$long."'-tbl_parkinglocations.lon) * pi()/180 / 2), 2) )))<=$distance group by Park_ID order by distance LIMIT $limits";
    }
    else if($event !='' && $event == 1)
    {

        $query = "SELECT DISTINCTROW tbl_parking.P_MaxAmt,tbl_parkinglocations.lat AS Lattitude,tbl_parkinglocations.lon AS Longitude,tbl_parking.P_Location as Park_ID,tbl_parking.*,tbl_parkinglocations.*,tbl_parkingevents.*,tbl_registeration.*,3956 * 2 * ASIN(SQRT(POWER(SIN(('".$lat."' - abs(tbl_parkinglocations.lat)) * pi()/180 / 2), 2) + COS('".$lat."' * pi()/180 ) * COS(abs(tbl_parkinglocations.lat) * pi()/180) * POWER(SIN(('".$long."'-tbl_parkinglocations.lon) * pi()/180 / 2), 2) )) as distance,1 AS Main,tbl_parkinglocations.P_Parkingextras AS P_LocationParkingextras,tbl_parkinglocations.P_Parkingattributes AS P_LocationParkingattributes,map_duration,tbl_parking.smart_lot,IF(tbl_favorite.user_id = '".$user_id."', tbl_favorite.status, 0) as fav_status, tbl_parking.P_GarageID, tbl_parking.P_GateID FROM tbl_parking
            INNER JOIN tbl_parkinglocations ON tbl_parkinglocations.Park_ID=tbl_parking.P_Location
            INNER JOIN tbl_registeration ON tbl_registeration.id=tbl_parking.P_UserID
            LEFT JOIN tbl_favorite ON tbl_favorite.Parking_ID=tbl_parking.P_ID
            RIGHT JOIN tbl_parkingevents ON tbl_parkingevents.PE_ParkID=tbl_parking.P_ID
            WHERE tbl_parking.P_Status=1 AND tbl_registeration.parking_control=1 AND Park_Location_Status=1 AND tbl_parkingevents.PE_End>'".$dt2."' AND P_Pricingtype='event' ".$query_ext." AND (3956 * 2 * ASIN(SQRT(POWER(SIN(('".$lat."' - abs(tbl_parkinglocations.lat)) * pi()/180 / 2), 2) + COS('".$lat."' * pi()/180 ) * COS(abs(tbl_parkinglocations.lat) * pi()/180) * POWER(SIN(('".$long."'-tbl_parkinglocations.lon) * pi()/180 / 2), 2) )))<=$distance group by tbl_parkinglocations.Park_ID order by distance LIMIT $limits";
    }
    else {

        $query="SELECT DISTINCTROW tbl_parking.P_MaxAmt,tbl_parkinglocations.lat AS Lattitude,tbl_parkinglocations.lon AS Longitude,tbl_parking.P_Location as Park_ID,tbl_parking.P_ID,tbl_parking.P_UserID,tbl_parking.Average_Price,tbl_parking.P_Lot_Type,tbl_parking.P_Parkingattributes,tbl_parking.P_Parkingextras,tbl_parking.P_Pricingtype,tbl_parking.P_Daily_Price_Type,tbl_parking.clicks,tbl_parking.views,tbl_parking.Todaysbookings,tbl_parking.average_reviews,tbl_parking.total_reviews,tbl_parking.P_Created,tbl_parking.P_FAmt,tbl_parkinglocations.Park_Name,tbl_parkinglocations.Park_Logo,tbl_parkinglocations.Park_Address ,tbl_parkinglocations.Park_City,tbl_parkinglocations.Park_State,tbl_parkinglocations.Park_Zip,tbl_parkinglocations.P_Airport_Distance,tbl_parkinglocations.Park_Image,tbl_parkinglocations.Airport_Near_Address,tbl_parkinglocations.Airport_Near_Address_ID,tbl_parkinglocations.Park_Typeoflocation,tbl_parkinglocations.Park_AirportVenue,tbl_parkinglocations.P_Parkingattributes,tbl_parkinglocations.P_Shuttleother,tbl_parkinglocations.P_Shuttledesc,tbl_registeration.firstname,tbl_registeration.display_name,3956 * 2 * ASIN(SQRT(POWER(SIN(('".$lat."' - abs(tbl_parkinglocations.lat)) * pi()/180 / 2), 2) + COS('".$lat."' * pi()/180 ) * COS(abs(tbl_parkinglocations.lat) * pi()/180) * POWER(SIN(('".$long."'-tbl_parkinglocations.lon) * pi()/180 / 2), 2) )) as distance,1 AS Main,tbl_parkinglocations.P_Parkingextras AS P_LocationParkingextras,tbl_parkinglocations.P_Parkingattributes AS P_LocationParkingattributes,tbl_parking.min_reservation,tbl_parking.smart_lot,IF(tbl_favorite.user_id = '".$user_id."', tbl_favorite.status, 0) as fav_status, tbl_parking.P_GarageID, tbl_parking.P_GateID,tbl_parking.Event_price, PE_ID,PE_ParkID,PE_EventID,PE_Eventdate,PE_Start,PE_End,PE_Venue,PE_EventName,PE_VenueName,PE_VenueAddress,PE_VenueCity,PE_VenueState,PE_VenueZip,PE_Created,PE_EventPrice,PE_EventSpots,PE_EventStart,PE_EventEnd
        FROM tbl_parking
        INNER JOIN tbl_parkinglocations ON tbl_parkinglocations.Park_ID=tbl_parking.P_Location
        INNER JOIN tbl_registeration ON tbl_registeration.id=tbl_parking.P_UserID
        LEFT JOIN tbl_favorite ON tbl_favorite.Parking_ID=tbl_parking.P_ID
        LEFT JOIN tbl_parkingevents ON tbl_parking.P_ID=tbl_parkingevents.PE_ParkID AND tbl_parkingevents.PE_Start>'".$dt2."'
        WHERE tbl_parking.P_Status=1 AND tbl_registeration.parking_control=1 AND Park_Location_Status=1 ".$query_ext." AND (3956 * 2 * ASIN(SQRT(POWER(SIN(('".$lat."' - abs(tbl_parkinglocations.lat)) * pi()/180 / 2), 2) + COS('".$lat."' * pi()/180 ) * COS(abs(tbl_parkinglocations.lat) * pi()/180) * POWER(SIN(('".$long."'-tbl_parkinglocations.lon) * pi()/180 / 2), 2) )))<=$distance group by Park_ID order by  distance LIMIT $limits";
    }
   echo $query; die;
    $res = mysqli_query($con,$query);
    $count = mysqli_num_rows($res);
    /*while($info = mysqli_fetch_assoc($res))
    {
        $P_Parkingattributes=explode(",",$info['P_LocationParkingattributes']);
        print_r($P_Parkingattributes);
    }
    die;*/
    if ($count > 0)
    {
        while($info = mysqli_fetch_assoc($res))
        {
			if($info['Average_Price']>0)
			{
				$info['Park_Address'] = current(explode(",",str_replace('United States','',$info['Park_Address'])));
				$info['Total_Rating']=GetTotalRatings($info["P_ID"],'Parking');
				/* Added by Hitesh Tank on 18-March-2016 */

				if(isset($info['P_Pricingtype'])){
					if((strcasecmp(strtolower($info['P_Pricingtype']),'hourly')) == 0)
					{

						$info['Average_Daily_Price']=getCalDailyPrice($checkinDate,$checkoutDate,$info['Average_Price']);

					}
				}
				/* Added by Hitesh Tank on 18-March-2016 */
				$info['Average_Rating']=getAverageRatings($info["P_ID"],'Parking');
				if($event =='' && $event != 1)
				{
					if($info['Park_AirportVenue']==1)
						$park_type='Airport Parking';
					else if($info['Park_AirportVenue']==0)
						$park_type='City Parking';
					if($info['P_Pricingtype']=='event')
						$park_type='Event Parking';
				}
				else {
					 $park_type='Event Parking';
				}
				$info['park_type']=$park_type;
				$all[] = array_map('utf8_encode', $info);
		}
        }


        /* added by Hitesh Tank on 7-March-2016 */
        $i=0;
        foreach($all as $data){
			
				$query="SELECT * FROM beacon WHERE Park_ID='".$data['Park_ID']."'";
				$res = mysqli_query($con,$query);
				$count = mysqli_num_rows($res);

				if($count > 0){
					while($info=mysqli_fetch_assoc($res)){
						$all[$i]['beacon'][]=$info;
					}
				}else{
					$all[$i]['beacon']=array();
				}


				$P_Parkingattributes=explode(",",$data['P_LocationParkingattributes']);
				$pCount=count($P_Parkingattributes);
				if($pCount>0)
				{

					foreach($P_Parkingattributes as $attributes)
					{
						/*
							Not defined
							This is parking for Recreational Vehicles
						*/
						$amenities='';
						if(strtolower($attributes)=="24 hour security")
							$amenities='24hr parking';
						else if(strtolower($attributes)=="attendant on-site at all times")
							$amenities='attendant on site';
						else if(strtolower($attributes)=="restroom facilities available to customers")
							$amenities='bathroom';
						else if(strtolower($attributes)=="car wash available")
							$amenities='carwash';
						else if(strtolower($attributes)=="covered parking" || strtolower($attributes)=="covered valet" || strtolower($attributes)=="covered self park")
							$amenities='covered parking';
						else if(strtolower($attributes)=="this location is handicap accessible")
							$amenities='handicap';
						else if(strtolower($attributes)=="mechanical services available")
							$amenities='mechanic service';
						else if(strtolower($attributes)=="oil change available")
							$amenities='oil change';
						else if(strtolower($attributes)=="free shuttle service to and from the airport")
							$amenities='shuttle';
						else if(strtolower($attributes)=="closed circuit surveillance")
							$amenities='surveillance/security camera';
						else if(strtolower($attributes)=="guaranteed to be unobstructed while the vehicle is parked")
							$amenities='unobstructed';
						else if(strtolower($attributes)=="valet parking" || strtolower($attributes)=="this is valet parking" || strtolower($attributes)=="covered valet"|| strtolower($attributes)=="outdoor valet")
							$amenities='valet';
						else
							$amenities='';
						if($amenities!='')
						$all[$i]['amenities'][]=$amenities;
					}
				}
				else{
					$all[$i]['amenities']=array();
				}
				//die;
				$i++;
			
        }
        /* added by Hitesh Tank on 7-March-2016 */


        /*array_walk_recursive($all, function(&$item, $key) {
            if(is_string($item)) {
                $item = htmlentities($item);
            }
        });*/

        //2nd March Static Data added for testing
        /*$staticData = array(
            "Lattitude"=> "37.7792461",
            "Longitude"=> "-122.393579",
            "P_ID"=> "517",
            "Average_Price"=> "30.00",
            "P_Lot_Type"=> "Covered Self Park(Event)",
            "P_Parkingattributes"=> "Attendant on-site at all times,Free shuttle service to and from the Airport,Guaranteed to be unobstructed while the vehicle is parked,This location is handicap accessible,Closed Circuit Surveillance",
            "P_Parkingextras"=> "",
            "P_Pricingtype"=> "daily",
            "P_Daily_Price_Type"=> "Week",
            "clicks"=> "570114",
            "views"=> "20316",
            "Todaysbookings"=> "24",
            "average_reviews"=> "5",
            "total_reviews"=> "57",
            "P_Created"=> "2013-10-03 12:40:19",
            "P_FAmt"=> "0",
            "Park_Name"=> "Townsend Garage - Night Game",
            "Park_Logo"=> "",
            "Park_Address"=> "153 Townsend St",
            "Park_City"=> "San Francisco",
            "Park_State"=> "CA",
            "Park_Zip"=> "94107",
            "P_Airport_Distance"=> "2 Blocks",
            "Park_Image"=> "",
            "Airport_Near_Address"=> "San Francisco International (SFO)",
            "Airport_Near_Address_ID"=> "0",
            "Park_Typeoflocation"=> "Business",
            "Park_AirportVenue"=> "1",
            "P_Shuttleother"=> "",
            "P_Shuttledesc"=> "<p>24x7 Free Shuttle Service. 4:00 AM - Till Midnight Every 15 Minutes. Midnight to 4:00 AM - On Call/Request.</p>",
            "firstname"=> "Michael",
            "display_name"=> "Dang",
            "distance"=> "12.224246402080114",
            "Main"=> "1",
            "P_LocationParkingextras"=> "24 Hour Security,Discounted Rates",
            "P_LocationParkingattributes"=> "Attendant on-site at all times,Free shuttle service to and from the Airport,Guaranteed to be unobstructed while the vehicle is parked,This location is handicap accessible,Closed Circuit Surveillance",
            "min_reservation"=> "0",
            "smart_lot"=> "1",
            "fav_status"=> "1",
            "P_GarageID"=> "5",
            "P_GateID"=> "10"

        );
        array_unshift($all,$staticData); */


                $content = array("status" => 1,"data" => $all,"count"=>count($all));
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
function GetTotalRatings($list_id,$cat){
    GLOBAL $con;
    if($list_id=='')
    {
        $list_id=0;
    }
    $sql="SELECT * FROM tbl_reviews WHERE R_Type='".$cat."' AND list_id=".$list_id;
    $execity = mysqli_query($con,$sql);
    $num=mysqli_num_rows($execity);
    if($num>0)
        return $num;
    else
    return 0;
}
function getAverageRatings($list_id,$cat){
    GLOBAL $con;
    if($list_id=='')
    {
        $list_id=0;
    }
    $execity = mysqli_query($con,"select * from tbl_reviews where R_Type='".$cat."' AND list_id=".$list_id);
    $Total=0;
    $Average=0;
    $num=mysqli_num_rows($con,$execity);
    $onestar=0;$twostar=0;$threestar=0;$fourstar=0;$fivestar=0;
    $i=0;
    while($v = mysqli_fetch_array($execity))
    {
        $Total+=$v['Average'];
        $i++;
    }
    if($Total>0)
        $Average=round($Total/$i,2);
    else
        $Average=0;

    if($Average>0)
        $Average=round($Average*2, 0)/2;
    else
        $Average=0;
    //$Average = floor($Total*$i)/$i;

    if($Average>5)
        $Average=5;
    return $Average;
}

?>

