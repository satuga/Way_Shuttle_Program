<?php
include '../common/config.php';
$lat=26;
$long=73;
	$sql_lat_col="merchant.geoLat";
	$sql_long_col="merchant.geoLong";
		
		// Check with Expire Records
		$dt2 = date('Y-m-d H:i:s');
		$Weekday=idate("w");
		$today=time();
		$current_time = date("H:i:s",strtotime($dt2));
		$Time_Zone="-8.00";
		
		// Location Match Query 
		$Query1="SELECT (SELECT count(*) FROM merchant_hours AS INR_TBL WHERE INR_TBL.merchantID=merchant.id AND INR_TBL.weekDay=MH.weekDay AND ('".$current_time.".+((merchant.timezone-merchant.timezone)*60*60)' BETWEEN INR_TBL.startTime AND INR_TBL.endTime OR '".$current_time."' BETWEEN INR_TBL.startTimeOther AND INR_TBL.endTimeOther)) AS OPEN_STATUS,MH.closed, merchant.*,merchant.logo AS Rest_Logo,3956 * 2 * ASIN(SQRT(POWER(SIN(($lat - abs($sql_lat_col)) * pi()/180 / 2), 2) + COS($lat * pi()/180 ) * COS(abs($sql_lat_col) * pi()/180) * POWER(SIN(($long-$sql_long_col) * pi()/180 / 2), 2) )) as distance,1 AS Main 
		FROM merchant 
		INNER JOIN tbl_registeration ON tbl_registeration.id= merchant.Res_UserID
		INNER JOIN merchant_hours AS MH ON MH.merchantID= merchant.id
		INNER JOIN merchant_cuisine ON merchant_cuisine.merchantID= merchant.id
		INNER JOIN tbl_cuisine ON tbl_cuisine.Cuisine_ID= merchant_cuisine.cuisineID
		WHERE merchant.status='Active' AND merchant.Deleted=0 AND tbl_registeration.dine_control=1 AND MH.weekDay=".$Weekday;
		if ($_REQUEST['dinedescription']!="" && $_REQUEST['dinedescription']!='Where are you going?') {
			$desc=explode(",",mysql_real_escape_string($_REQUEST['dinedescription']));
			if(count($desc)>1)
			{
				$Loc=explode(",",mysql_real_escape_string($_REQUEST['dinedescription']));
				$Strlen=strlen($_REQUEST['dinedescription']);
				$cnt=count($Loc);
				$Strlen1=strlen($Loc[$cnt-2]);
				$Strlen2=strlen($Loc[$cnt-1]);
				$Strdiff=$Strlen-($Strlen1+$Strlen2);
				
				$Street=substr($_REQUEST['dinedescription'], 0,$Strdiff);
				$Street=trim($Street);
				$Street = substr($Street, 0, strlen($Street)-1);
				$Query1.=" AND merchant.city='".ucwords(trim($Loc[$cnt-2]))."'";
				$Query1.=" AND merchant.state='".ucwords(trim($Loc[$cnt-1]))."'";
				if($Street<>'')
					$Query1.=" AND merchant.contactAddress LIKE '%".trim($Street)."%'";
			}
			else
			{
				$desc1=explode(" ",$_REQUEST['dinedescription']);
				$Query1.=" AND (";
				$rr=1;
				foreach($desc1 as $desc)
				{
				$Query1.=" (merchant.merchantName LIKE '%".$desc."%' OR merchant.aboutUs LIKE '%".$desc."%' OR merchant.city LIKE '%".$desc."%' OR merchant.state LIKE '%".$desc."%' OR tbl_cuisine.Cuisine_Name LIKE '%".$desc."%') ";
				if(COUNT($desc1)<>$rr)
					$Query1.=" OR ";
				$rr++;
				}
				$Query1.=" ) ";
			}
		}
		else
		{
			if ($_REQUEST['City']!="" && $_REQUEST['City']!="City" && $_REQUEST['City']!="City Name") {
			$Query1.=" AND merchant.city='".ucwords($_REQUEST[City])."'";
			}
			if ($_REQUEST['State']!="" && $_REQUEST['State']!="State" && $_REQUEST['State']!="State Name") {
					$Query1.=" AND merchant.state='".ucwords(trim($_REQUEST[State]))."'";
			}
		}
		if ($_REQUEST['dinekeywords']!="" && $_REQUEST['dinekeywords']!='Search Keyword') {
			$keywordss=explode(",",trim(trim($_REQUEST['dinekeywords'],",")));
			$Query1.=" AND (";
				$rr=1;
				foreach($keywordss as $keywordss1)
				{
				$Query1.=" (merchant.merchantName LIKE '%".$keywordss1."%' OR merchant.aboutUs LIKE '%".$keywordss1."%' OR merchant.city LIKE '%".$keywordss1."%' OR merchant.state LIKE '%".$keywordss1."%' OR tbl_cuisine.Cuisine_Name LIKE '%".$keywordss1."%') ";
				if(COUNT($keywordss)<>$rr)
					$Query1.=" OR ";
				$rr++;
				}
				$Query1.=" ) ";
		}
		if ($_REQUEST['Cuisines']!="" && $_REQUEST['Cuisines']!="all") {
		$Cuisines=explode(",",$_REQUEST['Cuisines']);
		$Query1.=" AND (";
		$CU=0;
		foreach($Cuisines as $CC=>$CCC)
		{
			if($CU==0)
			$Query1.=" merchant_cuisine.cuisineID='".$CCC."'";
			else
			$Query1.=" OR merchant_cuisine.cuisineID='".$CCC."'";
			$CU++;
		}
		$Query1.=" ) ";
		}
		if($_REQUEST['dineOptional_Services']!="")
		{
			$Optional_Servicess=explode(",",$_REQUEST['dineOptional_Services']);
			if(count($Optional_Servicess)>0)
			{
				$Query1.= " AND (";
				$cnn=1;
				foreach($Optional_Servicess as $p=>$q)
				{
					if(count($Optional_Servicess)==$cnn)
						$Query1.= " merchant.contactAddress LIKE '%".$q."%' ";
					else
						$Query1.= " merchant.contactAddress LIKE '%".$q."%' OR ";
					$cnn++;
				}
				$Query1.= " ) ";
			}
		}
		if($_REQUEST['RANGE']<>'')
		{
			$RangeVal=explode("-",$_REQUEST['RANGE']);
			$Min=trim(trim($RangeVal[0]),'$');
			$Max=trim(trim($RangeVal[1]),'$');
			$Query1.=" AND (Min_Price>= '".$Min."' OR Max_Price<='".$Max."' ";
			$Query1.=" OR Min_Price<= '".$Max."' OR Max_Price<='".$Min."') ";
			//1-100   =>   10 - 80
		}
		if($_REQUEST['dineRev']!="")
		{
			/*$Reviews=explode(",",$_REQUEST['dineRev']);
			if(count($Reviews)>0)
			{*/
				$Query1.= " AND merchant.average_reviews>=".$_REQUEST['dineRev'];
			//}
		}
		if ($_REQUEST['Type']!="" && $_REQUEST['Type']!=",") {
			$Typevalues=explode(",",$_REQUEST['Type']);
			foreach($Typevalues as $p=>$q)
			{
				if($q=='delivery')
					$Query1.= " AND merchant.delivery='Yes'";
				else if($q=='pickup')
					$Query1.= " AND merchant.takeout='Yes'";
				else if($q=='free-delivery')
					$Query1.= " AND merchant.deliveryFee=0";
				else if($q=='free-delivery')
					$Query1.= " AND merchant.deliveryFee=0";
				else if($q=='coupons')
					$Query1.= " AND merchant.coupons>0 AND coupons_expiry > '".$dt2."'";
				else if($q=='Favorites')
					$Query1.= " AND merchant.favourites>0";
				else if($q=='Tablebooking')
					$Query1.= " AND merchant.tablebooking='Yes'";
				else if($q=='open')
					$Query1.= " AND ('".$current_time.".+((merchant.timezone-".$Time_Zone.")*60*60)' BETWEEN MH.startTime AND MH.endTime OR '".$current_time."' BETWEEN MH.startTimeOther AND MH.endTimeOther)";
					
					
			}
		}
		$Query1.= " AND (3956 * 2 * ASIN(SQRT(POWER(SIN(($lat - abs($sql_lat_col)) * pi()/180 / 2), 2) + COS($lat * pi()/180 ) * COS(abs($sql_lat_col) * pi()/180) * POWER(SIN(($long-$sql_long_col) * pi()/180 / 2), 2) )))<=100";
	  // echo $Query1;exit;
		$res1=mysql_query($Query1) or die("line 154:-".mysql_error());
		$RC1=mysql_num_rows($res1);	
		// Location that are Not Matched Query 
		$Query2="SELECT  (SELECT count(*) FROM merchant_hours AS INR_TBL WHERE INR_TBL.merchantID=merchant.id AND INR_TBL.weekDay=MH.weekDay AND ('".$current_time.".+((merchant.timezone-merchant.timezone)*60*60)' BETWEEN INR_TBL.startTime AND INR_TBL.endTime OR '".$current_time."' BETWEEN INR_TBL.startTimeOther AND INR_TBL.endTimeOther)) AS OPEN_STATUS,MH.closed, merchant.*,merchant.logo AS Rest_Logo,3956 * 2 * ASIN(SQRT(POWER(SIN(($lat - abs($sql_lat_col)) * pi()/180 / 2), 2) + COS($lat * pi()/180 ) * COS(abs($sql_lat_col) * pi()/180) * POWER(SIN(($long-$sql_long_col) * pi()/180 / 2), 2) )) as distance,2 AS Main 
		FROM merchant 
		INNER JOIN tbl_registeration ON tbl_registeration.id= merchant.Res_UserID
		INNER JOIN merchant_hours AS MH ON MH.merchantID= merchant.id
		INNER JOIN merchant_cuisine ON merchant_cuisine.merchantID= merchant.id
		INNER JOIN tbl_cuisine ON tbl_cuisine.Cuisine_ID= merchant_cuisine.cuisineID
		WHERE merchant.status='Active' AND merchant.Deleted=0  AND tbl_registeration.dine_control=1 AND MH.weekDay=".$Weekday;
		
		if ($_REQUEST['dinedescription']!="" && $_REQUEST['dinedescription']!='Where are you going?') {
			$desc=explode(",",$_REQUEST['dinedescription']);
			if(count($desc)>1)
			{
				$Loc=explode(",",$_REQUEST['dinedescription']);
				$Strlen=strlen($_REQUEST['dinedescription']);
				$cnt=count($Loc);
				$Strlen1=strlen($Loc[$cnt-2]);
				$Strlen2=strlen($Loc[$cnt-1]);
				$Strdiff=$Strlen-($Strlen1+$Strlen2);
				
				$Street=substr($_REQUEST['dinedescription'], 0,$Strdiff);
				$Street=trim($Street);
				$Street = substr($Street, 0, strlen($Street)-1);
				$Query1.=" AND merchant.city!='".ucwords(trim($Loc[$cnt-2]))."'";
				$Query1.=" AND merchant.state!='".ucwords(trim($Loc[$cnt-1]))."'";
				
				/*if(count($desc)==3) {
				$Query2.=" AND merchant.contactAddress LIKE '%".ucwords($desc[0])."%'";
				$Query2.=" AND merchant.city!='".ucwords($desc[1])."'";
				}
				else {
				$Query2.=" AND merchant.city!='".ucwords($desc[0])."'";
				}*/
			}
			else
			{
				$desc1=explode(" ",$_REQUEST['dinedescription']);
				$Query2.=" AND (";
				$rr=1;
				foreach($desc1 as $desc)
				{
				$Query2.=" (merchant.merchantName NOT LIKE '%".$desc."%' AND merchant.aboutUs NOT LIKE '%".$desc."%') ";
				if(COUNT($desc1)<>$rr)
					$Query2.=" AND ";
				$rr++;
				}
				$Query2.=" ) ";
				$LOCS=explode(",",$_COOKIE['LOC']);
				$Query2.=" AND merchant.city!='".ucwords($LOCS[0])."'";
			}
		}
		else
		{
			if ($_REQUEST['City']!="" && $_REQUEST['City']!="City" && $_REQUEST['City']!="City Name") {
			$Query2.=" AND merchant.city!='".ucwords($_REQUEST[City])."'";
			}
		}
		if ($_REQUEST['dinekeywords']!="" && $_REQUEST['dinekeywords']!='Search Keyword') {
			$keywordss=explode(",",trim(trim($_REQUEST['dinekeywords'],",")));
			$Query2.=" AND (";
				$rr=1;
				foreach($keywordss as $keywordss1)
				{
				$Query2.=" (merchant.merchantName LIKE '%".$keywordss1."%' OR merchant.aboutUs LIKE '%".$keywordss1."%' OR merchant.city LIKE '%".$keywordss1."%' OR merchant.state LIKE '%".$keywordss1."%'  OR tbl_cuisine.Cuisine_Name LIKE '%".$keywordss1."%') ";
				if(COUNT($keywordss)<>$rr)
					$Query2.=" OR ";
				$rr++;
				}
				$Query2.=" ) ";
		}
		if ($_REQUEST['Cuisines']!="" && $_REQUEST['Cuisines']!="all") {
		$Cuisines=explode(",",$_REQUEST['Cuisines']);
		$Query2.=" AND (";
		$CU=0;
		foreach($Cuisines as $CC=>$CCC)
		{
			if($CU==0)
			$Query2.=" merchant_cuisine.cuisineID='".$CCC."'";
			else
			$Query2.=" OR merchant_cuisine.cuisineID='".$CCC."'";
			$CU++;
		}
		$Query2.=" ) ";
		}
		if($_REQUEST['dineOptional_Services']!="")
		{
			$Optional_Servicess=explode(",",$_REQUEST['dineOptional_Services']);
			if(count($Optional_Servicess)>0)
			{
				$Query2.= " AND (";
				$cnn=1;
				foreach($Optional_Servicess as $p=>$q)
				{
					if(count($Optional_Servicess)==$cnn)
						$Query2.= " merchant.contactAddress LIKE '%".$q."%' ";
					else
						$Query2.= " merchant.contactAddress LIKE '%".$q."%' OR ";
					$cnn++;
				}
				$Query2.= " ) ";
			}
		}
		if($_REQUEST['RANGE']<>'')
		{
			$RangeVal=explode("-",$_REQUEST['RANGE']);
			$Min=trim(trim($RangeVal[0]),'$');
			$Max=trim(trim($RangeVal[1]),'$');
			$Query2.=" AND (Min_Price>= '".$Min."' OR Max_Price<='".$Max."' ";
			$Query2.=" OR Min_Price<= '".$Max."' OR Max_Price<='".$Min."') ";
		}
		if($_REQUEST['dineRev']!="")
		{
			/*$Reviews=explode(",",$_REQUEST['dineRev']);
			if(count($Reviews)>0)
			{*/
				$Query2.= " AND merchant.average_reviews>=".$_REQUEST['dineRev'];
			//}
		}
		if ($_REQUEST['Type']!="" && $_REQUEST['Type']!=",") {
			$Typevalues=explode(",",$_REQUEST['Type']);
			foreach($Typevalues as $p=>$q)
			{
				if($q=='delivery')
					$Query2.= " AND merchant.delivery='Yes'";
				else if($q=='pickup')
					$Query2.= " AND merchant.takeout='Yes'";
				else if($q=='free-delivery')
					$Query2.= " AND merchant.deliveryFee=0";
				else if($q=='coupons')
					$Query2.= " AND merchant.coupons>0 AND coupons_expiry > '".$dt2."'";
				else if($q=='Favorites')
					$Query2.= " AND merchant.favourites>0";
				else if($q=='Tablebooking')
					$Query2.= " AND merchant.tablebooking='Yes'";
				else if($q=='open')
					$Query2.= " AND ('".$current_time.".+((merchant.timezone-".$Time_Zone.")*60*60)' BETWEEN MH.startTime AND MH.endTime OR '".$current_time."' BETWEEN MH.startTimeOther AND MH.endTimeOther)";
			}
		}
		//$Query2.= " AND (3956 * 2 * ASIN(SQRT(POWER(SIN(($lat - abs($sql_lat_col)) * pi()/180 / 2), 2) + COS($lat * pi()/180 ) * COS(abs($sql_lat_col) * pi()/180) * POWER(SIN(($long-$sql_long_col) * pi()/180 / 2), 2) )))<=".$_REQUEST['MILES'];
		$QueryCommon= " GROUP BY merchant.id";
		if($_REQUEST['sort_by']=='Top Results')
			$QueryCommon.= " ORDER BY Main ASC,clicks DESC,OPEN_STATUS DESC";
		else if($_REQUEST['sort_by']=='min-order')
			$QueryCommon.= " ORDER BY Main,minimumDeliveryAmount ASC,OPEN_STATUS DESC";
		else if($_REQUEST['sort_by']=='deliver-fee')
			$QueryCommon.= " ORDER BY Main,deliveryFee ASC,OPEN_STATUS DESC";
		else if($_REQUEST['sort_by']=='Highest Rated')
			$QueryCommon.= " ORDER BY Main ASC,average_reviews DESC,OPEN_STATUS DESC";
		else if($_REQUEST['sort_by']=='Most Reviewed')
			$QueryCommon.= " ORDER BY Main ASC,total_reviews DESC,OPEN_STATUS DESC";
		else if($_REQUEST['sort_by']=='Distance:nearest first')
			$QueryCommon.= " ORDER BY Main ASC,distance ASC,OPEN_STATUS DESC";
		else if($_REQUEST['sort_by']=='Name')
			$QueryCommon.= " ORDER BY Main,merchantName ASC,OPEN_STATUS DESC";
		else if($_REQUEST['sort_by']=='Latest')
			$QueryCommon.= " ORDER BY Main ASC,createdOn DESC,distance ASC,OPEN_STATUS DESC";
		else if($_REQUEST['sort_by']=='Open')
			$QueryCommon.= " ORDER BY Main ASC,OPEN_STATUS DESC,distance ASC";
		else
			$QueryCommon.= " ORDER BY Main ASC,distance ASC,OPEN_STATUS DESC";
		//$QueryCommon= " GROUP BY merchant.id ORDER BY OPEN_STATUS DESC";
		
		$QueryJoin= " UNION ";
	  $Query=$Query1.$QueryJoin.$Query2.$QueryCommon;
		//echo $Query=$Query1.$QueryCommon;
		$result=mysql_query($Query) or die("line 321:-".mysql_error());
		$TotalRecordCount=mysql_num_rows($result);

		$Totalpages 		= mysql_query("SELECT * FROM `tbl_control`") or die("line 324:-".mysql_error());
		$exec_paging		= mysql_fetch_array($Totalpages);
		//$end_count 			= $exec_paging['no_of_pages'];
		$Limits				= $exec_paging['results_per_page'];
        
		$Page = mysql_real_escape_string($_REQUEST['page']);
		if($Page==""){
			$Page=1;
		}
		$pageCount = ceil($TotalRecordCount/$Limits); 
		 $StartLimit = (($Page-1)*$Limits);
		
		if($TotalRecordCount > ($Page*$Limits)){
				$EndLimit = $Page*$Limits;
		}else{
				$EndLimit = $TotalRecordCount;
		}
		
		// chaded by logictree start
		
		if($mobile!=1)
		$sql1=" LIMIT ".$StartLimit.",".$Limits;
		else
		$sql1= '';
		
		//chaged by logictree end
		
		$Default=0;
		if($TotalRecordCount<1)
		{
			// Show Default Listings
			$Query1="SELECT  (SELECT count(*) FROM merchant_hours AS INR_TBL WHERE INR_TBL.merchantID=merchant.id AND INR_TBL.weekDay=MH.weekDay AND ('".$current_time.".+((merchant.timezone-merchant.timezone)*60*60)' BETWEEN INR_TBL.startTime AND INR_TBL.endTime OR '".$current_time."' BETWEEN INR_TBL.startTimeOther AND INR_TBL.endTimeOther)) AS OPEN_STATUS,MH.closed, merchant.*,merchant.logo AS Rest_Logo,";
			if($lat=='' || $long=='')
				$Query1.=" 0 AS distance, ";
			else
				$Query1.=" 3956 * 2 * ASIN(SQRT(POWER(SIN(($lat - abs($sql_lat_col)) * pi()/180 / 2), 2) + COS($lat * pi()/180 ) * COS(abs($sql_lat_col) * pi()/180) * POWER(SIN(($long-$sql_long_col) * pi()/180 / 2), 2) )) as distance, ";
			
			$Query1.=" 2 AS Main 
			FROM merchant 
			INNER JOIN tbl_registeration ON tbl_registeration.id= merchant.Res_UserID
			INNER JOIN merchant_hours AS MH ON MH.merchantID= merchant.id
			INNER JOIN merchant_cuisine ON merchant_cuisine.merchantID= merchant.id
			WHERE merchant.status='Active' AND merchant.Deleted=0  AND tbl_registeration.dine_control=1 AND MH.weekDay=".$Weekday;
			
			$QueryCommon= " GROUP BY merchant.id";
			if($_REQUEST['sort_by']=='Top Results')
				$QueryCommon.= " ORDER BY Main ASC,clicks DESC,OPEN_STATUS DESC";
			else if($_REQUEST['sort_by']=='min-order')
				$QueryCommon.= " ORDER BY Main,minimumDeliveryAmount ASC,OPEN_STATUS DESC";
			else if($_REQUEST['sort_by']=='deliver-fee')
				$QueryCommon.= " ORDER BY Main,deliveryFee ASC,OPEN_STATUS DESC";
			else if($_REQUEST['sort_by']=='Highest Rated')
				$QueryCommon.= " ORDER BY Main ASC,average_reviews DESC,OPEN_STATUS DESC";
			else if($_REQUEST['sort_by']=='Most Reviewed')
				$QueryCommon.= " ORDER BY Main ASC,total_reviews DESC,OPEN_STATUS DESC";
			else if($_REQUEST['sort_by']=='Distance:nearest first')
				$QueryCommon.= " ORDER BY Main ASC,distance ASC,OPEN_STATUS DESC";
			else if($_REQUEST['sort_by']=='Name')
				$QueryCommon.= " ORDER BY Main,merchantName ASC,OPEN_STATUS DESC";
			else if($_REQUEST['sort_by']=='Latest')
				$QueryCommon.= " ORDER BY Main ASC,createdOn DESC,distance ASC,OPEN_STATUS DESC";
			else
				$QueryCommon.= " ORDER BY Main ASC,distance ASC,OPEN_STATUS DESC";
			
			$SQL=$Query1.$QueryCommon;
			$result=mysql_query($SQL) or die("line 390:-".mysql_error());
			$TotalRecordCount=mysql_num_rows($result);
			$SQL=$SQL.$sql1;
			$Default=1;
			// End Show Default Listings
		}
		else
		{
		$Default=0;
		 $SQL=$Query.$sql1;
		}
		$res=mysql_query($SQL) or die(mysql_error());
        while($row=mysql_fetch_assoc($res)){
            $data[]=$row;
        }
        echo json_encode($data);

?>                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                