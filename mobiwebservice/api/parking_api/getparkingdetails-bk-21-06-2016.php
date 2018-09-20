<?php
error_reporting(0);
header('Content-Type: application/json');
include ('/../config.php');
include ('/../function.php');

$data = json_decode(file_get_contents('php://input'), TRUE);
$list_id = sanitize($data['data']['listid']);
$user_id = sanitize($data['data']['user_id']);
$lat = sanitize($data['data']['lat']);
$long = sanitize($data['data']['long']);
$startdate = sanitize($data['data']['startdate']);

function GetTotalRatings($list_id,$cat)
  {

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

function getMyAverageRatings($list_id,$cat,$user_id){
	GLOBAL $con;
	if($list_id=='')
	{
		$list_id=0;
	}

	$sql="select * from tbl_reviews where user_id='".$user_id."' AND R_Type='".$cat."' AND list_id=".$list_id;
	$execity = mysqli_query($con,$sql);
	$Total=0;
	$Average=0;
	$num=mysqli_num_rows($execity);
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
function GetLocationParking($Location)
 {
  GLOBAL $con;
  $sql="SELECT tbl_parking.P_Lot_Type,P_ID,P_Pricingtype,P_Daily_Price_Type,P_Weeklyprice,P_Monthlyprice,P_FAmt,Event_price FROM tbl_parking
  INNER JOIN tbl_parkinglocations ON tbl_parkinglocations.Park_ID=tbl_parking.P_Location
  INNER JOIN tbl_registeration ON tbl_registeration.id=tbl_parking.P_UserID
  WHERE tbl_parking.P_Status=1 AND tbl_registeration.parking_control=1 AND Park_Location_Status=1 AND P_Location=".$Location;
  //echo $sql;
  $result = mysqli_query($con, $sql);
  $TotalRecordCount=mysqli_num_rows($result);
  $i=0;
  $Record =array();
  while($res=mysqli_fetch_assoc($result)) {
   $Record[$i]['P_ID']=$res['P_ID'];
   $Record[$i]['P_Lot_Type']=$res['P_Lot_Type'];
   $Record[$i]['P_Pricingtype']=$res['P_Pricingtype'];
   $Record[$i]['Average_Price']=$res['Average_Price'];
   $Record[$i]['P_Daily_Price_Type']=$res['P_Daily_Price_Type'];
   $Record[$i]['P_Monthlyprice']=$res['P_Monthlyprice'];
   $Record[$i]['P_Weeklyprice']=$res['P_Weeklyprice'];
   $Record[$i]['P_FAmt']=$res['P_FAmt'];
   $Record[$i]['Event_price']=$res['Event_price'];
   $i++;
  }
  return $Record;
 }

if($list_id == '')
{
	$content = array("status" => "0","response" => ERROR, "data" => PARAMETER_MSG);
    echo json_encode($content);
    exit;
}
else
{
	$query = "SELECT tbl_parking.P_UserID,tbl_parkinglocations.lat AS Lattitude,tbl_parkinglocations.lon AS Longitude,tbl_parking.P_ID,tbl_parkinglocations.P_Instructions,tbl_parking.P_Location,tbl_parking.Average_Price,tbl_parking.P_Lot_Type,tbl_parking.P_Parkingattributes,tbl_parking.P_Parkingextras,tbl_parking.P_Pricingtype,tbl_parking.P_Daily_Price_Type,tbl_parking.clicks,tbl_parking.views,tbl_parking.Todaysbookings,tbl_parking.average_reviews,tbl_parking.total_reviews,tbl_parking.P_Created,tbl_parking.P_FAmt,tbl_parkinglocations.Park_Name,tbl_parkinglocations.Park_Logo,tbl_parkinglocations.Park_Address,tbl_parkinglocations.Park_Howtofind,tbl_parkinglocations.Park_Locdesc,tbl_parkinglocations.Park_SpecialInstructions ,tbl_parkinglocations.Park_City,tbl_parkinglocations.Park_State,tbl_parkinglocations.Park_Zip,tbl_parkinglocations.P_Airport_Distance,tbl_parkinglocations.Park_Image1,tbl_parkinglocations.Park_Image2,tbl_parkinglocations.Park_Image3,tbl_parkinglocations.Park_Image4,tbl_parkinglocations.Park_Image5,tbl_parkinglocations.Airport_Near_Address,tbl_parkinglocations.Airport_Near_Address_ID,tbl_parkinglocations.Park_Typeoflocation,tbl_parkinglocations.Park_AirportVenue,tbl_parkinglocations.P_Parkingattributes,tbl_parkinglocations.P_Shuttleother,tbl_parkinglocations.P_Shuttledesc,tbl_registeration.firstname,tbl_registeration.display_name,3956 * 2 * ASIN(SQRT(POWER(SIN(('".$lat."' - abs(tbl_parkinglocations.lat)) * pi()/180 / 2), 2) + COS('".$lat."' * pi()/180 ) * COS(abs(tbl_parkinglocations.lat) * pi()/180) * POWER(SIN(('".$long."'-tbl_parkinglocations.lon) * pi()/180 / 2), 2) )) as distance,1 AS Main,tbl_parkinglocations.P_Parkingextras AS P_LocationParkingextras,tbl_parkinglocations.P_Parkingattributes AS P_LocationParkingattributes,tbl_parking.min_reservation,tbl_parking.smart_lot,tbl_favorite.status as fav_status,P_FMIN,P_H1,Event_price,P_Weeklyprice,P_Monthlyprice,Special_Price_Desc   FROM tbl_parking
		INNER JOIN tbl_parkinglocations ON tbl_parkinglocations.Park_ID=tbl_parking.P_Location
		INNER JOIN tbl_registeration ON tbl_registeration.id=tbl_parking.P_UserID
		LEFT JOIN tbl_favorite ON tbl_favorite.Parking_ID = tbl_parking.P_ID
		WHERE tbl_parking.P_Status=1 AND tbl_parking.Deleted=0 AND tbl_parking.P_ID='".$list_id."' group by tbl_parking.P_ID";
  $res = mysqli_query($con,$query);
	$count = mysqli_num_rows($res);
	if ($count > 0)
    {
		while($info = mysqli_fetch_assoc($res))
		{
			if(isset($user_id) && !empty($user_id))
				$info['My_Rating']=getMyAverageRatings($info['P_ID'],'Parking',$user_id);
			else
				$info['My_Rating']=4;

			$info['Total_Rating']=GetTotalRatings($info['P_ID'],'Parking');
			// Add price rate for parking - 29-apr-2016 - Bindra Shah

			if($info['P_Pricingtype']=='daily')
			{
				$today=1;
				$Totaldays=7;
				$start=$startdate!='' ? date("Y-m-d",strtotime($startdate)): date('Y-m-d');
				while ($today<=$Totaldays)
				{
					if($info['P_Daily_Price_Type']=='Week')
					{
						$week=date('D', strtotime($start));
						$sqlw="SELECT PA_P_Dailyprice AS PRICE,PA_No_Spaces AS SPACES,PA_Updated FROM tbl_parkingweekdayavailability WHERE P_fromDate='".$start."' AND P_ID=".$list_id;
						$resw = mysqli_fetch_array(mysqli_query($con,$sqlw));
						$price["$week"]=$resw['PRICE'];
					}
					else
					{
						$sql="SELECT Park_ID,PA_P_Dailyprice,PA_No_Spaces,PA_Created FROM tbl_parkingavailability WHERE ('".$start."' BETWEEN P_fromDate AND PA_toDate) AND P_ID='".$list_id."'";
						$exe=mysqli_query($con,$sql);
						$res=mysqli_fetch_array($exe);
						$num=mysqli_num_rows($exe);
						$price["$week"]=$res['PA_P_Dailyprice'];
					}
					$start = date ("Y-m-d", strtotime("+1 day", strtotime($start)));
					$today++;
				}
			}
			else if($info['P_Pricingtype']=='weekly')
				$price = number_format($info['P_Weeklyprice'],2)." per Weekly";
			else if($info['P_Pricingtype']=='monthly')
				$price = number_format($info['P_Monthlyprice'],2)." per Monthly";
			else if($info['P_Pricingtype']=='hourly')
				$price = number_format($info['P_FAmt'],2);
			else if($info['P_Pricingtype']=='minute')
				$price = number_format($info['P_FAmt'],2);
			else if($info['P_Pricingtype']=='event')
				$price = number_format($info['Event_price'],2);
			else if($info['P_Pricingtype']=='special')
				$price = number_format($info['Event_price'],2);

			$Special_Price_Desc=explode("(",$info['Special_Price_Desc']);
			$Special_Price_Des=$Special_Price_Desc[0];

			if($info['P_Pricingtype']=='hourly' && $info['P_FMIN']==1)
				$price.= " per Every Hour";
			else if($info['P_Pricingtype']=='hourly' && $info['P_FMIN']>1)
				$price.= " Per ".$info['P_FMIN']." Hours";
			else if($info['P_Pricingtype']=='hourly')
				$price.= $info['P_H1']." per Hour";
			else if($info['P_Pricingtype']=='minute')
				$price.= $info['P_FMIN']." per Minutes";
			else if($info['P_Pricingtype']=='special' && $Special_Price_Des<>'')
				$price.= "&nbsp;".ucwords($Special_Price_Des);
			//print_r($info['price_rate']);die;
			if(is_array($price))
				$info['price_rate']=$price;
			else
			$info['price_rate']=strtolower($price);

			 // Add More details for parking Bindra Shah
            $info['about_parking']=$info['Park_Locdesc'];
            $P_Parkingattributes=explode(",",$info['P_Parkingattributes']);
            foreach($P_Parkingattributes as $m=>$n)
            {
                if($n=='Free shuttle drop off service' || $n=='Free shuttle pick up service' ||  $n=='Free shuttle service to and from the Airport' || $n=='Free shuttle service to and from the Venue' || $n=='Free shuttle service to the Venue' || $n=='Free shuttle service from the Venue')
                    $suttle=$n;
                if($n=='Free shuttle service Other')
                    $suttle='<p>Free shuttle service from '.$rec['P_Shuttleother'].'</p>';

            }
            if($info['P_Shuttledesc']<>'')
                $P_Shuttledesc='<p>'.strip_tags($info['P_Shuttledesc']).'</p>';
            $info['shuttle_info']=$suttle."\n".$P_Shuttledesc;
            $info['parking_instructions']=$info['P_Instructions'];
            $info['amenities']=!empty($P_Parkingattributes) ? $P_Parkingattributes : array();
            $parking_types=GetLocationParking($info['P_Location']);

            foreach ($parking_types as $key=>$PL) {
                if($PL['P_Pricingtype']=='daily')
                {
                    $fromDate=date('m/d/Y g:i a');
                    $week=date('l', strtotime($fromDate));
                    if($PL['P_Daily_Price_Type']=='Week' && $fromDate<>'')
                        $Price=number_format(GetAParkingWeekdayLowPrice($PL['P_ID']),2).'12';
                    else
                        $Price=number_format(GetParkingDailyPrice($PL['P_ID']),2).'32';
                    $checkin=$fromDate;
					          $total=$Price;
                }
                else if($PL['P_Pricingtype']=='weekly')
                    $Price=number_format($PL['P_Weeklyprice'],2);
                else if($PL['P_Pricingtype']=='monthly')
                    $Price=number_format($PL['P_Monthlyprice'],2);
                else if($PL['P_Pricingtype']=='hourly')
                {
                    $Price=number_format($PL['P_FAmt'],2);
                    $checkin=date('m/d/Y g:i a');
					          $total=$Price;
                }

                else if($PL['P_Pricingtype']=='minute')
                    $Price=number_format($PL['P_FAmt'],2);
                else if($PL['P_Pricingtype']=='event')
                    $Price=number_format($PL['Event_price'],2);
                else if($PL['P_Pricingtype']=='special')
                    $Price=number_format($PL['Event_price'],2);
                    $parking_types[$key]['Price']=number_format($Price,2);
                $parking_types[$key]['total']=number_format($total,2);
                $parking_types[$key]['checkIn']=date('m d, Y g:i A',strtotime($checkin));
                $parking_types[$key]['checkOut']=date('m d, Y g:i A',strtotime("+1 day",strtotime($checkin)));
            }
            $info['parking_types']=!empty($parking_types) ? $parking_types : array();
            if($user_id!='')
            {
                $EncryptKey = ENCRYPTKEY;
                $sql="Select *,RIGHT(AES_DECRYPT(Card_Number,'".$EncryptKey."'),4) AS Card_No From tbl_creditcarddetails WHERE Card_User_ID=".$user_id." AND Card_Default='1'";
                $res2=@mysqli_fetch_assoc(mysqli_query($con,$sql));
                if(!empty($res2))
                {
                    $card_type = $res2['Card_Type'];
                    if($card_type == 'Visa')
                        $card_type = 'VISA';
                    else if($card_type == 'MasterCard')
                        $card_type = 'MASTER CARD';
                    else if($card_type == 'American Express')
                    $card_type = 'AMEX';
                    else if($card_type == 'Discover')
                    $card_type = 'DISCOVER';
                    else if($card_type == 'Dinners Club')
                    $card_type = 'DINERSCLUB';
                    else
                    $card_type = $card_type;
                    $card_exp_year = substr($res2['Card_Exp_Year'],-2);
                    $card_array[]=array("Card_ID"=>$res2['Card_ID'],"Card_Type"=>$card_type,"CARD_NO"=>base64_encode($res2['Card_No']),"Card_Exp_Year"=>$card_exp_year,
                                        "Card_Exp_Month"=>$res2['Card_Exp_Month'],"Card_FirstName"=>$res2['Card_FirstName'],"Card_Street"=>$res2['Card_Street'],
                                        "Card_State"=>$res2['Card_State'],"Card_City"=>$res2['Card_City'],"Card_Zip"=>$res2['Card_Zip'],"Stripe_UserId"=>(isset($res2['Stripe_UserId'])?$res2['Stripe_UserId']:'0'),"Card_StripeCustID"=>(isset($res2['Card_StripeCustID'])?$res2['Card_StripeCustID']:'0'),"Card_Default"=>$res2['Card_Default']);
                }
            }
            $info['Card_details']=!empty($card_array) ? $card_array : array();
            // End Add More details for parking Bindra Shah
			$all[] =  $info;
			// End Add price rate for parking - 29-apr-2016 - Bindra Shah


			// $all[] =  array_map('utf8_encode', $info); // Add comment by bindra shah 29-apr-2016
		}

    array_walk_recursive($all, function(&$item, $key) {
			if(is_string($item)) {
				$item = @strip_tags($item);
			}
		});


		$lat = $all[0]['Lattitude'];
		$long = $all[0]['Longitude'];

		$similar = "SELECT tbl_parking.P_UserID,tbl_parkinglocations.lat AS Lattitude,tbl_parkinglocations.lon AS Longitude,tbl_parking.P_ID,tbl_parking.Average_Price,tbl_parking.P_Lot_Type,tbl_parking.P_Parkingattributes,tbl_parking.P_Parkingextras,tbl_parking.P_Pricingtype,tbl_parking.P_Daily_Price_Type,tbl_parking.clicks,tbl_parking.views,tbl_parking.Todaysbookings,tbl_parking.average_reviews,tbl_parking.total_reviews,tbl_parking.P_Created,tbl_parking.P_FAmt,tbl_parkinglocations.Park_Name,tbl_parkinglocations.Park_Logo,tbl_parkinglocations.Park_Address,tbl_parkinglocations.Park_Howtofind,tbl_parkinglocations.Park_Locdesc,tbl_parkinglocations.Park_SpecialInstructions ,tbl_parkinglocations.Park_City,tbl_parkinglocations.Park_State,tbl_parkinglocations.Park_Zip,tbl_parkinglocations.P_Airport_Distance,tbl_parkinglocations.Park_Image1,tbl_parkinglocations.Airport_Near_Address,tbl_parkinglocations.Airport_Near_Address_ID,tbl_parkinglocations.Park_Typeoflocation,tbl_parkinglocations.Park_AirportVenue,tbl_parkinglocations.P_Parkingattributes,tbl_parkinglocations.P_Shuttleother,tbl_parkinglocations.P_Shuttledesc,tbl_registeration.firstname,tbl_registeration.display_name,3956 * 2 * ASIN(SQRT(POWER(SIN(('".$lat."' - abs(tbl_parkinglocations.lat)) * pi()/180 / 2), 2) + COS('".$lat."' * pi()/180 ) * COS(abs(tbl_parkinglocations.lat) * pi()/180) * POWER(SIN(('".$long."'-tbl_parkinglocations.lon) * pi()/180 / 2), 2) )) as distance,1 AS Main,tbl_parkinglocations.P_Parkingextras AS P_LocationParkingextras,tbl_parkinglocations.P_Parkingattributes AS P_LocationParkingattributes,tbl_parking.min_reservation,tbl_parking.smart_lot,tbl_favorite.status as fav_status  FROM tbl_parking
			INNER JOIN tbl_parkinglocations ON tbl_parkinglocations.Park_ID=tbl_parking.P_Location
			INNER JOIN tbl_registeration ON tbl_registeration.id=tbl_parking.P_UserID
			LEFT JOIN tbl_favorite ON tbl_favorite.Parking_ID = tbl_parking.P_ID
			WHERE tbl_parking.P_Status=1 AND tbl_registeration.parking_control=1 AND Park_Location_Status=1 AND P_Pricingtype<>'event' '".$query_ext."' AND (3956 * 2 * ASIN(SQRT(POWER(SIN(('".$lat."' - abs(tbl_parkinglocations.lat)) * pi()/180 / 2), 2) + COS('".$lat."' * pi()/180 ) * COS(abs(tbl_parkinglocations.lat) * pi()/180) * POWER(SIN(('".$long."'-tbl_parkinglocations.lon) * pi()/180 / 2), 2) )))<=500 and tbl_parking.P_ID !='".$list_id."' limit 0,3";
		$res1 = mysqli_query($con,$similar);
		$count1 = mysqli_num_rows($res1);
		if ($count1 > 0)
		{
			while($info1 = mysqli_fetch_assoc($res1))
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
		$res2 = mysqli_query($con,$query2);
		$count2 = mysqli_num_rows($res2);
		if ($count2 > 0)
		{
			while($info2 = mysqli_fetch_assoc($res2))
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


		/*$new_all_data = array_map("remove_tags", $all);
		$content = array("status" => "1", "data" => $new_all_data);*/
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
