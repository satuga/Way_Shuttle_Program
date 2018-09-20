<?php
	include("config.php");
	include("functions.php"); 
	include("message_class.php");
	$Message_Obj =	new message();
	include("dineclass.php");
	$Dine_Obj	=	new Dine();
	GLOBAL $con;
	if($_REQUEST['orderAmount']<1)
	{
		echo "Please add some items to your cart first";
		exit;
	}
	// check if the order time is within open timing or not
    //$status = $Dine_Obj->getMerchantOpenCloseStatus($_REQUEST['MID'], $_REQUEST["orderdate"] . " " . $_REQUEST['ordertime'], $_REQUEST['orderfor']);

	//if ($status != "Open") {
		//echo "Restaurant is closed for ".$_SESSION["ebids_order"]['orderType']." for your selected order time. please select other time";
		//exit;
	//}
	// Check Minimum Delivery Amount
	$Min_Delivery = $Dine_Obj->getMinimunDeliveryCharges($_REQUEST['MID']);
	if($_REQUEST['orderAmount']<$Min_Delivery && $_REQUEST['orderfor']=='Delivery')
	{
		echo "Sorry, the minimum order amount for Delivery is $".number_format($Min_Delivery,2).". (Hint: There is no minimum order amount if you pick-up.)";
		exit;
	}
	
	if ($_REQUEST["User_ID"]!='')
	{	
		// User details
		$sql2="select firstname,lastname,street,city,state,voucher from tbl_registeration where id=".sanitize($_REQUEST["User_ID"]);
		$exe2=mysqli_query($con,$sql2);
		$res2=mysqli_fetch_array($exe2);
		$Buyer_Name=$res2['firstname']." ".$res2['lastname'];
		
		$rand=genRandomString();
		$code=$rand;
		$Query="SELECT * from tbl_cart where Cart_ID='".sanitize($_REQUEST['cart_id'])."'";
		$res = mysqli_query($con,$Query);
		$res1 = mysqli_query($con,$Query);
		$result1=mysqli_fetch_array($res1);
		$Order_Ids="";
		$Pay_TotalAmount=0;
		$voucher_created=0;
		$orderdate=date("Y-m-d",strtotime($_REQUEST['orderdate']));
		// Insert Order
		$Query="INSERT INTO orders (id,merchantID,Owner_ID,orderDate,orderTime,customerID,orderPlacedOn,orderType,orderAmount,orderStatus,paymentStatus,paymentType,orderTaxAmount,deliveryFee)
		VALUES 
	('','".sanitize($_REQUEST['MID'])."',".$result1['Owner_ID'].",'".mysqli_real_escape_string($con,$orderdate)."','".mysqli_real_escape_string($con,sanitize($_REQUEST['ordertime']))."','".sanitize($_REQUEST["User_ID"])."',NOW(),'".mysqli_real_escape_string($con,sanitize($_REQUEST['orderfor']))."','".mysqli_real_escape_string($con,sanitize($_REQUEST['orderAmount']))."','Pending','Due','Cash','".mysqli_real_escape_string($con,sanitize($_REQUEST['tax_total']))."','".mysqli_real_escape_string($con,sanitize($_REQUEST['deliveryFee']))."')"; 
		mysqli_query($con,$Query);
		$Order_Id=mysqli_insert_id($con);
		$itemIDs="";
		while ($result=mysqli_fetch_array($res)) {
			// Insert Order Items
			$Query="INSERT INTO order_items (id,orderID,itemID,quantity,price,size,notes,crust)
			VALUES 
			('','".$Order_Id."','".$result['Cart_ServiceID']."','".$result['Cart_Quantity']."','".$result['Amount']."','".mysqli_real_escape_string($con,$result['Size'])."','".mysqli_real_escape_string($con,$result['Notes'])."','".$result['crust']."')"; 
			mysqli_query($con,$Query);
			$orderItemID=mysqli_insert_id($con);
			$itemIDs.=",".$result['Cart_ServiceID'];
			// Insert Sub Items
			$Query2="SELECT * from tbl_cartsubitems where Cart_ID=".$result['Cart_ID'];
			$res2 = mysqli_query($con,$Query2);
			$SNum=mysqli_num_rows($res2);
			while ($result1=mysqli_fetch_array($res2)) {
				$SubGdetails=SubItemdetails($result1['subgroup_id']);
				if($result1['subitem_value']=='Left Side' || $result1['subitem_value']=='Right Side')
					$subItemPrice=$SubGdetails[1]/2;
				else
					$subItemPrice=$SubGdetails[1];
				if($result1['subgroup_x2']=='1')
					$subItemPrice=$SubGdetails[1]*2;
								
				$Query2="INSERT INTO order_subitems (id,Order_ID,orderItemID,subItemID,subItemName,subitem_value,subItemPrice)
			VALUES 
			('','".$Order_Id."','".$orderItemID."','".$result1['subgroup_id']."','".mysqli_real_escape_string($con,$SubGdetails[0])."','".mysqli_real_escape_string($con,$result1['subgroup_value'])."','".mysqli_real_escape_string($con,$subItemPrice)."')"; 
			mysqli_query($con,$Query2);
			}
			
			// For Miles/Points
			$sql="INSERT INTO tbl_points(P_ID,P_UserID,P_Transaction,P_Point_Thru,P_points,P_Created) VALUES('',".sanitize($_REQUEST["User_ID"]).",".$Order_Id.",'Payment Transaction','".round(sanitize($_REQUEST['orderAmount']))."',Now())";
			mysqli_query($con,$sql);
		}
		// For Message (Inbox)
		$Message_Obj->insertmessage(sanitize($_REQUEST['MID']),$res["Owner_Restaurant"],$itemIDs);
		
		
		
		$sql="INSERT INTO tbl_payment(Pay_TxnID,UsrID,Pay_TotalAmount,Pay_Created,Pay_Status) VALUES('".$Order_Id."','".sanitize($_REQUEST["User_ID"])."','".sanitize($_REQUEST['orderAmount'])."',NOW(),1)";
		mysqli_query($con,$sql);
		$Payment_Id=mysqli_insert_id($con);
		
		// Empty Cart
		$Qry="DELETE FROM tbl_cart where Cart_ID='".sanitize($_REQUEST['cart_id'])."'";
		mysqli_query($con,$Qry);
		$Redirect_Url = "http://www.bi.way.com/againnewbeta/redirecturl.php";
		//echo "Your Order has completed Sucessfully.";
	}
	/*else
	{
			print "<script language=javascript>window.location='signin_join.php?act=pay&id=".sanitize($_REQUEST["id"])."&plan=".$PaymentPlanID."';</script>";
			exit;
	}*/
?>