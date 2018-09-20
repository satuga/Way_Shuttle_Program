<?php 
ini_set('max_execution_time', 600);
error_reporting(0);
header('Content-Type: application/json');
include ('../config.php');
include ('../function.php');
/*
 * Developer Name: Bindra Shah
 * Date : 7-June-2016
 */
/* Required parameters which are required to create the query for searching */
$lat = sanitize($_REQUEST['lat']);
$long = sanitize($_REQUEST['long']);
$user_id = sanitize($_REQUEST['user_id']);
$device_id = sanitize($_REQUEST['device_id']);
$airport = sanitize($_REQUEST['airport']);
$city = sanitize($_REQUEST['city']);
$min = sanitize($_REQUEST['min']);
$max = sanitize($_REQUEST['max']);
$mindistance = sanitize($_REQUEST['mindistance']);
$maxdistance = sanitize($_REQUEST['maxdistance']);
$pricing = sanitize($_REQUEST['pricing']);
$dt2 = date('Y-m-d H:i:s');
$event = sanitize($_REQUEST['event']);
$checkinDate = sanitize($_REQUEST['checkinDate']) != '' ? sanitize($_REQUEST['checkinDate']) : date('Y-m-d H:i:s');
$checkoutDate = sanitize($_REQUEST['checkoutDate']);
$checkinTime = sanitize($_REQUEST['checkinTime']);
$checkoutTime = sanitize($_REQUEST['checkoutTime']);
$distance = 20;
if (isset($_REQUEST['distance']) && sanitize($_REQUEST['distance']) != '') {
    $distance = sanitize($_REQUEST['distance']);
}

/* check if the main parameters lat , long are valid or not */
if ($lat == '' || $long == '') {
    $content = array("status" => "0", "response" => ERROR, "message" => PARAMETER_MSG);
    echo json_encode($content);
    exit;
} else {
    $query_ext = $query_groupby = $tempwhere = '';
    $limit = 50;
    $type = 'all';

    $tempwhere = ($user_id != 40 && $user_id != 225995 && $user_id != 235724 && $user_id != 235723 && $user_id != 235722 && $user_id != 198563) ? ' ((tbl_parking.P_Status=1 AND Park_Location_Status = 1) OR tbl_parking.IsMobile = 1) AND ' : ''; 

/*$tempwhere = ($user_id != 40 && $user_id != 225995 && $user_id != 235724 && $user_id != 235723 && $user_id != 235722 && $user_id != 198563) ? ' tbl_parking.P_Status=1 AND Park_Location_Status = 1 AND ' : '';   */
    
    if ($airport != '' && $airport == 1)
        $type = "airport"; /* check if the airport is selected */
    if ($city != '' && $city == 1)
        $type = "city"; /* check if the city is selected */
    if ($event != '' && $event == 1)
        $type = "event"; /* check if the event is selected */

    /* check if the range is selected or not */
    if ($min != '' && $max != '') {
        $query_ext.=" AND (tbl_parking.Average_Price >= '" . $min . "' AND tbl_parking.Average_Price<='" . $max . "')";
    } else
        $query_ext.=" AND (tbl_parking.Average_Price >= '1' AND tbl_parking.Average_Price<='100')";
    /* checking the pricing for the parkings */
    if ($pricing != '' && $pricing != 'event')
        $query_ext.=" AND P_Pricingtype='" . $pricing . "'";
    /* checking the pricing for the parkings */
    if ($checkinDate == '' || $checkoutDate == '') {
        $stravailsql = "1 AvailabilityStatus";
    } else {
        $stravailsql = "IFNULL( CASE WHEN p_Daily_price_type ='Week' THEN (select min(CASE WHEN (IFNULL(p2.PA_No_Spaces,0)-IFNULL(occupiedSpaces,0))<=0 THEN '0' ELSE '1' END )
        from tbl_parkingweekdayavailability p2 use index (indexfromdate,indexpid) where p2.P_ID=tbl_parking.P_ID AND p2.p_fromdate between '" . $checkinDate . "'
        AND '" . $checkoutDate . "' group by p2.Park_ID) ELSE '1' END,0) AvailabilityStatus,( CASE WHEN tbl_parkinglocations.Park_Typeoflocation='Residence' THEN CASE WHEN tbl_parking.LastOrderID=0 THEN '1' ELSE '0' END ELSE '0' END ) ResidenceBoostStatus";
    }

    $distanceWithHaving = '';
    if(isset($mindistance) && isset($maxdistance)){
        $distanceWithHaving = " HAVING (distance > '".$mindistance."' and distance < '".$maxdistance."') ";
    }
    
    if ($type == 'all') { 
        //All parking
        $dt2 = date('Y-m-d H:i:s');
        $query = " select tbl_parkinglocations.smart_lot,tbl_parkinglocations.beacon_distance, " . $stravailsql . ",
     Park_Typeoflocation,tbl_parkinglocations.P_Parkingattributes,LastOrderID,Toppriority,tbl_parkinglocations.lat AS Lattitude, tbl_parkinglocations.lon AS Longitude,tbl_parking.P_ID,tbl_parking.Average_Price as Average_Price,tbl_parking.P_Lot_Type,tbl_parking.P_Parkingextras,tbl_parking.P_Pricingtype,tbl_parking.P_Daily_Price_Type,tbl_parking.clicks,tbl_parking.views,tbl_parking.Todaysbookings,tbl_parking.average_reviews,tbl_parking.total_reviews, tbl_parking.P_Created,tbl_parking.P_FAmt,tbl_parkinglocations.Park_Name,tbl_parkinglocations.Park_Logo, tbl_parkinglocations.Park_Address ,tbl_parkinglocations.Park_City,tbl_parkinglocations.Park_State,tbl_parkinglocations.Park_Zip, tbl_parkinglocations.Park_Phone, tbl_parkinglocations.P_Airport_Distance,tbl_parkinglocations.Park_Image,tbl_parkinglocations.Airport_Near_Address, tbl_parkinglocations.Airport_Near_Address_ID,tbl_parkinglocations.Park_AirportVenue,tbl_registeration.firstname,tbl_registeration.display_name,3956 * 2 * ASIN(SQRT(POWER(SIN(($lat - abs(tbl_parkinglocations.lat)) * pi()/180 / 2), 2) + COS($lat * pi()/180 ) * COS(abs(tbl_parkinglocations.lat) * pi()/180) * POWER(SIN(($long-tbl_parkinglocations.lon) * pi()/180 / 2), 2) )) as distance, 2 AS Main,tbl_parking.min_reservation,tbl_parking.Event_price, PE_ID,PE_ParkID,PE_EventID,PE_Eventdate,PE_Start,PE_End,PE_Venue,PE_EventName,PE_VenueName,PE_VenueAddress,PE_VenueCity,PE_VenueState,PE_VenueZip,PE_Created,PE_EventPrice,PE_EventSpots,PE_EventStart,PE_EventEnd,IF(tbl_favorite.user_id = '" . $user_id . "', tbl_favorite.status, 0) as fav_status,P_Status,IsMobile
      FROM tbl_parking USE INDEX(indexstatus)
      INNER JOIN tbl_parkinglocations ON tbl_parking.P_Location=tbl_parkinglocations.Park_ID
      INNER JOIN tbl_registeration ON tbl_parking.P_UserID=tbl_registeration.id
      LEFT JOIN tbl_favorite ON tbl_favorite.Parking_ID=tbl_parking.P_ID
      LEFT JOIN tbl_parkingevents ON tbl_parking.P_ID=tbl_parkingevents.PE_ParkID AND tbl_parkingevents.PE_Start>'" . $dt2 . "'
      WHERE
      $tempwhere tbl_registeration.parking_control=1 AND ((P_Pricingtype='event' AND PE_ID <> '') OR (P_Pricingtype<>'event'))
       ";
        $query_groupby = "group by tbl_parkinglocations.lat,tbl_parkinglocations.lon";
    } elseif ($type == 'event') {
        $query = "select tbl_parkinglocations.smart_lot,tbl_parkinglocations.beacon_distance,tbl_parkinglocations.lat AS Lattitude,tbl_parkinglocations.lon AS Longitude,tbl_parking.*,tbl_parkinglocations.*,tbl_parkingevents.*,tbl_registeration.*,3956 * 2 * ASIN(SQRT(POWER(SIN(($lat - abs(tbl_parkinglocations.lat)) * pi()/180 / 2), 2) + COS($lat * pi()/180 ) * COS(abs(tbl_parkinglocations.lat) * pi()/180) * POWER(SIN(($long-tbl_parkinglocations.lon) * pi()/180 / 2), 2) )) as distance,1 AS Main,IF(tbl_favorite.user_id = '" . $user_id . "', tbl_favorite.status, 0) as fav_status,IsMobile  FROM tbl_parking
          INNER JOIN tbl_parkinglocations ON tbl_parkinglocations.Park_ID=tbl_parking.P_Location
          INNER JOIN tbl_registeration ON tbl_registeration.id=tbl_parking.P_UserID
          LEFT JOIN tbl_favorite ON tbl_favorite.Parking_ID=tbl_parking.P_ID
          RIGHT JOIN tbl_parkingevents ON tbl_parkingevents.PE_ParkID=tbl_parking.P_ID,P_Status,IsMobile
          WHERE $tempwhere tbl_registeration.parking_control=1 AND tbl_parkingevents.PE_End>'" . $dt2 . "' AND (Park_AirportVenue=1 OR Park_AirportVenue=0) AND P_Pricingtype='event' AND PE_ID <> '' ";
    } else {   
    $query = " select tbl_parkinglocations.smart_lot,tbl_parkinglocations.beacon_distance, " . $stravailsql . ",
     Park_Typeoflocation,tbl_parkinglocations.P_Parkingattributes,LastOrderID,Toppriority,tbl_parkinglocations.lat AS Lattitude, tbl_parkinglocations.lon AS Longitude,tbl_parking.P_ID,tbl_parking.Average_Price,tbl_parking.P_Lot_Type,tbl_parking.P_Parkingextras,tbl_parking.P_Pricingtype,tbl_parking.P_Daily_Price_Type, tbl_parking.clicks,tbl_parking.views,tbl_parking.Todaysbookings,tbl_parking.average_reviews,tbl_parking.total_reviews, tbl_parking.P_Created,tbl_parking.P_FAmt,tbl_parkinglocations.Park_Name,tbl_parkinglocations.Park_Logo, tbl_parkinglocations.Park_Address ,tbl_parkinglocations.Park_City,tbl_parkinglocations.Park_State,tbl_parkinglocations.Park_Zip, tbl_parkinglocations.Park_Phone, tbl_parkinglocations.P_Airport_Distance,tbl_parkinglocations.Park_Image,tbl_parkinglocations.Airport_Near_Address, tbl_parkinglocations.Airport_Near_Address_ID,tbl_parkinglocations.Park_AirportVenue,tbl_registeration.firstname,tbl_registeration.display_name,3956 * 2 * ASIN(SQRT(POWER(SIN(($lat - abs(tbl_parkinglocations.lat)) * pi()/180 / 2), 2) + COS($lat * pi()/180 ) * COS(abs(tbl_parkinglocations.lat) * pi()/180) * POWER(SIN(($long-tbl_parkinglocations.lon) * pi()/180 / 2), 2) )) as distance, 2 AS Main,tbl_parking.min_reservation,tbl_parking.Event_price,IF(tbl_favorite.user_id = '" . $user_id . "', tbl_favorite.status, 0) as fav_status,P_Status,IsMobile
      FROM tbl_parking USE INDEX(indexstatus)
      INNER JOIN tbl_parkinglocations ON tbl_parking.P_Location=tbl_parkinglocations.Park_ID
      INNER JOIN tbl_registeration ON tbl_parking.P_UserID=tbl_registeration.id
      LEFT JOIN tbl_favorite ON tbl_favorite.Parking_ID=tbl_parking.P_ID
      WHERE $tempwhere tbl_registeration.parking_control=1 ";
        $is_type_empty = 0;
        if ($type == 'airport') {
            $query.=" AND (Park_AirportVenue=1 OR Park_AirportVenue=0) AND P_Pricingtype<>'event' ";
            $is_type_empty = 1;
        } else if ($type == 'city') {
            $query.=" AND Park_AirportVenue=0 AND P_Pricingtype<>'event'";
            $is_type_empty = 1;
        }
        if ($is_type_empty == 1) {
            $query_groupby = "group by tbl_parkinglocations.lat,tbl_parkinglocations.lon";
        }
    }

    $subSort = 0;
    $sort_by = !empty($_REQUEST['sort_by']) ? $_REQUEST['sort_by'] : '';
    if ($sort_by == 'Top Results')
        $QueryCommon .= " ORDER BY views DESC, Toppriority DESC,tbl_parkinglocations.smart_lot asc ";
    elseif ($sort_by == 'Highest Rated'){
        $subSort = 1;
        //$QueryCommon .= " ORDER BY Average_Rating DESC, Total_Rating DESC, Toppriority DESC,tbl_parkinglocations.smart_lot asc ";
    }
    elseif ($sort_by == 'LowestToHighest')
        $QueryCommon .= " ORDER BY Average_Price ASC , Toppriority DESC,tbl_parkinglocations.smart_lot asc ";
    elseif ($sort_by == 'HighestToLowest')
        $QueryCommon .= " ORDER BY  Average_Price DESC , Toppriority DESC,tbl_parkinglocations.smart_lot asc ";
    else
        $QueryCommon .= " ORDER BY Toppriority DESC,tbl_parkinglocations.smart_lot asc";

    $query.= "$query_ext  $query_groupby $distanceWithHaving $QueryCommon "; // LIMIT $limit";
    //echo $query; die;
    $result = mysqli_query($con, $query) or die("No Error");
    $TotalRecordCount = @mysqli_num_rows($result);
    $page = sanitize($_REQUEST['page']);
    if ($page == "") {
        $page = 1;
    }
    $pageCount = ceil($TotalRecordCount / $limit);
    $StartLimit = (($page - 1) * $limit);

    if ($TotalRecordCount > ($page * $limit)) {
        $EndLimit = $page * $limit;
    } else {
        $EndLimit = $TotalRecordCount;
    }
    $Limits = " LIMIT " . $StartLimit . "," . $limit;
    //echo $query; exit;
    $query = $query . $Limits;
    $res = mysqli_query($con, $query);
    $count = mysqli_num_rows($res);
    if ($count > 0) {
        
        while ($info = mysqli_fetch_assoc($res)) {
            //print_r($info);
            if (empty($info['Park_Name'])) {
                $info['Park_Name'] = $info['Park_Address'];
            }
            $distance = explode(",", $info['beacon_distance']);
            $beacon_distance_min = $distance[0];
            //$beacon_distance_max = $distance[1];
            $info['beacon_distance_min'] = $beacon_distance_min;
            //$info['beacon_distance_max'] = $beacon_distance_max;
            if ($info['Average_Price'] > 0) {
                $info['Park_Address'] = current(explode(",", str_replace('United States', '', $info['Park_Address'])));
                $info['Total_Rating'] = GetTotalRatings($info["P_ID"], 'Parking');
                /* Added by Hitesh Tank on 18-March-2016 */
                if (isset($info['P_Pricingtype'])) {
                    if ((strcasecmp(strtolower($info['P_Pricingtype']), 'hourly')) == 0) {
                        $info['Average_Daily_Price'] = getCalDailyPrice($checkinDate, $checkoutDate, $info['Average_Price']);
                    }
                }
                /* Added by Hitesh Tank on 18-March-2016 */
                $info['Average_Rating'] = getAverageRatings($info["P_ID"], 'Parking');
                if ($event == '' && $event != 1) {
                    if ($info['Park_AirportVenue'] == 1)
                        $park_type = 'Airport Parking';
                    else if ($info['Park_AirportVenue'] == 0)
                        $park_type = 'City Parking';
                    if ($info['P_Pricingtype'] == 'event')
                        $park_type = 'Event Parking';
                }
                else {
                    if ($info['Park_AirportVenue'] == 0)
                        $park_type = 'City Parking';
                    else
                        $park_type = 'Event Parking';
                }
                $info['park_type'] = $park_type;
                // set flag for smart Parking
                if ($info['smart_lot'] == 'yes')
                    $smart_parking = 1;
                else
                    $smart_parking = 0;
                $info['smart_lot'] = $smart_parking;
                //$info['smart_lot']=$smart_parking;

                $isParkingHasShuttle = isParkingHasShuttle($info["P_ID"]);
                $info['parking_has_shuttle'] = $isParkingHasShuttle;
                // Set price for all parking
                /* if($info['P_Pricingtype']=='daily')
                  {
                  $week=date('l', strtotime($checkinDate));
                  if($info['P_Daily_Price_Type']=='Week' && $checkinDate<>'')
                  $Price=number_format(GetAParkingWeekdayLowPrice($info['P_ID']),2);
                  else
                  $Price=number_format(GetParkingDailyPrice($info['P_ID']),2);
                  }
                  else if($info['P_Pricingtype']=='weekly')
                  $Price=number_format($info['P_Weeklyprice'],2);
                  else if($info['P_Pricingtype']=='monthly')
                  $Price=number_format($info['P_Monthlyprice'],2);
                  else if($info['P_Pricingtype']=='hourly')
                  $Price=number_format($info['P_FAmt'],2);
                  else if($info['P_Pricingtype']=='minute')
                  $Price=number_format($info['P_FAmt'],2);
                  else if($info['P_Pricingtype']=='event')
                  $Price=number_format($info['Event_price'],2);
                  else if($info['P_Pricingtype']=='special')
                  $Price=number_format($info['Event_price'],2);
                  else if($info['Average_Price']>0)
                  $Price=number_format($info['Average_Price'],2); */
                if ($info['P_Pricingtype'] == 'daily') {
                    $fromDate = $checkinDate;
                    $week = date('l', strtotime($fromDate));
                    if ($info['P_Daily_Price_Type'] == 'Week' && $fromDate <> '')
                        $Price = number_format(GetAParkingWeekdayLowPrice($info['P_ID']), 2);
                    else
                        $Price = number_format(GetParkingDailyPrice($info['P_ID']), 2);
                } else if ($info['P_Pricingtype'] == 'weekly')
                    $Price = number_format($info['P_Weeklyprice'], 2);
                else if ($info['P_Pricingtype'] == 'monthly')
                    $Price = number_format($info['P_Monthlyprice'], 2);
                else if ($info['P_Pricingtype'] == 'hourly')
                    $Price = number_format($info['P_FAmt'], 2);
                else if ($info['P_Pricingtype'] == 'minute')
                    $Price = number_format($info['P_FAmt'], 2);
                else if ($info['P_Pricingtype'] == 'event')
                    $Price = number_format($info['Event_price'], 2);
                else if ($info['P_Pricingtype'] == 'special')
                    $Price = number_format($info['Event_price'], 2);
                else if($v['Average_Price']>0)
                    $Price=number_format($info['Average_Price'],2);
                
                /*if ($Price > 0)
                    $Price = explode(".", number_format($Price, 2));*/
                //$info['Average_Price']=$Price[0].".".$Price[1];
                $info['Average_Price'] = number_format($Price, 2);
                $info = array_map('utf8_encode', $info);

                $queryBeacon = "SELECT SGT_ID as gate_id, SGT_GarageID as garage_id, SGT_BeaconUuid as beacon_id, SGT_GateType as gatemode, SGT_IsGateOpen as gate_status from tbl_smartgate where SGT_GarageID='" . $info['P_ID'] . "'";
                $resBeacon = mysqli_query($con, $queryBeacon);
                $countBeacon = mysqli_num_rows($resBeacon);

                if ($countBeacon > 0) {
                    while ($infoBeacon = mysqli_fetch_assoc($resBeacon)) {
                        $info['beacon'][] = $infoBeacon;
                    }
                } else {
                    $info['beacon'] = array();
                    //$all[$i]['beacon']=array(0=>array("gate_id" => '', "garage_id" => '', "beacon_id" => '', "gatemode" => '', "gate_status" => ''));
                }

                $P_Parkingattributes = explode(",", strtolower($info['P_Parkingattributes']));
                $pCount = count($P_Parkingattributes);
                if ($pCount > 0) {

                    $array1 = array("24 hour security", "attendant on-site at all times",
                        "restroom facilities available to customers", "car wash available",
                        "covered parking", "covered valet", "covered self park",
                        "this location is handicap accessible", "mechanical services available",
                        "oil change available", "free shuttle service to and from the airport",
                        "closed circuit surveillance", "guaranteed to be unobstructed while the vehicle is parked",
                        "valet parking", "this is valet parking", "covered valet", "outdoor valet");
                    $array2 = array("24hr parking", "attendant on site",
                        "bathroom", "carwash",
                        "covered parking", "covered parking", "covered parking",
                        "handicap", "mechanic service",
                        "oil change", "shuttle",
                        "surveillance/security camera", "unobstructed",
                        "valet", "valet", "valet", "valet");
                    $info['amenities'] = str_replace($array1, $array2, $P_Parkingattributes);

                    /* foreach($P_Parkingattributes as $attributes)
                      {
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
                      $info['amenities'][]=$amenities;
                      } */
                } else {
                    $info['amenities'] = array();
                }

                $all[] = $info;
            }
        }


        /* array_walk_recursive($all, function(&$item, $key) {
          if(is_string($item)) {
          $item = htmlentities($item);
          }
          }); */

        //2nd March Static Data added for testing
        /* $staticData = array(
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
        /* ------------------ Get total cart items before login -------------------------- */
        $total_cart = 0;
        $merchant_name = $merchant_id = '';

        if ($user_id != "") {
            $sql_count = "select m.id, m.merchantName from tbl_cart tc LEFT JOIN merchant m ON m.id = tc.Owner_Restaurant where tc.Cart_Type in('Dine', 'Parking') and tc.Cart_UserID='" . $user_id . "'";
        } else {
            $sql_count = "select m.id, m.merchantName from tbl_cart tc LEFT JOIN merchant m ON m.id = tc.Owner_Restaurant where tc.Cart_Type in('Dine', 'Parking') and tc.Sess_ID='" . $device_id . "'";
        }
        //echo $sql_count; die;
        $query_count = mysqli_query($con, $sql_count);
        if ($total_cart = mysqli_num_rows($query_count)) {
            $rec = mysqli_fetch_assoc($query_count);
            $merchant_name = $rec['merchantName'];
            $merchant_id = $rec['id'];
        }

        /* echo "<pre>";
          if($user_id!=40)
          {
          echo $key = array_search(1983, array_column($all, 'P_ID'));

          unset($all[$key]);
          print_r($all); die;
          } */
        if(!empty($subSort)){
            $all = array_orderby($all, 'Average_Rating', SORT_DESC, 'Total_Rating', SORT_DESC);
        }

        if(empty($all)){
            $content = array("status" => 0, "response" => ERROR, "message" => 'No parking lots available at this location.');
        } else {
            $content = array("status" => 1, "TotalRecordCount" => $TotalRecordCount, "count" => count($all), "pages" => $pageCount, "merchant_name" => $merchant_name, "merchant_id" => $merchant_id, "total_cart" => $total_cart, "data" => $all);
        }
        echo json_encode($content);
        exit;
    } else {
        $content = array("status" => 0, "response" => ERROR, "message" => 'No parking lots available at this location.');
        echo json_encode($content);
        exit;
    }
}
function array_orderby()
{
    $args = func_get_args();
    $data = array_shift($args);
    foreach ($args as $n => $field) {
        if (is_string($field)) {
            $tmp = array();
            foreach ($data as $key => $row)
                $tmp[$key] = $row[$field];
            $args[$n] = $tmp;
            }
    }
    $args[] = &$data;
    call_user_func_array('array_multisort', $args);
    return array_pop($args);
}
function GetTotalRatings($list_id, $cat) {
    GLOBAL $con;
    if ($list_id == '') {
        $list_id = 0;
    }
    $sql = "SELECT * FROM tbl_reviews WHERE R_Type='" . $cat . "' AND list_id=" . $list_id;
    $execity = mysqli_query($con, $sql);
    $num = mysqli_num_rows($execity);
    if ($num > 0)
        return $num;
    else
        return 0;
}

function getAverageRatings($list_id, $cat) {
    GLOBAL $con;
    if ($list_id == '') {
        $list_id = 0;
    }
    $execity = mysqli_query($con, "select * from tbl_reviews where R_Type='" . $cat . "' AND list_id=" . $list_id);
    $Total = 0;
    $Average = 0;
    $num = mysqli_num_rows($con, $execity);
    $onestar = 0;
    $twostar = 0;
    $threestar = 0;
    $fourstar = 0;
    $fivestar = 0;
    $i = 0;
    while ($v = mysqli_fetch_array($execity)) {
        $Total+=$v['Average'];
        $i++;
    }
    if ($Total > 0)
        $Average = round($Total / $i, 2);
    else
        $Average = 0;

    if ($Average > 0)
        $Average = round($Average * 2, 0) / 2;
    else
        $Average = 0;
    //$Average = floor($Total*$i)/$i;

    if ($Average > 5)
        $Average = 5;
    return $Average;
}

?>
 
