<?php
    
	
		$lat=37.36883;
		$long=-122.0363496;
		$sql_lat_col="tbl_parkinglocations.lat";
		$sql_long_col="tbl_parkinglocations.lon";
		
		// Check with Expire Records
		$dt2 = date('Y-m-d H:i:s');
		
		$Query1="SELECT tbl_parkinglocations.lat AS Lattitude,tbl_parkinglocations.lon AS Longitude,tbl_parking.*,tbl_parkinglocations.*,tbl_registeration.*,3956 * 2 * ASIN(SQRT(POWER(SIN(($lat - abs($sql_lat_col)) * pi()/180 / 2), 2) + COS($lat * pi()/180 ) * COS(abs($sql_lat_col) * pi()/180) * POWER(SIN(($long-$sql_long_col) * pi()/180 / 2), 2) )) as distance,1 AS Main,tbl_parkinglocations.P_Parkingextras AS P_LocationParkingextras,tbl_parkinglocations.P_Parkingattributes AS P_LocationParkingattributes  FROM tbl_parking 
		INNER JOIN tbl_parkinglocations ON tbl_parkinglocations.Park_ID=tbl_parking.P_Location
		INNER JOIN tbl_registeration ON tbl_registeration.id=tbl_parking.P_UserID
		WHERE tbl_parking.P_Status=1 AND tbl_registeration.parking_control=1 AND Park_Location_Status=1 ";
		
		if($_REQUEST['Optional_Services']!="")
		{
			$Optional_Servicess=explode(",",$_REQUEST['Optional_Services']);
			if(count($Optional_Servicess)>0)
			{
				$Query1.= " AND (";
				$cnn=1;
				foreach($Optional_Servicess as $p=>$q)
				{
					if(count($Optional_Servicess)==$cnn)
						$Query1.= " tbl_parkinglocations.Park_Address LIKE '%".$q."%' ";
					else
						$Query1.= " tbl_parkinglocations.Park_Address LIKE '%".$q."%' OR ";
					$cnn++;
				}
				$Query1.= " ) ";
			}
		}
		
		if($_REQUEST['Type']!='')
		{
			$Typesarr=explode(",",$_REQUEST['Type']);
			$Typesarr=array_unique($Typesarr);
			if(count($Typesarr)>0)
			{
				$tc=0;
				foreach($Typesarr as $p=>$q)
				{
				
					if($_REQUEST['typechk']=='false' && $_REQUEST['type']==$q)
						continue;
					if($tc==0 && $q<>'airport')
						$Typ1= "'".$q."'";
					else if($q<>'airport')
						$Typ1.= ",'".$q."'";
					if($q=='airport')
							$Query1.= " AND Park_AirportVenue=1";
					if($tc==0)
						$Typ= "'".$q."'";
					else
						$Typ.= ",'".$q."'";	
						
						if($q=='airport')
				
					$tc++;
				}
				$Typ1=trim($Typ1);
				$Typ1=trim($Typ1,",");
				if($Typ1<>'' && $Typ1<>"'city'")
					 $Query1.= " AND P_Pricingtype IN (".$Typ1.")";
				if($Typ1=="'city'" || $Typ1<>'city,airport')
					$Query2.= " AND Park_AirportVenue=0";
			}
			$Typ=str_replace("'","",$Typ);
			$_REQUEST['Type']=$Typ;
			/*else
			{
				$Query1.= " AND P_Pricingtype='".$_REQUEST['Type']."'";
			}*/
		}
		if($_REQUEST['RANGE']<>'')
		{
			$RangeVal=explode("-",$_REQUEST['RANGE']);
			$Min=trim(trim($RangeVal[0]),'$');
			$Max=trim(trim($RangeVal[1]),'$');
			$Query1.=" AND (tbl_parking.Average_Price >= '".$Min."' AND tbl_parking.Average_Price<='".$Max."')";
		}
		if($_REQUEST['parkingRev']!="")
		{
			/*$Reviews=explode(",",$_REQUEST['parkingRev']);
			if(count($Reviews)>0)
			{*/
				$Query1.= " AND tbl_parking.average_reviews>=".$_REQUEST['parkingRev'];
			//}
		}
		if ($_REQUEST['parkingdescription']!="" && $_REQUEST['parkingdescription']!='Where do you need Parking?') {
			$desc=explode(",",$_REQUEST['parkingdescription']);
			if(count($desc)>1)
			{
				$Loc=explode(",",$_REQUEST['parkingdescription']);
				$Strlen=strlen($_REQUEST['parkingdescription']);
				$cnt=count($Loc);
				$Strlen1=strlen($Loc[$cnt-2]);
				$Strlen2=strlen($Loc[$cnt-1]);
				$Strdiff=$Strlen-($Strlen1+$Strlen2);
				
				$Street=substr($_REQUEST['parkingdescription'], 0,$Strdiff);
				$Street=trim($Street);
				$Street = substr($Street, 0, strlen($Street)-1);
				$Query1.=" AND tbl_parkinglocations.Park_City='".ucwords(trim($Loc[$cnt-2]))."'";
				$Query1.=" AND tbl_parkinglocations.Park_State='".ucwords(trim($Loc[$cnt-1]))."'";
			}
			else
			{
				$desc1=explode(" ",$_REQUEST['parkingdescription']);
				$Query1.=" AND (";
				$rr=1;
				foreach($desc1 as $desc)
				{
				$Query1.=" (Airport_Near_Address LIKE '%".$desc."%' OR Park_Name LIKE '%".$desc."%' OR P_Specificdestination LIKE '%".$desc."%' OR tbl_parking.P_Instructions LIKE '%".$desc."%' OR tbl_parkinglocations.P_Instructions LIKE '%".$desc."%' OR parking_specific LIKE '%".$desc."%' OR tbl_parkinglocations.Park_Address LIKE '%".$desc."%' OR tbl_parkinglocations.Park_City LIKE '%".$desc."%' OR tbl_parkinglocations.Park_State LIKE '%".$desc."%') ";
				if(COUNT($desc1)<>$rr)
					$Query1.=" OR ";
				$rr++;
				}
				$Query1.=" ) ";
				
				//$Query1.=" AND (P_Specificdestination LIKE '%".$_REQUEST['parkingdescription']."%' OR P_Instructions LIKE '%".$_REQUEST['parkingdescription']."%' OR parking_specific LIKE '%".$_REQUEST['parkingdescription']."%' OR tbl_parkinglocations.Park_Address LIKE '%".$_REQUEST['parkingdescription']."%')";
				if ($_REQUEST['City']!="" && $_REQUEST['City']!="City" && $_REQUEST['City']!="City Name") {
				$Query1.=" AND tbl_parkinglocations.Park_City='".ucwords($_REQUEST[City])."'";
				}
				if ($_REQUEST['State']!="" && $_REQUEST['State']!="State" && $_REQUEST['State']!="State Name") {
					$Query1.=" AND tbl_parkinglocations.Park_State='".ucwords(trim($_REQUEST[State]))."'";
				}
			}
		} else
		{
			if ($_REQUEST['City']!="" && $_REQUEST['City']!="City" && $_REQUEST['City']!="City Name") {
			$Query1.=" AND tbl_parkinglocations.Park_City='".ucwords($_REQUEST[City])."'";
			}
			if ($_REQUEST['State']!="" && $_REQUEST['State']!="State" && $_REQUEST['State']!="State Name") {
				$Query1.=" AND tbl_parkinglocations.Park_State='".ucwords(trim($_REQUEST[State]))."'";
			}
		}
		
		if($_REQUEST['typeloc']!='' && $_REQUEST['typeloc']!='All')
			$Query1.= " AND Park_Typeoflocation='".$_REQUEST['typeloc']."'";
		$Query1.= " AND (3956 * 2 * ASIN(SQRT(POWER(SIN(($lat - abs($sql_lat_col)) * pi()/180 / 2), 2) + COS($lat * pi()/180 ) * COS(abs($sql_lat_col) * pi()/180) * POWER(SIN(($long-$sql_long_col) * pi()/180 / 2), 2) )))<=".$_REQUEST['MILES'];
		
		$res1=mysqli_query($con,$Query1);
		$RC1=mysqli_num_rows($res1);		
		// Location that are not matched
		$Query2="SELECT tbl_parkinglocations.lat AS Lattitude,tbl_parkinglocations.lon AS Longitude,tbl_parking.*,tbl_parkinglocations.*,tbl_registeration.*,3956 * 2 * ASIN(SQRT(POWER(SIN(($lat - abs($sql_lat_col)) * pi()/180 / 2), 2) + COS($lat * pi()/180 ) * COS(abs($sql_lat_col) * pi()/180) * POWER(SIN(($long-$sql_long_col) * pi()/180 / 2), 2) )) as distance,2 AS Main,tbl_parkinglocations.P_Parkingextras AS P_LocationParkingextras,tbl_parkinglocations.P_Parkingattributes AS P_LocationParkingattributes  FROM tbl_parking 
		INNER JOIN tbl_parkinglocations ON tbl_parkinglocations.Park_ID=tbl_parking.P_Location
		INNER JOIN tbl_registeration ON tbl_registeration.id=tbl_parking.P_UserID
		WHERE tbl_parking.P_Status=1 AND tbl_registeration.parking_control=1 AND Park_Location_Status=1 ";
		if($_REQUEST['Optional_Services']!="")
		{
			$Optional_Servicess=explode("--",$_REQUEST['Optional_Services']);
			if(count($Optional_Servicess)>0)
			{
				$Query2.= " AND (";
				$cnn=1;
				foreach($Optional_Servicess as $p=>$q)
				{
					if(count($Optional_Servicess)==$cnn)
						$Query2.= " tbl_parkinglocations.Park_Address LIKE '%".$q."%' ";
					else
						$Query2.= " tbl_parkinglocations.Park_Address LIKE '%".$q."%' OR ";
					$cnn++;
				}
				$Query2.= " ) ";
			}
		}
		
		if($_REQUEST['Type']!='')
		{
			$Typesarr=explode(",",$_REQUEST['Type']);
			$Typesarr=array_unique($Typesarr);
			if(count($Typesarr)>0)
			{
				$tc=0;
				foreach($Typesarr as $p=>$q)
				{
					
					if($_REQUEST['typechk']=='false' && $_REQUEST['type']==$q)
						continue;
					if($tc==0 && $q<>'airport')
						$Typ1= "'".$q."'";
					else if($q<>'airport')
						$Typ1.= ",'".$q."'";
						
					if($tc==0)
						$Typ= "'".$q."'";
					else
						$Typ.= ",'".$q."'";
					if($q=='airport')
							$Query2.= " AND Park_AirportVenue=1";
					
					$tc++;
				}
				$Typ1=str_replace(',city','',$Typ1);
				$Typ1=trim($Typ1);
				$Typ1=trim($Typ1,",");
				if($Typ1<>'' && $Typ1<>"'city'")
					 $Query2.= " AND P_Pricingtype IN (".$Typ1.")";
				if($Typ1=='city')
					$Query2.= " AND Park_AirportVenue=0";
			}
			$Typ=str_replace("'","",$Typ);
			
			$_REQUEST['Type']=$Typ;
			/*else
			{
				$Query2.= " AND P_Pricingtype='".$_REQUEST['Type']."'";
			}*/
		}
		if($_REQUEST['RANGE']<>'')
		{
			$RangeVal=explode("-",$_REQUEST['RANGE']);
			$Min=trim(trim($RangeVal[0]),'$');
			$Max=trim(trim($RangeVal[1]),'$');
			$Query2.=" AND (tbl_parking.Average_Price >= '".$Min."' AND tbl_parking.Average_Price<='".$Max."')";
		}
		if($_REQUEST['parkingRev']!="")
		{
			/*$Reviews=explode(",",$_REQUEST['parkingRev']);
			if(count($Reviews)>0)
			{*/
				$Query2.= " AND tbl_parking.average_reviews>=".$_REQUEST['parkingRev'];
			//}
		}
		
		if ($_REQUEST['parkingdescription']!="" && $_REQUEST['parkingdescription']!='Where do you need Parking?') {
			$desc=explode(",",$_REQUEST['parkingdescription']);
			if(count($desc)>1)
			{
				$Query2.=" AND tbl_parkinglocations.Park_City!='".ucwords(trim($desc[0]))."'";
				//$Query2.=" AND tbl_parkinglocations.Park_State='".ucwords(trim($desc[1]))."'";
			}
			else
			{
				$desc1=explode(" ",$_REQUEST['parkingdescription']);
				$Query2.=" AND (";
				$rr=1;
				foreach($desc1 as $desc)
				{
				$Query2.=" (Airport_Near_Address LIKE '%".$desc."%' OR Park_Name LIKE '%".$desc."%' OR P_Specificdestination LIKE '%".$desc."%' OR tbl_parking.P_Instructions LIKE '%".$desc."%' OR tbl_parkinglocations.P_Instructions LIKE '%".$desc."%' OR parking_specific LIKE '%".$desc."%' OR tbl_parkinglocations.Park_Address LIKE '%".$desc."%' OR tbl_parkinglocations.Park_City LIKE '%".$desc."%' OR tbl_parkinglocations.Park_State LIKE '%".$desc."%') ";
				if(COUNT($desc1)<>$rr)
					$Query2.=" OR ";
				$rr++;
				}
				$Query2.=" ) ";
				//$Query2.=" AND (P_Specificdestination LIKE '%".$_REQUEST['parkingdescription']."%' OR P_Instructions LIKE '%".$_REQUEST['parkingdescription']."%' OR parking_specific LIKE '%".$_REQUEST['parkingdescription']."%' OR tbl_parkinglocations.Park_Address LIKE '%".$_REQUEST['parkingdescription']."%')";
				if ($_REQUEST['City']!="" && $_REQUEST['City']!="City" && $_REQUEST['City']!="City Name") {
				$Query2.=" AND tbl_parkinglocations.Park_City!='".ucwords($_REQUEST[City])."'";
				}
			}
		} else
		{
			if ($_REQUEST['City']!="" && $_REQUEST['City']!="City" && $_REQUEST['City']!="City Name") {
			$Query2.=" AND tbl_parkinglocations.Park_City!='".ucwords($_REQUEST[City])."'";
			}
			/*if ($_REQUEST['State']!="" && $_REQUEST['State']!="State" && $_REQUEST['State']!="State Name") {
				$Query2.=" AND tbl_parkinglocations.Park_State='".ucwords($_REQUEST[State])."'";
			}*/
		}
		
		if($_REQUEST['typeloc']!='' && $_REQUEST['typeloc']!='All')
			$Query2.= " AND Park_Typeoflocation='".$_REQUEST['typeloc']."'";
		$Query2.= " AND (3956 * 2 * ASIN(SQRT(POWER(SIN(($lat - abs($sql_lat_col)) * pi()/180 / 2), 2) + COS($lat * pi()/180 ) * COS(abs($sql_lat_col) * pi()/180) * POWER(SIN(($long-$sql_long_col) * pi()/180 / 2), 2) )))<=".$_REQUEST['MILES'];
		//$QueryCommon= " GROUP BY P_ID ";	
		$QueryCommon= " GROUP BY tbl_parking.P_ID";
		//echo $_REQUEST['sort_by'];
		if($_REQUEST['sort_by']=='Name')
			$QueryCommon.= " ORDER BY Main ASC,Park_Name ASC";
		else if($_REQUEST['sort_by']=='Top Results')
			$QueryCommon.= " ORDER BY Main ASC,distance ASC,Todaysbookings DESC";
		else if($_REQUEST['sort_by']=='Price:lowest first')
			$QueryCommon.= " ORDER BY Main ASC,Average_Price ASC";
		else if($_REQUEST['sort_by']=='Price:highest first')
			$QueryCommon.= " ORDER BY Main ASC,Average_Price DESC";
		else if($_REQUEST['sort_by']=='Highest Rated')
			$QueryCommon.= " ORDER BY Main ASC,average_reviews DESC";
		else if($_REQUEST['sort_by']=='Most Reviewed')
			$QueryCommon.= " ORDER BY Main ASC,total_reviews DESC";
		else if($_REQUEST['sort_by']=='Distance:nearest first')
			$QueryCommon.= " ORDER BY distance ASC,Todaysbookings DESC";
		else if($_REQUEST['sort_by']=='Latest')
			$QueryCommon.= " ORDER BY Main ASC, P_Created DESC";
		else
			$QueryCommon.= " ORDER BY Main ASC,ROUND(distance) ASC,Todaysbookings DESC";
			
		
		$QueryJoin= " UNION ";
		
		// parking Availability Take checking
		if($_REQUEST['CHECKOUT']<>'' && $_REQUEST['CHECKIN']<>'')
		{
		
			$Query=$Query1.$QueryJoin.$Query2.$QueryCommon;	
		}
		else
		{
			$Query=$Query1.$QueryJoin.$Query2.$QueryCommon;	
		}
		
		$result=mysqli_query($con,$Query);
		$TotalRecordCount=mysqli_num_rows($result);
		// For Previous Next in details page
		$record1=array();
		$j=0;
		while($rec=mysqli_fetch_array($result))
		{
			$record1[$j]['Service_id']			=	$rec['LLid'];	
			$j++;
		}
		// End For Previous Next in details page

		$Totalpages 		= mysqli_query($con,"SELECT * FROM `tbl_control`");
		$exec_paging		= mysqli_fetch_array($Totalpages);
		//$end_count 			= $exec_paging['no_of_pages'];
		$Limits				= $exec_paging['results_per_page'];

		$Limits = $Limits;
		$Page = $_REQUEST['page'];
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
		$sql1=" LIMIT ".$StartLimit.",".$Limits;
		$SQL=$Query.$sql1;
		
		$Default=0;
		if($TotalRecordCount<1)
		{
			// Show Default Listings
			$Query1="SELECT tbl_parkinglocations.lat AS Lattitude,tbl_parkinglocations.lon AS Longitude,tbl_parking.*,tbl_parkinglocations.*,tbl_registeration.*,3956 * 2 * ASIN(SQRT(POWER(SIN(($lat - abs($sql_lat_col)) * pi()/180 / 2), 2) + COS($lat * pi()/180 ) * COS(abs($sql_lat_col) * pi()/180) * POWER(SIN(($long-$sql_long_col) * pi()/180 / 2), 2) )) as distance,1 AS Main,tbl_parkinglocations.P_Parkingextras AS P_LocationParkingextras,tbl_parkinglocations.P_Parkingattributes AS P_LocationParkingattributes  FROM tbl_parking 
			INNER JOIN tbl_parkinglocations ON tbl_parkinglocations.Park_ID=tbl_parking.P_Location
			INNER JOIN tbl_registeration ON tbl_registeration.id=tbl_parking.P_UserID
			WHERE tbl_parking.P_Status=1 AND tbl_registeration.parking_control=1 AND Park_Location_Status=1 ";
			
			$QueryCommon= " GROUP BY tbl_parking.P_ID";
		
			if($_REQUEST['sort_by']=='Name')
				$QueryCommon.= " ORDER BY Main ASC,Park_Name ASC";
			else if($_REQUEST['sort_by']=='Top Results')
				$QueryCommon.= " ORDER BY Main ASC,clicks DESC";
			else if($_REQUEST['sort_by']=='Price:lowest first')
				$QueryCommon.= " ORDER BY Main ASC,Average_Price ASC";
			else if($_REQUEST['sort_by']=='Price:highest first')
				$QueryCommon.= " ORDER BY Main ASC,Average_Price DESC";
			else if($_REQUEST['sort_by']=='Highest Rated')
				$QueryCommon.= " ORDER BY Main ASC,average_reviews DESC";
			else if($_REQUEST['sort_by']=='Most Reviewed')
				$QueryCommon.= " ORDER BY Main ASC,total_reviews DESC";
			else if($_REQUEST['sort_by']=='Distance:nearest first' && $_REQUEST['Zip']>0 && $_REQUEST['Zipvalid']==1)
				$QueryCommon.= " ORDER BY distance ASC";
			else if($_REQUEST['sort_by']=='Latest')
				$QueryCommon.= " ORDER BY Main ASC, P_Created DESC";
			else
				$QueryCommon.= " ORDER BY Main ASC,ROUND(distance) ASC,Todaysbookings DESC";
			
			$SQL=$Query1.$QueryCommon;
			$result=mysqli_query($con,$SQL);
			$TotalRecordCount=mysqli_num_rows($result);
			$SQL=$SQL.$sql1;
			$Default=1;
			// End Show Default Listings
		}
		else
		{
		$Default=0;
		$SQL=$Query.$sql1;
		}
		$res=mysqli_query($con,$SQL);
		
		$record=array();
        $i=0;
	while($rec=mysqli_fetch_array($res))
	{
		$record[$i]['id']			=	$rec['id'];
		$record[$i]['P_UserID']			=	$rec['P_UserID'];
		$record[$i]['distance']			=	$rec['distance'];
		$record[$i]['lat']			=	$rec['Lattitude'];
		$record[$i]['lon']			=	$rec['Longitude'];
		
		$record[$i]['Main']		=	$rec['Main'];
		$record[$i]['P_ID']			=	$rec['P_ID'];
		$record[$i]['P_Company']	=	$rec['P_Company'];
		$record[$i]['P_Location']	=	$rec['P_Location'];
		$record[$i]['P_Spots']		=	$rec['P_Spots'];
		$record[$i]['Park_Name']		=	$rec['Park_Name'];
		$record[$i]['parking_specific']		=	$rec['parking_specific'];
		$record[$i]['P_Specificdestination'] =	$rec['P_Specificdestination'];
		$record[$i]['P_LocationParkingattributes']	=	$rec['P_LocationParkingattributes'];
		$record[$i]['P_Instructions']=$rec['P_Instructions'];
		$record[$i]['Park_Image']=$rec['Park_Image'];
		$record[$i]['Park_Logo']=$rec['Park_Logo'];
		$record[$i]['P_Pricingtype']			=	$rec['P_Pricingtype'];
		$record[$i]['Special_Price_Desc']			=	$rec['Special_Price_Desc'];
		$record[$i]['P_Dailyprice']			=	$rec['P_Dailyprice'];
		$record[$i]['P_Weeklyprice']			=	$rec['P_Weeklyprice'];
		$record[$i]['P_Monthlyprice']			=	$rec['P_Monthlyprice'];
		$record[$i]['Park_Address']			=	$rec['Park_Address'];
		$record[$i]['Park_City']			=	$rec['Park_City'];
		$record[$i]['Park_State']			=	$rec['Park_State'];
		$record[$i]['P_Free_Shuffle']			=	$rec['P_Free_Shuffle'];
		$record[$i]['P_H1']			=	$rec['P_H1'];
		$record[$i]['P_FMIN']			=	$rec['P_FMIN'];
		$record[$i]['P_P1']			=	$rec['P_P1'];
		$record[$i]['P_H2']			=	$rec['P_H2'];
		$record[$i]['P_P2']			=	$rec['P_P2'];
		$record[$i]['P_H3']			=	$rec['P_H3'];
		$record[$i]['P_Lot_Type']			=	$rec['P_Lot_Type'];
		$record[$i]['Event_price']			=	$rec['Event_price'];
		$record[$i]['P_Daily_Price_Type']			=	$rec['P_Daily_Price_Type'];
		$record[$i]['P_Daily']			=	$rec['P_Daily'];
		$record[$i]['P_Created']			=	$rec['P_Created'];
		$record[$i]['Park_City']=	$rec['Park_City'];
		$record[$i]['P_Parkingextras']=$rec['P_LocationParkingextras'];
		$record[$i]['Park_Airport']=$rec['Park_Airport'];
		$record[$i]['Park_AirportVenue']=$rec['Park_AirportVenue'];
		$record[$i]['P_Airport_Distance']=$rec['P_Airport_Distance'];
		$record[$i]['P_Event_Distance']=$rec['P_Event_Distance'];
		$record[$i]['Park_EventVenue']=$rec['Park_EventVenue'];
		$record[$i]['Park_EventCity']=$rec['Park_EventCity'];
		$record[$i]['P_Event_Distance']=$rec['P_Event_Distance'];
		$record[$i]['Airport_Near_Address']=$rec['Airport_Near_Address'];
		$record[$i]['Airport_Near_Address_ID']=$rec['Airport_Near_Address_ID'];	
		
		$record[$i]['P_FMIN']		=	$rec['P_FMIN'];
		$record[$i]['P_FAmt']		=	$rec['P_FAmt'];
		$record[$i]['P_IncMin']		=	$rec['P_IncMin'];
		$record[$i]['P_IncAmt']		=	$rec['P_IncAmt'];
		$record[$i]['P_MaxMinEnable']		=	$rec['P_MaxMinEnable'];
		$record[$i]['P_MaxMin']		=	$rec['P_MaxMin'];
		$record[$i]['P_MaxAmt']		=	$rec['P_MaxAmt'];
	
		
		$record[$i]['firstname']			=	$rec['firstname'];
		$record[$i]['lastname']			=	$rec['lastname'];
		$record[$i]['display_name']		=	$rec['display_name'];
		$record[$i]['street']		=	$rec['street'];
		$record[$i]['city']		=	$rec['city'];
		$record[$i]['state']		=	$rec['state'];
		$record[$i]['countries']		=	$rec['countries'];
		$record[$i]['states']		=	$rec['states'];
		$record[$i]['country']		=	$rec['country'];
		$record[$i]['logo']		=	$rec['logo'];
		$record[$i]['facebook_id']		=	$rec['facebook_id'];
		$record[$i]['Allow_FixedPrice']		=	$rec['Allow_FixedPrice'];
		$record[$i]['clicks']		=	$rec['clicks'];
		$record[$i]['button_image']		=	$rec['button_image'];
		
		$record[$i]['P_Parkingattributes']		=	$rec['P_Parkingattributes'];
		$record[$i]['P_Shuttledesc']		=	$rec['P_Shuttledesc'];
		$record[$i]['P_Shuttleother']		=	$rec['P_Shuttleother'];
		
		
		$record[$i]['api']		=	$rec['api'];
		$i++;
	}
	$output=array("record"=>$record,"totalRecordCount"=>$TotalRecordCount,"rc"=>$RC1,"default"=>$Default);
    echo json_encode($output);
    exit;

?>                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    