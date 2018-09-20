<?php
header("Content-Type: application/json");
include 'config.php';
include ('function.php');
GLOBAL $con;
$data = json_decode(file_get_contents('php://input'), TRUE);
$user_id = $data['data']['user_id'];
$page = $data['data']['page']!='' ? trim($data['data']['page']):'1';
$status = $data['data']['status']; // current & previous
$limit=20;
$expire=date("Y-m-d H:i:s");
$expiredate=date("Y-m-d");
$expiretime=date("H:i:s");
if($status == 'current')
{
	//AND orders.orderTime>='$expiretime' 
	$paymentTranWhere=" WHERE from_date>='$expire' AND  UsrID=$user_id AND Parking_ID>0";
	$ordersWhere=" WHERE  orders.orderDate>='$expiredate' AND orders.orderTime>='$expiretime'  AND  orders.customerID=$user_id";
	$userWhere=" WHERE T_UsrID=$user_id";
	$bookingWhere=" WHERE  Book_date>='$expiredate' AND Book_Start_Time>='$expiretime'  AND Book_UserID=$user_id";
}
else
{
	$paymentTranWhere=" WHERE from_date<'$expire' AND  UsrID=$user_id";
$ordersWhere=" WHERE  orders.orderDate<'$expiredate'  AND  orders.customerID=	$user_id";
	$userWhere=" WHERE T_UsrID=$user_id";
	$bookingWhere=" WHERE  Book_date<'$expiredate'   AND Book_UserID=$user_id";
}
/*$paymentTranWhere=" WHERE   UsrID=$user_id";
	$ordersWhere=" WHERE   customerID=	$user_id";
	$userWhere=" WHERE T_UsrID=$user_id";
	$bookingWhere=" WHERE  Book_UserID=$user_id";*/
$sql1 = "SELECT tbl_registeration.firstname AS Name,tbl_registeration.email_add,tbl_registeration.voucher,PaymentSource,Item_ID,TxnID,code,TotalAmount,UsrID,PaymentDesc,Voucher_File,tbl_paymenttransaction.Status,date_format( tbl_paymenttransaction.TxnDate,'%d/%m/%Y') as regdate,tbl_paymenttransaction.TxnDate AS TxnDate,1 AS Main,tbl_paymenttransaction.Activity_ID,tbl_paymenttransaction.Movie_ID,tbl_paymenttransaction.Care_ID,tbl_paymenttransaction.Parking_ID,tbl_paymenttransaction.Ticket_ID,tbl_paymenttransaction.Cat_ID,tbl_paymenttransaction.OrderStatus,DeliveryMethod,orderType,Redeem,TxnDate AS ORDERCREATED,Ticket_Type AS Ticket_Type,Ticket_Quantity AS Ticket_Quantity,quantity,Dine_ID AS Dine_ID,from_date,to_date,0 AS orderDate,0 AS orderTime,tbl_paymenttransaction.Ticket_Title,Movie_Tickets,Instant_MovieTicket,tbl_paymenttransaction.Payat_Lot,PNF_Confirmation,PNF_TxnID FROM tbl_paymenttransaction
			INNER JOIN tbl_registeration ON  tbl_registeration.id=tbl_paymenttransaction.UsrID
			WHERE UsrID=".$user_id;

		$sql2 = "SELECT tbl_registeration.firstname AS Name,tbl_registeration.email_add,tbl_registeration.voucher,orders.paymentType AS PaymentSource,merchantID AS Item_ID,orders.id AS TxnID,code,orders.orderAmount AS TotalAmount,customerID AS UsrID,comments AS PaymentDesc,Voucher_File,orders.paymentStatus AS Status,date_format(orders.orderDate,'%d/%m/%Y') as regdate,orders.orderPlacedOn AS TxnDate,2 AS Main,0 AS Activity_ID,0 AS Movie_ID,0 AS Care_ID,0 AS Parking_ID,0 AS Ticket_ID,72 AS Cat_ID,orders.OrderStatus,DeliveryMethod,orderType,Redeem,orderPlacedOn AS ORDERCREATED,0 AS Ticket_Type,0 AS Ticket_Quantity,GiftCard_QTY AS quantity,GiftCard_ID AS Dine_ID,orderDate AS from_date,0 AS to_date,orderDate,orderTime,0 AS Ticket_Title,0 AS Movie_Tickets,0 AS Instant_MovieTicket,0 AS Payat_Lot,0 AS PNF_Confirmation,0 AS PNF_TxnID FROM orders
			INNER JOIN tbl_registeration ON  tbl_registeration.id=orders.customerID
			WHERE customerID=".$user_id;
		$sql3 = "SELECT tbl_registeration.firstname AS Name,tbl_registeration.email_add,tbl_registeration.voucher,PaymentSource,T_Package AS Item_ID,tbl_packagetransaction.T_ID AS TxnID,code,T_TotalAmount AS TotalAmount,T_UsrID AS UsrID,PaymentDesc,0 AS Voucher_File,0 AS Status,date_format( tbl_packagetransaction.T_Created,'%d/%m/%Y') as regdate,tbl_packagetransaction.T_Created AS TxnDate,3 AS Main,0 AS Activity_ID,0 AS Movie_ID,0 AS Care_ID,0 AS Parking_ID,0 AS Ticket_ID,0 AS Cat_ID,OrderStatus,DeliveryMethod,'Package' AS orderType,Redeem,tbl_packagetransaction.T_Created AS ORDERCREATED,0 AS Ticket_Type,0 AS Ticket_Quantity,0 AS quantity,0 AS Dine_ID,'0000-00-00 00:00:00' AS from_date,0 AS to_date,0 AS orderDate,0 AS orderTime,0 AS Ticket_Title,0 AS Movie_Tickets,0 AS Instant_MovieTicket,0 AS Payat_Lot,0 AS PNF_Confirmation,0 AS PNF_TxnID FROM tbl_packagetransaction
			INNER JOIN tbl_registeration ON  tbl_registeration.id=tbl_packagetransaction.T_UsrID
			WHERE T_UsrID=".$user_id;
		$sql4 = "SELECT tbl_registeration.firstname AS Name,tbl_registeration.email_add,tbl_registeration.voucher,0 AS PaymentSource,Book_Restaurant AS Item_ID,tbl_tablebooking_bookings.Book_ID AS TxnID,code,0 AS TotalAmount,Book_UserID AS UsrID,Book_Notes AS PaymentDesc,0 AS Voucher_File,0 AS Status,date_format(tbl_tablebooking_bookings.Book_Created,'%d/%m/%Y') as regdate,tbl_tablebooking_bookings.Book_Created AS TxnDate,4 AS Main,0 AS Activity_ID,0 AS Movie_ID,0 AS Care_ID,0 AS Parking_ID,0 AS Ticket_ID,72 AS Cat_ID,0 AS OrderStatus,0 AS DeliveryMethod,0 AS orderType,Redeem,Book_Created AS ORDERCREATED,0 AS Ticket_Type,0 AS Ticket_Quantity,Book_Size AS quantity,Book_Restaurant AS Dine_ID,Book_date AS from_date,0 AS to_date,Book_date AS orderDate,Book_Start_Time AS orderTime,0 AS Ticket_Title,0 AS Movie_Tickets,0 AS Instant_MovieTicket,0 AS Payat_Lot,0 AS PNF_Confirmation,0 AS PNF_TxnID FROM  tbl_tablebooking_bookings
			INNER JOIN tbl_registeration ON  tbl_registeration.id=tbl_tablebooking_bookings.Book_UserID
			WHERE Book_UserID=".$user_id;
		 $sql=$sql1." UNION ".$sql2." UNION ".$sql3." UNION ".$sql4; // die;
		
$arow = mysqli_query($con,$sql);
$TotalRecordCount=mysqli_num_rows($arow);
$total_page =  intval($TotalRecordCount/$limit) + 1;
$eu = $page == 1 ? '0':(($page)*$limit);
$sqlLimit .= " LIMIT ".$eu.", ".$limit;
$orderBy=" order by ORDERCREATED desc";
$query=$sql.$orderBy.$sqlLimit;
//$query=$sql;
$res = mysqli_query($con,$query);
$count = mysqli_num_rows($res);
$contact = array();
$i=0;
if($TotalRecordCount > 0)
{
	while($aRow=mysqli_fetch_array($res))
	{ 
		//var_dump($aRow);
		//$rr = getrestaurantinfo($aRow['Item_ID'],72);
		$v = $aRow;
		//print_r($v); 
        if($v['Main']==1)
        {
            if($v['Activity_ID']>0)
            {
                $C_Title="Play";
                $CT="PL";
                $cat=64; $Item_ID=$v['Activity_ID'];
            }
            else if($v['Movie_ID']>0)
            {
                $C_Title="Movie";
                $cat=70;
                $Item_ID=$v['Movie_ID'];
            }
            else if($v['Ticket_ID']>0)
            {
                $C_Title="Ticket";
                $cat=71; $Item_ID=$v['Ticket_ID'];
            }
            else if($v['Care_ID']>0)
            {
                $C_Title="Care";
                $cat=61; $Item_ID=$v['Care_ID'];
            }
            else if($v['Parking_ID']>0) {
                $C_Title="Parking";
                $cat=45;  $Item_ID=$v['Parking_ID'];
                $Venue=ParkingDetails($v['Parking_ID']);
                $address= $Venue[0].", ".$Venue[1].", ".$Venue[2];

            }
            $Service_Title=GetServiceTitle($Item_ID,$cat);
            if(strlen($Service_Title[0])>60)
                $title=substr($Service_Title[0],0,60)."...";
            else
                $title=$Service_Title[0];
        }
        else if($v['Main']==3)
        {
            $C_Title="Package";
            $Service_Title=GetServiceTitle($v['Item_ID'],'Package');
            $title="Package - ".$Service_Title[0];
        }
        else if($v['Main']==4)
        {
            $C_Title="Table Booking";
            $cat=72;
            $Service_Title=GetServiceTitle($v['Item_ID'],$cat);
            $title="".$Service_Title[0];
        }
        else
        {
            $C_Title="Dine";
            if($v['orderType']=='Gift Card')
                $Service_Title=GetGiftCardTitle($v['Dine_ID']);
            else
                $Service_Title=GetServiceTitle($v['Item_ID'],72);
			$rr = getrestaurantinfo($v['Item_ID'],72);
            $title=$Service_Title;
            $address= $rr['address'];
            $phone= $rr['phone'];
        }
        if($v['Ticket_ID']>0 && $v['Ticket_Title']<>'')
            $title=$v['Ticket_Title'];
        if($title=='')
            $title=$C_Title;
	//echo $title."- ".$rr['title']."<br/>";
	//echo $address;
	//echo $v['Main'];
        $contact[$i]['TxnID']=$aRow['TxnID'];
        $contact[$i]['UsrID']=$aRow['UsrID'];
        $contact[$i]['type']=$C_Title;
        $contact[$i]['Cat_ID']=$aRow['Cat_ID'];
        $contact[$i]['title'] = $title;
        $contact[$i]['address'] = $address;
        $contact[$i]['phone'] = $phone;
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
		$contact[$i]['customer_image'] = $aRow['customer_image'];
		$contact[$i]['Ticket_Type'] = $aRow['Ticket_Type'];
		$contact[$i]['Ticket_Quantity'] = $aRow['Ticket_Quantity'];
		$contact[$i]['Main'] = $aRow['Main'];
		$contact[$i]['id'] = $aRow['id'];
		$contact[$i]['user_id'] = $aRow['user_id'];
		$contact[$i]['Name'] = $aRow['Name'];
		$contact[$i]['description'] = $aRow['description'];
		$contact[$i]['DeliveryMethod'] = $aRow['DeliveryMethod'];
		$contact[$i]['DeliveryLat'] = $aRow['DeliveryLat'];
		$contact[$i]['DeliveryLong'] = $aRow['DeliveryLong'];
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
		$contact[$i]['res_lat'] = $rr['geoLat'];
		$contact[$i]['res_long'] = $rr['geoLong'];

		$rid = $rr['R_Id'];
		if($rid != '')
		{
			$sql_r="SELECT count(Review_ID) as r_count FROM tbl_reviews where list_id=".$rid." and R_Type = 'Dine'";
			$exe_r=mysqli_query($con,$sql_r);
			$rec=mysqli_fetch_array($exe_r);

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
		$contact = removeNull($contact);
		$output=array("status"=>"1","count"=>$count,'pages'=> $total_page,"data"=>$contact);
		echo json_encode($output);exit;
	}
}
else
{
	$output=array("status"=>"0","data"=>"No orders found");
	echo json_encode($output);exit;
}
?>
