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
		
		//echo "<pre>";
		//print_r($cart['parking']);
		//echo "</pre>"; die;
		
      if(isset($cart['parking']) && !empty($cart['parking'])){
		$temp_array="";
		$GetData = array();
        $parking_index=0;
			
		for($p=0; $p<count($cart['parking']); $p++):

			$cartServiceId = $cart['parking'][$p]['Cart_ServiceID'];
			$query = "SELECT tbl_parking.P_UserID,tbl_parkinglocations.lat AS Lattitude,tbl_parkinglocations.lon AS Longitude,tbl_parking.P_ID,tbl_parking.Average_Price,tbl_parking.P_Lot_Type,tbl_parking.P_Parkingattributes,tbl_parking.P_Parkingextras,tbl_parking.P_Pricingtype,tbl_parking.P_Daily_Price_Type,tbl_parking.clicks,tbl_parking.views,tbl_parking.Todaysbookings,tbl_parking.average_reviews,tbl_parking.total_reviews,tbl_parking.P_Created,tbl_parking.P_FAmt,tbl_parkinglocations.Park_Name,tbl_parkinglocations.Park_Logo,tbl_parkinglocations.Park_Address,tbl_parkinglocations.Park_Howtofind,tbl_parkinglocations.Park_Locdesc,tbl_parkinglocations.Park_SpecialInstructions ,tbl_parkinglocations.Park_City,tbl_parkinglocations.Park_State,tbl_parkinglocations.Park_Zip,tbl_parkinglocations.P_Airport_Distance,tbl_parkinglocations.Park_Image1,tbl_parkinglocations.Park_Image2,tbl_parkinglocations.Park_Image3,tbl_parkinglocations.Park_Image4,tbl_parkinglocations.Park_Image5,tbl_parkinglocations.Airport_Near_Address,tbl_parkinglocations.Airport_Near_Address_ID,tbl_parkinglocations.Park_Typeoflocation,tbl_parkinglocations.Park_AirportVenue,tbl_parkinglocations.P_Parkingattributes,tbl_parkinglocations.P_Shuttleother,tbl_parkinglocations.P_Shuttledesc,tbl_registeration.firstname,tbl_registeration.display_name,3956 * 2 * ASIN(SQRT(POWER(SIN(('".$lat."' - abs(tbl_parkinglocations.lat)) * pi()/180 / 2), 2) + COS('".$lat."' * pi()/180 ) * COS(abs(tbl_parkinglocations.lat) * pi()/180) * POWER(SIN(('".$long."'-tbl_parkinglocations.lon) * pi()/180 / 2), 2) )) as distance,1 AS Main,tbl_parkinglocations.P_Parkingextras AS P_LocationParkingextras,tbl_parkinglocations.P_Parkingattributes AS P_LocationParkingattributes,tbl_parking.min_reservation,tbl_parking.smart_lot,tbl_favorite.status as fav_status,P_FMIN,P_H1,Event_price,P_Weeklyprice,P_Monthlyprice,Special_Price_Desc   FROM tbl_parking
			INNER JOIN tbl_parkinglocations ON tbl_parkinglocations.Park_ID=tbl_parking.P_Location
			INNER JOIN tbl_registeration ON tbl_registeration.id=tbl_parking.P_UserID
			LEFT JOIN tbl_favorite ON tbl_favorite.Parking_ID = tbl_parking.P_ID
			WHERE tbl_parking.P_Status=1 AND tbl_parking.Deleted=0 AND tbl_parking.P_ID='".$cartServiceId."'";
		
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
								$sqlw="SELECT PA_P_Dailyprice AS PRICE,PA_No_Spaces AS SPACES,PA_Updated FROM tbl_parkingweekdayavailability WHERE P_fromDate='".$start."' AND P_ID=".$cartServiceId;
								$resw = mysqli_fetch_array(mysqli_query($con,$sqlw));
								$price["$week"]=$resw['PRICE'];
							}
							else
							{
								$sql="SELECT Park_ID,PA_P_Dailyprice,PA_No_Spaces,PA_Created FROM tbl_parkingavailability WHERE ('".$start."' BETWEEN P_fromDate AND PA_toDate) AND P_ID='".$cartServiceId."'";
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
					
					if(is_array($price))
						$info['price_rate']=$price;
					else
					$info['price_rate']=strtolower($price);
					$all[] =  $info;
					// End Add price rate for parking - 29-apr-2016 - Bindra Shah	
				}
				$GetData[] = $price;
			}	
				
		endfor;		
		$temp_array['price_rate']=$GetData;			
		
				
		
        foreach($cart['parking'] as $park_traverse_data){
          if($park_traverse_data['Payat_Lot']>0){
			  
    				$Payat_Lot=$park_traverse_data['Payat_Lot'];
          }
    			else { $Payat_Lot=0; }

          if($park_traverse_data['Payment_Type']=='partial'){
    				$Payment_Type=$park_traverse_data['Payment_Type']; }
    			else{
    				$Payment_Type='full'; }

            $get_loc = @mysqli_fetch_assoc(mysqli_query($con,"select tbl_parkinglocations.Park_Address AS Title,tbl_parkinglocations.Park_Name from tbl_parking INNER JOIN tbl_parkinglocations ON tbl_parkinglocations.Park_ID=tbl_parking.P_Location where P_ID=".$park_traverse_data['Cart_ServiceID']));
			

            $temp_array['Cart_ID'] = $park_traverse_data['Cart_ID'];
      			$temp_array['title'] = $get_loc['Title'];

      			$temp_array['park_name'] = $get_loc['Park_Name'];

      			$temp_array['cart_quantity'] = $park_traverse_data['Cart_Quantity'];

      			$temp_array['Amount'] = "$".number_format($park_traverse_data['Amount'],2);


            //-----get point percentage-----------//
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
      echo json_encode(array('status'=>1,'cart'=>$main_cart),true);
      exit;
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

?>
