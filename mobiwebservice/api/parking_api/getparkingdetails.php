<?php

error_reporting(0);
header('Content-Type: application/json');
include ('../config.php');
include ('../function.php');

$data = json_decode(file_get_contents('php://input'), TRUE);
$list_id = sanitize($data['data']['listid']);
$pe_id = isset($data['data']['pe_id']) ? sanitize($data['data']['pe_id']) : '';
$user_id = sanitize($data['data']['user_id']);
$lat = sanitize($data['data']['lat']);
$long = sanitize($data['data']['long']);
$startdate = sanitize($data['data']['startdate']);
$todate = date('Y-m-d H:i:s');
$statusQuery = ($user_id == 40 || $user_id == 225995 || $user_id == 235724 || $user_id == 235723 || $user_id == 235722 || $user_id == 198563) ? "" : "tp.P_Status=1 AND ";

function GetLocationParking($Location, $statusQuery) {
    GLOBAL $con;
    $dt2 = date('Y-m-d H:i:s');
    $sql="SELECT tp.P_Lot_Type,P_ID,P_Pricingtype,P_Daily_Price_Type,P_Weeklyprice,P_Monthlyprice,P_FAmt,Event_price FROM tbl_parking tp "
           . "INNER JOIN tbl_parkinglocations ON tbl_parkinglocations.Park_ID=tp.P_Location  "
           . "INNER JOIN tbl_registeration ON tbl_registeration.id=tp.P_UserID  "
           . "LEFT JOIN tbl_parkingevents ON tp.P_ID=tbl_parkingevents.PE_ParkID AND tbl_parkingevents.PE_Start>'" . $dt2 . "' "
           . "WHERE $statusQuery tbl_registeration.parking_control=1 AND ((tp.P_Pricingtype='event' AND tbl_parkingevents.PE_ID <> '') OR (tp.P_Pricingtype<>'event')) AND Park_Location_Status=1 and  Average_Price > 0 AND P_Location=".$Location;
    //echo $sql;
    $result = mysqli_query($con, $sql);
    $TotalRecordCount = mysqli_num_rows($result);
    $i = 0;
    $Record = array();
    while ($res = mysqli_fetch_assoc($result)) {
        $Record[$i]['P_ID'] = $res['P_ID'];
        $Record[$i]['P_Lot_Type'] = $res['P_Lot_Type'];
        $Record[$i]['P_Pricingtype'] = $res['P_Pricingtype'];
        //$Record[$i]['Average_Price'] = $res['Average_Price'];
        $Record[$i]['P_Daily_Price_Type'] = $res['P_Daily_Price_Type'];
        $Record[$i]['P_Monthlyprice'] = $res['P_Monthlyprice'];
        $Record[$i]['P_Weeklyprice'] = $res['P_Weeklyprice'];
        $Record[$i]['P_FAmt'] = $res['P_FAmt'];
        $Record[$i]['Event_price'] = $res['Event_price'];
        if($res['P_Pricingtype']=='daily')
        {
            $fromDate = $todate;
            $week=date('l', strtotime($fromDate));
            if($res['P_Daily_Price_Type']=='Week' && $fromDate<>'')
                $Price=number_format(GetAParkingWeekdayLowPrice($res['P_ID']),2);
            else
                $Price=number_format(GetParkingDailyPrice($res['P_ID']),2);
        } else if($res['P_Pricingtype']=='weekly')
                $Price=number_format($res['P_Weeklyprice'],2);
        else if($res['P_Pricingtype']=='monthly')
                $Price=number_format($res['P_Monthlyprice'],2);
        else if($res['P_Pricingtype']=='hourly')
                $Price=number_format($res['P_FAmt'],2);
        else if($res['P_Pricingtype']=='minute')
                $Price=number_format($res['P_FAmt'],2);
        else if($res['P_Pricingtype']=='event')
                $Price=number_format($res['Event_price'],2);
        else if($res['P_Pricingtype']=='special')
                $Price=number_format($res['Event_price'],2);

        $Record[$i]['Average_Price']=number_format($Price,2);
        $i++;
    }
    return $Record;
}

if ($list_id == '') {
    $content = array("error" => 0, "status" => "0", "response" => ERROR, "data" => PARAMETER_MSG);
    echo json_encode($content);
    exit;
} else {

    $query = "select tp.P_UserID,tp.P_ID,tp.P_Location,tp.Average_Price,tp.P_Lot_Type,  tp.P_Parkingattributes,tp.P_Parkingextras,tp.P_Pricingtype,tp.P_Daily_Price_Type,tp.clicks,tp.views,tp.Todaysbookings,tp.average_reviews,tp.total_reviews,tp.P_Created,tp.P_FAmt,tpl.Park_Name,tp.min_reservation,tpl.smart_lot,tpl.Park_Logo,tpl.Park_Address,tpl.Park_Howtofind,tpl.Park_Locdesc,tpl.Park_SpecialInstructions,tpl.Park_City,tpl.Park_State,tpl.Park_Zip,tpl.P_Airport_Distance,tpl.Park_Image1,tpl.Park_Image2,tpl.Park_Image3,tpl.Park_Image4,tpl.Park_Image5,tpl.Airport_Near_Address,tpl.Airport_Near_Address_ID,tpl.Park_Typeoflocation,tpl.Park_AirportVenue,tpl.P_Parkingattributes,tpl.P_Shuttleother,tpl.P_Shuttledesc,tr.firstname,tr.display_name,tbl_favorite.status as fav_status
    FROM tbl_parking tp
    INNER JOIN tbl_parkinglocations tpl ON tpl.Park_ID=tp.P_Location
    INNER JOIN tbl_registeration tr ON tr.id=tp.P_UserID
    LEFT JOIN tbl_favorite ON tbl_favorite.Parking_ID = tp.P_ID
    WHERE $statusQuery tp.Deleted=0 AND tp.P_ID='" . $list_id . "' group by tp.P_ID";
    $res = mysqli_query($con, $query);
    $count = mysqli_num_rows($res);
    //echo $query; die;
    if ($count > 0) {
        while ($info = mysqli_fetch_assoc($res)) {
            // Add More details for parking Bindra Shah
            if ($info['Park_AirportVenue'] == 1)
                $park_type = 'Airport Parking';
            else if ($info['Park_AirportVenue'] == 0)
                $park_type = 'City Parking';
            if ($info['P_Pricingtype'] == 'event')
                $park_type = 'Event Parking';
            $info['park_type'] = $park_type;
            if ($info['smart_lot'] == 'yes')
                $smart_lot = 1;
            else {
                $smart_lot = 0;
            }
            $info['smart_lot'] = $smart_lot;
            if (isset($user_id) && !empty($user_id))
                $info['My_Rating'] = getAllAverageRatings($info['P_ID'], 'Parking', $user_id);
            else
                $info['My_Rating'] = 4;

            $getParkingAverageRatings = getParkingAverageRatings($info['P_ID'], 'Parking');
            $info['Total_Rating'] = $getParkingAverageRatings['average_rating'];
            $info['Total_Rating_Count'] = $getParkingAverageRatings['average_rating_count'];
            $info['about_parking'] = $info['Park_Locdesc'];
            $P_Parkingattributes = explode(",", $info['P_Parkingattributes']);
            $pCount = count($P_Parkingattributes);
            $suttle_exist = 0;
            if ($pCount > 0)
                foreach ($P_Parkingattributes as $attributes) {
                    if ($attributes == 'Free shuttle drop off service' || $attributes == 'Free shuttle pick up service' || $attributes == 'Free shuttle service to and from the Airport' || $attributes == 'Free shuttle service to and from the Venue' || $attributes == 'Free shuttle service to the Venue' || $attributes == 'Free shuttle service from the Venue')
                        $suttle = $attributes;
                    if ($attributes == 'Free shuttle service Other')
                        $suttle = '<p>Free shuttle service from ' . $rec['P_Shuttleother'] . '</p>';
                    $amenities = '';
                    if (strtolower($attributes) == "24 hour security")
                        $amenities = '24hr parking';
                    else if (strtolower($attributes) == "attendant on-site at all times")
                        $amenities = 'attendant on site';
                    else if (strtolower($attributes) == "restroom facilities available to customers")
                        $amenities = 'bathroom';
                    else if (strtolower($attributes) == "car wash available")
                        $amenities = 'carwash';
                    else if (strtolower($attributes) == "covered parking" || strtolower($attributes) == "covered valet" || strtolower($attributes) == "covered self park")
                        $amenities = 'covered parking';
                    else if (strtolower($attributes) == "this location is handicap accessible")
                        $amenities = 'handicap';
                    else if (strtolower($attributes) == "mechanical services available")
                        $amenities = 'mechanic service';
                    else if (strtolower($attributes) == "oil change available")
                        $amenities = 'oil change';
                    else if (strtolower($attributes) == "free shuttle service to and from the airport")
                        $amenities = 'shuttle';
                    else if (strtolower($attributes) == "closed circuit surveillance")
                        $amenities = 'surveillance/security camera';
                    else if (strtolower($attributes) == "guaranteed to be unobstructed while the vehicle is parked")
                        $amenities = 'unobstructed';
                    else if (strtolower($attributes) == "valet parking" || strtolower($attributes) == "this is valet parking" || strtolower($attributes) == "covered valet" || strtolower($attributes) == "outdoor valet")
                        $amenities = 'valet';
                    else
                        $amenities = '';
                    if ($amenities != '')
                        $amenity[] = $amenities;
                    if ($attributes == 'Free shuttle drop off service' || $attributes == 'Free shuttle pick up service' || $attributes == 'Free shuttle service to and from the Airport')
                        $suttle_exist = 1;
                }
            if ($info['P_Shuttledesc'] <> '')
                $P_Shuttledesc = '<p>' . strip_tags($info['P_Shuttledesc']) . '</p>';
            $info['shuttle'] = $suttle_exist;
            $info['shuttle_info'] = $suttle . "\n" . $P_Shuttledesc;
            $isParkingHasShuttle = isParkingHasShuttle($info["P_ID"]);
            $info['parking_has_shuttle'] = $isParkingHasShuttle;
            $info['parking_instructions'] = !empty($info['P_Instructions']) ? $info['P_Instructions'] : '';
            $info['amenities'] = !empty($P_Parkingattributes) ? $P_Parkingattributes : array();
            $info['amenities'] = !empty($amenity) ? $amenity : array();
            $parking_types = GetLocationParking($info['P_Location'], $statusQuery);

            foreach ($parking_types as $key => $PL) {
                $fdate = date("Y-m-d g:i A");
                $tdate = date('Y-m-d g:i A', strtotime("+2 day", strtotime($fdate)));
                $parkingData = getParkingPriceAndTotal($flag = 1, $fdate, $tdate, $PL['P_ID'], $pe_id); // define flag for determine that it's detail file
                // print_r($parkingData); die;

                $parking_types[$key]['price_rate'] = !empty($parkingData['price_rate']) ? $parkingData['price_rate'] : array();
                $parking_types[$key]['min_reservation'] = $parkingData['min_reservation'];
                $parking_types[$key]['price'] = $parkingData['price'];
                $parking_types[$key]['subtotal'] = $parkingData['subtotal'];
                $parking_types[$key]['tax'] = $parkingData['tax'];
                $parking_types[$key]['overnight_fee'] = $parkingData['overnight_fee'];
                $parking_types[$key]['total'] = $parkingData['total'];
                $parking_types[$key]['diff'] = $parkingData['days'];
                $parking_types[$key]['checkIn'] = date('m d, Y g:i A', $parkingData['fdate']);
                $parking_types[$key]['checkOut'] = date('m d, Y g:i A', $parkingData['tdate']);
                //echo $parkingData['fdate']; die;
            }
            $info['parkingTypeStatus'] = getParkingTypeByPid($list_id, $statusQuery);
            $info['parking_types'] = !empty($parking_types) ? $parking_types : array();
            if ($user_id != '') {
                $EncryptKey = ENCRYPTKEY;
                $sql = "Select *,RIGHT(AES_DECRYPT(Card_Number,'" . $EncryptKey . "'),4) AS Card_No From tbl_creditcarddetails WHERE Card_User_ID=" . $user_id . " AND Card_Default='1'";
                $res2 = @mysqli_fetch_assoc(mysqli_query($con, $sql));
                if (!empty($res2)) {
                    $card_type = $res2['Card_Type'];
                    if ($card_type == 'Visa')
                        $card_type = 'VISA';
                    else if ($card_type == 'MasterCard')
                        $card_type = 'MASTER CARD';
                    else if ($card_type == 'American Express')
                        $card_type = 'AMEX';
                    else if ($card_type == 'Discover')
                        $card_type = 'DISCOVER';
                    else if ($card_type == 'Dinners Club')
                        $card_type = 'DINERSCLUB';
                    else
                        $card_type = $card_type;
                    $card_exp_year = substr($res2['Card_Exp_Year'], -2);
                    $card_array[] = array("Card_ID" => $res2['Card_ID'], "Card_Type" => $card_type, "CARD_NO" => base64_encode($res2['Card_No']), "Card_Exp_Year" => $card_exp_year,
                        "Card_Exp_Month" => $res2['Card_Exp_Month'], "Card_FirstName" => $res2['Card_FirstName'], "Card_Street" => $res2['Card_Street'],
                        "Card_State" => $res2['Card_State'], "Card_City" => $res2['Card_City'], "Card_Zip" => $res2['Card_Zip'], "Stripe_UserId" => (isset($res2['Stripe_UserId']) ? $res2['Stripe_UserId'] : '0'), "Card_StripeCustID" => (isset($res2['Card_StripeCustID']) ? $res2['Card_StripeCustID'] : '0'), "Card_Default" => $res2['Card_Default']);
                }
            }
            $info['Card_details'] = !empty($card_array) ? $card_array : array();
            // End Add More details for parking Bindra Shah
            if($info['P_Pricingtype']=='daily')
            {
                $fromDate = $todate;
                $week=date('l', strtotime($fromDate));
                if($info['P_Daily_Price_Type']=='Week' && $fromDate<>'')
                    $Price=number_format(GetAParkingWeekdayLowPrice($info['P_ID']),2);
                else
                    $Price=number_format(GetParkingDailyPrice($info['P_ID']),2);
            } else if($info['P_Pricingtype']=='weekly')
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

            $info['Average_Price']=number_format($Price,2);
            if (empty($info['Park_Name'])) {
                $info['Park_Name'] = $info['Park_Address'];
            }
            $all[] = $info;
        }

        array_walk_recursive($all, function(&$item, $key) {
            if (is_string($item)) {
                $item = @strip_tags($item);
            }
        });
        $all = removeNull($all);
        $content = array("status" => "1", "data" => $all);
        echo $json = json_encode($content);
        exit;
    } else {
        $content = array("response" => ERROR, "message" => 'No Records Found');
        echo json_encode($content);
        exit;
    }
}
?>
