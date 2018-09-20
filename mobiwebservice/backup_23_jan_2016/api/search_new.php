<?php
include ('config.php');
include ('function.php');

$lat = $_REQUEST['lat'];
$long = $_REQUEST['long'];
$sql_lat_col = "merchant.geoLat";
$sql_long_col = "merchant.geoLong";

$Host_Path='https://www.bi.way.com';
$merchant_logo_path = $Host_Path . '/admin/upload/users/';
// Check with Expire Records
$dt2 = date('Y-m-d H:i:s');
$Weekday = idate("w");

$today = time();
$current_time = date("H:i:s", strtotime($dt2));
$Time_Zone = "-8.00";
$distance=$_REQUEST['distance'];

//lat=37.773972&long=-122.431297&distance=30&Type=findtable&user_id=40
// Location Match Query
$Query1 = "SELECT (SELECT count(*) FROM merchant_hours AS INR_TBL WHERE INR_TBL.merchantID=merchant.id AND INR_TBL.weekDay=MH.weekDay AND ('" .
    $current_time . ".+((merchant.timezone-merchant.timezone)*60*60)' BETWEEN INR_TBL.startTime AND INR_TBL.endTime OR '" .
    $current_time . "' BETWEEN INR_TBL.startTimeOther AND INR_TBL.endTimeOther)) AS OPEN_STATUS,MH.closed, merchant.*,merchant.logo AS Rest_Logo,3956 * 2 * ASIN(SQRT(POWER(SIN(($lat - abs($sql_lat_col)) * pi()/180 / 2), 2) + COS($lat * pi()/180 ) * COS(abs($sql_lat_col) * pi()/180) * POWER(SIN(($long-$sql_long_col) * pi()/180 / 2), 2) )) as distance,1 AS Main
		FROM merchant
		INNER JOIN tbl_registeration ON tbl_registeration.id= merchant.Res_UserID
		INNER JOIN merchant_hours AS MH ON MH.merchantID= merchant.id
		INNER JOIN merchant_cuisine ON merchant_cuisine.merchantID= merchant.id
		INNER JOIN tbl_cuisine ON tbl_cuisine.Cuisine_ID= merchant_cuisine.cuisineID
		WHERE merchant.status='Active' AND merchant.Deleted=0 AND tbl_registeration.dine_control=1 AND MH.weekDay=" .
    $Weekday;

if ($_REQUEST['dinedescription'] != "" && $_REQUEST['dinedescription'] != 'Where are you going?')
{
	echo "test";
    $desc = explode(",", mysql_real_escape_string($_REQUEST['dinedescription']));
    if (count($desc) > 1) {
        $Loc = explode(",", mysql_real_escape_string($_REQUEST['dinedescription']));
        $Strlen = strlen($_REQUEST['dinedescription']);
        $cnt = count($Loc);
        $Strlen1 = strlen($Loc[$cnt - 2]);
        $Strlen2 = strlen($Loc[$cnt - 1]);
        $Strdiff = $Strlen - ($Strlen1 + $Strlen2);

        $Street = substr($_REQUEST['dinedescription'], 0, $Strdiff);
        $Street = trim($Street);
        $Street = substr($Street, 0, strlen($Street) - 1);
        $Query1 .= " AND merchant.city='" . ucwords(trim($Loc[$cnt - 2])) . "'";
        $Query1 .= " AND merchant.state='" . ucwords(trim($Loc[$cnt - 1])) . "'";
        if ($Street <> '')
            $Query1 .= " AND merchant.contactAddress LIKE '%" . trim($Street) . "%'";
    } else {
        $desc1 = explode(" ", $_REQUEST['dinedescription']);
        $Query1 .= " AND (";
        $rr = 1;
        foreach ($desc1 as $desc) {
            $Query1 .= " (merchant.merchantName LIKE '%" . $desc .
                "%' OR merchant.aboutUs LIKE '%" . $desc . "%' OR merchant.city LIKE '%" . $desc .
                "%' OR merchant.state LIKE '%" . $desc .
                "%' OR tbl_cuisine.Cuisine_Name LIKE '%" . $desc . "%') ";
            if (COUNT($desc1) <> $rr)
                $Query1 .= " OR ";
            $rr++;
        }
        $Query1 .= " ) ";
    }
}
else
{
    if ($_REQUEST['City'] != "" && $_REQUEST['City'] != "City" && $_REQUEST['City'] != "City Name")
	{
        $Query1 .= " AND merchant.city='" . ucwords($_REQUEST[City]) . "'";
    }
    if ($_REQUEST['State'] != "" && $_REQUEST['State'] != "State" && $_REQUEST['State'] != "State Name")
	{
        $Query1 .= " AND merchant.state='" . ucwords(trim($_REQUEST[State])) . "'";
    }
}

if ($_REQUEST['dinekeywords'] != "" && $_REQUEST['dinekeywords'] != 'Search Keyword')
{
    $keywordss = explode(",", trim(trim($_REQUEST['dinekeywords'], ",")));
    $Query1 .= " AND (";
    $rr = 1;
    foreach ($keywordss as $keywordss1) {
        $Query1 .= " (merchant.merchantName LIKE '%" . $keywordss1 .
            "%' OR merchant.aboutUs LIKE '%" . $keywordss1 . "%' OR merchant.city LIKE '%" .
            $keywordss1 . "%' OR merchant.state LIKE '%" . $keywordss1 .
            "%' OR tbl_cuisine.Cuisine_Name LIKE '%" . $keywordss1 . "%') ";
        if (COUNT($keywordss) <> $rr)
            $Query1 .= " OR ";
        $rr++;
    }
    $Query1 .= " ) ";
}

if ($_REQUEST['Cuisines'] != "" && $_REQUEST['Cuisines'] != "all")
{
    $Cuisines = explode(",", $_REQUEST['Cuisines']);
    $Query1 .= " AND (";
    $CU = 0;
    foreach ($Cuisines as $CC => $CCC) {
        if ($CU == 0)
            $Query1 .= " merchant_cuisine.cuisineID='" . $CCC . "'";
        else
            $Query1 .= " OR merchant_cuisine.cuisineID='" . $CCC . "'";
        $CU++;
    }
    $Query1 .= " ) ";
}
if ($_REQUEST['dineOptional_Services'] != "")
{
    $Optional_Servicess = explode(",", $_REQUEST['dineOptional_Services']);
    if (count($Optional_Servicess) > 0) {
        $Query1 .= " AND (";
        $cnn = 1;
        foreach ($Optional_Servicess as $p => $q) {
            if (count($Optional_Servicess) == $cnn)
                $Query1 .= " merchant.contactAddress LIKE '%" . $q . "%' ";
            else
                $Query1 .= " merchant.contactAddress LIKE '%" . $q . "%' OR ";
            $cnn++;
        }
        $Query1 .= " ) ";
    }
}
if ($_REQUEST['RANGE'] <> '')
{
    $RangeVal = explode("-", $_REQUEST['RANGE']);
    $Min = trim(trim($RangeVal[0]), '$');
    $Max = trim(trim($RangeVal[1]), '$');
    $Query1 .= " AND (Min_Price>= '" . $Min . "' OR Max_Price<='" . $Max . "' ";
    $Query1 .= " OR Min_Price<= '" . $Max . "' OR Max_Price<='" . $Min . "') ";
    //1-100   =>   10 - 80
}
if ($_REQUEST['dineRev'] != "")
{
    /*$Reviews=explode(",",$_REQUEST['dineRev']);
    if(count($Reviews)>0)
    {*/
    $Query1 .= " AND merchant.average_reviews>=" . $_REQUEST['dineRev'];
    //}
}
if ($_REQUEST['Type'] != "" && $_REQUEST['Type'] != ",")
{
    $Typevalues = explode(",", strtolower($_REQUEST['Type']));
    foreach ($Typevalues as $p => $q)
	{
        if ($q == 'delivery')
            $Query1 .= " AND merchant.delivery='Yes'";
        elseif ($q == 'pickup')
            $Query1 .= " AND merchant.takeout='Yes'";
        elseif ($q == 'free-delivery')
            $Query1 .= " AND merchant.deliveryFee=0";

        elseif ($q == 'coupons')
            $Query1 .= " AND merchant.coupons>0 AND coupons_expiry > '" . $dt2 . "'";
        elseif ($q == 'favorites')
            $Query1 .= " AND merchant.favourites>0";
        elseif ($q == 'tablebooking' OR $q=='findtable') //added by Hitesh Tank on 17-Sep-2015 (OR $q=='findtable') condition
            $Query1 .= " AND merchant.tablebooking='Yes'";
        elseif ($q == 'open')
            $Query1 .= " AND ('" . $current_time . ".+((merchant.timezone-" . $Time_Zone .
                ")*60*60)' BETWEEN MH.startTime AND MH.endTime OR '" . $current_time .
                "' BETWEEN MH.startTimeOther AND MH.endTimeOther)";


    }
}
$Query1 .= " AND (3956 * 2 * ASIN(SQRT(POWER(SIN(($lat - abs($sql_lat_col)) * pi()/180 / 2), 2) + COS($lat * pi()/180 ) * COS(abs($sql_lat_col) * pi()/180) * POWER(SIN(($long-$sql_long_col) * pi()/180 / 2), 2) )))<=$distance";


$QueryCommon = " GROUP BY merchant.id";
if ($_REQUEST['sort_by'] == 'Top Results')
    $QueryCommon .= " ORDER BY Main ASC,clicks DESC,OPEN_STATUS DESC";
elseif ($_REQUEST['sort_by'] == 'min-order')
    $QueryCommon .= " ORDER BY Main,minimumDeliveryAmount ASC,OPEN_STATUS DESC";
elseif ($_REQUEST['sort_by'] == 'deliver-fee')
    $QueryCommon .= " ORDER BY Main,deliveryFee ASC,OPEN_STATUS DESC";
elseif ($_REQUEST['sort_by'] == 'Highest Rated')
    $QueryCommon .= " ORDER BY Main ASC,average_reviews DESC,OPEN_STATUS DESC";
elseif ($_REQUEST['sort_by'] == 'Most Reviewed')
    $QueryCommon .= " ORDER BY Main ASC,total_reviews DESC,OPEN_STATUS DESC";
elseif ($_REQUEST['sort_by'] == 'Distance:nearest first')
    $QueryCommon .= " ORDER BY Main ASC,distance ASC,OPEN_STATUS DESC";
elseif ($_REQUEST['sort_by'] == 'Name')
    $QueryCommon .= " ORDER BY Main,merchantName ASC,OPEN_STATUS DESC";
elseif ($_REQUEST['sort_by'] == 'Latest')
    $QueryCommon .= " ORDER BY Main ASC,createdOn DESC,distance ASC,OPEN_STATUS DESC";
elseif ($_REQUEST['sort_by'] == 'Open')
    $QueryCommon .= " ORDER BY Main ASC,OPEN_STATUS DESC,distance ASC";
else
    $QueryCommon .= " ORDER BY isholla DESC,Main ASC,distance ASC,OPEN_STATUS DESC";


$Query = $Query1 . $QueryCommon;


$result = mysql_query($Query) or die("line 321:-" . mysql_error());
$TotalRecordCount = mysql_num_rows($result);

$Limits = 50;

$Page = mysql_real_escape_string($_REQUEST['page']);
if ($Page == "")
{
    $Page = 1;
}
$pageCount = ceil($TotalRecordCount / $Limits);
$StartLimit = (($Page - 1) * $Limits);

if ($TotalRecordCount > ($Page * $Limits)) {
    $EndLimit = $Page * $Limits;
} else {
    $EndLimit = $TotalRecordCount;
}

// chaded by logictree start

if ($mobile != 1)
    $sql1 = " LIMIT " . $StartLimit . "," . $Limits;
else
    $sql1 = '';

//chaged by logictree end


    $Default = 0;
    $SQL = $Query . $sql1;

$res = mysql_query($SQL) or die(mysql_error());
$data = array();
$i = 0;
$count = mysql_num_rows($res);

if ($count > 0) {
    while ($rec = mysql_fetch_assoc($res)) {
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
        if ($currentStatus == 'Close') {
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
        if ($rec['closed'] == "yes" || $currentStatus == 'Close') {
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

	    $query_img = "SELECT merchant.logo, items.itemImage FROM merchant
		INNER JOIN menus ON menus.merchantID=merchant.id
		INNER JOIN items ON items.menuID=menus.id where merchantID = '".$rec['id']."'";
		$result_img = mysql_query($query_img) or die(mysql_error());
    $MainPhoto = "";
		$row_img = mysql_fetch_assoc($result_img);
    //print_r($row_img); exit;
    // while($row_img = mysql_fetch_assoc($result_img)){
    //   if($row_img['itemImage']!="")
  	// 	{
    //     if(getimagesize("https://ab1500744ae37146b435-d4bb9b393d47327fd7ff71746382e858.ssl.cf5.rackcdn.com/".$row_img['itemImage'])) {
    //       $MainPhoto = $row_img['itemImage'];
    //     }
  	// 	}
    //   if($MainPhoto != ""){
    //     break;
    //   }
    // }
    // if(getimagesize("https://ab1500744ae37146b435-d4bb9b393d47327fd7ff71746382e858.ssl.cf5.rackcdn.com/".$row_img['itemImage'])) {
    //     $MainPhoto = $row_img['itemImage'];
    // } else {
    //     $row_img = mysql_fetch_assoc($result_img);
    //     $MainPhoto = $row_img['itemImage'];
    // }
    if($row_img['itemImage'] != "") {
      $MainPhoto = "https://ab1500744ae37146b435-d4bb9b393d47327fd7ff71746382e858.ssl.cf5.rackcdn.com/".$row_img['itemImage'];
    } else if($row_img['logo'] != ""){
       $MainPhoto = $merchant_logo_path.$row_img['logo'];
    } else {
      $MainPhoto = "https://ab1500744ae37146b435-d4bb9b393d47327fd7ff71746382e858.ssl.cf5.rackcdn.com/"."defaultImage.jpg";
    }


        /*
        if ($rec['Rest_Logo'] <> '')
        {
            $MainPhoto = $row_img['itemImage'];
        }
        else
        {
            $MainPhoto = "searchResultImage.jpg";
        }*/
        //$merchant_logo_path
        $rest_logo = $MainPhoto;





        $data[$i]['id'] = $rec['id'];
        $data[$i]['distance'] = $rec['distance'];
        $data[$i]['merchantName'] = $rec['merchantName'];
        $data[$i]['geoLat'] = $rec['geoLat'];
        $data[$i]['geoLong'] = $rec['geoLong'];
		$data[$i]['rating'] = $rec['average_reviews'];
        $data[$i]['tablebooking'] = $rec['tablebooking'];
        $data[$i]['contactAddress'] = $rec['contactAddress'];
        $data[$i]['postalCode'] = $rec['postalCode'];
        $data[$i]['delivery'] = $rec['delivery'];
        $data[$i]['takeout'] = $rec['takeout'];
        $data[$i]['Rest_Logo'] = $rest_logo;
        $data[$i]['cuisine_img'] = '';
        $data[$i]['minimumDeliveryAmount'] = $rec['minimumDeliveryAmount'];
        $data[$i]['cuisine'] = $rec['cuisine'];
        $data[$i]['deliveryFee'] = $rec['deliveryFee'];
        $data[$i]['timezone'] = $rec['timezone'];
        $data[$i]['city'] = $rec['city'];
        $data[$i]['state'] = $rec['state'];
        $data[$i]['status'] = $res_status;
        $data[$i]['day'] = $dayy;
        $data[$i]['time'] = $OpenAt . ' ' . $timezone;
        $data[$i]['isholla'] = $rec['isholla'];


        if($_REQUEST['user_id'] != '')
        {
        	$fav_status = "select status from tbl_favorite where user_id = '".$_REQUEST['user_id']."' and Dine_ID = '".$rec['id']."'";
        	$fav_status_que = mysql_query($fav_status);
        	$fav_count = mysql_num_rows($fav_status_que);
        	if($fav_count > 0)
        	{
        		$fav = mysql_fetch_assoc($fav_status_que);
        		$data[$i]['fav_status'] = $fav['status'];
        	}
        	else
        	{
        		$data[$i]['fav_status'] = '0';
        	}
        }
        else
        {
        	$data[$i]['fav_status'] = '0';
        }


        $i++;
    }

    /*------------- Cart Cound -----------*/
    if($_REQUEST['user_id'] != '')
    {
	    $cart_status = "select m.merchantName,m.minimumDeliveryAmount,c.* from tbl_cart as c left join merchant as m on m.id = c.Owner_Restaurant where c.Cart_UserID = '".$_REQUEST['user_id']."' order by c.Cart_ID desc";
	    $cart_status_que = mysql_query($cart_status);
	    $cart_res_id = mysql_fetch_assoc($cart_status_que);
	    $cart_count = mysql_num_rows($cart_status_que);
	    if($cart_count == 0)
	    {
	    	$restaurant_id = "";
    		$dinning_type = "";
    		$merchantName = "";
    		$minimumDeliveryAmount = "";
	    }
	    else
	    {
	    	$restaurant_id = $cart_res_id['Owner_Restaurant'];
	    	$dinning_type = $cart_res_id['Dinning_type'];
    		$merchantName = $cart_res_id['merchantName'];
    		$minimumDeliveryAmount = $cart_res_id['minimumDeliveryAmount'];
	    }
    }
    else
    {
    	$cart_status = "select m.merchantName,m.minimumDeliveryAmount,c.* from tbl_cart as c left join merchant as m on m.id = c.Owner_Restaurant where c.Sess_ID = '".$_REQUEST['device_id']."' order by c.Cart_ID desc";
	    $cart_status_que = mysql_query($cart_status);
	    $cart_res_id = mysql_fetch_assoc($cart_status_que);
	    $cart_count = mysql_num_rows($cart_status_que);
	    if($cart_count == 0)
	    {
	    	$cart_count = "0";
	    	$restaurant_id = "";
	    	$dinning_type = "";
	    	$minimumDeliveryAmount = "";
	    	$merchantName = "";
	    }
	    else
	    {
	    	$cart_count = $cart_count;
	    	$restaurant_id = $cart_res_id['Owner_Restaurant'];
	    	$dinning_type = $cart_res_id['Dinning_type'];
    		$merchantName = $cart_res_id['merchantName'];
    		$minimumDeliveryAmount = $cart_res_id['minimumDeliveryAmount'];
	    }

    }
    $output = array("status" => "1", "data" => $data,"cart_count"=>"$cart_count","restaurant_id"=>$restaurant_id,"type"=>$dinning_type,"merchantName"=>$merchantName,"minimumDeliveryAmount"=>$minimumDeliveryAmount);
    echo json_encode($output);
    exit;
}
else
{
    $output = array("status" => "0", "data" => "No restaurant found");
    echo json_encode($output);
    exit;
}

?>
