<?php
//include("../common/config.php");
include("config.php");
//include("../include/functions.php"); 
include("function.php"); 
include "../dine/dineclassbk.php";
$Dine_Obj = new Dine();
$cart=array();
$subtotal=0;
$user_id = $_REQUEST['user_id'];
if($_REQUEST['device_id']){
    $order_for=$_REQUEST['order_for'];
    $Cart=$Dine_Obj->ShowCart();
    if($Cart[1]>0) {
			foreach ($Cart[0] as $ind => $itemx) {
			$item = $itemx['itemName'];
			$menu=$Dine_Obj->GetMenuFromItem($itemx['Cart_ServiceID']);
			$Cus_Pizza=$Dine_Obj->GetCus_Pizza($itemx['Cart_ServiceID']);
			$Free_Toppings=$Dine_Obj->GetFree_Toppings($itemx['Cart_ServiceID']);
            $tax = getMerchantTaxRate($itemx['Owner_Restaurant']);
            if($order_for=='delivery'){
                $deliveryFee = getMerchantDeliveryFee($itemx['Owner_Restaurant']);
            }
            else{
                $deliveryFee = "0.00";
            }
            
			$Csql="SELECT tbl_cartsubitems.subgroup_id,tbl_cartsubitems.subgroup_value,subitems.subitemName,subitems.subitemPrice,tbl_cartsubitems.Free_Toppings AS FreeToppings,tbl_cartsubitems.price_index FROM tbl_cartsubitems INNER JOIN subitems ON subitems.id=tbl_cartsubitems.subgroup_id WHERE Cart_ID=".$itemx['Cart_ID'];
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
			$subitemprice=$subitemprice*$itemx['Cart_Quantity'];
			$subtotal += ($itemx['Amount'] * $itemx['Cart_Quantity'])+$subitemprice;
			
			$Csql="SELECT tbl_cartsubitems.subgroup_id,tbl_cartsubitems.subgroup_value,subitems.subitemName,subitems.subitemPrice,subgroups.subgroup_name,tbl_cartsubitems.price_index FROM tbl_cartsubitems INNER JOIN subitems ON subitems.id=tbl_cartsubitems.subgroup_id
			INNER JOIN subgroups ON subgroups.id=subitems.subgroup_id
			WHERE Cart_ID=".$itemx['Cart_ID']." ORDER BY subgroups.id";
			$Cexe=mysqli_query($con,$Csql);
			$Cnum=mysqli_num_rows($Cexe);
			
			if($Cnum>0)
			{
			    $subgroup_array=array();
                $TotalsubitemPrice=0;
                while($CRes=mysqli_fetch_array($Cexe)){
                $subitemPrice=$CRes['subitemPrice'];
				$subgroup_array[]=array("subgroup_name"=>$CRes['subgroup_name'],"subitem_name"=>$CRes['subitemName'],"subitem_price"=>$CRes['subitemPrice'],"subgroup_id"=>$CRes['subgroup_id']);
                $TotalsubitemPrice=$TotalsubitemPrice+$subitemPrice;
                }
			}
            else{
               $subgroup_array=array(); 
            }
							
					
              $total=number_format((($itemx['Amount'] * $itemx['Cart_Quantity'])+$TotalsubitemPrice),2);
              $cart[$ind]=$itemx;
              $cart[$ind]['subgroup']=$subgroup_array;
              $cart[$ind]['total']=$total;
              
              $tax_total = sprintf("%.2f",$subtotal*$tax/100);
		      $order_total = $tax_total+$subtotal+$deliveryFee;
			  $menu_totalamount=$subtotal;
			  
			  $subtotal = number_format($subtotal,2);
		 }
        $content=array("status"=>1,"cart"=>$cart,"sub_total"=>"$subtotal","total_amount"=>"$order_total","tax_total"=>"$tax_total","total_delivery"=>"$deliveryFee");
        echo json_encode($content);
        exit;    
      }
      else{
        $content=array("status"=>1,"cart"=>$cart);
        echo json_encode($content);
        exit;    
      }
}
else{
    $content=array("status"=>0,"message"=>PARAMETER_MSG);
    echo json_encode($content);
    exit;
}

?>