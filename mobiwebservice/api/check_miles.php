<?php
header('Content-Type: application/json');
include ('config.php');
include ('function.php');
$data = json_decode(file_get_contents('php://input'), TRUE);
$merchant_id = sanitize($data['data']['merchant_id']);
$deliveryaddress_id = sanitize($data['data']['deliveryaddress_id']);

if($deliveryaddress_id != '' && $merchant_id != '')
{
    $zipfound=0;
    // Get current Restaurant records
    $merchantQuery = "select * from merchant where id ='".$merchant_id."'";
    $merchantRes = mysqli_query($con,$merchantQuery);
    $merchantCount = mysqli_num_rows($merchantRes);
    if($merchantCount > 0)
    {
        $merchantDeliveryDetails = mysqli_fetch_assoc($merchantRes);
		$merchantDeliveryMiles = $merchantDeliveryDetails['deliverymiles'];
		//get rest lat long
		$merchantAddress=$merchantDeliveryDetails['ContactAddress'].', '.$merchantDeliveryDetails['city'].', '.$merchantDeliveryDetails['state'];
		$merchantLats=getlatandlon($merchantAddress);
		$merchantLat=$merchantLats[0];
		$merchantLon=$merchantLats[1];

        // get Delivery address of user
        $deliveryAddressQuery = "select * from tbl_deliveryaddress where DE_ID='".$deliveryaddress_id."'";
        $deliveryAddressRes = mysqli_query($con,$deliveryAddressQuery);
        $deliveryAddressCount = mysqli_num_rows($deliveryAddressRes);
        if($deliveryAddressCount > 0)
        {
            $deliveryAddressDetails = mysqli_fetch_assoc($deliveryAddressRes);
			//print_r($deliveryAddressDetails['DE_City']); die;
			$Delivery_Address = $deliveryAddressDetails['DE_Address'].', '.$deliveryAddressDetails['DE_City'].', '.$deliveryAddressDetails['DE_State'].', '.$deliveryAddressDetails['DE_Zipcode'];
			//$Delivery_Address = $deliveryAddressDetails['DE_Address'];
			$Delivery_lats=getlatandlon($Delivery_Address);
			$Delivery_lat=$Delivery_lats[0];
			$Delivery_lon=$Delivery_lats[1];
			if($Delivery_lat!='' && $Delivery_lon!='')
			{
				$dist=distance($Delivery_lat,$Delivery_lon,$merchantLat,$merchantLon);
				if($dist<=$merchantDeliveryMiles && $dist>0){
					$zipfound=1;
					$output=array("status"=>"1","message"=>'Delivery is available for location you have selected. We deliver up to '.$merchantDeliveryMiles.' miles');
					echo json_encode($output);exit;
				}
				else
					$zipfound=0;
			}
			else{
				$output=array("status"=>"0","message"=>"We could not found any address");
				echo json_encode($output);exit;
			}
        }
        else {
            $output=array("status"=>"0","message"=>"We could not found any address");
            echo json_encode($output);exit;
        }
        if($zipfound==0)
        {
			$dist=(number_format($dist,1));
            $output=array("status"=>"0","message"=>'Delivery not available for location you have selected. We deliver up to '.$merchantDeliveryMiles.' miles and you are '.$dist.' miles away.');
            echo json_encode($output);exit;
        }

    }
    else {
        $output=array("status"=>"0","message"=>"We could not found Restaurant.");
        echo json_encode($output);exit;
    }
}
else
{
    $output=array("status"=>"0","response"=>"error","message"=>"Parameter Missing");
    echo json_encode($output);exit;
}

