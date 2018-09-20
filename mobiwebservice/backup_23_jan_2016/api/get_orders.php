<?php 
include 'config.php';
include ('function.php');

$data = json_decode(file_get_contents('php://input'), TRUE);
$user_id = $data['data']['user_id'];
$type = $data['data']['type'];
$page_get = $data['data']['page'];
$status = $data['data']['status'];
if($status == 'current')
{
	$dd = " AND orderStatus IN ('Pending','Confirmed','Accepted','Picked Up')";
}
else
{
	$dd = " AND orderStatus NOT IN('Pending','Confirmed','Accepted','Picked Up')";
}

if($page_get =="")
{
	$page=1;
}
else
{
	$page=trim($page_get);
}
	
$sql1 = "SELECT tbl_registeration.firstname AS Name,tbl_registeration.email_add,tbl_registeration.voucher,PaymentSource,Item_ID,TxnID,code,TotalAmount,UsrID,PaymentDesc,Voucher_File,tbl_paymenttransaction.Status,date_format( tbl_paymenttransaction.TxnDate,'%d/%m/%Y') as regdate,tbl_paymenttransaction.TxnDate AS TxnDate,1 AS Main,tbl_paymenttransaction.Activity_ID,tbl_paymenttransaction.Movie_ID,tbl_paymenttransaction.Care_ID,tbl_paymenttransaction.Parking_ID,tbl_paymenttransaction.Ticket_ID,tbl_paymenttransaction.Cat_ID,tbl_paymenttransaction.OrderStatus,DeliveryMethod,orderType,Redeem,TxnDate AS ORDERCREATED,Ticket_Type AS Ticket_Type,Ticket_Quantity AS Ticket_Quantity,quantity,Dine_ID AS Dine_ID,from_date,to_date,0 AS orderDate,0 AS orderTime,tbl_paymenttransaction.Ticket_Title,Movie_Tickets,Instant_MovieTicket,tbl_paymenttransaction.Payat_Lot FROM tbl_paymenttransaction
			INNER JOIN tbl_registeration ON  tbl_registeration.id=tbl_paymenttransaction.UsrID 
			WHERE UsrID=".$user_id.$dd;	
$sql2 = "SELECT tbl_registeration.firstname AS Name,tbl_registeration.email_add,tbl_registeration.voucher,orders.paymentType AS PaymentSource,merchantID AS Item_ID,orders.id AS TxnID,code,orders.orderAmount AS TotalAmount,customerID AS UsrID,comments AS PaymentDesc,Voucher_File,orders.paymentStatus AS Status,date_format(orders.orderDate,'%d/%m/%Y') as regdate,orders.orderPlacedOn AS TxnDate,2 AS Main,0 AS Activity_ID,0 AS Movie_ID,0 AS Care_ID,0 AS Parking_ID,0 AS Ticket_ID,72 AS Cat_ID,orders.OrderStatus,DeliveryMethod,orderType,Redeem,orderPlacedOn AS ORDERCREATED,0 AS Ticket_Type,0 AS Ticket_Quantity,GiftCard_QTY AS quantity,GiftCard_ID AS Dine_ID,orderDate AS from_date,0 AS to_date,orderDate,orderTime,0 AS Ticket_Title,0 AS Movie_Tickets,0 AS Instant_MovieTicket,0 AS Payat_Lot FROM orders
	INNER JOIN tbl_registeration ON  tbl_registeration.id=orders.customerID 
	WHERE customerID=".$user_id.$dd;
$sql3 = "SELECT tbl_registeration.firstname AS Name,tbl_registeration.email_add,tbl_registeration.voucher,PaymentSource,T_Package AS Item_ID,tbl_packagetransaction.T_ID AS TxnID,code,T_TotalAmount AS TotalAmount,T_UsrID AS UsrID,PaymentDesc,0 AS Voucher_File,0 AS Status,date_format( tbl_packagetransaction.T_Created,'%d/%m/%Y') as regdate,tbl_packagetransaction.T_Created AS TxnDate,3 AS Main,0 AS Activity_ID,0 AS Movie_ID,0 AS Care_ID,0 AS Parking_ID,0 AS Ticket_ID,0 AS Cat_ID,OrderStatus,DeliveryMethod,'Package' AS orderType,Redeem,tbl_packagetransaction.T_Created AS ORDERCREATED,0 AS Ticket_Type,0 AS Ticket_Quantity,0 AS quantity,0 AS Dine_ID,'0000-00-00 00:00:00' AS from_date,0 AS to_date,0 AS orderDate,0 AS orderTime,0 AS Ticket_Title,0 AS Movie_Tickets,0 AS Instant_MovieTicket,0 AS Payat_Lot FROM tbl_packagetransaction
	INNER JOIN tbl_registeration ON  tbl_registeration.id=tbl_packagetransaction.T_UsrID 
	WHERE T_UsrID=".$user_id.$dd;
$sql4 = "SELECT tbl_registeration.firstname AS Name,tbl_registeration.email_add,tbl_registeration.voucher,0 AS PaymentSource,Book_Restaurant AS Item_ID,tbl_tablebooking_bookings.Book_ID AS TxnID,code,0 AS TotalAmount,Book_UserID AS UsrID,Book_Notes AS PaymentDesc,0 AS Voucher_File,0 AS Status,date_format(tbl_tablebooking_bookings.Book_Created,'%d/%m/%Y') as regdate,tbl_tablebooking_bookings.Book_Created AS TxnDate,4 AS Main,0 AS Activity_ID,0 AS Movie_ID,0 AS Care_ID,0 AS Parking_ID,0 AS Ticket_ID,72 AS Cat_ID,0 AS OrderStatus,0 AS DeliveryMethod,0 AS orderType,Redeem,Book_Created AS ORDERCREATED,0 AS Ticket_Type,0 AS Ticket_Quantity,Book_Size AS quantity,Book_Restaurant AS Dine_ID,Book_date AS from_date,0 AS to_date,Book_date AS orderDate,Book_Start_Time AS orderTime,0 AS Ticket_Title,0 AS Movie_Tickets,0 AS Instant_MovieTicket,0 AS Payat_Lot FROM  tbl_tablebooking_bookings
	INNER JOIN tbl_registeration ON  tbl_registeration.id=tbl_tablebooking_bookings.Book_UserID 
	WHERE Book_UserID=".$user_id.$dd;
$SQLORDER.=" ORDER BY ORDERCREATED DESC"; 
//$sql=$sql1." UNION ".$sql2." UNION ".$sql3." UNION ".$sql4.$SQLORDER;
$sql=$sql1." UNION ".$sql2." UNION ".$sql3.$SQLORDER;


$arow = mysql_query($sql);
$TotalRecordCount=mysql_num_rows($arow);		

$limit = '40';
$total_page =  round($TotalRecordCount/$limit) + 1;

if($page == 1)
{
	$eu = '0';
}
else
{
	$eu = (($page)*$limit);		
}
	$sqlLimit .= " LIMIT ".$eu.", ".$limit;
	$query=$sql.$sqlLimit;
	$res = mysql_query($query);
	$count = mysql_num_rows($res);
	$contact = array();
	$i=0;

if($TotalRecordCount > 0) 
{
	while($aRow=mysql_fetch_array($res))
	{	
		$rr = getrestaurantinfo($aRow['Item_ID'],72);
		$contact[$i]['TxnID']=$aRow['TxnID'];
		$contact[$i]['UsrID']=$aRow['UsrID'];
		$contact[$i]['Cat_ID']=$aRow['Cat_ID'];
		$contact[$i]['title'] = GetServiceTitle($aRow['Item_ID'],72);
		$contact[$i]['address'] = $rr['address'];
		$contact[$i]['phone'] = $rr['phone'];
		$contact[$i]['Activity_ID']=$aRow['Activity_ID'];
		$contact[$i]['Care_ID']=$aRow['Care_ID'];
		$contact[$i]['Dine_ID']=$aRow['Dine_ID'];
		$contact[$i]['Parking_ID']=$aRow['Parking_ID'];
		$contact[$i]['Ticket_ID']=$aRow['Ticket_ID'];
		$contact[$i]['Movie_ID']=$aRow['Movie_ID'];
		$contact[$i]['TxnDate']=$aRow['TxnDate'];
		$contact[$i]['PaymentPlanID']=$aRow['PaymentPlanID'];
		$contact[$i]['PaymentSource']=$aRow['PaymentSource'];
		$contact[$i]['RefBy']=$aRow['RefBy'];
		$contact[$i]['PaymentDesc']=$aRow['PaymentDesc'];
		$contact[$i]['Instant_MovieTicket']=$aRow['Instant_MovieTicket'];
		$contact[$i]['Movie_Tickets']=$aRow['Movie_Tickets'];
		$contact[$i]['quantity']=$aRow['quantity'];
		$contact[$i]['Amount']=$aRow['Amount'];
		$contact[$i]['TotalAmount']=$aRow['TotalAmount'];
		$contact[$i]['code']=$aRow['code'];
		$contact[$i]['from_date']=$aRow['from_date'];
		$contact[$i]['to_date']=$aRow['to_date'];
		$contact[$i]['Ticket_Title']=$aRow['Ticket_Title'];
		$contact[$i]['Ticket_EventID']=$aRow['Ticket_EventID'];
		$contact[$i]['Ticket_EventVenue']=$aRow['Ticket_EventVenue'];
		$contact[$i]['Ticket_Section']=$aRow['Ticket_Section'];
		$contact[$i]['Ticket_Row']=$aRow['Ticket_Row'];
		$contact[$i]['Ticket_Owner']=$aRow['Ticket_Owner'];
		$contact[$i]['Redeem']=$aRow['Redeem'];
		$contact[$i]['Status']=$aRow['Status'];
		$contact[$i]['regdate']=$aRow['regdate'];
		$contact[$i]['Discount']=$aRow['Discount'];
		$contact[$i]['Item_ID'] = $aRow['Item_ID'];
		$contact[$i]['firstname'] = $aRow['firstname'];
		$contact[$i]['lastname'] = $aRow['lastname'];
		$contact[$i]['email_add'] = $aRow['email_add'];
		$contact[$i]['voucher'] = $aRow['voucher'];	
		$contact[$i]['Ticket_Type'] = $aRow['Ticket_Type'];	
		$contact[$i]['Ticket_Quantity'] = $aRow['Ticket_Quantity'];	
		$contact[$i]['Main'] = $aRow['Main'];	
		$contact[$i]['id'] = $aRow['id'];
		$contact[$i]['user_id'] = $aRow['user_id'];
		$contact[$i]['Name'] = $aRow['Name'];
		$contact[$i]['description'] = $aRow['description'];	
		$contact[$i]['DeliveryMethod'] = $aRow['DeliveryMethod'];	
		$contact[$i]['OrderStatus'] = $aRow['OrderStatus'];
		$contact[$i]['Voucher_File'] = $aRow['Voucher_File'];
		$contact[$i]['Payat_Lot'] = $aRow['Payat_Lot'];
		$contact[$i]['orderDate'] = $aRow['orderDate'];
		$contact[$i]['orderTime'] = $aRow['orderTime'];
		$contact[$i]['orderType'] = $aRow['orderType'];
		
		$driver_info = getDriverinfo($aRow['TxnID']);
		if(!empty($driver_info))
		{
			$contact[$i]['driver_firstname'] = $driver_info['firstname'];
			$contact[$i]['driver_lastname'] = $driver_info['lastname'];
			$contact[$i]['driver_mobile'] = $driver_info['mobile_phone'];
			$contact[$i]['driver_id'] = $driver_info['driver_id'];
		}
		else
		{
			$contact[$i]['driver_firstname'] = "";
			$contact[$i]['driver_lastname'] = "";
			$contact[$i]['driver_mobile'] = "";
			$contact[$i]['driver_id'] = "";
		}
		
		
		$contact[$i]['res_logo'] = $rr['logo'];
		
		$rid = $rr['R_Id'];
		if($rid != '')
		{
			$sql_r="SELECT count(Review_ID) as r_count FROM tbl_reviews where list_id=".$rid." and R_Type = 'Dine'";
			$exe_r=mysql_query($sql_r);
			$rec=mysql_fetch_array($exe_r);
		
			$contact[$i]['review_count'] = $rec['r_count'];
		}
		else
		{
			$contact[$i]['review_count'] = "0";
		}
		$i++;

	}
	if($count == 0)
	{
		$output=array("status"=>"0","data"=>"No Records found");
		echo json_encode($output);exit;
	}
	else 
	{
		$output=array("status"=>"1",'pages'=> $total_page,"data"=>$contact);
		echo json_encode($output);exit;
	}
}	
else
{
	$output=array("status"=>"0","data"=>"No orders found");
	echo json_encode($output);exit;
}
?>