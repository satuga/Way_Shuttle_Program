<?php
include('config.php');
include('function.php');

$data = json_decode(file_get_contents('php://input'), TRUE);
$user_id = $data['data']['user_id'];

//$query="select m.*,f.* from tbl_favorite as f left join merchant as m ON m.id = f.Dine_ID where f.user_id=".$user_id." AND f.status = '1' and f.Dine_ID != '' order by f.id desc";


$merchant_logo_path = $Host_Path . '/admin/upload/users/';
// Check with Expire Records
$dt2 = date('Y-m-d H:i:s');
$Weekday = idate("w");

$today = time();
$current_time = date("H:i:s", strtotime($dt2));
$Time_Zone = "-8.00";

$query = "SELECT (SELECT count(*) FROM merchant_hours AS INR_TBL WHERE INR_TBL.merchantID=merchant.id AND INR_TBL.weekDay=MH.weekDay AND ('" .
    $current_time . ".+((merchant.timezone-merchant.timezone)*60*60)' BETWEEN INR_TBL.startTime AND INR_TBL.endTime OR '" .
    $current_time . "' BETWEEN INR_TBL.startTimeOther AND INR_TBL.endTimeOther)) AS OPEN_STATUS,MH.closed, merchant.*,merchant.logo AS Rest_Logo,f.status as fav_status 
		
        FROM tbl_favorite as f 
		left JOIN merchant ON merchant.id= f.Dine_ID
		left JOIN tbl_registeration ON tbl_registeration.id= merchant.Res_UserID
		left JOIN merchant_hours AS MH ON MH.merchantID= merchant.id
		WHERE f.user_id=".$user_id." AND f.status = '1' and f.Dine_ID != '' and MH.weekDay=" .
    $Weekday;


$res = mysqli_query($con,$query);
$count = mysqli_num_rows($res);
if($count>0)
{
	$i=0;
	while($rec=mysqli_fetch_assoc($res))
	{
		
		
		/*-------------------- Open and close --------------------*/
		$res_status = 'open';

        $Time_Zone_Diff = abs($Time_Zone - $rec['timezone']);
        //$weekday=idate("w");
        $weekday = date('w', strtotime("now $Time_Zone_Diff hours"));
        $today = time();
        //$currTime = date("H:i:s",$today);
        $currDay = date('F j, Y, H:i:s', strtotime("now $Time_Zone_Diff hours"));
        $currTime = date('H:i:s', strtotime("now $Time_Zone_Diff hours"));
        $Open_Times = getMerchantTodaysOpenTimes($rec['id'], $currDay);

        $Open_Times1 = explode("and", $Open_Times);
        $Open_Times11 = explode("to", $Open_Times1[0]);
        $Open_Times12 = explode("to", $Open_Times1[1]);


        $currentStatus = RestaurantStatus($rec['id']);
        
        // End Restaurant Open & Close checking
        if ($currentStatus == 'Close') 
        {
            if (strtotime($Open_Times11[0]) > strtotime($Open_Times11[1])) {
                //echo "GREATER";
                $currDay = date('F j, Y, H:i:s', strtotime("now $Time_Zone_Diff hours"));
                $currDay = date('F j, Y, H:i:s', strtotime('-1 day', strtotime($currDay)));
                $currTime = date('H:i:s', strtotime("now $Time_Zone_Diff hours"));
                $Open_Times = getMerchantTodaysOpenTimes($rec['id'], $currDay);

                $Open_Times1 = explode("and", $Open_Times);
                $Open_Times11 = explode("to", $Open_Times1[0]);
                $Open_Times12 = explode("to", $Open_Times1[1]);
                if (trim($Open_Times11[1]) == '23:59:59')
                    $Open_Times11_temp = '00:00:00';
                else
                    $Open_Times11_temp = $Open_Times11[1];
                $Temp_time = "00:00:00";
                if (strtotime($currTime) <= strtotime($Open_Times11_temp) && strtotime($currTime) >=
                    $Temp_time)
                    $currentStatus = "open";
            }
        }
        if ($rec['closed'] == "yes" || $currentStatus == 'Close') 
        {
            $res_status = 'close';
            $Open_Time1 = trim($Open_Times11[0]);
            $Close_Time1 = trim($Open_Times11[1]);


            $Open_Time2 = trim($Open_Times12[0]);
            $Close_Time2 = trim($Open_Times12[1]);

            $Time_Zone_Diff = abs($Time_Zone - $rec['timezone']);
            $currTime = trim(date('H:i:s', strtotime("now $Time_Zone_Diff hours")));

            if ($currTime < $Open_Time1 && strtotime($Open_Time1) <> strtotime($Close_Time1)) {
                if (date("g:i A", strtotime($Close_Time1)) == '11:59 PM')
                    $end = "12:00 AM";
                else
                    $end = date("g:i A", strtotime($Close_Time1));
                $OpenAt = date("g:i A", strtotime($Open_Time1)) . " - " . $end;
                $dayy = "TODAY!";
            } elseif ($currTime < $Open_Time2 && strtotime($Open_Time2) != strtotime($Close_Time2)) {
                if (date("g:i A", strtotime($Close_Time2)) == '11:59 PM')
                    $end = "12:00 AM";
                else
                    $end = date("g:i A", strtotime($Close_Time2));
                $OpenAt = date("g:i A", strtotime($Open_Time2)) . " - " . $end;
                $dayy = "TODAY!";
            } else {
               $tmpday = $weekday;
                $Openstatus = 'Yes';
                while ($Openstatus == 'Yes') {
                    $tmpday++;
                    $Openstatus = GetOpenORClose($rec['id'], $tmpday);
                }
                $weekday = $tmpday;
                
				if ($weekday == 0)
                    $dayy = "SUN";
                elseif ($weekday == 1)
                    $dayy = "MON";
                elseif ($weekday == 2)
                    $dayy = "TUE";
                elseif ($weekday == 3)
                    $dayy = "WED";
                elseif ($weekday == 4)
                    $dayy = "THU";
                elseif ($weekday == 5)
                    $dayy = "FRI";
                elseif ($weekday == 6)
                    $dayy = "SAT";
                if ($dayy == '')
                    $OpenAt = "Closed";
                else {
                    $OpenAt = date("g:i A", strtotime($Open_Time1)) . " - " . date("g:i A",
                        strtotime($Close_Time1));
                    $Open_Times = getMerchantTodaysOpenTimes($rec['id'], '', '', $weekday);
                    $Open_Times1 = explode("and", $Open_Times);

                    $Open_Times11 = explode("to", $Open_Times1[0]);
                    $Open_Times12 = explode("to", $Open_Times1[1]);
                    $Open_Time1 = trim($Open_Times11[0]);
                    $Close_Time1 = trim($Open_Times11[1]);
                    $Open_Time2 = trim($Open_Times12[0]);
                    $Close_Time2 = trim($Open_Times12[1]);
                    if (trim($Open_Times1[0]) == "16:00:00 to 16:00:00")
                        $OpenAt = date("g:i A", strtotime($Open_Time2)) . " - " . date("g:i A",
                            strtotime($Close_Time2));
                    else
                        $OpenAt = date("g:i A", strtotime($Open_Time1)) . " - " . date("g:i A",
                            strtotime($Close_Time1));
                }
            }
            if ($rec['timezone'] == '-5.0')
                $timezone = " (EST)";
            elseif ($rec['timezone'] == '-8.0')
                $timezone = " (PST)";
            elseif ($rec['timezone'] == '-7.0')
                $timezone = " (MST)";
            elseif ($rec['timezone'] == '-4.0')
                $timezone = " (AST)";
            else
                $timezone = " (EST)";
            $OpenAt . $timezone;
        } else {
            $OpenAt = "";
            $timezone = "";

        }
		
        
        
        
		$query_img = "SELECT items.itemImage FROM merchant 
		INNER JOIN menus ON menus.merchantID=merchant.id
		INNER JOIN items ON items.menuID=menus.id where merchantID = '".$rec['id']."' and items.featured = 1 LIMIT 0,1";
		$result_img = mysqli_query($con,$query_img) or die(mysqli_error($con));
		$row_img = mysqli_fetch_assoc($result_img);
		if($row_img['itemImage']!="")
		{
		   $MainPhoto = $row_img['itemImage'];
		}
	    else
        {
            $MainPhoto = $merchant_logo_path."searchResultImage.jpg";
        }
		
		/*---------------- Restuarant photo -----------------*/
		/*if ($rec['Rest_Logo'] <> '')
            $MainPhoto = $rec['Rest_Logo'];
        else
            $MainPhoto = "searchResultImage.jpg";

        $rest_logo = $merchant_logo_path . $MainPhoto;*/
        $rest_logo = $MainPhoto;
	
        
		$info[$i]['id'] = $rec['id'];
        $info[$i]['distance'] = "";
        $info[$i]['merchantName'] = $rec['merchantName'];
        $info[$i]['geoLat'] = $rec['geoLat'];
        $info[$i]['geoLong'] = $rec['geoLong'];
		$info[$i]['rating'] = $rec['average_reviews'];
        $info[$i]['tablebooking'] = $rec['tablebooking'];
        $info[$i]['contactAddress'] = $rec['contactAddress'];
        $info[$i]['postalCode'] = $rec['postalCode'];
        $info[$i]['delivery'] = $rec['delivery'];
        $info[$i]['takeout'] = $rec['takeout'];
        $info[$i]['Rest_Logo'] = $rest_logo;
        $info[$i]['minimumDeliveryAmount'] = $rec['minimumDeliveryAmount'];
        $info[$i]['cuisine'] = $rec['cuisine'];
        $info[$i]['deliveryFee'] = $rec['deliveryFee'];
        $info[$i]['timezone'] = $rec['timezone'];
        $info[$i]['city'] = $rec['city'];
        $info[$i]['state'] = $rec['state'];
        $info[$i]['status'] = $res_status;
        $info[$i]['day'] = $dayy;
        $info[$i]['fav_status'] = $rec['fav_status'];
        $info[$i]['time'] = $OpenAt . ' ' . $timezone;
        $i++;
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
