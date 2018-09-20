<?php
include ('config.php');
include ('function.php');

$data = json_decode(file_get_contents('php://input'), TRUE);
$driver_id = sanitize($data['data']['driver_id']);

function replacer(& $item, $key) {
    	if ($item === null) {
       		$item = '';
    	}
	}

if($driver_id != '')
{
	//$driver_order = "select * from driver_accept_ride where driver_id = '".$driver_id."' and status = '3'";
	$driver_order = "select m.merchantName as res_name,m.telephone as res_telephone,m.geoLat as res_lat,m.geoLong as res_long,m.contactAddress as res_address,m.logo as res_logo,reg.firstname as user_firstname,reg.lastname as user_lastname,reg.home_phone as user_homephone,reg.work_phone as user_workphone,reg.mobile_phone as user_mobilephone,reg.logo as customer_image,o.*,ride.status as order_status from driver_accept_ride as ride
	left join orders as o ON o.id = ride.order_id
	left join tbl_registeration as reg ON reg.id = o.customerID
	left join merchant as m ON m.id = o.merchantID
	where ride.driver_id = '".$driver_id."' and ride.status = '3' order by o.id desc";


	$driver_query = mysqli_query($con,$driver_order);
	$rows = mysqli_num_rows($driver_query);
	if($rows > 0)
	{
		$i=0;
		while($rowss = mysqli_fetch_assoc($driver_query))
		{
			$all_orders = array();
			$order = "select it.*,items.* from order_items as items left join items as it on it.id = items.itemID where items.orderID = '".$rowss['id']."'";
			$order_detail = mysqli_query($con,$order);
			while($order_info = mysqli_fetch_assoc($order_detail))
			{
				$all_orders[] = $order_info;
			}

			$records[$i]['id'] = $rowss['id'];
			$records[$i]['Owner_ID'] = $rowss['Owner_ID'];
			$records[$i]['orderDate'] = $rowss['orderDate'];
			$records[$i]['orderTime'] = $rowss['orderTime'];
			$records[$i]['customerID'] = $rowss['customerID'];
			$records[$i]['orderPlacedOn'] = $rowss['orderPlacedOn'];
			$records[$i]['orderType'] = $rowss['orderType'];
			$records[$i]['orderAmount'] = $rowss['orderAmount'];
			$records[$i]['orderStatus'] = $rowss['orderStatus'];

			$records[$i]['paymentStatus'] = $rowss['paymentStatus'];
			$records[$i]['orderCompleted'] = $rowss['orderCompleted'];
			$records[$i]['comments'] = $rowss['comments'];
			$records[$i]['address'] = $rowss['address'];
			$records[$i]['address2'] = $rowss['address2'];

			$records[$i]['province'] = $rowss['province'];
			$records[$i]['city'] = $rowss['city'];
			$records[$i]['zipcode'] = $rowss['zipcode'];
			$records[$i]['apartment'] = $rowss['apartment'];
			$records[$i]['floor'] = $rowss['floor'];
			$records[$i]['digicode'] = $rowss['digicode'];
			$records[$i]['company'] = $rowss['company'];

			$records[$i]['department'] = $rowss['department'];
			$records[$i]['deliveryPhone'] = $rowss['deliveryPhone'];
			$records[$i]['code'] = $rowss['code'];
			$records[$i]['DeliveryMethod'] = $rowss['DeliveryMethod'];
			$records[$i]['DeliveryAddress'] = $rowss['DeliveryAddress'];

			$records[$i]['Status_Updatedby'] = $rowss['Status_Updatedby'];
			$records[$i]['Status_Updatedat'] = $rowss['Status_Updatedat'];
			$records[$i]['IsHollaMeals'] = $rowss['IsHollaMeals'];
			$records[$i]['delivery_lat'] = $rowss['delivery_lat'];
			$records[$i]['delivery_long'] = $rowss['delivery_long'];
			$records[$i]['distance'] = $rowss['distance'];


			$records[$i]['res_name'] = $rowss['res_name'];
			$records[$i]['res_telephone'] = $rowss['res_telephone'];
			$records[$i]['res_lat'] = $rowss['res_lat'];
			$records[$i]['res_long'] = $rowss['res_long'];
			$records[$i]['res_address'] = $rowss['res_address'];
			$records[$i]['res_logo'] = $rowss['res_logo'];

			$records[$i]['user_firstname'] = $rowss['user_firstname'];
			$records[$i]['user_lastname'] = $rowss['user_lastname'];
			$records[$i]['user_homephone'] = $rowss['user_homephone'];
			$records[$i]['user_workphone'] = $rowss['user_workphone'];
			$records[$i]['user_mobilephone'] = $rowss['user_mobilephone'];
      $records[$i]['customer_image'] = $rowss['customer_image'];
			$records[$i]['order_status'] = $rowss['order_status'];
			$records[$i]['order_details'] = $all_orders;
			$all_orders = '';
			$i++;

			//$records[] = $rowss;
		}

		array_walk_recursive($records, 'replacer');


		$output=array("status"=>"1","data"=>$records);
		echo json_encode($output);
		exit;
	}
	else
	{
		$output=array("status"=>"0","data"=>"No Records Found");
		echo json_encode($output);exit;
	}
}
else
{
	$output=array("status"=>"0","data"=>"Please add correct data");
	echo json_encode($output);exit;
}


?>
