<?php
function GetTransactions($transaction_id)
  {
	$sqll="select * from tbl_gate_parking_transaction where transaction_id=".$transaction_id;
	$exe=mysqli_query($con,$sqll);
	$rec=mysqli_fetch_array($exe);
	
	$sqll="select *,tbl_parkinglocations.*,tbl_parking.* from tbl_parking 
	INNER JOIN tbl_parkinglocations ON tbl_parkinglocations.Park_ID=tbl_parking.P_Location
	where P_ID=".$rec['garagae_id'];
	$exe=mysqli_query($con,$sqll);
	$res=mysqli_fetch_array($exe);
	$fdate=strtotime($rec['fromDate']);
	$tdate=strtotime($rec['toDate']);
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
		while ($ik<=$days) {
			$cl=0;
			if($res['P_Daily_Price_Type']=='Week')
			{
				$week=date('l', strtotime($start));
				
				$sqlw="SELECT PA_P_Dailyprice AS PRICE,PA_No_Spaces AS SPACES,PA_Updated FROM tbl_parkingweekdayavailability WHERE P_fromDate='".$start."' AND P_ID=".$_REQUEST['id'];
				$resw = mysqli_fetch_array(mysqli_query($con,$sqlw));
				$totalprice=$totalprice+$resw['PRICE'];
				if($Payment_Type=='partial' && $Payment_Collection=='days' && $ik<=$Payment_Collection_Amt)
					$PayatLotdaysAmt+=$resw['PRICE'];
				//Check for Inventory
				$sql1="SELECT COUNT(TxnID) AS CNT FROM tbl_paymenttransaction WHERE ('".$start."' BETWEEN date_format(from_date, '%Y-%m-%d') AND date_format(to_date, '%Y-%m-%d')) AND TxnDate>='".$resw['PA_Updated']."' AND Parking_ID='".$_REQUEST['id']."' AND OrderStatus!='Cancelled'";
				$exe1=mysqli_query($con,$sql1);
				$res1=mysqli_fetch_array($exe1);
				
				if($res1['CNT']>=$resw['SPACES'])
				{
					$closed=1;
					if($cl==0)
						$closeddates.=", ".date("F j, Y",strtotime($start));
				}
			}
			else
			{
				$sql="SELECT Park_ID,PA_P_Dailyprice,PA_No_Spaces,PA_Created FROM tbl_parkingavailability WHERE ('".$start."' BETWEEN P_fromDate AND PA_toDate) AND P_ID='".$_REQUEST['id']."'";
				
				$exe=mysqli_query($con,$sql);
				$res=mysqli_fetch_array($exe);
				$num=mysqli_num_rows($exe);
				$totalprice=$totalprice+$res['PA_P_Dailyprice'];
				if($num<1){ 
					$closed=1;
					$cl=1;
					$closeddates.=", ".date("F j, Y",strtotime($start));
				}
				//Check for Inventory
				$sql1="SELECT COUNT(TxnID) AS CNT FROM tbl_paymenttransaction WHERE ('".$start."' BETWEEN date_format(from_date, '%Y-%m-%d') AND date_format(to_date, '%Y-%m-%d')) AND Parking_ID='".$_REQUEST['id']."' AND TxnDate>='".$res['PA_Created']."' AND OrderStatus!='Cancelled'";
				$exe1=mysqli_query($con,$sql1);
				$res1=mysqli_fetch_array($exe1);
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
	else if($res['P_Pricingtype']=='weekly')
{
	$price=number_format($res['P_Weeklyprice'],2);
	$pr="Week";
	$fdate=strtotime($_REQUEST['fromDate']);
	$tdate=strtotime($_REQUEST['toDate']);
	$diff = ceil(($tdate - $fdate)/ ((3600*24)*7));
	$totalprice=$price*$diff;
}
else if($res['P_Pricingtype']=='event')
{
	$price=number_format($res['Event_price'],2);
	$pr="Event";
	$fdate=strtotime($_REQUEST['fromDate']);
	$tdate=strtotime($_REQUEST['toDate']);
	//$diff = ceil(($tdate - $fdate)/ 3600/24);
	$diff=1;
	$totalprice=$price*$diff;
	
}
else if($res['P_Pricingtype']=='special')
{
	$price=number_format($res['Event_price'],2);
	$pr="Special";
	$fdate=strtotime($_REQUEST['fromDate']);
	$tdate=strtotime($_REQUEST['toDate']);
	$diff = ceil(($tdate - $fdate)/ 3600/24);
	$totalprice=$price*$diff;
}
else if($res['P_Pricingtype']=='monthly')
{
	$price=number_format($res['P_Monthlyprice'],2);
	$pr="Month";
	$fdate=strtotime($_REQUEST['fromDate']);
	$tdate=strtotime($_REQUEST['toDate']);
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
		else {
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
		else {
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
	$fdate=strtotime($_REQUEST['fromDate']);
	$tdate=strtotime($_REQUEST['toDate']);
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
	else {
		$totalprice=$price*$diff;
	}
	
	
}else if($res['P_Pricingtype']=='minute')
{
	$otherprice=0;
	$price=number_format($res['P_FAmt'],2);
	$totalprice=$price;
	$pr="Hourly";
	$fdate=strtotime($_REQUEST['fromDate']);
	$tdate=strtotime($_REQUEST['toDate']);
	
	$diff=round(abs($tdate - $fdate) / 60,2);
	
	//echo "<br>".$diff = ceil($diff/ 1440);
	//$hl = ceil(($diff - $dl*60*60*24)/60/60);
	//echo "<br>".$diff = ceil(($diff - $dl*60*60*24 - $hl*60*60)/60);
	
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
		$csql="SELECT * FROM tbl_categories_settings WHERE Cat_ID=45 AND Parking_ID=".$_REQUEST['id'];
		$cexe=mysqli_query($con,$csql);
		$ext_setting=0;
		while($cres=mysqli_fetch_array($cexe))
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
	// Admin External Fees
	if($Park_AirportVenue==1)
	{
	if($ext_setting==0)
	{
		$csql="SELECT * FROM tbl_categories_settings WHERE Setting_Value>0 AND Cat_ID=45 AND Parking_ID=0";
		$cexe=mysqli_query($con,$csql);
		while($cres=mysqli_fetch_array($cexe))
		{
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
	$csql="SELECT * FROM tbl_sell_fee WHERE Parking_ID=".mysqli_real_escape_string($con,$_REQUEST['id']);
	$cexe=mysqli_query($con,$csql);
	
	while($cres=mysqli_fetch_array($cexe))
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
	/*if($res['P_Pricingtype']=='hourly')
	{
		$extfees=$extfees+$Overnight_Fee;
	}*/
	
	$totalpriceval=$totalprice+$extfees+$Overnight_Fee;
	
	
	//echo $Payment_Collection;
	$fromDate1=date("Y-m-d H:i:s",strtotime($_REQUEST['fromDate']));
	$toDate1=date("Y-m-d H:i:s",strtotime($_REQUEST['toDate']));
	$sql="DELETE FROM tbl_cart WHERE Cart_Type='Parking' AND Cart_ServiceID=".$_REQUEST['id']." AND Sess_ID='".session_id()."'";
	mysqli_query($con,$sql);
	// Pay at Lot calculation
	if($Payment_Type=='partial' && $Payment_Collection=='fixed' && $Payment_Collection_Amt>0)
		$Payat_Lot=$Payment_Collection_Amt;
	else if($Payment_Type=='partial' && $Payment_Collection=='percent' && $Payment_Collection_Amt>0)
		$Payat_Lot=($totalprice*$Payment_Collection_Amt/100)+$Way_Fee;
	else if($Payment_Type=='partial' && $Payment_Collection=='percentwithcharges' && $Payment_Collection_Amt>0)
		$Payat_Lot=(($totalpriceval-$Way_Fee)*$Payment_Collection_Amt/100)+$Way_Fee;
	else if($Payment_Type=='partial' && $Payment_Collection=='days' && $Payment_Collection_Amt>0)
		$Payat_Lot=$PayatLotdaysAmt;
	if($Payment_Type=='partial')
	{
		$Payat_Lot=$totalpriceval-$Payat_Lot;
		if($Payat_Lot<0)
			$Payat_Lot=0;
	}
	/*
	 $query="INSERT INTO tbl_cart(Sess_ID,Cart_UserID,Cart_ServiceID,Owner_ID,Cart_Quantity,Cart_Type,Amount,charges,Overnight_Fee,TotalAmount,Payat_Lot,Payment_Type,Parking_type,from_date,to_date,Cart_Created,NetPark_rate,NetPark_daily_rate,Way_Fee,Parking_Event_PE_ID)
		  VALUES('".session_id()."','".$User_ID."','".$_REQUEST['id']."','".$_REQUEST['Owner']."','".$diff."','Parking','".$totalprice."','".$extfees."','".$Overnight_Fee."','".round($totalpriceval,2)."','".$Payat_Lot."','".$Payment_Type."','".$_REQUEST['Pricingtype']."','".$fromDate1."','".$toDate1."',Now(),'".$NetPark_rate."','".$NetPark_daily_rate."','".$Way_Fee."','".$_REQUEST['PE_ID']."');";	
	
	$objPayTran = new PaymentInfoManagerDO();
	$objPayTran->UserID = $rec['user_id'];
	$objPayTran->ParkingID = $rec['garage_id'];
	$objPayTran->TotalAmount = $totalprice;
	$objPayTran->Quantity = $diff;
	$objPayTran->Discount = 0;
	$objPayTran->ExternalFees = $extfees;
	$objPayTran->OverNightFee = $Overnight_Fee;
	$objPayTran->PayAtLot = $Payat_Lot;
	$objPayTran->WayFee =$Way_Fee;
	$objPayTran->CartType = 'Parking';
	$objPayTran->OwnerID = $res['P_UserID'];
	return $objPayTran;*/
  }
  ?>