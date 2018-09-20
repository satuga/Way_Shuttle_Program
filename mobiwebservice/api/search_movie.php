<?php

        
		include '../common/config.php';
		$lat=37.36883;
		$long=-122.0363496;
		$sql_lat_col="tbl_activitycompany.lat";
		$sql_long_col="tbl_activitycompany.lon";
		
		// Check with Expire Records
		$dt2 = date('Y-m-d H:i:s');
		
		// Location Match Query 
		$Query1="SELECT  tbl_activitycompany.lat AS ACLAT,tbl_activitycompany.lon AS ACLON,tbl_activities.*,tbl_activities.Act_ID AS Act_ID1,tbl_registeration.*,tbl_activitycompany.*,tbl_subcategories.Cat_Name,tbl_subcategories.SubCat_ID,3956 * 2 * ASIN(SQRT(POWER(SIN(($lat - abs($sql_lat_col)) * pi()/180 / 2), 2) + COS($lat * pi()/180 ) * COS(abs($sql_lat_col) * pi()/180) * POWER(SIN(($long-$sql_long_col) * pi()/180 / 2), 2) )) as distance,1 AS Main 
		FROM tbl_activities 
		INNER JOIN tbl_registeration ON tbl_registeration.id= tbl_activities.Act_UserID
		INNER JOIN tbl_subcategories ON tbl_subcategories.SubCat_ID=tbl_activities.Act_Cat_ID
		INNER JOIN tbl_categories ON tbl_categories.Cat_ID=tbl_subcategories.Cat_ID
		INNER JOIN  tbl_activitycompany ON  tbl_activitycompany.C_ID=tbl_activities.Act_CompanyID
		INNER JOIN tbl_activities_subcategory ON tbl_activities_subcategory.Sub_Activity=tbl_activities.Act_ID
		WHERE tbl_subcategories.status=1 AND tbl_registeration.activities_control=1 AND tbl_activities.Deleted=0 AND tbl_categories.Cat_ID=64 AND tbl_activities.Act_Status=1 ";
		//INNER JOIN tbl_activities_price ON tbl_activities_price.P_Activity=tbl_activities.Act_ID
		if ($_REQUEST['PlayType']!="" && $_REQUEST['PlayType']!=",") {
			$PlayTypes=explode(",",$_REQUEST['PlayType']);
			$Query1.= " AND ";
			$ppi=0;
			foreach($PlayTypes as $pp=>$ss)
			{
			if($ppi==0)
				$Query1.= "(tbl_activities_subcategory.Sub_Subcat=".$ss;
			else
				$Query1.= " OR tbl_activities_subcategory.Sub_Subcat=".$ss;
			$ppi++;
			}
			$Query1.= ")";
			 
		}
		
		if ($_REQUEST['sub_cat']!="") {
			$Query1.=" AND tbl_activities.Act_Cat_ID='".$_REQUEST[sub_cat]."'";
		}
		if ($_REQUEST['Companyid']!="") {
			$Query1.=" AND tbl_activitycompany.C_Management IN(".$_REQUEST[Companyid].")";
		}
		if($_REQUEST['PRANGE']<>'')
		{
			$RangeVal=explode("-",$_REQUEST['PRANGE']);
			$Min=trim(trim($RangeVal[0]),'$');
			$Max=trim(trim($RangeVal[1]),'$');
			//$Query1.=" AND (tbl_activities_price.Act_Price >= '".$Min."' AND tbl_activities_price.Act_Price<='".$Max."')";
		}
		if($_REQUEST['movieRev']!="")
		{
			$Query1.= " AND tbl_activities.average_reviews>=".$_REQUEST['movieRev'];
		}
		if ($_REQUEST['moviekeywords']!="" && $_REQUEST['moviekeywords']!='Search Keyword') {
			//$keywordss=explode(",",$_REQUEST['moviekeywords']);
			$keywordss=explode(",",str_replace(" ",",",trim($_REQUEST['moviekeywords'])));
			$Query1.=" AND (";
				$rr=1;
				foreach($keywordss as $keywordss1)
				{
				//$Query1.=" (tbl_activitycompany.C_CompanyName LIKE '%".$keywordss1."%' OR  tbl_activities.Act_Title LIKE '%".$keywordss1."%' OR tbl_activitycompany.C_Address LIKE '%".$keywordss1."%' OR tbl_activitycompany.C_State LIKE '%".$keywordss1."%' OR tbl_activitycompany.C_City LIKE '%".$keywordss1."%' OR tbl_activitycompany.C_Zipcode LIKE '%".$keywordss1."%' OR tbl_activities.Act_Intro LIKE '%".$keywordss1."%' OR tbl_activities.Act_Overview LIKE '%".$keywordss1."%' OR tbl_activities.Act_Inclusions LIKE '%".$keywordss1."%' OR tbl_activities.Act_Exclusions LIKE '%".$keywordss1."%') ";
				$Query1.=" (tbl_activitycompany.C_CompanyName LIKE '%".$keywordss1."%' OR  tbl_activities.Act_Title LIKE '%".$keywordss1."%' OR tbl_activitycompany.C_Address LIKE '%".$keywordss1."%' OR tbl_activitycompany.C_State LIKE '%".$keywordss1."%' OR tbl_activitycompany.C_City LIKE '%".$keywordss1."%' OR tbl_activitycompany.C_Zipcode LIKE '%".$keywordss1."%') ";
				if(COUNT($keywordss)<>$rr)
					$Query1.=" OR ";
				$rr++;
				}
				$Query1.=" ) ";
		}
		if ($_REQUEST['moviedescription']!="" && $_REQUEST['moviedescription']!='Where are you going?') {
			$desc=explode(",",$_REQUEST['moviedescription']);
			if(count($desc)>1)
			{
				$Loc=explode(",",$_REQUEST['moviedescription']);
				$Strlen=strlen($_REQUEST['moviedescription']);
				$cnt=count($Loc);
				$Strlen1=strlen($Loc[$cnt-2]);
				$Strlen2=strlen($Loc[$cnt-1]);
				$Strdiff=$Strlen-($Strlen1+$Strlen2);
				
				$Street=substr($_REQUEST['moviedescription'], 0,$Strdiff);
				$Street=trim($Street);
				$Street = substr($Street, 0, strlen($Street)-1);
				$Query1.=" AND tbl_activitycompany.C_City='".ucwords(trim($Loc[$cnt-2]))."'";
				
				$state=explode(" ",$Loc[$cnt-1]);
				if(COUNT($state)==3)
				{
				$Query1.=" AND tbl_activitycompany.C_State='".trim($state[1])."' AND tbl_activitycompany.C_Zipcode='".trim($state[2])."'";
				}
				else
				{
				$Query1.=" AND tbl_activitycompany.C_State='".ucwords(trim($Loc[$cnt-1]))."'";
				}
				
			}
			else
			{
				$desc1=explode(" ",$_REQUEST['moviedescription']);
				
				//$Query1.=" ) ";
				if ($_REQUEST['City']!="" && $_REQUEST['City']!="City" && $_REQUEST['City']!="City Name") {
				$Query1.=" AND tbl_activitycompany.C_City='".ucwords($_REQUEST[City])."'";
				}
				if ($_REQUEST['State']!="" && $_REQUEST['State']!="State" && $_REQUEST['State']!="State Name") {
					$Query1.=" AND tbl_activitycompany.C_State='".ucwords(trim($_REQUEST[State]))."'";
				}
				else
				{
					if(count($desc)==1)
					$Query1.=" AND tbl_activitycompany.C_Zipcode='".trim($_REQUEST['moviedescription'])."'";
				}
			}
		} else
		{
			if ($_REQUEST['City']!="" && $_REQUEST['City']!="City" && $_REQUEST['City']!="City Name") {
			$Query1.=" AND tbl_activitycompany.C_City='".ucwords($_REQUEST[City])."'";
			}
			if ($_REQUEST['State']!="" && $_REQUEST['State']!="State" && $_REQUEST['State']!="State Name") {
				$Query1.=" AND tbl_activitycompany.C_State='".ucwords(trim($_REQUEST[State]))."'";
			}
		}
		if($_REQUEST['PlayType']=='86')
			$Query1.= " AND (3956 * 2 * ASIN(SQRT(POWER(SIN(($lat - abs($sql_lat_col)) * pi()/180 / 2), 2) + COS($lat * pi()/180 ) * COS(abs($sql_lat_col) * pi()/180) * POWER(SIN(($long-$sql_long_col) * pi()/180 / 2), 2) )))<=".$_REQUEST['MILES'];
		/*$dt21 = date('Y-m-d H:i:s',strtotime($_REQUEST['fromDate']));
		$dt22 = date('Y-m-d H:i:s',strtotime($_REQUEST['toDate']));
		if($_REQUEST['fromDate']<>'' && $_REQUEST['toDate']<>'')
			$Query1.=" AND Act_StartDate >= '".$dt21."' AND Act_EndDate <= '".$dt22."'";
		else
			$Query1.=" AND Act_StartDate <= '".$dt2."' AND Act_EndDate >= '".$dt2."'";*/
		
		//$res1=mysqli_query($con,$Query1) or die(mysqli_error($con));
		//$RC1=mysqli_num_rows($res1);	
		// Location that are Not Matched Query 
		$Query2="SELECT  tbl_activitycompany.lat AS ACLAT,tbl_activitycompany.lon AS ACLON,tbl_activities.*,tbl_activities.Act_ID AS Act_ID1,tbl_registeration.*,tbl_activitycompany.*,tbl_subcategories.Cat_Name,tbl_subcategories.SubCat_ID,3956 * 2 * ASIN(SQRT(POWER(SIN(($lat - abs($sql_lat_col)) * pi()/180 / 2), 2) + COS($lat * pi()/180 ) * COS(abs($sql_lat_col) * pi()/180) * POWER(SIN(($long-$sql_long_col) * pi()/180 / 2), 2) )) as distance,2 AS Main 
		FROM tbl_activities 
		INNER JOIN tbl_registeration ON tbl_registeration.id= tbl_activities.Act_UserID
		INNER JOIN tbl_subcategories ON tbl_subcategories.SubCat_ID=tbl_activities.Act_Cat_ID
		INNER JOIN tbl_categories ON tbl_categories.Cat_ID=tbl_subcategories.Cat_ID
		INNER JOIN  tbl_activitycompany ON  tbl_activitycompany.C_ID=tbl_activities.Act_CompanyID
		INNER JOIN tbl_activities_subcategory ON tbl_activities_subcategory.Sub_Activity=tbl_activities.Act_ID
		WHERE tbl_subcategories.status=1 AND tbl_registeration.activities_control=1 AND tbl_activities.Deleted=0 AND tbl_categories.Cat_ID=64 AND tbl_activities.Act_Status=1 ";
		//INNER JOIN tbl_activities_price ON tbl_activities_price.P_Activity=tbl_activities.Act_ID
		if ($_REQUEST['PlayType']!="" && $_REQUEST['PlayType']!=",") {
			$PlayTypes=explode(",",$_REQUEST['PlayType']);
			$Query2.= " AND ";
			$ppi=0;
			foreach($PlayTypes as $pp=>$ss)
			{
			if($ppi==0)
				$Query2.= "(tbl_activities_subcategory.Sub_Subcat=".$ss;
			else
				$Query2.= " OR tbl_activities_subcategory.Sub_Subcat=".$ss;
			$ppi++;
			}
			$Query2.= ")";
		}
		
		if ($_REQUEST['sub_cat']!="") {
			$Query2.=" AND tbl_activities.Act_Cat_ID='".$_REQUEST[sub_cat]."'";
		}
		if ($_REQUEST['Companyid']!="") {
			$Query2.=" AND tbl_activitycompany.C_Management IN(".$_REQUEST[Companyid].")";
		}
		if($_REQUEST['PRANGE']<>'')
		{
			$RangeVal=explode("-",$_REQUEST['PRANGE']);
			$Min=trim(trim($RangeVal[0]),'$');
			$Max=trim(trim($RangeVal[1]),'$');
			//$Query2.=" AND (tbl_activities_price.Act_Price >= '".$Min."' AND tbl_activities_price.Act_Price<='".$Max."')";
		}
		if($_REQUEST['movieRev']!="")
		{
			$Query2.= " AND tbl_activities.average_reviews>=".$_REQUEST['movieRev'];
		}
		if ($_REQUEST['moviekeywords']!="" && $_REQUEST['moviekeywords']!='Search Keyword') {
			//$keywordss=explode(",",$_REQUEST['moviekeywords']);
			$keywordss=explode(",",str_replace(" ",",",trim($_REQUEST['moviekeywords'])));
			$Query2.=" AND (";
				$rr=1;
				foreach($keywordss as $keywordss1)
				{
				//$Query2.=" (tbl_activitycompany.C_CompanyName LIKE '%".$keywordss1."%' OR  tbl_activities.Act_Title LIKE '%".$keywordss1."%' OR tbl_activitycompany.C_Address LIKE '%".$keywordss1."%' OR tbl_activitycompany.C_State LIKE '%".$keywordss1."%' OR tbl_activitycompany.C_City LIKE '%".$keywordss1."%' OR tbl_activitycompany.C_Zipcode LIKE '%".$keywordss1."%' OR tbl_activities.Act_Intro LIKE '%".$keywordss1."%' OR tbl_activities.Act_Overview LIKE '%".$keywordss1."%' OR tbl_activities.Act_Inclusions LIKE '%".$keywordss1."%' OR tbl_activities.Act_Exclusions LIKE '%".$keywordss1."%') ";
				$Query2.=" (tbl_activitycompany.C_CompanyName LIKE '%".$keywordss1."%' OR  tbl_activities.Act_Title LIKE '%".$keywordss1."%' OR tbl_activitycompany.C_Address LIKE '%".$keywordss1."%' OR tbl_activitycompany.C_State LIKE '%".$keywordss1."%' OR tbl_activitycompany.C_City LIKE '%".$keywordss1."%' OR tbl_activitycompany.C_Zipcode LIKE '%".$keywordss1."%') ";
				if(COUNT($keywordss)<>$rr)
					$Query2.=" OR ";
				$rr++;
				}
				$Query2.=" ) ";
		}
		if ($_REQUEST['moviedescription']!="" && $_REQUEST['moviedescription']!='Where are you going?') {
			$desc=explode(",",$_REQUEST['moviedescription']);
			if(count($desc)==1)
			{
				$Query2.=" AND tbl_activitycompany.C_Zipcode!='".trim($_REQUEST['moviedescription'])."'";
			}
			else if(count($desc)>1)
			{
				$Query2.=" AND tbl_activitycompany.C_City!='".ucwords(trim($desc[0]))."'";
				//$Query2.=" AND tbl_activitycompany.C_State='".ucwords(trim($desc[1]))."'";
			}
			else
			{
				$desc1=explode(" ",$_REQUEST['moviedescription']);
				if ($_REQUEST['City']!="" && $_REQUEST['City']!="City" && $_REQUEST['City']!="City Name") {
			$Query2.=" AND tbl_activitycompany.C_City!='".ucwords($_REQUEST[City])."'";
			}
			}
		} else
		{
			if ($_REQUEST['City']!="" && $_REQUEST['City']!="City" && $_REQUEST['City']!="City Name") {
			$Query2.=" AND tbl_activitycompany.C_City!='".ucwords($_REQUEST[City])."'";
			}
		}
		if($_REQUEST['PlayType']=='86')
			$Query2.= " AND (3956 * 2 * ASIN(SQRT(POWER(SIN(($lat - abs($sql_lat_col)) * pi()/180 / 2), 2) + COS($lat * pi()/180 ) * COS(abs($sql_lat_col) * pi()/180) * POWER(SIN(($long-$sql_long_col) * pi()/180 / 2), 2) )))<=".$_REQUEST['MILES'];

		$QueryCommon= " GROUP BY Act_ID1 ";
		
		if($_REQUEST['sort_by']=='Top Results')
			$QueryCommon.= " ORDER BY Main ASC,clicks DESC";
		else if($_REQUEST['sort_by']=='Price:lowest first')
			$QueryCommon.= " ORDER BY Main, Act_Start_Price ASC";
		else if($_REQUEST['sort_by']=='Price:highest first')
			$QueryCommon.= " ORDER BY Main, Act_Start_Price DESC";
		else if($_REQUEST['sort_by']=='Highest Rated')
			$QueryCommon.= " ORDER BY Main ASC, average_reviews DESC";
		else if($_REQUEST['sort_by']=='Most Reviewed')
			$QueryCommon.= " ORDER BY Main ASC, total_reviews DESC";
		else if($_REQUEST['sort_by']=='Distance:nearest first' && $_REQUEST['Zip']>0 && $_REQUEST['Zipvalid']==1)
			$QueryCommon.= " ORDER BY Main ASC, distance ASC";
		else if($_REQUEST['sort_by']=='Latest')
			$QueryCommon.= " ORDER BY Main ASC, Act_Created DESC";
		else
			$QueryCommon.= " ORDER BY Main ASC, distance ASC";
		if($_REQUEST['City']=='' && $_REQUEST['PlayType']!='86')	
		{
			$Query=$Query1.$QueryCommon;
		}
		else
		{
			$QueryJoin= " UNION ";
			$Query=$Query1.$QueryJoin.$Query2.$QueryCommon;
		}
	
		
		$result=mysqli_query($con,$Query) or die("line 248:-".mysqli_error($con));
		
		$TotalRecordCount=mysqli_num_rows($result);
		
		$Totalpages 		= mysqli_query($con,"SELECT * FROM `tbl_control`")  or die("line 251:-".mysqli_error($con));
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
			$Query1="SELECT  tbl_activitycompany.lat AS ACLAT,tbl_activitycompany.lon AS ACLON,tbl_activities.*,tbl_activities.Act_ID AS Act_ID1,tbl_registeration.*,tbl_activitycompany.*,tbl_subcategories.Cat_Name,tbl_subcategories.SubCat_ID,3956 * 2 * ASIN(SQRT(POWER(SIN(($lat - abs($sql_lat_col)) * pi()/180 / 2), 2) + COS($lat * pi()/180 ) * COS(abs($sql_lat_col) * pi()/180) * POWER(SIN(($long-$sql_long_col) * pi()/180 / 2), 2) )) as distance,1 AS Main 
			FROM tbl_activities 
			INNER JOIN tbl_registeration ON tbl_registeration.id= tbl_activities.Act_UserID
			INNER JOIN tbl_subcategories ON tbl_subcategories.SubCat_ID=tbl_activities.Act_Cat_ID
			INNER JOIN tbl_categories ON tbl_categories.Cat_ID=tbl_subcategories.Cat_ID
			INNER JOIN  tbl_activitycompany ON  tbl_activitycompany.C_ID=tbl_activities.Act_CompanyID
			INNER JOIN tbl_activities_subcategory ON tbl_activities_subcategory.Sub_Activity=tbl_activities.Act_ID
			WHERE tbl_subcategories.status=1 AND tbl_registeration.activities_control=1 AND tbl_activities.Deleted=0 AND tbl_categories.Cat_ID=64 AND tbl_activities.Act_Status=1 AND Act_Cat_ID=86";
			
			$QueryCommon= " GROUP BY Act_ID1 ";
		
			if($_REQUEST['sort_by']=='Top Results')
				$QueryCommon.= " ORDER BY Main ASC,clicks DESC";
			else if($_REQUEST['sort_by']=='Price:lowest first')
				$QueryCommon.= " ORDER BY Main, Act_Start_Price ASC";
			else if($_REQUEST['sort_by']=='Price:highest first')
				$QueryCommon.= " ORDER BY Main, Act_Start_Price DESC";
			else if($_REQUEST['sort_by']=='Highest Rated')
				$QueryCommon.= " ORDER BY Main ASC, average_reviews DESC";
			else if($_REQUEST['sort_by']=='Most Reviewed')
				$QueryCommon.= " ORDER BY Main ASC, total_reviews DESC";
			else if($_REQUEST['sort_by']=='Distance:nearest first' && $_REQUEST['Zip']>0 && $_REQUEST['Zipvalid']==1)
				$QueryCommon.= " ORDER BY Main ASC, distance ASC";
			else if($_REQUEST['sort_by']=='Latest')
				$QueryCommon.= " ORDER BY Main ASC, Act_Created DESC";
			else
				$QueryCommon.= " ORDER BY Main ASC, distance ASC";
			
			
			$result=mysqli_query($con,$SQL) or die("line 304:-".mysqli_error($con));
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
		$res=mysqli_query($con,$SQL) or die("line 315:-".mysqli_error($con));
		$record=array();
        $i=0;
	while($rec=mysqli_fetch_array($res))
	{
		$record[$i]['id']=$rec['id'];
		$record[$i]['Act_CompanyID']=$rec['Act_CompanyID'];
		$record[$i]['C_CompanyName']=$rec['C_CompanyName'];
		$record[$i]['distance']=$rec['distance'];
		$record[$i]['Act_ID']=$rec['Act_ID'];
		$record[$i]['Act_UserID']=$rec['Act_UserID'];
		$record[$i]['Act_Cat_ID']=$rec['Act_Cat_ID'];
		$record[$i]['C_CompanyName']=$rec['C_CompanyName'];
		$record[$i]['C_Theatre']=$rec['C_Theatre'];
		$record[$i]['C_Management']=$rec['C_Management'];
		$record[$i]['Cat_Name']=$rec['Cat_Name'];
		$record[$i]['Act_Title']=$rec['Act_Title'];
		$record[$i]['Act_Start_Price']=$rec['Act_Start_Price'];
		$record[$i]['Act_Start_OriginalPrice']		=$rec['Act_Start_OriginalPrice'];
		
		$record[$i]['Act_Intro']=$rec['Act_Intro'];
		$record[$i]['Main']=$rec['Main'];
		$record[$i]['SubCat_ID']=$rec['SubCat_ID'];
		$record[$i]['Act_Overview']=$rec['Act_Overview'];
		$record[$i]['Act_StartDate']=$rec['Act_StartDate'];
		$record[$i]['Act_EndDate']=$rec['Act_EndDate'];
		$record[$i]['Act_Address']=$rec['C_Address'];
		$record[$i]['Act_City']=$rec['C_City'];
		$record[$i]['Act_State']		=	$rec['C_State'];
		$record[$i]['C_Address']		=	$rec['C_Address'];
		$record[$i]['C_City']		=	$rec['C_City'];
		$record[$i]['C_State']		=	$rec['C_State'];
		$record[$i]['C_Zipcode']		=	$rec['C_Zipcode'];
		$record[$i]['clicks']			=	$rec['clicks'];
		$record[$i]['Act_Created']			=	$rec['Act_Created'];
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
		$record[$i]['Allow_IWant']		=	$rec['Allow_IWant'];
		$record[$i]['button_image']		=	$rec['button_image'];
		$record[$i]['lat']		=	$rec['ACLAT'];
		$record[$i]['lon']		=	$rec['ACLON'];
		$i++;
	
	}
$output=array("record"=>$record,"totalRecordCount"=>$TotalRecordCount,"rc"=>$RC1,"default"=>$Default);
echo json_encode($output);
exit;

?>                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                  