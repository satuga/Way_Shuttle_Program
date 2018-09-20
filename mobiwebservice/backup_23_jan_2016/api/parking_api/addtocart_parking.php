<?php
include 'config.php';
include 'function.php';

$device_id = $_REQUEST['device_id'];	
$parking_id = $_REQUEST['id'];
$fromdate = $_REQUEST['fromDate'];
//$fromdate = '04/22/2015 5:02 am';
$todate = $_REQUEST['toDate'];
//$todate = '04/30/2015 5:02 am';
$user_id = $_REQUEST['user_id'];
$owner = $_REQUEST['owner_id'];
$pe_id = $_REQUEST['pe_id'];
$pricing_type = $_REQUEST['pricing_type'];

if($todate != '' && $fromdate != '' && $parking_id != '' && $device_id != '')
{
	// get the parking data from the database
	$sqll="select *,tbl_parkinglocations.*,tbl_parking.* from tbl_parking 
	INNER JOIN tbl_parkinglocations ON tbl_parkinglocations.Park_ID=tbl_parking.P_Location
	where P_ID=".$parking_id;
	$exe=mysql_query($sqll);
	$res=mysql_fetch_array($exe);

	$fdate=strtotime($fromdate);
	$tdate=strtotime($todate);
	$Park_AirportVenue=$res['Park_AirportVenue'];
	$Payment_Type=$res['Payment_Type'];
	$Payment_Collection=$res['Payment_Collection'];
	$Payment_Collection_Amt=$res['Payment_Collection_Amt'];
	$Payat_Lot=0;

	if($res['min_reservation']>0)
		$min_reservation=$res['min_reservation'];
	else	
		$min_reservation=0;
	
	if($Park_AirportVenue<>1)
		$Park_AirportVenue=0;
		

	if($fdate>=$tdate)
	{
		echo "Invalid Date";
		exit;
	}
	
	if($res['P_Pricingtype']=='daily')
	{
		// Check Net Park Availability
		//include("netpark-availability.php");
		$start = $fdate;
		$end = $tdate;
		$start=date("Y-m-d",$fdate);
		$end=date("Y-m-d",$tdate);
		$num=1;
		$diff=0;
		$error="";
		$totalprice=0;
		$diff = abs($tdate - $fdate); 
		
		$years   = floor($diff / (365*60*60*24)); 
		$months  = floor(($diff - $years * 365*60*60*24) / (30*60*60*24)); 
		$days    = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
		$hours   = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24)/ (60*60)); 
		$minuts  = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24 - $hours*60*60)/ 60); 
		if($years>0)
		{
			$days=$days+($years*364);
			
			$days--;
			$days=$days;
		}
		if($months>0)
		{
			$days=$days+($months*30);
			
			$days--;
			$days=$days;
		}	
		
		if($hours>0 || $minuts>0)
			$days++;
		
		$diff=$days;
		if($min_reservation> 0 && $min_reservation>$diff)
		{
			$closed=1;
			echo $min_reservation."-day minimum stay required.";
			exit;
		}
		$ik=1;
		$closed=0;
		$closeddates="";
		$PayatLotdaysAmt=0;
		
		while ($ik<=$days) 
		{
			$cl=0;
			
			if($res['P_Daily_Price_Type']=='Week')
			{
				$week=date('l', strtotime($start));
				//$sqlw="SELECT P_".$week."_Price AS PRICE,P_".$week."_Space AS SPACES FROM tbl_parking WHERE P_ID=".$_REQUEST['id'];
				
				$sqlw="SELECT PA_P_Dailyprice AS PRICE,PA_No_Spaces AS SPACES,PA_Updated FROM tbl_parkingweekdayavailability WHERE P_fromDate='".$start."' AND P_ID=".$parking_id;
				$resw = mysql_fetch_array(mysql_query($sqlw));
				$totalprice=$totalprice+$resw['PRICE'];
				if($Payment_Type=='partial' && $Payment_Collection=='days' && $ik<=$Payment_Collection_Amt)
					$PayatLotdaysAmt+=$resw['PRICE'];
				
				//Check for Inventory
				$sql1="SELECT COUNT(TxnID) AS CNT FROM tbl_paymenttransaction WHERE ('".$start."' BETWEEN date_format(from_date, '%Y-%m-%d') AND date_format(to_date, '%Y-%m-%d')) AND TxnDate>='".$resw['PA_Updated']."' AND Parking_ID='".$parking_id."' AND OrderStatus!='Cancelled'";
				$exe1=mysql_query($sql1);
				$res1=mysql_fetch_array($exe1);
				
				if($res1['CNT']>=$resw['SPACES'])
				{
					$closed=1;
					if($cl==0)
						$closeddates.=", ".date("F j, Y",strtotime($start));
				}
			}
			else
			{
				$sql="SELECT Park_ID,PA_P_Dailyprice,PA_No_Spaces,PA_Created FROM tbl_parkingavailability WHERE ('".$start."' BETWEEN P_fromDate AND PA_toDate) AND P_ID='".$parking_id."'";
				
				$exe=mysql_query($sql);
				$res=mysql_fetch_array($exe);
				$num=mysql_num_rows($exe);
				$totalprice=$totalprice+$res['PA_P_Dailyprice'];
				if($num<1){ 
					$closed=1;
					$cl=1;
					$closeddates.=", ".date("F j, Y",strtotime($start));
				}
				//Check for Inventory
				$sql1="SELECT COUNT(TxnID) AS CNT FROM tbl_paymenttransaction WHERE ('".$start."' BETWEEN date_format(from_date, '%Y-%m-%d') AND date_format(to_date, '%Y-%m-%d')) AND Parking_ID='".$parking_id."' AND TxnDate>='".$res['PA_Created']."' AND OrderStatus!='Cancelled'";
				$exe1=mysql_query($sql1);
				$res1=mysql_fetch_array($exe1);
				if($res1['CNT']>=$res['PA_No_Spaces'])
				{
					$closed=1;
					if($cl==0)
						$closeddates.=", ".date("F j, Y",strtotime($start));
				}
			}
			
			$start = date ("Y-m-d", strtotime("+1 day", strtotime($start)));
			//$diff++;
			$ik++;
		}
		
		if($closed==1)
		{
			echo 'One of the days you have selected('.trim($closeddates,',').') is sold out and you may need to select another time or a different parking lot.';
			exit;
		}
		if($error=='Closed')
		{
			echo "Closed";
			exit;
		}
	}
	
	//echo "Total Price = ".$totalprice;
	//echo "<br />";
	
	if($res['P_Pricingtype']=='weekly')
	{
		$price=number_format($res['P_Weeklyprice'],2);
		$pr="Week";
		$fdate=strtotime($fromdate);
		$tdate=strtotime($todate);
		$diff = ceil(($tdate - $fdate)/ ((3600*24)*7));
		$totalprice=$price*$diff;
	}
	else if($res['P_Pricingtype']=='event')
	{
		$price=number_format($res['Event_price'],2);
		$pr="Event";
		$fdate=strtotime($fromdate);
		$tdate=strtotime($todate);
		//$diff = ceil(($tdate - $fdate)/ 3600/24);
		$diff=1;
		$totalprice=$price*$diff;
	}
	else if($res['P_Pricingtype']=='special')
	{
		$price=number_format($res['Event_price'],2);
		$pr="Special";
		$fdate=strtotime($fromdate);
		$tdate=strtotime($todate);
		$diff = ceil(($tdate - $fdate)/ 3600/24);
		$totalprice=$price*$diff;
	}
	else if($res['P_Pricingtype']=='monthly')
	{
		$price=number_format($res['P_Monthlyprice'],2);
		$pr="Month";
		$fdate=strtotime($fromdate);
		$tdate=strtotime($todate);
		$diff = ceil(($tdate - $fdate)/ ((3600*24)*30));
		$totalprice=$price*$diff;
	}
	else if($res['P_Pricingtype']=='hourly')
	{
		$Overnight_Fee=0;
		
		//Check Lot opened for selected arrival date
		$weekfday=date("l",$fdate);
		
		if($weekfday=='Monday')
		{
			$Park_timing=$res['P_OP_Monday'];
			$Park_Closed=$res['P_OP_Monday_Closed'];
		}
		else if($weekfday=='Tuesday')
		{
			$Park_timing=$res['P_OP_Tuesday'];
			$Park_Closed=$res['P_OP_Tuesday_Closed'];
		}
		else if($weekfday=='Wednesday')
		{
			$Park_timing=$res['P_OP_Wednesday'];
			$Park_Closed=$res['P_OP_Wednesday_Closed'];
		}
		else if($weekfday=='Thursday')
		{
			$Park_timing=$res['P_OP_Thursday'];
			$Park_Closed=$res['P_OP_Thursday_Closed'];
		}
		else if($weekfday=='Friday')
		{
			$Park_timing=$res['P_OP_Friday'];
			$Park_Closed=$res['P_OP_Friday_Closed'];
		}
		else if($weekfday=='Saturday')
		{
			$Park_timing=$res['P_OP_Saturday'];
			$Park_Closed=$res['P_OP_Saturday_Closed'];
		}
		else if($weekfday=='Sunday')
		{
			$Park_timing=$res['P_OP_Sunday'];
			$Park_Closed=$res['P_OP_Sunday_Closed'];
		}

		if($Park_Closed==1 || $Park_timing=='closed')
		{
			echo 'Parking lot is closed during the time you have selected.';
			exit;
		}
		else if($Park_timing=='24hr')
		{
			echo '';
		}
		else
		{
			$Park_timings=explode("-",$Park_timing);
			
			//echo "<br>From Time:".$Current_Time=date("g:i A",$fdate);
			$Current_Date=date("Y-m-d",$fdate);
			$today_timestamp=$fdate;
			//echo "<br>open Time:".$Park_timings[0];
			$start_timestamp=strtotime($Current_Date.' '.$Park_timings[0]);
			//echo "<br>Close Time:".$Park_timings[1];
			if(strtotime($Park_timings[1])<strtotime($Park_timings[0]))
				$Current_Date = date("Y-m-d",strtotime("+1 day", strtotime($Current_Date)));
			$end_timestamp=strtotime($Current_Date.' '.$Park_timings[1]);
			if(($today_timestamp >= $start_timestamp) && ($today_timestamp <= $end_timestamp))
			{
				
			}
			else 
			{
				//Check for Over Night Parking
				if($res['P_Overnight']==1)
				{
					// Calculate Over Night Rate
					$Overnight_Fee=$Overnight_Fee+$res['P_Overnight_Fee'];
				}
				else
				{
					echo 'Overnight parking is not permitted in this location';
					//echo 'Parking Closed on Selected Arrival Time';
					exit;
				}
			}
		}
		
		//Check Lot opened for selected arrival date
		$weektday=date("l",$tdate);
		
		if($weektday=='Monday')
		{
			$Park_timing=$res['P_OP_Monday'];
			$Park_Closed=$res['P_OP_Monday_Closed'];
		}
		else if($weektday=='Tuesday')
		{
			$Park_timing=$res['P_OP_Tuesday'];
			$Park_Closed=$res['P_OP_Tuesday_Closed'];
		}
		else if($weektday=='Wednesday')
		{
			$Park_timing=$res['P_OP_Wednesday'];
			$Park_Closed=$res['P_OP_Wednesday_Closed'];
		}
		else if($weektday=='Thursday')
		{
			$Park_timing=$res['P_OP_Thursday'];
			$Park_Closed=$res['P_OP_Thursday_Closed'];
		}
		else if($weektday=='Friday')
		{
			$Park_timing=$res['P_OP_Friday'];
			$Park_Closed=$res['P_OP_Friday_Closed'];
		}
		else if($weektday=='Saturday')
		{
			$Park_timing=$res['P_OP_Saturday'];
			$Park_Closed=$res['P_OP_Saturday_Closed'];
		}
		else if($weektday=='Sunday')
		{
			$Park_timing=$res['P_OP_Sunday'];
			$Park_Closed=$res['P_OP_Sunday_Closed'];
		}
		if($Park_Closed==1 || $Park_timing=='closed')
		{
			echo 'Parking lot is closed during the time you have selected.';
			exit;
		}
		else if($Park_timing=='24hr')
		{
			echo '';
		}
		else
		{
			$Park_timings=explode("-",$Park_timing);
			
			//echo "<br>From Time:".$Current_Time=date("g:i A",$tdate);
			$Current_Date=date("Y-m-d",$tdate);
			$today_timestamp=$tdate;
			//echo "<br>open Time:".$Park_timings[0];
			$start_timestamp=strtotime($Current_Date.' '.$Park_timings[0]);
			//echo "<br>Close Time:".$Park_timings[1];
			if(strtotime($Park_timings[1])<strtotime($Park_timings[0]))
				$Current_Date = date("Y-m-d",strtotime("+1 day", strtotime($Current_Date)));
			$end_timestamp=strtotime($Current_Date.' '.$Park_timings[1]);

			if(($today_timestamp >= $start_timestamp) && ($today_timestamp <= $end_timestamp))
			{
				
			}
			else 
			{
				//Check for Over Night Parking
				if($res['P_Overnight']==1)
				{
					// Calculate Over Night Rate
					$Overnight_Fee=$Overnight_Fee+$res['P_Overnight_Fee'];
				}
				else
				{
					echo 'Overnight parking is not permitted in this location';
					//echo 'Parking Closed on Selected Arrival Time';
					exit;
				}
			}
		}
		
		
		$price=number_format($res['P_FAmt'],2);
		//$totalprice=$price;
		$pr="Hourly";
		$fdate=strtotime($fromdate);
		$tdate=strtotime($todate);
		$diff = $tdate - $fdate;
		$d1 = ceil(($diff)/ 60/60/24);
		$diff = ceil(($diff - $dl*60*60*24)/60/60);
		
		if($res['P_MaxMinEnable']==1 && $res['P_MaxAmt']>0)
		{
			$P_MaxMin=$res['P_MaxMin'];
			if($P_MaxMin<1)
				$P_MaxMin=24;
			$Total_Hours=$diff;
			$Avg_Hours=$Total_Hours/$P_MaxMin;
			if($Avg_Hours>=1)
			{
				$otherprice=floor($Avg_Hours)*$res['P_MaxAmt'];
				$Remaining_Hours=$Total_Hours%$P_MaxMin;
				$Remaining_Price=$Remaining_Hours*$price;
				if($Remaining_Price>$res['P_MaxAmt'])
					$Remaining_Price=$res['P_MaxAmt'];
				$totalprice=$otherprice+$Remaining_Price;
			}
			else
			{
				$otherprice=$Total_Hours*$price;
				if($otherprice>$res['P_MaxAmt'])
					$otherprice=$res['P_MaxAmt'];
				$totalprice=$otherprice;
			}
			//$totalprice=$Avg_Hours;
		}
		else 
		{
			$totalprice=$price*$diff;
		}
		
		
	}
	else if($res['P_Pricingtype']=='minute')
	{
		$otherprice=0;
		$price=number_format($res['P_FAmt'],2);
		$totalprice=$price;
		$pr="Hourly";
		$fdate=strtotime($fromdate);
		$tdate=strtotime($todate);
		
		$diff=round(abs($tdate - $fdate) / 60,2);
		
		
		if($diff>$res['P_FMIN'])
		{
			if($res['P_MaxMinEnable']==1)
			{
				$diff1=$diff-$res['P_FMIN'];
				if($diff1>=$res['P_MaxMin'])
				{
					$othermin=floor($diff1/$res['P_MaxMin']);
					$otherprice=$othermin*$res['P_MaxAmt'];
					$othermin1=$diff1%$res['P_MaxMin'];
					if($othermin1>0)
					{
						$othermin1=ceil($othermin1/$res['P_IncMin']);
						$otherprice1=$othermin1*$res['P_IncAmt'];
						$otherprice=$otherprice+$otherprice1;
					}
				}
				else
				{
					$diff1=ceil($diff1/$res['P_IncMin']);
					$otherprice=$diff1*$res['P_IncAmt'];
				}
			}
			else
			{
				if($res['P_IncMin']==0)
					$P_IncMin=$res['P_FMIN'];
				else
					$P_IncMin=$res['P_IncMin'];
				if($res['P_IncAmt']==0)
					$P_IncAmt=$res['P_FAmt'];
				else
					$P_IncAmt=$res['P_IncAmt'];
				$diff1=$diff-$res['P_FMIN'];
				$diff1=ceil($diff1/$P_IncMin);
				$otherprice=$diff1*$P_IncAmt;
			}
		}
		$diff=$diff/$res['P_FMIN'];
		$totalprice=$totalprice+$otherprice;
	}
	
	
	
	$extfees=0;
	$Way_Fee=0;
	// Admin External Fee based on Individual Listing
	
	if($Park_AirportVenue==1)
	{
		$csql="SELECT * FROM tbl_categories_settings WHERE Cat_ID=45 AND Parking_ID=".$parking_id;
		$cexe=mysql_query($csql);
		
		$ext_setting=0;
		while($cres=mysql_fetch_array($cexe))
		{
			$ext_setting=1;
			if($cres['format']=='%')
			{
				$per_amt=$totalprice*$cres['Setting_Value']/100;
				$extfees=$extfees+$per_amt;
			}
			else
			{
				$per_amt=$cres['Setting_Value'];
				$extfees=$extfees+$cres['Setting_Value'];
			}
		}
		$Way_Fee=$extfees;
	}
	
	//echo "Way Fees =".$Way_Fee;
	//echo "<br />";
	
	// Admin External Fees
	if($Park_AirportVenue==1)
	{
		if($ext_setting==0)
		{
			$csql="SELECT * FROM tbl_categories_settings WHERE Setting_Value>0 AND Cat_ID=45 AND Parking_ID=0";
			$cexe=mysql_query($csql);
			while($cres=mysql_fetch_array($cexe))
			{
			//echo "Ddd";
				if($cres['format']=='%')
				{
					$per_amt=$totalprice*$cres['Setting_Value']/100;
					$extfees=$extfees+$per_amt;
					$Way_Fee=$Way_Fee+$per_amt;
				}
				else
				{
					$per_amt=$cres['Setting_Value'];
					$extfees=$extfees+$cres['Setting_Value'];
					$Way_Fee=$Way_Fee+$cres['Setting_Value'];
				}
			}
			
		}
	}
	
	
	
	// Get Additional Charges
	$csql="SELECT * FROM tbl_sell_fee WHERE Parking_ID=".mysql_real_escape_string($parking_id);
	$cexe=mysql_query($csql);
	
	while($cres=mysql_fetch_array($cexe))
	{
		if($cres['format']=='%')
		{
			$per_amt=round($totalprice*$cres['Setting_Value']/100,2);
			$extfees=$extfees+$per_amt;
		}
		else
		{
			$per_amt=$cres['Setting_Value'];
			$extfees=$extfees+$cres['Setting_Value'];
		}
	}
	
	//echo $extfees;
	//echo "<br />";
	//echo $totalprice;
	//echo "<br />";
	$totalpriceval=$totalprice+$extfees+$Overnight_Fee;
	
	
	
	if($user_id=='')
	{
		$User_ID=0;
	}
	else
	{
		$User_ID=$user_id;
	}
	
	//echo $Payment_Collection;
	$fromDate1=date("Y-m-d H:i:s",strtotime($fromdate));
	$toDate1=date("Y-m-d H:i:s",strtotime($todate));
	
	
	
	$sql="DELETE FROM tbl_cart WHERE Cart_Type='Parking' AND Cart_ServiceID=".$parking_id." AND Sess_ID='".$device_id."'";
	mysql_query($sql);
	
	// Pay at Lot calculation
	if($Payment_Type=='partial' && $Payment_Collection=='fixed' && $Payment_Collection_Amt>0)
		$Payat_Lot=$Payment_Collection_Amt;
	else if($Payment_Type=='partial' && $Payment_Collection=='percent' && $Payment_Collection_Amt>0)
		$Payat_Lot=($totalprice*$Payment_Collection_Amt/100)+$Way_Fee;
	else if($Payment_Type=='partial' && $Payment_Collection=='percentwithcharges' && $Payment_Collection_Amt>0)
		$Payat_Lot=(($totalpriceval-$Way_Fee)*$Payment_Collection_Amt/100)+$Way_Fee;
	else if($Payment_Type=='partial' && $Payment_Collection=='percentwithwaycharges' && $Payment_Collection_Amt>0)
		$Payat_Lot=(($totalpriceval-$Way_Fee)*$Payment_Collection_Amt/100)+$Way_Fee;
	else if($Payment_Type=='partial' && $Payment_Collection=='days' && $Payment_Collection_Amt>0)
		$Payat_Lot=$PayatLotdaysAmt;
		
	if($Payment_Type=='partial')
	{
		$Payat_Lot=$totalpriceval-$Payat_Lot;
		if($Payat_Lot<0)
		{
			$Payat_Lot=0;
		}
	}
	
	$query="INSERT INTO tbl_cart(Sess_ID,Cart_UserID,Cart_ServiceID,Owner_ID,Cart_Quantity,Cart_Type,Amount,charges,Overnight_Fee,TotalAmount,Payat_Lot,Payment_Type,Parking_type,from_date,to_date,Cart_Created,NetPark_rate,NetPark_daily_rate,Way_Fee,Parking_Event_PE_ID)
		  VALUES('".$device_id."','".$User_ID."','".$parking_id."','".$owner."','".$diff."','Parking','".$totalprice."','".$extfees."','".$Overnight_Fee."','".round($totalpriceval,2)."','".$Payat_Lot."','".$Payment_Type."','".$pricing_type."','".$fromDate1."','".$toDate1."',Now(),'".$NetPark_rate."','".$NetPark_daily_rate."','".$Way_Fee."','".$pe_id."');";	
	
	$res = mysql_query($query);
	$Cart_ID=mysql_insert_id();
	if($Cart_ID !='' )
	{
		$output = array("status" => 1, "cart_id" => $Cart_ID);
        echo json_encode($output);
        exit;
	}
}
else
{
	$output = array("status" => 0, "error" => 'Please Pass Required Parameters');
    echo json_encode($output);
    exit;
}	
	
?>