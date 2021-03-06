﻿<?php
/************************************************************
FILE :	ParkingManagerDA.php
PURPOSE: parking database transactions
AUTHOR: Gopalan Mani
DATE  : 05 FEB 2015
**************************************************************/
//include("../DataObjectLayer/GateParkingTransactionManagerDO.php");
//include("../DataObjectLayer/PaymentInfoManagerDO.php");
include("MyPDOManagerDA.php");

class ParkingManagerDA
{

	function GetFeaturedBooking($UserID, $ParkingID)
	{
	GLOBAL $con;
     $db = new MyPDOManagerDA();
	 $reccount=0;
    /*$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
    $ret = $db->query("CALL USP_GetFeaturedParking(?,?)",$ParkingID,$UserID);

    while ($o = $ret->fetch())
    {
        $array = (array) $o ;
        $txnid=$array['txnid'];
		$reccount++;
    } */


    //where $EntryDateTime between (bookingstartdate+30min) and (bookingendtime)

		$objFBookingInfo = new FeaturedBookingInfoManagerDO();

		if($reccount==0)
		{
			$objFBookingInfo->IsFeaturedBooking= 0;
			$objFBookingInfo->BookingID=0;
		}
		else
		{
			$objFBookingInfo->IsFeaturedBooking=1;
			$objFBookingInfo->BookingID=$txnid;
			$objFBookingInfo->EntryDateTime=$EntryDateTime;
			$objFBookingInfo->UserID = $rec['user_id'];
			$objFBookingInfo->ParkingID = $rec['garage_id'];
			$objFBookingInfo->TotalAmount = $totalpriceval;
			$objFBookingInfo->Quantity = $diff;
			$objFBookingInfo->Discount = 0;
			$objFBookingInfo->ExternalFees = $extfees;
			$objFBookingInfo->OverNightFee = $Overnight_Fee;
			$objFBookingInfo->PayAtLot = $Payat_Lot;
			$objFBookingInfo->WayFee =$Way_Fee;
			$objFBookingInfo->CartType = 'Parking';
			$objFBookingInfo->OwnerID = $res['P_UserID'];
			$objFBookingInfo->FromDate = $fdate;
			$objFBookingInfo->ToDate = $tdate;
		}

		return $objFBookingInfo;

	}

  function AddGateParkingTransaction(GateParkingTransactionManagerDO $object)
  {
	  GLOBAL $con;
    $retArr = array();
		$object->amount = $object->amount > 0 ? $object->amount :0;
		$sql_insert_trans = "insert into tbl_gate_parking_transaction set IsValidEnty=".$object->IsValidEnty.", gate_id='".$object->GateID."', garage_id='".$object->GarageID."', user_id='".$object->UserID."', in_time=now(), entry_mode='".$object->EntryMode."', ip_address='".$object->IPAddress."', user_agent='".$object->HTTPUserAgent."', out_time='0000-00-00 00:00:00', total_time='00:00:00', amount=".$object->amount.", FeaturedBookingID=".$object->BookingID.",  txn_id='".$object->txn_id."'";
		// echo $sql_insert_trans; die;
    $trans_insert_result=mysqli_query($con,$sql_insert_trans) or die(mysqli_error($con));
		if ($trans_insert_result===false)
		{
			$flag = -1;
			$msg = $this->dbMsg;
		} else
		{
			$trans_id = mysqli_insert_id($con);

		}
		$retArr['flag'] = $flag;
		$retArr['msg'] = $msg;
		$retArr['trans_id'] = $trans_id;
		return $retArr;
  }


  function GetAvailability($ParkingID)
  {
	  GLOBAL $con;
	  $sqll="select *,tbl_parkinglocations.*,tbl_parking.* from tbl_parking
	  INNER JOIN tbl_parkinglocations ON tbl_parkinglocations.Park_ID=tbl_parking.P_Location
	  where P_ID=".$ParkingID;
	  $exe=mysqli_query($con,$sqll);
	  $res=mysqli_fetch_array($exe);
	  $closed=0;
    if($res['P_Pricingtype']=='hourly')
	  {
      $closed=1;
    }
    else if($res['P_Pricingtype']=='weekly')
    {
      $closed=1;
    }
    else if($res['P_Pricingtype']=='monthly')
    {
      $closed=1;
    }
	  else if($res['P_Pricingtype']=='daily')
	  {
		  $start=date("Y-m-d");
		  if($res['P_Daily_Price_Type']=='Week')
		  {
				  $week=date('l', strtotime($start));
				  $sqlw="SELECT PA_P_Dailyprice AS PRICE,PA_No_Spaces AS SPACES,PA_Updated FROM tbl_parkingweekdayavailability WHERE P_fromDate='".$start."' AND P_ID=".$ParkingID;
		      $resw = mysqli_fetch_array(mysqli_query($con,$sqlw));
				  //Check for Inventory
				  $sql1="SELECT COUNT(TxnID) AS CNT FROM tbl_paymenttransaction WHERE ('".$start."' BETWEEN date_format(from_date, '%Y-%m-%d') AND date_format(to_date, '%Y-%m-%d')) AND TxnDate>='".$resw['PA_Updated']."' AND Parking_ID='".$ParkingID."' AND OrderStatus!='Cancelled'";
		      $exe1=mysqli_query($con,$sql1);
				  $res1=mysqli_fetch_array($exe1);
				  if($res1['CNT']>=$resw['SPACES'])
				  {
					  $closed=0;
				  }
          else
          {
            $closed=1;
          }
    	  }
			  else
			  {
				  $sql="SELECT Park_ID,PA_P_Dailyprice,PA_No_Spaces,PA_Created FROM tbl_parkingavailability WHERE ('".$start."' BETWEEN P_fromDate AND PA_toDate) AND P_ID='".$ParkingID."'";
				  $exe=mysqli_query($con,$sql);
				  $res=mysqli_fetch_array($exe);
				  $num=mysqli_num_rows($exe);
				  if($num<1){
					  $closed=0;
				  }
          else
          {
          $closed=1;
          }

				  //Check for Inventory
				  $sql1="SELECT COUNT(TxnID) AS CNT FROM tbl_paymenttransaction WHERE ('".$start."' BETWEEN date_format(from_date, '%Y-%m-%d') AND date_format(to_date, '%Y-%m-%d')) AND Parking_ID='".$ParkingID."' AND TxnDate>='".$res['PA_Created']."' AND OrderStatus!='Cancelled'";
				  $exe1=mysqli_query($con,$sql1);
				  $res1=mysqli_fetch_array($exe1);
				  if($res1['CNT']>=$res['PA_No_Spaces'])
				  {
					  $closed=0;
				  }
          else
          {
            $closed=1;
          }
			  }

	  }
    return $closed;
  }

  function GetTransactionAmount($transaction_id)
{
	GLOBAL $con;
	$sqll="select *,now() as out_time from tbl_gate_parking_transaction where transaction_id=".$transaction_id;
	$exe=mysqli_query($con,$sqll);
	$rec=mysqli_fetch_array($exe);

	$sqll="select tbl_parkinglocations.*,tbl_parking.* from tbl_parking
	INNER JOIN tbl_parkinglocations ON tbl_parkinglocations.Park_ID=tbl_parking.P_Location
	where P_ID=".$rec['garage_id'];
	$exe=mysqli_query($con,$sqll);
	$res=mysqli_fetch_array($exe);

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

  $fdate=strtotime($rec['in_time']);
  $tdate=strtotime($rec['out_time']);


	if($fdate>=$tdate)
	{
		echo "Invalid Date";
		exit;
	}



	if($res['P_Pricingtype']=='daily')
	{

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
		if($days<1)
			$days=1;
		$diff=$days;





   /* if($min_reservation> 0 && $min_reservation>$diff)
		{
			$closed=1;
			echo $min_reservation."-day minimum stay required.";
			exit;
		}*/


		$ik=1;
		$closed=0;
		$closeddates="";
		$PayatLotdaysAmt=0;
		while ($ik<=$days) {
			$cl=0;

			if($res['P_Daily_Price_Type']=='Week')
			{
				$week=date('l', strtotime($start));
				$sqlw="SELECT PA_P_Dailyprice AS PRICE,PA_No_Spaces AS SPACES,PA_Updated FROM tbl_parkingweekdayavailability WHERE P_fromDate='".$start."' AND P_ID=".$rec['garage_id'];
				$resw = mysqli_fetch_array(mysqli_query($con,$sqlw));
				$totalprice=$totalprice+$resw['PRICE'];
				if($Payment_Type=='partial' && $Payment_Collection=='days' && $ik<=$Payment_Collection_Amt)
					$PayatLotdaysAmt+=$resw['PRICE'];
				//Check for Inventory
				$sql1="SELECT COUNT(TxnID) AS CNT FROM tbl_paymenttransaction WHERE ('".$start."' BETWEEN date_format(from_date, '%Y-%m-%d') AND date_format(to_date, '%Y-%m-%d')) AND TxnDate>='".$resw['PA_Updated']."' AND Parking_ID='".$rec['garage_id']."' AND OrderStatus!='Cancelled'";
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
				$sql="SELECT Park_ID,PA_P_Dailyprice,PA_No_Spaces,PA_Created FROM tbl_parkingavailability WHERE ('".$start."' BETWEEN P_fromDate AND PA_toDate) AND P_ID='".$rec['garage_id']."'";
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
				$sql1="SELECT COUNT(TxnID) AS CNT FROM tbl_paymenttransaction WHERE ('".$start."' BETWEEN date_format(from_date, '%Y-%m-%d') AND date_format(to_date, '%Y-%m-%d')) AND Parking_ID='".$rec['garage_id']."' AND TxnDate>='".$res['PA_Created']."' AND OrderStatus!='Cancelled'";
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
		$csql="SELECT * FROM tbl_categories_settings WHERE Cat_ID=45 AND Parking_ID=".$rec['garage_id'];
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
	$csql="SELECT * FROM tbl_sell_fee WHERE Parking_ID=".mysqli_real_escape_string($con,$rec['garage_id']);
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


	$objPayTran = new PaymentInfoManagerDO();

	$objPayTran->UserID = $rec['user_id'];
	$objPayTran->ParkingID = $rec['garage_id'];
	$objPayTran->TotalAmount = $totalpriceval;
	$objPayTran->Quantity = $diff;
	$objPayTran->Discount = 0;
	$objPayTran->ExternalFees = $extfees;
	$objPayTran->OverNightFee = $Overnight_Fee;
	$objPayTran->PayAtLot = $Payat_Lot;
	$objPayTran->WayFee =$Way_Fee;
	$objPayTran->CartType = 'Parking';
	$objPayTran->OwnerID = $res['P_UserID'];
	$objPayTran->FromDate = $fdate;
	$objPayTran->ToDate = $tdate;
	return $objPayTran;
}



 function AddTransaction(PaymentInfoManagerDO $objPayinfoDO)
 {
  GLOBAL $con;
  $Query="INSERT INTO tbl_paymenttransaction (UsrID,Owner_ID,Parking_ID,Activity_ID,Ticket_ID,Movie_ID,Cat_ID,from_date,to_date,Ticket_Title,Ticket_EventID,RetailAmount,TotalRetailAmount,Ticket_EventVenue,Ticket_Section,Ticket_Row,Ticket_Owner,Show_Time,Movie_Name,Club_Number,TxnDate,PaymentSource,quantity,Amount,Discount,charges,Overnight_Fee,Way_Fee,charges_details,TotalAmount,Payat_Lot,Payment_Type,code,Status,Ticket_Type,Ticket_Quantity,care_payment_type,Parking_type,Card_Type,Card_Name,Card_Number,Card_Expiry_Month,Card_Expiry_Year,Card_Zip,Card_Address,Card_State,Card_City,DeliveryMethod,DeliveryState,DeliveryCity,DeliveryZip,DeliveryEmail,DeliveryAddress,PaymentDesc,points,NetPark_rate,NetPark_daily_rate,Instant_MovieTicket,Theater_Chain,Movie_Tickets,Amount_Code,Parking_Event_PE_ID)
			VALUES
			('".$objPayinfoDO->UserID."','".$objPayinfoDO->OwnerID."','".$objPayinfoDO->ParkingID."','0','0','0', '45','".$objPayinfoDO->FromDate."','".$objPayinfoDO->ToDate."','','','','','','','','','','','',now(),'Credit Card','".$objPayinfoDO->Quantity."','".$objPayTran->Amount."','".$objPayTran->Discount."','".$objPayTran->ExternalFees."','".$objPayinfoDO->OverNightFee."','".$objPayinfoDO->WayFee."','','".$objPayinfoDO->TotalAmount."','".$objPayinfoDO->PayAtLot."','','','1','','0','','".$result[Parking_type]."','".mysqli_real_escape_string($con,$ResCC['Card_Type'])."','".mysqli_real_escape_string($con,$ResCC['Card_FirstName'])."',AES_ENCRYPT('".$ResCC['CC_NUMBER']."','".$EncryptKey."'),'".mysqli_real_escape_string($con,$ResCC['Card_Exp_Month'])."','".mysqli_real_escape_string($con,$ResCC['Card_Exp_Year'])."','".mysqli_real_escape_string($con,$ResCC['Card_Zip'])."','".mysqli_real_escape_string($con,$ResCC['Card_Street'])."','".mysqli_real_escape_string($con,strtoupper($ResCC['Card_State']))."','".mysqli_real_escape_string($con,$ResCC['Card_City'])."','".mysqli_real_escape_string($con,$_REQUEST['DeliveryMethod'.$pk])."','','','','".mysqli_real_escape_string($con,$Delivery_Email)."','','".$Instructions."','".$Points."','','','','','',AES_ENCRYPT('".$_REQUEST['ver_code']."','".$EncryptKey."'),'')";
	mysqli_query($con,$Query);
	$transaction_id=mysqli_insert_id($con);

	return $transaction_id;

 }

function IsAvailableCreditCard($User_ID)
{
	GLOBAL $con;
	//  Credit card details
	$retvalue=false;
	$sqlCC="SELECT Card_Number FROM tbl_creditcarddetails WHERE Card_User_ID=".$User_ID;
	$exeCC=mysqli_query($con,$sqlCC);
	$NUMCC = mysqli_num_rows($exeCC);

	//$NUMCC=mysqli_query($con,$exeCC);
	if($NUMCC>0)
		$retvalue=true;
	return $retvalue;
}
 function ValidPayment($objPayTran)
{
	GLOBAL $con;
	$TotalAmount=$objPayTran->TotalAmount;
	$TotalAmount=preg_replace('#[^0-9.]+#','',(number_format(($TotalAmount),2)));
	$User_ID=$objPayTran->User_ID;
	GLOBAL $EncryptKey;

	//  Credit card details
	$sqlCC="SELECT *,AES_DECRYPT(Card_Number,'".$EncryptKey."') AS CC_NUMBER FROM tbl_creditcarddetails WHERE Card_User_ID=".$User_ID;
	$exeCC=mysqli_query($con,$sqlCC);
	$ResCC=mysqli_fetch_array($exeCC);

	//  Credit card details
	$sql="SELECT * FROM tbl_registeration WHERE id=".$User_ID;
	$exe=mysqli_query($con,$sql);
	$Res=mysqli_fetch_array($exe);
	// Check AVS Verifivation with Zero Value

	// Firstdata Payment Integration
	//$wsdl = "https://ws.firstdataglobalgateway.com/fdggwsapi/services/order.wsdl";              	// PROD WSDL
	$wsdl = "https://ws.merchanttest.firstdataglobalgateway.com/fdggwsapi/services/order.wsdl";   // CTE WSDL - Note lines 74 and 75 regarding CTE

	$userid = "WS1001310340._.1";                      // "Username" from WS000000._.1.auth.txt
	$password = "8aRvEzMn";                               // "Password" from WS000000._.1.auth.txt
	$pemlocation = realpath("fdcode/WS1001310340._.1.pem");  // Location of "WS000000._.1.pem"
	$kslocation = realpath("fdcode/WS1001310340._.1.key");   // Location of "WS000000._.1.key"
	$keyname = "ckp_1414348609";                  // From WS000000._.1.key.pw.txt

	//  AVS Verification
	$transactiontype = "sale";
	$creditcardnumber = $ResCC['CC_NUMBER'];
	$cardexpirationmonth = $ResCC['Card_Exp_Month'];
	$cardexpirationyear = substr($ResCC['Card_Exp_Year'],(strlen($ResCC['Card_Exp_Year'])-2),2);
	$orderAmounttest=0;
	$chargetotal=preg_replace('#[^0-9.]+#','',(number_format(($orderAmounttest),2)));
	// Billing Address
	$Billing_Name=mysqli_real_escape_string($con,$ResCC['CC_First_Name']);
	$Billing_Address=mysqli_real_escape_string($con,$ResCC['Card_Street']);
	$Billing_City=mysqli_real_escape_string($con,$ResCC['Card_City']);
	$Billing_State=mysqli_real_escape_string($con,$ResCC['Card_State']);
	$Billing_Zip=mysqli_real_escape_string($con,$ResCC['Card_Zip']);

	$cardcvv=$_REQUEST['ver_code'];
	$body = "<SOAP-ENV:Envelope xmlns:SOAP-ENV=\"http://schemas.xmlsoap.org/soap/envelope/\"><SOAP-ENV:Header/><SOAP-ENV:Body><ns4:FDGGWSApiOrderRequest xmlns:ns2=\"http://secure.linkpt.net/fdggwsapi/schemas_us/v1\" xmlns:ns3=\"http://secure.linkpt.net/fdggwsapi/schemas_us/a1\" xmlns:ns4=\"http://secure.linkpt.net/fdggwsapi/schemas_us/fdggwsapi\"><ns2:Transaction><ns2:CreditCardTxType><ns2:Type>";
	$body .= $transactiontype;  //  Transaction Type
	$body .= "</ns2:Type></ns2:CreditCardTxType>";
	$body .= "<ns2:CreditCardData>";
	$body .= "<ns2:CardNumber>";
	$body .= $creditcardnumber;  //  Credit Card Number
	$body .= "</ns2:CardNumber>";
	$body .= "<ns2:ExpMonth>";
	$body .= $cardexpirationmonth;  //  Card Expiration Month
	$body .= "</ns2:ExpMonth><ns2:ExpYear>";
	$body .= $cardexpirationyear;  //  Card Expiration Year
	$body .= "</ns2:ExpYear>";
	$body .= "<ns2:CardCodeValue>";
	$body .= $cardcvv;  //  CVV
	$body .= "</ns2:CardCodeValue>";
	$body .= "<ns2:CardCodeIndicator>PROVIDED</ns2:CardCodeIndicator>";
	$body .= "</ns2:CreditCardData><ns2:Payment><ns2:ChargeTotal>";
	$body .= $chargetotal;  //  Charge Total
	$body .= "</ns2:ChargeTotal></ns2:Payment>";

	//$body .= "<ns2:CardCodeValue>";
	//$body .= $cardcvv;  //  CVV
	//$body .= "</ns2:CardCodeValue>";


	// Plz Remove below
	$body .= "<ns2:TransactionDetails>
	<ns2:Recurring>No</ns2:Recurring>
	<ns2:TransactionOrigin>ECI</ns2:TransactionOrigin>
	</ns2:TransactionDetails>";
	// Plz Remove above

	//Billing
	$body .= "<ns2:Billing><ns2:Name>".$Billing_Name."</ns2:Name><ns2:Address1>".$Billing_Address."</ns2:Address1><ns2:Address2>172</ns2:Address2><ns2:City>".$Billing_City."</ns2:City><ns2:State>".$Billing_State."</ns2:State><ns2:Zip>".$Billing_Zip."</ns2:Zip><ns2:Country>USA</ns2:Country></ns2:Billing>";

	//Shipping
	//$body .= "<ns2:Shipping><ns2:Address1>".$DeliveryAddress."</ns2:Address1><ns2:City>".$DeliveryCity."</ns2:City><ns2:State>".$DeliveryState."</ns2:State><ns2:Zip>".$DeliveryZip."</ns2:Zip><ns2:Country>USA</ns2:Country></ns2:Shipping>";

	$body .= "</ns2:Transaction>";

	$body .= "</ns4:FDGGWSApiOrderRequest></SOAP-ENV:Body></SOAP-ENV:Envelope>";

	// initializing cURL with the IPG API URL:
	$ch = curl_init($wsdl);

	// setting the request type to POST:
	curl_setopt($ch, CURLOPT_POST, 1);

	// setting the content type:
	curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: text/xml"));

	// setting the authorization method to BASIC:
	curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

	// supplying your credentials:
	curl_setopt($ch, CURLOPT_USERPWD, "$userid:$password");

	// filling the request body with your SOAP message:
	curl_setopt($ch, CURLOPT_POSTFIELDS, $body);

	// telling cURL to verify the server certificate:
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

	// setting the path where cURL can find the certificate to verify the
	// received server certificate against:

	curl_setopt($ch, CURLOPT_SSLCERT, $pemlocation);
	curl_setopt($ch, CURLOPT_SSLKEY, $kslocation);                              // For CTE, comment out this line
	curl_setopt($ch, CURLOPT_SSLKEYPASSWD, $keyname);                           // For CTE, comment out this line

	// telling cURL to return the HTTP response body as operation result
	// value when calling curl_exec:
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

	// calling cURL and saving the SOAP response message in a variable which
	// contains a string like "<SOAP-ENV:Envelope ...>...</SOAP-ENV:Envelope>":

	$result = curl_exec($ch);

	// closing cURL:
	curl_close($ch);

	$array = xml2array($result);
	$AVSResponse = $array['SOAP-ENV:Envelope']['SOAP-ENV:Body']['fdggwsapi:FDGGWSApiOrderResponse']['fdggwsapi:AVSResponse'];

	if($AVSResponse<>'YYYM' && $AVSResponse<>'YYAM' && $AVSResponse<>'YYDM' && $AVSResponse<>'YYFM' && $AVSResponse<>'YYMM')
	{
		if($AVSResponse=='NYZM' && $TotalAmount<=250)
		{

		}
		else {
		//$visit_ip = getenv(REMOTE_ADDR);
		//$sql="INSERT INTO tbl_firstdatatransaction(TxnID,UserID,Amount,CardType,CardName,CardNumber,Card_Epx_Month,Card_Exp_Year,Card_Address,Card_State,Card_City,Card_Zip,FDT_CommercialServiceProvider,FDT_TransactionTime,FDT_TransactionID,FDT_ProessorReferenceNumber,FDT_ProcessorResponseMessage,FDT_ErrorMessage,FDT_OrderID,FDT_ApprovalCode,FDT_AVSResponse,FDT_TDate,FDT_TransactionResult,FDT_ProcessorApprovalCode,FDT_CalculatedTax,FDT_CalculatedShipping,FDT_TransactionScore,FDT_AuthenticationResponseCode,Created_On,Ip_Address) VALUES('".$Payment_Id1."','".$User_ID."','".$chargetotal1."','".mysqli_real_escape_string($con,$ResCC['Card_Type'])."','".mysqli_real_escape_string($con,$ResCC['Card_FirstName'])."',AES_ENCRYPT('".$ResCC['CC_NUMBER']."','".$EncryptKey."'),'".mysqli_real_escape_string($con,$ResCC['Card_Exp_Month'])."','".mysqli_real_escape_string($con,$ResCC['Card_Exp_Year'])."','".mysqli_real_escape_string($con,$ResCC['Card_Street'])."','".mysqli_real_escape_string($con,$ResCC['Card_State'])."','".mysqli_real_escape_string($con,$ResCC['Card_City'])."','".mysqli_real_escape_string($con,$ResCC['Card_Zip'])."','".$CommercialServiceProvider."','".$TransactionTime."','".$TransactionID."','".$ProessorReferenceNumber."','".$ProcessorResponseMessage."','".$ErrorMessage."','".$OrderID."','".$ApprovalCode."','".$AVSResponse."','".$TDate."','".$TransactionResult."','".$ProcessorApprovalCode."','".$CalculatedTax."','".$CalculatedShipping."','".$TransactionScore."','".$AuthenticationResponseCode."',Now(),'".$visit_ip."')";

		//mysqli_query($con,$sql);
			echo 'AVS Failed';
			exit;
		}
	}
	// End AVS Verification

	// Original Payment starts here

	$transactiontype = "sale";
	$creditcardnumber = $ResCC['CC_NUMBER'];
	$cardexpirationmonth = $ResCC['Card_Exp_Month'];
	$cardexpirationyear = substr($ResCC['Card_Exp_Year'],(strlen($ResCC['Card_Exp_Year'])-2),2);

	$chargetotal=preg_replace('#[^0-9.]+#','',(number_format(($TotalAmount),2)));

	// Billing Address
	$Billing_Name=mysqli_real_escape_string($con,$ResCC['CC_First_Name']);
	$Billing_Address=mysqli_real_escape_string($con,$ResCC['Card_Street']);
	$Billing_City=mysqli_real_escape_string($con,$ResCC['Card_City']);
	$Billing_State=mysqli_real_escape_string($con,$ResCC['Card_State']);
	$Billing_Zip=mysqli_real_escape_string($con,$ResCC['Card_Zip']);

	$cardcvv=$_REQUEST['ver_code'];
	$body = "<SOAP-ENV:Envelope xmlns:SOAP-ENV=\"http://schemas.xmlsoap.org/soap/envelope/\"><SOAP-ENV:Header/><SOAP-ENV:Body><ns4:FDGGWSApiOrderRequest xmlns:ns2=\"http://secure.linkpt.net/fdggwsapi/schemas_us/v1\" xmlns:ns3=\"http://secure.linkpt.net/fdggwsapi/schemas_us/a1\" xmlns:ns4=\"http://secure.linkpt.net/fdggwsapi/schemas_us/fdggwsapi\"><ns2:Transaction><ns2:CreditCardTxType><ns2:Type>";
	$body .= $transactiontype;  //  Transaction Type
	$body .= "</ns2:Type></ns2:CreditCardTxType>";
	$body .= "<ns2:CreditCardData>";
	$body .= "<ns2:CardNumber>";
	$body .= $creditcardnumber;  //  Credit Card Number
	$body .= "</ns2:CardNumber>";
	$body .= "<ns2:ExpMonth>";
	$body .= $cardexpirationmonth;  //  Card Expiration Month
	$body .= "</ns2:ExpMonth><ns2:ExpYear>";
	$body .= $cardexpirationyear;  //  Card Expiration Year
	$body .= "</ns2:ExpYear>";
	$body .= "<ns2:CardCodeValue>";
	$body .= $cardcvv;  //  CVV
	$body .= "</ns2:CardCodeValue>";
	$body .= "</ns2:CreditCardData><ns2:Payment><ns2:ChargeTotal>";
	$body .= $chargetotal;  //  Charge Total
	$body .= "</ns2:ChargeTotal></ns2:Payment>";

	//$body .= "<ns2:CardCodeValue>";
	//$body .= $cardcvv;  //  CVV
	//$body .= "</ns2:CardCodeValue>";


	// Plz Remove below
	$body .= "<ns2:TransactionDetails>
	<ns2:Recurring>No</ns2:Recurring>
	<ns2:TransactionOrigin>ECI</ns2:TransactionOrigin>
	</ns2:TransactionDetails>";
	// Plz Remove above


	//Billing
	$body .= "<ns2:Billing><ns2:Name>".$Billing_Name."</ns2:Name><ns2:Address1>".$Billing_Address."</ns2:Address1><ns2:City>".$Billing_City."</ns2:City><ns2:State>".$Billing_State."</ns2:State><ns2:Zip>".$Billing_Zip."</ns2:Zip><ns2:Country>USA</ns2:Country></ns2:Billing>";


	$body .= "</ns2:Transaction>";

	$body .= "</ns4:FDGGWSApiOrderRequest></SOAP-ENV:Body></SOAP-ENV:Envelope>";

	// initializing cURL with the IPG API URL:
	$ch = curl_init($wsdl);

	// setting the request type to POST:
	curl_setopt($ch, CURLOPT_POST, 1);

	// setting the content type:
	curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: text/xml"));

	// setting the authorization method to BASIC:
	curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

	// supplying your credentials:
	curl_setopt($ch, CURLOPT_USERPWD, "$userid:$password");

	// filling the request body with your SOAP message:
	curl_setopt($ch, CURLOPT_POSTFIELDS, $body);

	// telling cURL to verify the server certificate:
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

	// setting the path where cURL can find the certificate to verify the
	// received server certificate against:

	curl_setopt($ch, CURLOPT_SSLCERT, $pemlocation);
	curl_setopt($ch, CURLOPT_SSLKEY, $kslocation);                              // For CTE, comment out this line
	curl_setopt($ch, CURLOPT_SSLKEYPASSWD, $keyname);                           // For CTE, comment out this line

	// telling cURL to return the HTTP response body as operation result
	// value when calling curl_exec:
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

	// calling cURL and saving the SOAP response message in a variable which
	// contains a string like "<SOAP-ENV:Envelope ...>...</SOAP-ENV:Envelope>":

	$result = curl_exec($ch);

	// closing cURL:
	curl_close($ch);



	$array = xml2array($result);

	$CommercialServiceProvider = $array['SOAP-ENV:Envelope']['SOAP-ENV:Body']['fdggwsapi:FDGGWSApiOrderResponse']['fdggwsapi:CommercialServiceProvider'];
	$TransactionTime = $array['SOAP-ENV:Envelope']['SOAP-ENV:Body']['fdggwsapi:FDGGWSApiOrderResponse']['fdggwsapi:TransactionTime'];
	$TransactionID = $array['SOAP-ENV:Envelope']['SOAP-ENV:Body']['fdggwsapi:FDGGWSApiOrderResponse']['fdggwsapi:TransactionID'];
	$ProessorReferenceNumber = $array['SOAP-ENV:Envelope']['SOAP-ENV:Body']['fdggwsapi:FDGGWSApiOrderResponse']['fdggwsapi:ProessorReferenceNumber'];
	$ProcessorResponseMessage = $array['SOAP-ENV:Envelope']['SOAP-ENV:Body']['fdggwsapi:FDGGWSApiOrderResponse']['fdggwsapi:ProcessorResponseMessage'];
	$ErrorMessage = $array['SOAP-ENV:Envelope']['SOAP-ENV:Body']['fdggwsapi:FDGGWSApiOrderResponse']['fdggwsapi:ErrorMessage'];
	$OrderID = $array['SOAP-ENV:Envelope']['SOAP-ENV:Body']['fdggwsapi:FDGGWSApiOrderResponse']['fdggwsapi:OrderID'];
	$ApprovalCode = $array['SOAP-ENV:Envelope']['SOAP-ENV:Body']['fdggwsapi:FDGGWSApiOrderResponse']['fdggwsapi:ApprovalCode'];
	$AVSResponse = $array['SOAP-ENV:Envelope']['SOAP-ENV:Body']['fdggwsapi:FDGGWSApiOrderResponse']['fdggwsapi:AVSResponse'];
	$TDate = $array['SOAP-ENV:Envelope']['SOAP-ENV:Body']['fdggwsapi:FDGGWSApiOrderResponse']['fdggwsapi:TDate'];
	$TransactionResult = $array['SOAP-ENV:Envelope']['SOAP-ENV:Body']['fdggwsapi:FDGGWSApiOrderResponse']['fdggwsapi:TransactionResult'];
	$ProessorResponseCode = $array['SOAP-ENV:Envelope']['SOAP-ENV:Body']['fdggwsapi:FDGGWSApiOrderResponse']['fdggwsapi:ProessorResponseCode'];
	$ProcessorApprovalCode = $array['SOAP-ENV:Envelope']['SOAP-ENV:Body']['fdggwsapi:FDGGWSApiOrderResponse']['fdggwsapi:ProcessorApprovalCode'];
	$CalculatedTax = $array['SOAP-ENV:Envelope']['SOAP-ENV:Body']['fdggwsapi:FDGGWSApiOrderResponse']['fdggwsapi:CalculatedTax'];
	$CalculatedShipping = $array['SOAP-ENV:Envelope']['SOAP-ENV:Body']['fdggwsapi:FDGGWSApiOrderResponse']['fdggwsapi:CalculatedShipping'];
	$TransactionScore = $array['SOAP-ENV:Envelope']['SOAP-ENV:Body']['fdggwsapi:FDGGWSApiOrderResponse']['fdggwsapi:TransactionScore'];
	$AuthenticationResponseCode = $array['SOAP-ENV:Envelope']['SOAP-ENV:Body']['fdggwsapi:FDGGWSApiOrderResponse']['fdggwsapi:AuthenticationResponseCode'];

	$TransactionResult=getTransactionResult($result);
	if($ErrorMessage=='' || $ErrorMessage=='Array')
		$ErrorMessage=$TransactionResult;
	if($TransactionResult=='APPROVED' && $TransactionID>0)
	{
		echo "payment Sucess";
		exit;
	}
	// Original Payment ends here
}

/*  xml2array() will convert the given XML text to an array in the XML structure.  */

function xml2array($contents, $get_attributes=1, $priority = 'tag')
{
	GLOBAL $con;
	if(!$contents) return array();

	if(!function_exists('xml_parser_create')) {
	//print "'xml_parser_create()' function not found!";
	return array();
	}

	//Get the XML parser of PHP - PHP must have this module for the parser to work
	$parser = xml_parser_create('');
	xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, "UTF-8"); # http://minutillo.com/steve/weblog/2004/6/17/php-xml-and-character-encodings-a-tale-of-sadness-rage-and-data-loss
	xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
	xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
	xml_parse_into_struct($parser, trim($contents), $xml_values);
	xml_parser_free($parser);

	if(!$xml_values) return;

	//Initializations
	$xml_array = array();
	$parents = array();
	$opened_tags = array();
	$arr = array();

	$current = &$xml_array; //Reference

	//Go through the tags.
	$repeated_tag_index = array();//Multiple tags with same name will be turned into an array
	foreach($xml_values as $data) {
	unset($attributes,$value);//Remove existing values, or there will be trouble

	//This command will extract these variables into the foreach scope
	// tag(string), type(string), level(int), attributes(array).
	extract($data);//We could use the array by itself, but this is cooler.

	$result = array();
	$attributes_data = array();

	if(isset($value)) {
	if($priority == 'tag') $result = $value;
	else $result['value'] = $value; //Put the value in a assoc array if we are in the 'Attribute' mode
	}

	//Set the attributes too.
	if(isset($attributes) and $get_attributes) {
	foreach($attributes as $attr => $val) {
	if($priority == 'tag') $attributes_data[$attr] = $val;
	else $result['attr'][$attr] = $val; //Set all the attributes in a array called 'attr'
	}
	}

	//See tag status and do the needed.
	if($type == "open") {//The starting of the tag '<tag>'
	$parent[$level-1] = &$current;
	if(!is_array($current) or (!in_array($tag, array_keys($current)))) { //Insert New tag
	$current[$tag] = $result;
	if($attributes_data) $current[$tag. '_attr'] = $attributes_data;
	$repeated_tag_index[$tag.'_'.$level] = 1;

	$current = &$current[$tag];

	} else { //There was another element with the same tag name

	if(isset($current[$tag][0])) {//If there is a 0th element it is already an array
	$current[$tag][$repeated_tag_index[$tag.'_'.$level]] = $result;
	$repeated_tag_index[$tag.'_'.$level]++;
	} else {//This section will make the value an array if multiple tags with the same name appear together
	$current[$tag] = array($current[$tag],$result);//This will combine the existing item and the new item together to make an array
	$repeated_tag_index[$tag.'_'.$level] = 2;

	if(isset($current[$tag.'_attr'])) { //The attribute of the last(0th) tag must be moved as well
	$current[$tag]['0_attr'] = $current[$tag.'_attr'];
	unset($current[$tag.'_attr']);
	}

	}
	$last_item_index = $repeated_tag_index[$tag.'_'.$level]-1;
	$current = &$current[$tag][$last_item_index];
	}

	} elseif($type == "complete") { //Tags that ends in 1 line '<tag />'
	//See if the key is already taken.
	if(!isset($current[$tag])) { //New Key
	$current[$tag] = $result;
	$repeated_tag_index[$tag.'_'.$level] = 1;
	if($priority == 'tag' and $attributes_data) $current[$tag. '_attr'] = $attributes_data;

	} else { //If taken, put all things inside a list(array)
	if(isset($current[$tag][0]) and is_array($current[$tag])) {//If it is already an array...

	// ...push the new element into that array.
	$current[$tag][$repeated_tag_index[$tag.'_'.$level]] = $result;

	if($priority == 'tag' and $get_attributes and $attributes_data) {
	$current[$tag][$repeated_tag_index[$tag.'_'.$level] . '_attr'] = $attributes_data;
	}
	$repeated_tag_index[$tag.'_'.$level]++;

	} else { //If it is not an array...
	$current[$tag] = array($current[$tag],$result); //...Make it an array using using the existing value and the new value
	$repeated_tag_index[$tag.'_'.$level] = 1;
	if($priority == 'tag' and $get_attributes) {
	if(isset($current[$tag.'_attr'])) { //The attribute of the last(0th) tag must be moved as well

	$current[$tag]['0_attr'] = $current[$tag.'_attr'];
	unset($current[$tag.'_attr']);
	}

	if($attributes_data) {
	$current[$tag][$repeated_tag_index[$tag.'_'.$level] . '_attr'] = $attributes_data;
	}
	}
	$repeated_tag_index[$tag.'_'.$level]++; //0 and 1 index is already taken
	}
	}

	} elseif($type == 'close') { //End of tag '</tag>'
	$current = &$parent[$level-1];
	}
	}

	return($xml_array);
}
function getParkingPriceAndTotal($fromdate,$todate,$P_ID){
    GLOBAL $con;
    // echo $fromdate;
    // echo $todate;

    $sqll="select *,tbl_parkinglocations.*,tbl_parking.* from tbl_parking
    INNER JOIN tbl_parkinglocations ON tbl_parkinglocations.Park_ID=tbl_parking.P_Location
    where P_ID=".$P_ID;
    $exe=mysqli_query($con,$sqll);
    $res=mysqli_fetch_array($exe);
    if($res['min_reservation']>0)
        $min_reservation=$res['min_reservation'];
    else
        $min_reservation=0;
    $fdate=strtotime($fromdate);
    $tdate=strtotime($todate);
	$Park_AirportVenue=$res['Park_AirportVenue'];
    if($Park_AirportVenue<>1)
        $Park_AirportVenue=0;
    $totalprice=$price=$diff=0;
    // echo $res['P_Pricingtype'];
    if($res['P_Pricingtype']=='daily')
	{
        if($min_reservation>0)
        //
		$start = $fdate;
		$end = $tdate;
		$start=date("Y-m-d",$fdate);
		$end=date("Y-m-d",$tdate);
		$num=1;
		$error="";
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

			//$days--;
			$days=$days;
		}

		if($hours>0 || $minuts>0)
			$days++;


        $diff=$days;
		if($min_reservation> 0 && $min_reservation>$diff)
		{
			 $tdate=strtotime(date('Y-m-d g:i A',strtotime("+$min_reservation day",$fdate)));
             $start=date("Y-m-d",$fdate);
             $end=date("Y-m-d",$tdate);
             $num=1;
             $error="";
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

                 //$days--;
                 $days=$days;
             }

             if($hours>0 || $minuts>0)
                 $days++;


             $diff=$days;
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
				$sqlw="SELECT PA_P_Dailyprice AS PRICE,PA_No_Spaces AS SPACES,PA_Updated FROM tbl_parkingweekdayavailability WHERE P_fromDate='".$start."' AND P_ID=".$P_ID;
				$resw = mysqli_fetch_array(mysqli_query($con,$sqlw));
                $price=$resw['PRICE'];
				$totalprice=$totalprice+$resw['PRICE'];
				if($Payment_Type=='partial' && $Payment_Collection=='days' && $ik<=$Payment_Collection_Amt)
					$PayatLotdaysAmt+=$resw['PRICE'];
				//Check for Inventory
				$sql1="SELECT COUNT(TxnID) AS CNT FROM tbl_paymenttransaction WHERE ('".$start."' BETWEEN date_format(from_date, '%Y-%m-%d') AND date_format(to_date, '%Y-%m-%d')) AND Parking_ID='".$P_ID."' AND OrderStatus!='Cancelled'";

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
				$sql="SELECT Park_ID,PA_P_Dailyprice,PA_No_Spaces,PA_Created FROM tbl_parkingavailability WHERE ('".$start."' BETWEEN P_fromDate AND PA_toDate) AND P_ID='".$P_ID."'";
				$exe=mysqli_query($con,$sql);
				$res1=mysqli_fetch_array($exe);
				$num=mysqli_num_rows($exe);
                $price=$res1['PA_P_Dailyprice'];
				$totalprice=$totalprice+$res1['PA_P_Dailyprice'];
				if($num<1){
					$closed=1;
					$cl=1;
					$closeddates.=", ".date("F j, Y",strtotime($start));
				}
				//Check for Inventory
				$sql1="SELECT COUNT(TxnID) AS CNT FROM tbl_paymenttransaction WHERE ('".$start."' BETWEEN date_format(from_date, '%Y-%m-%d') AND date_format(to_date, '%Y-%m-%d')) AND Parking_ID='".$P_ID."' AND TxnDate>='".$res1['PA_Created']."' AND OrderStatus!='Cancelled'";
				$exe1=mysqli_query($con,$sql1);
				$res1=mysqli_fetch_array($exe1);
				if($res1['CNT']>=$res1['PA_No_Spaces'])
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

	}
    if($res['P_Pricingtype']=='weekly')
    {
    	$price=number_format($res['P_Weeklyprice'],2);
    	$pr="Week";

    	$diff = ceil(($tdate - $fdate)/ ((3600*24)*7));
    	$totalprice=$price*$diff;
    }
    else if($res['P_Pricingtype']=='event')
    {
    	$price=number_format($res['Event_price'],2);
    	$pr="Event";

    	//$diff = ceil(($tdate - $fdate)/ 3600/24);
    	$diff=1;
    	$totalprice=$price*$diff;
    }
    else if($res['P_Pricingtype']=='special')
    {
    	$price=number_format($res['Event_price'],2);
    	$pr="Special";

    	$diff = ceil(($tdate - $fdate)/ 3600/24);
    	$totalprice=$price*$diff;
    }
    else if($res['P_Pricingtype']=='monthly')
    {
    	$price=number_format($res['P_Monthlyprice'],2);
    	$pr="Month";

    	$diff = ceil(($tdate - $fdate)/ ((3600*24)*31));
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

    	/*if($Park_Closed==1 || $Park_timing=='closed')
    	{
    		echo 'Parking lot is closed during the time you have selected.';
    		exit;
    	}
    	else if($Park_timing=='24hr')
    	{
    		echo '';
    	}*/
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
    	/*if($Park_Closed==1 || $Park_timing=='closed')
    	{
    		echo 'Parking lot is closed during the time you have selected.';
    		exit;
    	}
    	else if($Park_timing=='24hr')
    	{
    		echo '';
    	}*/
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
    		/*	else
    			{
    				echo 'Overnight parking is not permitted in this location';
    				//echo 'Parking Closed on Selected Arrival Time';
    				exit;
    			}*/
    		}
        	}


        	$price=number_format($res['P_FAmt'],2);
        	//$totalprice=$price;
        	$pr="Hourly";
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

        	}
        	else {
				$totalprice=$this->getNewMainHourPrice($price, $diff);
        		//$totalprice=$price*$diff;
        	}

    }
    else if($res['P_Pricingtype']=='minute')
{
	$otherprice=0;
	$price=number_format($res['P_FAmt'],2);
	$totalprice=$price;
	$pr="Hourly";


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
        // get tax and fees
        $extfees=0;
        $Way_Fee=0;
        // Admin External Fee based on Individual Listing
        if($Park_AirportVenue==1)
        {
            $csql="SELECT * FROM tbl_categories_settings WHERE Cat_ID=45 AND Parking_ID=".$P_ID;
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
        $csql="SELECT * FROM tbl_sell_fee WHERE Parking_ID=".mysqli_real_escape_string($con,$P_ID);
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
        $total=$totalprice+$extfees+$Overnight_Fee;
    return  array('price'=>$price,'subtotal' => $totalprice ,'total' => $total ,'tax'=>$extfees,'overnight_fee'=>$Overnight_Fee,'days'=>$diff,'fdate'=>$fdate,'tdate'=>$tdate,'min_reservation'=>$min_reservation );
}
	function getNewMainHourPrice($price, $diff){
        $minDiffCal = 4;
        $maxDiffCal = 5;
        $totalHoursCal = 24;
        $diffCal = $diff%$totalHoursCal;
        if($diff>=$totalHoursCal){
            $TotalHoursLoop = number_format($diff/$totalHoursCal);
        } else {
            $TotalHoursLoop = 0;
        }
        $totalprice = 0;
        for($i=0;$i<=$TotalHoursLoop;$i++){
            if($i == $TotalHoursLoop){
                $totalprice += $this->getNewSubHourPrice($price, $diffCal, $minDiffCal, $maxDiffCal);
            } else {
                $totalprice += $this->getNewSubHourPrice($price, $maxDiffCal, $minDiffCal, $maxDiffCal);
            }
        }
        return $totalprice;
    }

    function getNewSubHourPrice($price, $diffCal, $minDiffCal, $maxDiffCal){
        $totalprice = '';
        if($diffCal <= $minDiffCal){
            $totalprice = $price*$diffCal;
        } 
        if($diffCal >= $maxDiffCal) {
            $totalprice = $price*$maxDiffCal;
        }
        return $totalprice;
    }
    function getParkingTypeByPidWithDetail($P_ID, $statusQuery = ''){
        GLOBAL $con;
        $sql="SELECT P_ID, P_Location, P_Pricingtype  FROM `tbl_parking` tp where ".$statusQuery." tp.P_Location = (select tp1.P_Location from `tbl_parking` tp1 where tp1.P_ID = '".$P_ID."') AND (tp.P_Pricingtype = 'hourly' OR tp.P_Pricingtype = 'daily') ORDER BY tp.`P_ID`";
        //echo $sql;
        $result = mysqli_query($con, $sql);
        $parkingTypeStatus = '';
        $mainDetail = array();
        $detailArray = array();
        $countParkingResult = mysqli_num_rows($result);
        if ($countParkingResult > 0) {
            while($res=mysqli_fetch_assoc($result)) {
                if($res['P_Pricingtype']=='daily' || $res['P_Pricingtype']=='hourly'){
                    if($res['P_Pricingtype']=='hourly'){
                        $parkingTypeStatus['h'] = 'h';
                        $detailArray['hourly'] = $res;
                    } else if($res['P_Pricingtype']=='daily'){
                        $parkingTypeStatus['d'] = 'd';
                    }
                }
            }
            asort($parkingTypeStatus);
            $parkingTypeStatus = implode('', $parkingTypeStatus);
        }
        $parkingTypeStatus = preg_replace("/(.)\\1+/", "$1", $parkingTypeStatus);
        $mainDetail['parkingTypeStatus'] = $parkingTypeStatus;
        $mainDetail['parkingDetails'] = $detailArray;
        return $mainDetail;
    }
}
?>
