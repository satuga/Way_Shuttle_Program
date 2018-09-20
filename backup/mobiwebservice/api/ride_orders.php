<?php
include ('config.php');
include ('function.php');

$data = json_decode(file_get_contents('php://input'), TRUE);
$latitude = $data['data']['order_lat'];
$longitude = $data['data']['order_long'];
$driver_id = $data['data']['driver_id'];

function replacer(& $item, $key) {
    	if ($item === null) {
       		$item = '';
    	}
	}

if($latitude != '' && $longitude != '')
{
	$user_query = "select *,
   (((acos(sin(('$latitude'*pi()/180)) * sin((`delivery_lat`*pi()/180))+cos(('$latitude'*pi()/180))
    * cos((`delivery_lat`*pi()/180)) * cos((('$longitude'- `delivery_long`)*pi()/180))))*180/pi())*60*1.1515)
    AS distance from orders where orderStatus = 'Confirmed' and customerID != '".$driver_id."' having distance<20 order by id desc";
	$res = mysqli_query($con,$user_query);
	$i=0;
	$info = array();
	while($aRow=mysqli_fetch_array($res))
	{
		//$info[] = $aRow;
		$all_orders = array();
		$res_detail = "select * from merchant where id = '".$aRow['merchantID']."'";
		$res_detail_info = mysqli_query($con,$res_detail);

		$user_detail = "select firstname,lastname,home_phone,work_phone,mobile_phone from tbl_registeration where id = '".$aRow['customerID']."'";
		$user_detail_info = mysqli_query($con,$user_detail);
		$dd = array();
		while($user_info = mysqli_fetch_array($user_detail_info))
		{
			$dd[] = $user_info;
		}

		//orders infor
		$order = "select it.*,items.* from order_items as items left join items as it on it.id = items.itemID where items.orderID = '".$aRow['id']."'";
		$order_detail = mysqli_query($con,$order);
		while($order_info = mysqli_fetch_array($order_detail))
		{
			$all_orders[] = $order_info;
		}


		while($res_info=mysqli_fetch_array($res_detail_info))
		{
			$info[$i]['id'] = $aRow['id'];
			$info[$i]['Owner_ID'] = $aRow['Owner_ID'];
			$info[$i]['orderDate'] = $aRow['orderDate'];
			$info[$i]['orderTime'] = $aRow['orderTime'];
			$info[$i]['customerID'] = $aRow['customerID'];
			$info[$i]['orderPlacedOn'] = $aRow['orderPlacedOn'];
			$info[$i]['orderType'] = $aRow['orderType'];
			$info[$i]['orderAmount'] = $aRow['orderAmount'];
			$info[$i]['tip'] = $aRow['tip'];
			$info[$i]['orderStatus'] = $aRow['orderStatus'];

			$info[$i]['paymentStatus'] = $aRow['paymentStatus'];
			$info[$i]['orderCompleted'] = $aRow['orderCompleted'];
			$info[$i]['comments'] = $aRow['comments'];
			$info[$i]['address'] = $aRow['address'];
			$info[$i]['address2'] = $aRow['address2'];

			$info[$i]['province'] = $aRow['province'];
			$info[$i]['city'] = $aRow['city'];
			$info[$i]['zipcode'] = $aRow['zipcode'];
			$info[$i]['apartment'] = $aRow['apartment'];
			$info[$i]['floor'] = $aRow['floor'];
			$info[$i]['digicode'] = $aRow['digicode'];
			$info[$i]['company'] = $aRow['company'];

			$info[$i]['department'] = $aRow['department'];
			$info[$i]['deliveryPhone'] = $aRow['deliveryPhone'];
			$info[$i]['code'] = $aRow['code'];
			$info[$i]['DeliveryMethod'] = $aRow['DeliveryMethod'];
			$info[$i]['DeliveryAddress'] = $aRow['DeliveryAddress'];

			$info[$i]['Status_Updatedby'] = $aRow['Status_Updatedby'];
			$info[$i]['Status_Updatedat'] = $aRow['Status_Updatedat'];
			$info[$i]['IsHollaMeals'] = $aRow['IsHollaMeals'];
			$info[$i]['delivery_lat'] = $aRow['delivery_lat'];

			$info[$i]['delivery_lat'] = $aRow['delivery_lat'];
			$info[$i]['delivery_long'] = $aRow['delivery_long'];
			$info[$i]['distance'] = $aRow['distance'];


			$info[$i]['res_name'] = $res_info['merchantName'];
			$info[$i]['res_telephone'] = $res_info['telephone'];
			$info[$i]['res_lat'] = $res_info['geoLat'];
			$info[$i]['res_long'] = $res_info['geoLong'];
			$info[$i]['res_address'] = $res_info['contactAddress'];
			$info[$i]['res_logo'] = $res_info['logo'];

			$info[$i]['user_firstname'] = $dd[0]['firstname'];
			$info[$i]['user_lastname'] = $dd[0]['lastname'];
			$info[$i]['user_homephone'] = $dd[0]['home_phone'];
			$info[$i]['user_workphone'] = $dd[0]['work_phone'];
			$info[$i]['user_mobilephone'] = $dd[0]['mobile_phone'];
			$info[$i]['order_details'] = $all_orders;

		}
		//$i++;

		$i++;
	}




	array_walk_recursive($info, 'replacer');

	/*-------------- Driver related records -------------*/
	//$driver_order = "select * from driver_accept_ride where driver_id = '".$driver_id."' and status != '3'";
	$driver_order = "select m.merchantName as res_name,m.telephone as res_telephone,m.geoLat as res_lat,m.geoLong as res_long,m.contactAddress as res_address,m.logo as res_logo,reg.firstname as user_firstname,reg.lastname as user_lastname,reg.home_phone as user_homephone,reg.work_phone as user_workphone,reg.mobile_phone as user_mobilephone,o.*,ride.status as order_status from driver_accept_ride as ride left join orders as o ON o.id = ride.order_id left join tbl_registeration as reg ON reg.id = o.customerID
left join merchant as m ON m.id = o.merchantID where ride.driver_id = '".$driver_id."' and ride.status != '3' order by o.id desc";

	$driver_query = mysqli_query($con,$driver_order);
	$i=0;
	while($rowss = mysqli_fetch_array($driver_query))
	{
		//$records[] = $rowss;

		//orders infor
		$all_orders = array();
		$order = "select it.*,items.* from order_items as items left join items as it on it.id = items.itemID where items.orderID = '".$rowss['id']."'";
		$order_detail = mysqli_query($con,$order);
		while($order_info = mysqli_fetch_array($order_detail))
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
		$records[$i]['tip'] = $rowss['tip'];
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
		$records[$i]['order_status'] = $rowss['order_status'];
		$records[$i]['order_details'] = $all_orders;
		$i++;
	}

	//array_walk_recursive($records, 'replacer');

	if(empty($records)) { $records = array();}
	$arr = array();
	$arr['driver_orders'] = $records;
	$arr['nearby'] = array_values($info);

	if(empty($records) && empty($info))
	{
		$output=array("status"=>"0","data"=>"No orders found");
		echo json_encode($output);
		exit;
	}
	else
	{
		$output=array("status"=>"1","data"=>$arr);
		echo json_encode($output);
		exit;
	}
}
else
{
	$output=array("status"=>"0","data"=>"Please add correct data");
	echo json_encode($output);exit;
}


?>
