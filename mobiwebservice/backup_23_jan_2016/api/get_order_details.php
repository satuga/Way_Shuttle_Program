<?php
include 'config.php';
include ('function.php');
include "../dine/dineclassbk.php";
$EncryptKey = ENCRYPTKEY;

$data = json_decode(file_get_contents('php://input'), TRUE);
$user_id = $data['data']['user_id'];
$txn_id = $data['data']['txn_id'];

$sq="SELECT orders.id AS Order_ID,merchant.logo,tbl_registeration.firstname,tbl_registeration.*,tbl_registeration.email_add,orders.*,merchant.merchantName,merchant.contactAddress,merchant.city AS RES_CITY,merchant.state AS RES_STATE,tbl_registeration.street,tbl_registeration.city AS C_CITY,tbl_registeration.state AS C_STATE,orders.PaymentDesc,orders.DeliveryAddress,merchant.postalCode,AES_DECRYPT(orders.Card_Number,'".$EncryptKey."') AS Card_No FROM orders
INNER JOIN tbl_registeration ON tbl_registeration.id=orders.customerID
INNER JOIN merchant ON merchant.id=orders.merchantID 			
 WHERE orders.id=".mysql_real_escape_string($txn_id); 
$i = 0;
$subtotal = 0;
$res = mysql_query($sq);
while($aRow=mysql_fetch_array($res))
{	
	
	$tax = getMerchantTaxRate($aRow['merchantID']);
	$tax_total = ($aRow['orderAmount']*$tax);
	
	$contact['id'] = $aRow['Order_ID'];
	$contact['merchantID']=$aRow['merchantID'];
	$contact['merchantName']=$aRow['merchantName'];
	$contact['orderDate']=$aRow['orderDate'];
	$contact['orderTime']=$aRow['orderTime'];
	$contact['orderPlacedOn']=$aRow['orderPlacedOn'];
	$contact['orderType']=$aRow['orderType'];
	$contact['logo']=$aRow['logo'];
	$contact['orderAmount']=$aRow['orderAmount'];
	$contact['orderStatus']=$aRow['orderStatus'];
	$contact['paymentStatus']=$aRow['paymentStatus'];
	$contact['orderCompleted']=$aRow['orderCompleted'];
	$contact['paymentType']=$aRow['paymentType'];
	$contact['orderTaxAmount']=$aRow['orderTaxAmount'];
	$contact['discount']=$aRow['discount'];
	$contact['deliveryFee']=$aRow['deliveryFee'];
	$contact['customerID'] = $aRow['customerID'];
	$contact['firstname'] = $aRow['firstname'];
	$contact['display_name'] = $aRow['display_name'];
	$contact['lastname'] = $aRow['lastname'];
	$contact['email_add'] = $aRow['email_add'];
	$contact['contact_principle'] = $aRow['contact_principle'];
	$contact['home_phone'] = $aRow['home_phone'];
	$contact['mobile_phone'] = $aRow['mobile_phone'];
	$contact['work_phone'] = $aRow['work_phone'];
	
	$contact['code'] = $aRow['code'];
	$contact['Card_Address'] = $aRow['Card_Address'];
	$contact['Card_State'] = $aRow['Card_State'];
	$contact['Card_City'] = $aRow['Card_City'];
	$contact['Card_Type'] = $aRow['Card_Type'];
	$contact['Card_Name'] = $aRow['Card_Name'];
	$contact['Card_Number'] = $aRow['Card_No'];
	
	$contact['DeliveryAddress'] = $aRow['DeliveryAddress'];
	$contact['contactAddress'] = $aRow['contactAddress'];
	$contact['RES_CITY'] = $aRow['RES_CITY'];
	$contact['RES_STATE'] = $aRow['RES_STATE'];
	$contact['Redeem'] = $aRow['Redeem'];
	$contact['street'] = $aRow['street'];
	$contact['C_CITY'] = $aRow['C_CITY'];
	$contact['C_STATE'] = $aRow['C_STATE'];
	$contact['postalCode'] = $aRow['postalCode'];
	$contact['PaymentDesc'] = $aRow['PaymentDesc'];
	$contact['points_earned'] = $aRow['points_earned'];
	
	
	$sql="SELECT order_items.*,items.itemName,items.Cus_Pizza,orders.PaymentDesc,menus.menuName FROM order_items 
	INNER JOIN items ON items.id=order_items.itemID 
	INNER JOIN orders ON orders.id=order_items.orderID 
	INNER JOIN menus ON menus.id=items.menuID 
	WHERE orderID=".$aRow['Order_ID'];
	$exe=mysql_query($sql);
	$y = 0;
	while($rec=mysql_fetch_array($exe))
	{
		$record[$y]['id']	=	$rec['id'];
		$record[$y]['itemID']	=	$rec['itemID'];
		$record[$y]['itemName']	=	$rec['itemName'];
		$record[$y]['menuName']	=	$rec['menuName'];
		$record[$y]['quantity']	=	$rec['quantity'];
		$record[$y]['price']	=	$rec['price'];
		$record[$y]['size']	=	$rec['size'];
		$record[$y]['notes']=	$rec['notes'];
		$record[$y]['crust']	=	$rec['crust'];
		$record[$y]['Cus_Pizza']=	$rec['Cus_Pizza'];
		$record[$y]['PaymentDesc']	=	$rec['PaymentDesc'];
		//$record[$y]['Size']	=	$rec['size'];
		
		$subtotal += ($rec['price'] * $rec['quantity']);
		
		$y++;
	}
	$tax_total = sprintf("%.2f",$subtotal*$tax/100);
	$contact['orders'] = $record;
	$contact['tax'] = $tax_total;
	//$i++;
}

$output=array("status"=>"1","data"=>$contact);
echo json_encode($output);
exit;
?>