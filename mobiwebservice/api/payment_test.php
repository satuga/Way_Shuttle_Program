<?php
error_reporting(0);
include ('config.php');
//include("../include/functions.php"); 
include("function.php"); 
include("../message_class.php");

include 'encrypt/security.php';
$rep=0;
$PTI_TID=0;
$Package_TotalAmount=0;
$EncryptKey=ENCRYPTKEY;
$adminmail=ADMINMAIL;

//----------- decrypt card number -----------------//
$encrypted_card_number=rawurldecode($_REQUEST['Card_Number']);
$card_number=Security::decrypt($encrypted_card_number,CARDENCRYPT);

//echo $card_number;exit;


function xml2array($contents, $get_attributes=1, $priority = 'tag') 
{
	if(!$contents) return array();
	
	if(!function_exists('xml_parser_create')) {
	//print "'xml_parser_create()' function not found!";
	return array();
	}
	
	//Get the XML parser of PHP - PHP must have this module for the parser to work
	$parser = xml_parser_create('');
	xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, "UTF-8"); # http://minutillo.com/steve/weblog/2004/6/17/php-xml-and-character-encodings-a-tale-of-sadness-rage-and-data-loss
	xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
	xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
	xml_parse_into_struct($parser, trim($contents), $xml_values);
	xml_parser_free($parser);
	
	if(!$xml_values) return;
	
	//Initializations
	$xml_array = array();
	$parents = array();
	$opened_tags = array();
	$arr = array();
	
	$current = &$xml_array; //Reference
	
	//Go through the tags.
	$repeated_tag_index = array();//Multiple tags with same name will be turned into an array
	foreach($xml_values as $data) {
	unset($attributes,$value);//Remove existing values, or there will be trouble
	
	//This command will extract these variables into the foreach scope
	// tag(string), type(string), level(int), attributes(array).
	extract($data);//We could use the array by itself, but this is cooler.
	
	$result = array();
	$attributes_data = array();
	
	if(isset($value)) {
	if($priority == 'tag') $result = $value;
	else $result['value'] = $value; //Put the value in a assoc array if we are in the 'Attribute' mode
	}
	
	//Set the attributes too.
	if(isset($attributes) and $get_attributes) {
	foreach($attributes as $attr => $val) {
	if($priority == 'tag') $attributes_data[$attr] = $val;
	else $result['attr'][$attr] = $val; //Set all the attributes in a array called 'attr'
	}
	}
	
	//See tag status and do the needed.
	if($type == "open") {//The starting of the tag '<tag>'
	$parent[$level-1] = &$current;
	if(!is_array($current) or (!in_array($tag, array_keys($current)))) { //Insert New tag
	$current[$tag] = $result;
	if($attributes_data) $current[$tag. '_attr'] = $attributes_data;
	$repeated_tag_index[$tag.'_'.$level] = 1;
	
	$current = &$current[$tag];
	
	} else { //There was another element with the same tag name
	
	if(isset($current[$tag][0])) {//If there is a 0th element it is already an array
	$current[$tag][$repeated_tag_index[$tag.'_'.$level]] = $result;
	$repeated_tag_index[$tag.'_'.$level]++;
	} else {//This section will make the value an array if multiple tags with the same name appear together
	$current[$tag] = array($current[$tag],$result);//This will combine the existing item and the new item together to make an array
	$repeated_tag_index[$tag.'_'.$level] = 2;
	
	if(isset($current[$tag.'_attr'])) { //The attribute of the last(0th) tag must be moved as well
	$current[$tag]['0_attr'] = $current[$tag.'_attr'];
	unset($current[$tag.'_attr']);
	}
	
	}
	$last_item_index = $repeated_tag_index[$tag.'_'.$level]-1;
	$current = &$current[$tag][$last_item_index];
	}
	
	} elseif($type == "complete") { //Tags that ends in 1 line '<tag />'
	//See if the key is already taken.
	if(!isset($current[$tag])) { //New Key
	$current[$tag] = $result;
	$repeated_tag_index[$tag.'_'.$level] = 1;
	if($priority == 'tag' and $attributes_data) $current[$tag. '_attr'] = $attributes_data;
	
	} else { //If taken, put all things inside a list(array)
	if(isset($current[$tag][0]) and is_array($current[$tag])) {//If it is already an array...
	
	// ...push the new element into that array.
	$current[$tag][$repeated_tag_index[$tag.'_'.$level]] = $result;
	
	if($priority == 'tag' and $get_attributes and $attributes_data) {
	$current[$tag][$repeated_tag_index[$tag.'_'.$level] . '_attr'] = $attributes_data;
	}
	$repeated_tag_index[$tag.'_'.$level]++;
	
	} else { //If it is not an array...
	$current[$tag] = array($current[$tag],$result); //...Make it an array using using the existing value and the new value
	$repeated_tag_index[$tag.'_'.$level] = 1;
	if($priority == 'tag' and $get_attributes) {
	if(isset($current[$tag.'_attr'])) { //The attribute of the last(0th) tag must be moved as well
	
	$current[$tag]['0_attr'] = $current[$tag.'_attr'];
	unset($current[$tag.'_attr']);
	}
	
	if($attributes_data) {
	$current[$tag][$repeated_tag_index[$tag.'_'.$level] . '_attr'] = $attributes_data;
	}
	}
	$repeated_tag_index[$tag.'_'.$level]++; //0 and 1 index is already taken
	}
	}
	
	} elseif($type == 'close') { //End of tag '</tag>'
	$current = &$parent[$level-1];
	}
	}
	return($xml_array);
}

if ($_REQUEST["User_ID"])
{	
	$User_ID=$_REQUEST["User_ID"];
	$tip = $_REQUEST['tip'];
	
	
	$deliver_id = $_REQUEST['Delivery_address'];
	$del_query = "select * from tbl_deliveryaddress where DE_ID ='".$deliver_id."'";
	$address_qu = mysqli_query($con,$del_query);
	$address_count = mysqli_num_rows($address_qu);
	if($address_count > 0)
	{
		while($d_address=mysqli_fetch_array($address_qu))
		{
			$address_info[] = $d_address;
		}
		$deliver_address_info = $address_info[0]['DE_Address'].','.$address_info[0]['BLDG_No'].','.$address_info[0]['DE_State'].','.$address_info[0]['DE_City'].','.$address_info[0]['DE_Zipcode'];
	}
	else 
	{
		$deliver_address_info = "";
	}
	
	//$sqlCC="select *,AES_DECRYPT(Card_Number,'".$EncryptKey."') AS CC_NUMBER from tbl_creditcarddetails where Card_Number=AES_ENCRYPT(".$card_number.",'".$EncryptKey."') AND Card_User_ID=".$User_ID;
	$sqlCC="select *,Card_Number AS CC_NUMBER from tbl_creditcarddetails where Card_Number='".$encrypted_card_number."' AND Card_User_ID=".$User_ID;
	$exeCC=mysqli_query($con,$sqlCC);
	$ResCCNum=mysqli_num_rows($exeCC);

	if($ResCCNum<1)
	{
		$sql="INSERT INTO tbl_creditcarddetails(Card_User_ID,Card_Type,Card_Number,Card_Exp_Month,Card_Exp_Year,Card_FirstName,Card_Street,Card_City,Card_State,Card_Zip,Card_Created)VALUES(".$User_ID.",'".mysqli_real_escape_string($con,$_REQUEST['payment_method'])."',AES_ENCRYPT('".mysqli_real_escape_string($con,$card_number)."','".$EncryptKey."'),'".mysqli_real_escape_string($con,$_REQUEST['exp_month'])."','".mysqli_real_escape_string($con,$_REQUEST['exp_date'])."','".mysqli_real_escape_string($con,$_REQUEST['CC_First_Name'])."','".mysqli_real_escape_string($con,$_REQUEST['Street_Address'])."','".mysqli_real_escape_string($con,$_REQUEST['City'])."','".mysqli_real_escape_string($con,$_REQUEST['State'])."','".mysqli_real_escape_string($con,$_REQUEST['Zip_Code'])."',Now())";
		$exeCC=mysqli_query($con,$sql);
		$Card_ID=mysqli_insert_id($con);
	}
	else
	{
		$ResCC=mysqli_fetch_array($exeCC);
		$Card_ID=$ResCC['Card_ID'];
		$sql="UPDATE tbl_creditcarddetails SET Card_Type='".mysqli_real_escape_string($con,$_REQUEST['payment_method'])."',Card_Number='".$encrypted_card_number."',Card_Exp_Month='".mysqli_real_escape_string($con,$_REQUEST['exp_month'])."',Card_Exp_Year='".mysqli_real_escape_string($con,$_REQUEST['exp_date'])."',Card_FirstName='".mysqli_real_escape_string($con,$_REQUEST['CC_First_Name'])."',Card_Street='".mysqli_real_escape_string($con,$_REQUEST['Street_Address'])."',Card_State='".mysqli_real_escape_string($con,$_REQUEST['State'])."',Card_City='".mysqli_real_escape_string($con,$_REQUEST['City'])."',Card_Zip='".mysqli_real_escape_string($con,$_REQUEST['Zip_Code'])."' WHERE Card_ID=".$Card_ID;
		mysqli_query($con,$sql);
	}
	
	//---------------- Get Credit card details ---------------------//
	//$sqlCC="select *,AES_DECRYPT(Card_Number,'".$EncryptKey."') AS CC_NUMBER from tbl_creditcarddetails where Card_ID=".$Card_ID;
	$sqlCC="select *,Card_Number AS CC_NUMBER from tbl_creditcarddetails where Card_ID=".$Card_ID;
	$exeCC=mysqli_query($con,$sqlCC);
	$ResCC=mysqli_fetch_array($exeCC);
	//  End Credit card details
	
	
	$no = Security::decrypt($ResCC['CC_NUMBER'],CARDENCRYPT);
	$ResCC['CC_NUMBER'] = $no;
	
	
	if($_REQUEST['pay_type']=='CreditCard')
	{
			
		// Firstdata Payment Integration
		//$wsdl = "https://ws.firstdataglobalgateway.com/fdggwsapi/services/order.wsdl";              	// PROD WSDL
		
		$wsdl = "https://ws.merchanttest.firstdataglobalgateway.com/fdggwsapi/services/order.wsdl";   // CTE WSDL - Note lines 74 and 75 regarding CTE

		$userid = "WS1909160836._.1";                      // "Username" from WS000000._.1.auth.txt
		$password = "uxxLKeg4";                               // "Password" from WS000000._.1.auth.txt
		$pemlocation = realpath("../WS1909160836._.1.pem");   // Location of "WS000000._.1.pem"
		$kslocation = realpath("../WS1909160836._.1.key");   // Location of "WS000000._.1.key"
		$keyname = "ckp_1436185714";                  // From WS000000._.1.key.pw.txt

		$transactiontype = "sale";
		//$creditcardnumber = $ResCC['CC_NUMBER'];
		$creditcardnumber = $ResCC['CC_NUMBER'];
		
		
		$cardexpirationmonth = $ResCC['Card_Exp_Month'];
		$cardexpirationyear = substr($ResCC['Card_Exp_Year'],(strlen($ResCC['Card_Exp_Year'])-2),2);
		//$chargetotal = $_REQUEST['orderAmount'];
		//$chargetotal=preg_replace('#[^0-9.]+#','',($_REQUEST['orderAmount']-$_REQUEST['Pending_Amount']));
		$orderAmount=preg_replace('#[^0-9.]+#','',($_REQUEST['orderAmount']));
		$Pending_Amount=preg_replace('#[^0-9.]+#','',($_REQUEST['Pending_Amount']));
		$chargetotal=preg_replace('#[^0-9.]+#','',(number_format(($orderAmount-$Pending_Amount),2)));

		$DeliveryAddress=$_REQUEST['DeliveryAddress'];
		
		// Billing Address
		$Billing_Name="testing";
		$Billing_Address=$_REQUEST['Street_Address'];
		$Billing_City = $_REQUEST['City'];
		$Billing_State = $_REQUEST['State'];
		$Billing_Zip=$_REQUEST['Zip_Code'];
		
		$cardcvv="123";
		
		$body = "<SOAP-ENV:Envelope xmlns:SOAP-ENV=\"http://schemas.xmlsoap.org/soap/envelope/\"><SOAP-ENV:Header/><SOAP-ENV:Body><ns4:FDGGWSApiOrderRequest xmlns:ns2=\"http://secure.linkpt.net/fdggwsapi/schemas_us/v1\" xmlns:ns3=\"http://secure.linkpt.net/fdggwsapi/schemas_us/a1\" xmlns:ns4=\"http://secure.linkpt.net/fdggwsapi/schemas_us/fdggwsapi\"><ns2:Transaction><ns2:CreditCardTxType><ns2:Type>";
		$body .= $transactiontype;  //  Transaction Type
		$body .= "</ns2:Type></ns2:CreditCardTxType>";
		$body .= "<ns2:CreditCardData>";
		$body .= "<ns2:CardNumber>";
		$body .= $creditcardnumber;  //  Credit Card Number
		$body .= "</ns2:CardNumber>";
		$body .= "<ns2:ExpMonth>";
		$body .= $cardexpirationmonth;  //  Card Expiration Month
		$body .= "</ns2:ExpMonth><ns2:ExpYear>";
		$body .= $cardexpirationyear;  //  Card Expiration Year
		$body .= "</ns2:ExpYear>";
		$body .= "<ns2:CardCodeValue>";
		$body .= $cardcvv;  //  CVV
		$body .= "</ns2:CardCodeValue>";
		$body .= "<ns2:CardCodeIndicator>PROVIDED</ns2:CardCodeIndicator>";
		$body .= "</ns2:CreditCardData><ns2:Payment><ns2:ChargeTotal>";
		$body .= $chargetotal;  //  Charge Total
		$body .= "</ns2:ChargeTotal></ns2:Payment>";
		
		//$body .= "<ns2:CardCodeValue>";
		//$body .= $cardcvv;  //  CVV
		//$body .= "</ns2:CardCodeValue>";


		// Plz Remove below
		$body .= "<ns2:TransactionDetails>
		<ns2:Recurring>No</ns2:Recurring>
		<ns2:TransactionOrigin>ECI</ns2:TransactionOrigin>
		</ns2:TransactionDetails>";
		// Plz Remove above
		
		//Billing
		$body .= "<ns2:Billing><ns2:Name>".$Billing_Name."</ns2:Name><ns2:Address1>".$Billing_Address."</ns2:Address1><ns2:Address2>172</ns2:Address2><ns2:City>".$Billing_City."</ns2:City><ns2:State>".$Billing_State."</ns2:State><ns2:Zip>".$Billing_Zip."</ns2:Zip><ns2:Country>USA</ns2:Country></ns2:Billing>"; 
		
		//Shipping
		//$body .= "<ns2:Shipping><ns2:Address1>".$DeliveryAddress."</ns2:Address1><ns2:City>".$DeliveryCity."</ns2:City><ns2:State>".$DeliveryState."</ns2:State><ns2:Zip>".$DeliveryZip."</ns2:Zip><ns2:Country>USA</ns2:Country></ns2:Shipping>"; 
		 
		$body .= "</ns2:Transaction>";
		
		$body .= "</ns4:FDGGWSApiOrderRequest></SOAP-ENV:Body></SOAP-ENV:Envelope>";
		// initializing cURL with the IPG API URL:
		$ch = curl_init($wsdl);
		
		// setting the request type to POST:
		curl_setopt($ch, CURLOPT_POST, 1);
		
		// setting the content type:
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: text/xml"));
		
		// setting the authorization method to BASIC:
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		
		// supplying your credentials:
		curl_setopt($ch, CURLOPT_USERPWD, "$userid:$password");
		
		// filling the request body with your SOAP message:
		curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
		
		// telling cURL to verify the server certificate:
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		
		// setting the path where cURL can find the certificate to verify the
		// received server certificate against:
		
		curl_setopt($ch, CURLOPT_SSLCERT, $pemlocation);
		curl_setopt($ch, CURLOPT_SSLKEY, $kslocation);                              // For CTE, comment out this line
		curl_setopt($ch, CURLOPT_SSLKEYPASSWD, $keyname);                           // For CTE, comment out this line
		
		// telling cURL to return the HTTP response body as operation result
		// value when calling curl_exec:
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		
		// calling cURL and saving the SOAP response message in a variable which
		// contains a string like "<SOAP-ENV:Envelope ...>...</SOAP-ENV:Envelope>":
		$data=curl_getinfo($ch);
		$result = curl_exec($ch);
		// closing cURL:
		curl_close($ch); 
		
		
		//$result = '<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/"><SOAP-ENV:Header/><SOAP-ENV:Body><fdggwsapi:FDGGWSApiOrderResponse xmlns:fdggwsapi="http://secure.linkpt.net/fdggwsapi/schemas_us/fdggwsapi"><fdggwsapi:CommercialServiceProvider>CSI</fdggwsapi:CommercialServiceProvider><fdggwsapi:TransactionTime>Tue Jul 07 05:37:29 2015</fdggwsapi:TransactionTime><fdggwsapi:TransactionID>20854538</fdggwsapi:TransactionID><fdggwsapi:ProcessorReferenceNumber>OK242C</fdggwsapi:ProcessorReferenceNumber><fdggwsapi:ProcessorResponseMessage>APPROVED</fdggwsapi:ProcessorResponseMessage><fdggwsapi:ErrorMessage/><fdggwsapi:OrderId>A-afc45434-a007-44b5-b5ab-56ebba270bfe</fdggwsapi:OrderId><fdggwsapi:ApprovalCode>OK242C0020854538:YNAM:</fdggwsapi:ApprovalCode><fdggwsapi:AVSResponse>YNAM</fdggwsapi:AVSResponse><fdggwsapi:TDate>1436261848</fdggwsapi:TDate><fdggwsapi:TransactionResult>APPROVED</fdggwsapi:TransactionResult><fdggwsapi:ProcessorResponseCode>A</fdggwsapi:ProcessorResponseCode><fdggwsapi:ProcessorApprovalCode/><fdggwsapi:CalculatedTax/><fdggwsapi:CalculatedShipping/><fdggwsapi:TransactionScore>100</fdggwsapi:TransactionScore><fdggwsapi:FraudAction>ACCEPT</fdggwsapi:FraudAction><fdggwsapi:AuthenticationResponseCode>M</fdggwsapi:AuthenticationResponseCode></fdggwsapi:FDGGWSApiOrderResponse></SOAP-ENV:Body></SOAP-ENV:Envelope>';
		
		$array = xml2array($result);
		
	

		
		
		$CommercialServiceProvider = $array['SOAP-ENV:Envelope']['SOAP-ENV:Body']['fdggwsapi:FDGGWSApiOrderResponse']['fdggwsapi:CommercialServiceProvider'];
		$TransactionTime = $array['SOAP-ENV:Envelope']['SOAP-ENV:Body']['fdggwsapi:FDGGWSApiOrderResponse']['fdggwsapi:TransactionTime'];
		$TransactionID = $array['SOAP-ENV:Envelope']['SOAP-ENV:Body']['fdggwsapi:FDGGWSApiOrderResponse']['fdggwsapi:TransactionID'];
		$ProessorReferenceNumber = $array['SOAP-ENV:Envelope']['SOAP-ENV:Body']['fdggwsapi:FDGGWSApiOrderResponse']['fdggwsapi:ProessorReferenceNumber'];
		$ProcessorResponseMessage = $array['SOAP-ENV:Envelope']['SOAP-ENV:Body']['fdggwsapi:FDGGWSApiOrderResponse']['fdggwsapi:ProcessorResponseMessage'];
		$ErrorMessage = $array['SOAP-ENV:Envelope']['SOAP-ENV:Body']['fdggwsapi:FDGGWSApiOrderResponse']['fdggwsapi:ErrorMessage'];
		$OrderID = $array['SOAP-ENV:Envelope']['SOAP-ENV:Body']['fdggwsapi:FDGGWSApiOrderResponse']['fdggwsapi:OrderID'];
		$ApprovalCode = $array['SOAP-ENV:Envelope']['SOAP-ENV:Body']['fdggwsapi:FDGGWSApiOrderResponse']['fdggwsapi:ApprovalCode'];
		$AVSResponse = $array['SOAP-ENV:Envelope']['SOAP-ENV:Body']['fdggwsapi:FDGGWSApiOrderResponse']['fdggwsapi:AVSResponse'];
		$TDate = $array['SOAP-ENV:Envelope']['SOAP-ENV:Body']['fdggwsapi:FDGGWSApiOrderResponse']['fdggwsapi:TDate'];
		$TransactionResult = $array['SOAP-ENV:Envelope']['SOAP-ENV:Body']['fdggwsapi:FDGGWSApiOrderResponse']['fdggwsapi:TransactionResult'];
		$ProessorResponseCode = $array['SOAP-ENV:Envelope']['SOAP-ENV:Body']['fdggwsapi:FDGGWSApiOrderResponse']['fdggwsapi:ProessorResponseCode'];
		$ProcessorApprovalCode = $array['SOAP-ENV:Envelope']['SOAP-ENV:Body']['fdggwsapi:FDGGWSApiOrderResponse']['fdggwsapi:ProcessorApprovalCode'];
		$CalculatedTax = $array['SOAP-ENV:Envelope']['SOAP-ENV:Body']['fdggwsapi:FDGGWSApiOrderResponse']['fdggwsapi:CalculatedTax'];
		$CalculatedShipping = $array['SOAP-ENV:Envelope']['SOAP-ENV:Body']['fdggwsapi:FDGGWSApiOrderResponse']['fdggwsapi:CalculatedShipping'];
		$TransactionScore = $array['SOAP-ENV:Envelope']['SOAP-ENV:Body']['fdggwsapi:FDGGWSApiOrderResponse']['fdggwsapi:TransactionScore'];
		$AuthenticationResponseCode = $array['SOAP-ENV:Envelope']['SOAP-ENV:Body']['fdggwsapi:FDGGWSApiOrderResponse']['fdggwsapi:AuthenticationResponseCode'];

	}

	$TransactionResult=getTransactionResult($result);
	if($ErrorMessage=='' || $ErrorMessage=='Array')
	{
		$ErrorMessage=$TransactionResult;
	}
	
	
	echo "<pre>";
	print_r($array);
	exit;
	
?>