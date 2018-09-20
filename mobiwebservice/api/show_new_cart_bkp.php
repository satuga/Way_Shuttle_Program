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


if(isset($device_id) || isset($user_id)){
    $order_for=$_REQUEST['order_for'];
    $Cart=$Dine_Obj->MergeShowCart();

    $cart=array();
    if(isset($Cart[0]) && !empty($Cart[0])){
      $i=0; // for dinning
      $j=0; // for parking
      foreach($Cart[0] as $cart_data){

          if($cart_data['Cart_Type'] == 'Dine'){ //check it type is dine then put into dine object otherwise parking
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
          }else if($cart_data['Cart_Type'] == 'Parking'){
            $cart['parking'][$j]=$cart_data;
            $j++;
          } // end if type parking condition
      } // end cart main outer loop


      if(isset($cart['dine']) && !empty($cart['dine'])){

        $dine_index=0;
          foreach($cart['dine'] as $dine_traverse_data){
            $temp_array="";
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
      			$Cexe=mysql_query($Csql);
      			$Cnum=mysql_num_rows($Cexe);

            $subitemprice=0;
      			$subitemprice=0;
      			if($Cnum>0)
      			{
      				while($CRes=mysql_fetch_array($Cexe))
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
      			$Cexe=mysql_query($Csql);
      			$Cnum=mysql_num_rows($Cexe);
            if($Cnum>0)
            {
                $subgroup_array=array();
                $TotalsubitemPrice=0;
                while($CRes=mysql_fetch_array($Cexe)){
                  $subitemPrice=$CRes['subitemPrice'];
                  $subgroup_array[]=array("subgroup_name"=>$CRes['subgroup_name'],"subitem_name"=>$CRes['subitemName'],"subitem_price"=>$CRes['subitemPrice'],"subgroup_id"=>$CRes['subgroup_id']);
                  $TotalsubitemPrice=$TotalsubitemPrice+$subitemPrice;
                }
            }
            else{
               $subgroup_array=array();
             }
             $subtotal += number_format((($dine_traverse_data['Amount'] * $dine_traverse_data['Cart_Quantity'])+$TotalsubitemPrice),2);
             $total=number_format((($dine_traverse_data['Amount'] * $dine_traverse_data['Cart_Quantity'])+$TotalsubitemPrice),2);
             $temp_array=$dine_traverse_data;
             $temp_array['subgroup']=$subgroup_array;
             $tax_total = sprintf("%.2f",$TotalsubitemPrice*$tax/100);
             $order_total = $tax_total+$total+$deliveryFee;

             $main_total=$main_total+floatval($order_total);


            //  $temp_array['fees']=array('tax'=>$tax_total,'delivery_charge'=>$deliveryFee);
             $menu_totalamount=$subtotal;
             $subtotal = number_format($subtotal,2);
             $temp_array['total']=$total;
             //  $main_cart['dine']['items'][] = $temp_array;
             $main_cart['dine'][] = $temp_array;
             $dine_index++;
          } // main dine loop exit;
          $main_cart['sub_total']=$subtotal;
          $main_cart['tax_total']=$tax_total;
          $main_cart['total_delivery']=$deliveryFee;

      //  $main_cart['dine']['total_delivery']=$total_deliveryFee;

    }else{
      $main_cart['dine']=array();
    } //end if of dine

      if(isset($cart['parking']) && !empty($cart['parking'])){
        $parking_index=0;
        foreach($cart['parking'] as $park_traverse_data){
          $temp_array="";
          if($park_traverse_data['Payat_Lot']>0){
    				$Payat_Lot=$park_traverse_data['Payat_Lot'];
          }
    			else { $Payat_Lot=0; }

          if($park_traverse_data['Payment_Type']=='partial'){
    				$Payment_Type=$park_traverse_data['Payment_Type']; }
    			else{
    				$Payment_Type='full'; }

            $get_loc = @mysql_fetch_assoc(mysql_query("select tbl_parkinglocations.Park_Address AS Title,tbl_parkinglocations.Park_Name from tbl_parking INNER JOIN tbl_parkinglocations ON tbl_parkinglocations.Park_ID=tbl_parking.P_Location where P_ID=".$park_traverse_data['Cart_ServiceID']));
            $temp_array['Cart_ID'] = $park_traverse_data['Cart_ID'];
      			$temp_array['title'] = $get_loc['Title'];

      			$temp_array['park_name'] = $get_loc['Park_Name'];

      			$temp_array['cart_quantity'] = $park_traverse_data['Cart_Quantity'];

      			$temp_array['Amount'] = "$".number_format($park_traverse_data['Amount'],2);


            //-----get point percentage-----------//
      			$cat = '45';
      			$Point = @mysql_fetch_assoc(mysql_query("SELECT Point_Percentage FROM tbl_categories WHERE Cat_ID=".$cat));
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
            // $main_cart['parking']['items'][] = $temp_array;

            $temp_array['total']=number_format(($temp_array['cart_quantity'] * 	$park_traverse_data['Amount']),2);
            $main_cart['parking'][] = $temp_array;
            $main_total=$main_total+$temp_array['total']+$totalfees;
            $parking_index++;
        } //end $park data traverse

        //-----------getting the total amount of the cart----------//

        if($_REQUEST['user_id'] !=""){
          $total_park = @mysql_fetch_array(mysql_query("select SUM(TotalAmount) AS TOTAL from tbl_cart where Cart_Type='Parking' AND Cart_UserID ='".$user_id."'"));
        }else{
          $total_park = @mysql_fetch_array(mysql_query("select SUM(TotalAmount) AS TOTAL from tbl_cart where Cart_Type='Parking' AND Sess_ID='".$device_id."' "));
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

      }else{
        $main_cart['parking']=array();
      } // end parking if

      $main_cart['total_amount']=$main_total;
      // $main_cart['sub_total']=$subtotal;
      // $main_cart['tax_total']=$tax_total;
      // $main_cart['total_delivery']=$deliveryFee;
      echo json_encode(array('status'=>1,'cart'=>$main_cart),true);
      exit;
    }else{
      echo json_encode(array('status'=>0),true);
      exit;
    } // end main cart if


}
else{
    $content=array("status"=>0,"message"=>PARAMETER_MSG);
    echo json_encode($content);
    exit;
}

?>
