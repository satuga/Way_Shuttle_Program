<?php

header('Content-Type: application/json');
/**
 * @Method		  :	get
 * @Params		  :
 * @author      : Hitesh Tank
 * @created		  :	8-Feb-2016
 * @Modified by	:
 * @Comment		  :
 **/

//include("../common/config.php");
include("config.php");
//include("../include/functions.php");
include("function.php");
//include("parking_api/function.php");
include "../dine/dineclassbk.php";

$device_id = $_REQUEST['device_id'];
$user_id = $_REQUEST['user_id'];
$Dine_Obj = new Dine();
$cart=array();
$main_cart=array();
$main_total=0;
$subtotal=0;
$order_total=0;
$tax_total=0;
$deliveryFee=0;
$lat = '';
$long = '';

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
  
function getMyAverageRatings($list_id,$cat,$user_id)
{
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

if(isset($device_id) || isset($user_id)){
    $order_for=$_REQUEST['order_for'];
    $Cart=$Dine_Obj->MergeShowCart();
//print_r($Cart);
      $cart=array();
      if(isset($Cart[0]) && !empty($Cart[0])){
        $i=0; // for dinning
        $j=0; // for parking
        foreach($Cart[0] as $cart_data){
			//print_r($cart_data['Cart_Type']);
          if(strtolower($cart_data['Cart_Type']) == 'dine'){ //check it type is dine then put into dine object otherwise parking
                $cart['dine'][$i]['Cart_ID'] = $cart_data['Cart_ID'];
                $cart['dine'][$i]['Cart_Type'] = $cart_data['Cart_Type'];
                $cart['dine'][$i]['itemName'] = $cart_data['itemName'];
                $cart['dine'][$i]['Owner_Restaurant'] = $cart_data['Owner_Restaurant'];
                $cart['dine'][$i]['Cart_ServiceID'] = $cart_data['Cart_ServiceID'];
                $cart['dine'][$i]['Cart_Quantity'] = $cart_data['Cart_Quantity'];
                $cart['dine'][$i]['Amount'] = $cart_data['Amount'];
                $cart['dine'][$i]['charges'] = $cart_data['charges'];
                $cart['dine'][$i]['Ticket_Type'] = $cart_data['Ticket_Type'];
                $cart['dine'][$i]['Cart_Created'] = $cart_data['Cart_Created'];
                $cart['dine'][$i]['Notes'] = $cart_data['Notes'];
                $cart['dine'][$i]['Cus_Pizza'] = $cart_data['Cus_Pizza'];
                $cart['dine'][$i]['crust'] = $cart_data['crust'];
                $cart['dine'][$i]['Size'] = $cart_data['Size'];
                $i++;
          }else if(strtolower($cart_data['Cart_Type']) == 'parking'){
            $cart['parking'][$j]=$cart_data;
            $j++;
          } // end if type parking condition
      } // end cart main outer loop
//print_r($cart['parking']);die;
      if(isset($cart['dine']) && !empty($cart['dine'])){
		//$itemExisting ='';
        $dine_index=0;
          foreach($cart['dine'] as $dine_traverse_data){
            $temp_array="";
			//$tax_total =0;
            $item = $dine_traverse_data['itemName'];
            $menu=$Dine_Obj->GetMenuFromItem($dine_traverse_data['Cart_ServiceID']);
      			$Cus_Pizza=$Dine_Obj->GetCus_Pizza($dine_traverse_data['Cart_ServiceID']);
      			$Free_Toppings=$Dine_Obj->GetFree_Toppings($dine_traverse_data['Cart_ServiceID']);
            $tax = getMerchantTaxRate($dine_traverse_data['Owner_Restaurant']);
            if($order_for=='delivery'){
                $deliveryFee = getMerchantDeliveryFee($dine_traverse_data['Owner_Restaurant']);
            }
            else{
                $deliveryFee = "0.00";
            }
            $Csql="SELECT tbl_cartsubitems.subgroup_id,tbl_cartsubitems.subgroup_value,subitems.subitemName,subitems.subitemPrice,tbl_cartsubitems.Free_Toppings AS FreeToppings,tbl_cartsubitems.price_index FROM tbl_cartsubitems INNER JOIN subitems ON subitems.id=tbl_cartsubitems.subgroup_id WHERE Cart_ID=".$dine_traverse_data['Cart_ID'];
            $Csql="SELECT tbl_cartsubitems.subgroup_id,tbl_cartsubitems.subgroup_value,subitems.subitemName,subitems.subitemPrice,tbl_cartsubitems.Free_Toppings AS FreeToppings,tbl_cartsubitems.price_index FROM tbl_cartsubitems INNER JOIN subitems ON subitems.id=tbl_cartsubitems.subgroup_id WHERE Cart_ID=".$dine_traverse_data['Cart_ID'];
      			$Cexe=mysqli_query($con,$Csql);
      			$Cnum=mysqli_num_rows($Cexe);

            $subitemprice=0;
      			$subitemprice=0;
      			if($Cnum>0)
      			{
      				while($CRes=mysqli_fetch_array($Cexe))
      				{
      					if($SCRes['FreeToppings']!=1)
      						$subitemprice=$subitemprice+$CRes['subitemPrice'];
      				}
      			}
            // $subitemprice=$subitemprice*$itemx['Cart_Quantity'];
      // 			$subtotal += ($dine_traverse_data['Amount'] * $dine_traverse_data['Cart_Quantity'])+$subitemprice;

            $Csql="SELECT tbl_cartsubitems.subgroup_id,tbl_cartsubitems.subgroup_value,subitems.subitemName,subitems.subitemPrice,subgroups.subgroup_name,tbl_cartsubitems.price_index FROM tbl_cartsubitems INNER JOIN subitems ON subitems.id=tbl_cartsubitems.subgroup_id
      			INNER JOIN subgroups ON subgroups.id=subitems.subgroup_id
      			WHERE Cart_ID=".$dine_traverse_data['Cart_ID']." ORDER BY subgroups.id";
      			$Cexe=mysqli_query($con,$Csql);
      			$Cnum=mysqli_num_rows($Cexe);
            if($Cnum>0)
            {
                $subgroup_array=array();
                $TotalsubitemPrice=0;
                while($CRes=mysqli_fetch_array($Cexe)){
                  $subitemPrice=$CRes['subitemPrice']* $dine_traverse_data['Cart_Quantity'];
                 // Added by bindra shah20-apr-2016 
				  if($CRes['subitemPrice']>0)
                    {
                        //echo ' ($ '.number_format($CRes['subitemPrice'],2).')';
                        $price_index=$CRes['price_index'];
                        $subitemPrice1=explode(",",$CRes['subitemPrice']);

                        if(COUNT($subitemPrice1)>1)
                            $subitemprice=$subitemPrice1[$price_index-1];
                        else
                            $subitemprice=$CRes['subitemPrice'];

                        if($CRes['FreeToppings']==1)
                            $subitemprice=0;

                    }
					 $subgroup_array[]=array("subgroup_name"=>$CRes['subgroup_name'],"subitem_name"=>$CRes['subitemName'],"subitem_price"=>$CRes['subitemPrice'],"subgroup_id"=>$CRes['subgroup_id'],"subitemPrice"=>"$subitemPrice");
					 // End
                  $TotalsubitemPrice=$TotalsubitemPrice+$subitemPrice;
                }
            }
            else{
               $subgroup_array=array();
             }
             $subtotal += number_format((($dine_traverse_data['Amount'] * $dine_traverse_data['Cart_Quantity'])+$TotalsubitemPrice),2);
             $currenttotal = number_format((($dine_traverse_data['Amount'] * $dine_traverse_data['Cart_Quantity'])+$TotalsubitemPrice),2);
             $total=number_format((($dine_traverse_data['Amount'] * $dine_traverse_data['Cart_Quantity'])+$TotalsubitemPrice),2);
             $temp_array=$dine_traverse_data;
             $temp_array['subgroup']=$subgroup_array;
             $Currenttax = sprintf("%.2f",$currenttotal*$tax/100);
             $tax_total  += $Currenttax;
             $order_total = $tax_total+$total+$deliveryFee;

         // $main_total=$main_total+floatval($order_total);


             $temp_array['fees']=array('tax'=>$Currenttax,'delivery_charge'=>$deliveryFee);
             $menu_totalamount=$subtotal;
             $subtotal = number_format($subtotal,2);
             //$temp_array['total']=$total+$Currenttax+$deliveryFee;
             $temp_array['total']=$total+$Currenttax;
             //  $main_cart['dine']['items'][] = $temp_array;
             $main_cart['dine'][] = $temp_array;
			 $total_dine_tax = $temp_array['total']; //total dine tax only 
             $dine_index++;
          } // main dine loop exit;
        //  $main_cart['sub_total']=$subtotal;
         // $main_cart['tax_total']=$tax_total;
         // $main_cart['total_delivery']=$deliveryFee;
          //$main_cart['dine_total']=$subtotal+$tax_total+$deliveryFee;
		    $main_cart['dine_total']=$subtotal;

      //  $main_cart['dine']['total_delivery']=$total_deliveryFee;

    }else{
      $main_cart['dine']=array();
    } //end if of dine
		
      if(isset($cart['parking']) && !empty($cart['parking'])){
		$temp_array="";
		
        $parking_index=0;
			
        foreach($cart['parking'] as $park_traverse_data){
          if($park_traverse_data['Payat_Lot']>0){
			  
    				$Payat_Lot=$park_traverse_data['Payat_Lot'];
          }
    			else { $Payat_Lot=0; }

          if($park_traverse_data['Payment_Type']=='partial'){
    				$Payment_Type=$park_traverse_data['Payment_Type']; }
    			else{
    				$Payment_Type='full'; }

            $get_loc = @mysqli_fetch_assoc(mysqli_query($con,"select P_Daily_Price_Type,P_Pricingtype,P_Weeklyprice,P_Monthlyprice,P_FAmt,Event_price,Special_Price_Desc,P_FMIN,tbl_parkinglocations.Park_Address AS Title,tbl_parkinglocations.Park_Name from tbl_parking INNER JOIN tbl_parkinglocations ON tbl_parkinglocations.Park_ID=tbl_parking.P_Location where P_ID=" . $park_traverse_data['Cart_ServiceID']));
            //    print_r($get_loc); die;
                $temp_array['Cart_ID'] = $park_traverse_data['Cart_ID'];
                $temp_array['title'] = $get_loc['Title'];
                $temp_array['park_name'] = $get_loc['Park_Name'];
                $temp_array['cart_quantity'] = $park_traverse_data['Cart_Quantity'];
                $temp_array['Amount'] = "$" . number_format($park_traverse_data['Amount'], 2);
                $temp_array['P_Pricingtype'] = $get_loc['P_Pricingtype'];
                // Add price rate for parking - 29-apr-2016 - Bindra Shah
                $cartServiceId = $park_traverse_data['Cart_ServiceID'];
                $price='';
                if($get_loc['P_Pricingtype']=='daily')
                {
                    $startdate= $park_traverse_data['from_date'];
                    $enddate = $park_traverse_data['to_date'];
                    $today = 1;
                    $start= date("Y-m-d",strtotime($startdate));
                    $temp_array['days'] =$start;

					$start_date_time = new DateTime($startdate);
					$fdate= $start_date_time->format('Y-m-d');
					
					$end_date_time = new DateTime($enddate);
					$tdate= $end_date_time->format('Y-m-d');
				
				//Edit by kadia ravi	
					$Totaldays = ceil(abs(strtotime($tdate) - strtotime($fdate)) / 86400)+1;
					$temp_array['Totaldays'] =$Totaldays;
					$holidays=array();
					$WorkingDays = getWorkingDays($fdate,$tdate,$holidays);
					$temp_array['working_days'] = $WorkingDays;
					$temp_array['weekend_days'] = $Totaldays - $WorkingDays;
					$temp_array['arrival_date'] = $park_traverse_data['from_date'];
                	$temp_array['departs_date'] = $park_traverse_data['to_date'];
					$Dayprice=$DayCount=0;
					$Weekendprice=$WeekendCount=0;
				//End of edit by kadia ravi				
                    while ($today<=$Totaldays)
                    {
                        if($get_loc['P_Daily_Price_Type']=='Week')
                        {
                            $week=date('D', strtotime($start));
                            $sqlw="SELECT PA_P_Dailyprice AS PRICE,PA_No_Spaces AS SPACES,PA_Updated FROM tbl_parkingweekdayavailability WHERE P_fromDate='".$start."' AND P_ID=".$cartServiceId;
                            $resw = mysqli_fetch_array(mysqli_query($con,$sqlw));
                            $price["$week"]=$resw['PRICE'];
							if($week=='Mon' || $week == 'Tue' || $week== 'Wed' || $week == 'Thu' || $week=='Fri')
							{
								$Dayprice+=$resw['PRICE'];
								$DayCount++;
							}
							else{
								$Weekendprice+=$resw['PRICE'];
								$WeekendCount++;
							}
						
                        }
                        else
                        {
                            $sql="SELECT Park_ID,PA_P_Dailyprice,PA_No_Spaces,PA_Created FROM tbl_parkingavailability WHERE ('".$start."' BETWEEN P_fromDate AND PA_toDate) AND P_ID='".$cartServiceId."'";
                            $exe=mysqli_query($con,$sql);
                            $res=mysqli_fetch_array($exe);
                            $num=mysqli_num_rows($exe);
                            $price["$week"]=$res['PA_P_Dailyprice'];
						if($week=='Mon')
							$newprice+=$res['PA_P_Dailyprice'];
                        }
                        $start = date ("Y-m-d", strtotime("+1 day", strtotime($start)));
                        $today++;
                    }
                }
                else if($get_loc['P_Pricingtype']=='weekly')
                    $price = number_format($get_loc['P_Weeklyprice'],2)." per Weekly";
                else if($get_loc['P_Pricingtype']=='monthly')
                    $price = number_format($get_loc['P_Monthlyprice'],2)." per Monthly";
                else if($get_loc['P_Pricingtype']=='hourly')
                    $price = number_format($get_loc['P_FAmt'],2);
                else if($get_loc['P_Pricingtype']=='minute')
                    $price = number_format($get_loc['P_FAmt'],2);
                else if($get_loc['P_Pricingtype']=='event')
                    $price = number_format($get_loc['Event_price'],2);
                else if($get_loc['P_Pricingtype']=='special')
                    $price = number_format($get_loc['Event_price'],2);

                $Special_Price_Desc=explode("(",$get_loc['Special_Price_Desc']);
                $Special_Price_Des=$Special_Price_Desc[0];

                if($get_loc['P_Pricingtype']=='hourly' && $get_loc['P_FMIN']==1)
                    $price.= " per Every Hour";
                else if($get_loc['P_Pricingtype']=='hourly' && $get_loc['P_FMIN']>1)
                    $price.= " Per ".$get_loc['P_FMIN']." Hours";
                else if($get_loc['P_Pricingtype']=='hourly')
                    $price.= $get_loc['P_H1']." per Hour";
                else if($get_loc['P_Pricingtype']=='minute')
                    $price.= $get_loc['P_FMIN']." per Minutes";
                else if($get_loc['P_Pricingtype']=='special' && $Special_Price_Des<>'')
                    $price.= "&nbsp;".ucwords($Special_Price_Des);

                if(is_array($price))
                    $temp_array['price_rate']=$price;
                else
                    $temp_array['price_rate']=strtolower($price);
					
			//Edit by kadia ravi
					/*$WrokingPrice = $price['Mon']+$price['Tue']+$price['Wed']+$price['Thu']+$price['Fri'];
					$WeekEndPrice = $price['Sat']+$price['Sun'];
				
					$TotalWorkingPrice = $WrokingPrice * $WorkingDays;
					$TotalWeekEndPrice = $WeekEndPrice * ($Totaldays - $WorkingDays);
				
					$temp_array['TotalWorkingPrice'] = $park_traverse_data['Amount'] * $TotalWorkingPrice;
					$temp_array['TotalWeekEndPrice'] = $park_traverse_data['Amount'] * $TotalWeekEndPrice;*/
					$temp_array['TotalWorkingPrice'] = $Dayprice;
					$temp_array['TotalWorkingDays'] = $DayCount;
					$temp_array['TotalWeekendPrice'] = $Weekendprice;
					$temp_array['TotalWeekendDays'] = $WeekendCount;
					
					//end of edit by kadia ravi
					
				
				
                // End Add price rate for parking - 29-apr-2016 - Bindra Shah

                // -----get point percentage-----------//
      			$cat = '45';
      			$Point = @mysqli_fetch_assoc(mysqli_query($con,"SELECT Point_Percentage FROM tbl_categories WHERE Cat_ID=".$cat));
      			$PointPercentage = $Point['Point_Percentage'];
      			$Points1=($park_traverse_data['TotalAmount']*$PointPercentage)/100;
      			$Points=$Points+$Points1;

      			$temp_array['points'] = $Points1;
//
				

            //----------getting the parking Fee--------//
      			$fees = GetParkingfee($park_traverse_data['Cart_ServiceID']);
                $totalfees = 0;

      			$temp_array['fees'] = array();
      			if(count($fees) > 0 )
      			{
      				foreach($fees[0] as $pp=>$qq)
      				{
      					if($qq['format']=='%')
      						$Setting_Charge=$park_traverse_data['Amount']*$qq['Setting_Value']/100;
      					else
      						$Setting_Charge=$qq['Setting_Value'];

      					if($qq['format']=='%')
      					{
      						$temp_array['fees'][$qq['Setting_Name']] = "$".number_format($Setting_Charge,2);
                  $totalfees += number_format($Setting_Charge,2);
      					}
      					else
      					{
      						$temp_array['fees'][$qq['Setting_Name']] = '$'.number_format($Setting_Charge,2);
                  $totalfees += number_format($Setting_Charge,2);
      					}

      				}
      			}
            //--------------overnight fee----------//
            if($park_traverse_data['Overnight_Fee']>0)
            {
              $temp_array['overnight_fee'] = '$'.number_format($park_traverse_data['Overnight_Fee'],2);
            }
            $temp_array['arrival_date'] = $park_traverse_data['from_date'];
            $temp_array['departs_date'] = $park_traverse_data['to_date'];


            // $main_cart['parking']['items'][] = $temp_array;

            //$temp_array['total']=number_format(($temp_array['cart_quantity'] * 	$park_traverse_data['Amount']),2);
            $temp_array['total']=number_format(($park_traverse_data['Amount']),2) + $totalfees;
			$subtotal +=number_format(($park_traverse_data['Amount']),2);
			$tax_total +=$totalfees;
            $main_cart['parking'][] = $temp_array;
            $main_total=$main_total+$temp_array['total'];
            $park_total+= number_format(($park_traverse_data['Amount']),2);
            $parking_index++;
        } //end $park data traverse
		// Final total calculations
		$main_total = $main_cart['dine_total'] + $park_total + $total_dine_tax + $totalfees;
        //-----------getting the total amount of the cart----------//

        if($_REQUEST['user_id'] !=""){
          $total_park = @mysqli_fetch_array(mysqli_query($con,"select SUM(TotalAmount) AS TOTAL from tbl_cart where Cart_Type='Parking' AND Cart_UserID ='".$user_id."'"));
        }else{
          $total_park = @mysqli_fetch_array(mysqli_query($con,"select SUM(TotalAmount) AS TOTAL from tbl_cart where Cart_Type='Parking' AND Sess_ID='".$device_id."' "));
        }


    		//---------if partial payment-----------//
    		if($Payment_Type=='partial')
    		{
    			$pay_now = "$".number_format((($total['TOTAL'])-$Payat_Lot),2);
    			$balance_due = "$".number_format(($Payat_Lot),2);
    		}
    		else
    		{
    			$pay_now = '';
    			$balance_due = '';
    		}
    		$way_amount = number_format(round($Points,2),2);
        // $main_total=$main_total+$total_park['TOTAL'];
        $main_cart['paynow']=$pay_now;
        $main_cart['balance_due']=$balance_due;
        $main_cart['way_buck']=$way_amount;
        $main_cart['park_total']=$park_total;
		
      }else{
        $main_cart['parking']=array();
      } // end parking if

     
      $main_cart['sub_total']=$subtotal;
      $main_cart['tax_total']=$tax_total;
      $main_cart['total_delivery']=$deliveryFee;
	  $main_cart['total_amount']=$subtotal+$tax_total+$deliveryFee;
	  if(!empty($main_cart['dine']) || !empty($main_cart['parking']))
	  {
		$EncryptKey = ENCRYPTKEY;
		$sql="Select *,RIGHT(AES_DECRYPT(Card_Number,'".$EncryptKey."'),4) AS Card_No From tbl_creditcarddetails WHERE Card_User_ID=".$user_id." AND Card_Default='1'"; 
		$res2=@mysqli_fetch_assoc(mysqli_query($con,$sql));
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
		$main_cart['Card_details']=$card_array;
		echo json_encode(array('status'=>1,'cart'=>$main_cart),true);
		exit;
	  }
	  else{ echo json_encode(array('status'=>0,'message'=>"Your cart is empty"),true);
      exit;}
      
    }else{
      echo json_encode(array('status'=>0,'message'=>"Your cart is empty"),true);
      exit;
    } // end main cart if


}
else{
    $content=array("status"=>0,"message"=>PARAMETER_MSG);
    echo json_encode($content);
    exit;
}


//The function returns the no. of business days between two dates and it skips the holidays
function getWorkingDays($startDate,$endDate,$holidays){
    // do strtotime calculations just once
    $endDate = strtotime($endDate);
    $startDate = strtotime($startDate);


    //The total number of days between the two dates. We compute the no. of seconds and divide it to 60*60*24
    //We add one to inlude both dates in the interval.
    $days = ($endDate - $startDate) / 86400 + 1;

    $no_full_weeks = floor($days / 7);
    $no_remaining_days = fmod($days, 7);

    //It will return 1 if it's Monday,.. ,7 for Sunday
    $the_first_day_of_week = date("N", $startDate);
    $the_last_day_of_week = date("N", $endDate);

    //---->The two can be equal in leap years when february has 29 days, the equal sign is added here
    //In the first case the whole interval is within a week, in the second case the interval falls in two weeks.
    if ($the_first_day_of_week <= $the_last_day_of_week) {
        if ($the_first_day_of_week <= 6 && 6 <= $the_last_day_of_week) $no_remaining_days--;
        if ($the_first_day_of_week <= 7 && 7 <= $the_last_day_of_week) $no_remaining_days--;
    }
    else {
        // (edit by Tokes to fix an edge case where the start day was a Sunday
        // and the end day was NOT a Saturday)

        // the day of the week for start is later than the day of the week for end
        if ($the_first_day_of_week == 7) {
            // if the start date is a Sunday, then we definitely subtract 1 day
            $no_remaining_days--;

            if ($the_last_day_of_week == 6) {
                // if the end date is a Saturday, then we subtract another day
                $no_remaining_days--;
            }
        }
        else {
            // the start date was a Saturday (or earlier), and the end date was (Mon..Fri)
            // so we skip an entire weekend and subtract 2 days
            $no_remaining_days -= 2;
        }
    }

    //The no. of business days is: (number of weeks between the two dates) * (5 working days) + the remainder
//---->february in none leap years gave a remainder of 0 but still calculated weekends between first and last day, this is one way to fix it
   $workingDays = $no_full_weeks * 5;
    if ($no_remaining_days > 0 )
    {
      $workingDays += $no_remaining_days;
    }

    //We subtract the holidays
    foreach($holidays as $holiday){
        $time_stamp=strtotime($holiday);
        //If the holiday doesn't fall in weekend
        if ($startDate <= $time_stamp && $time_stamp <= $endDate && date("N",$time_stamp) != 6 && date("N",$time_stamp) != 7)
            $workingDays--;
    }

    return $workingDays;
}

?>
