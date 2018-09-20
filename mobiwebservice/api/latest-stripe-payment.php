<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
include ('config.php');
require_once('stripe-config.php');
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




	if($_REQUEST['pay_type']=='CreditCard')
	{
		 $token  = $_REQUEST['stripe_token'];

		 $amount  = $_REQUEST['orderAmount']*100;
		 $email  = $_REQUEST['Delivery_Email'];
		 $currency = "USD";

		 if($token == "" || $amount == "" || $email == ""){
		 	  $content=array("status"=>"0","response"=>"error","message"=>"Parameter Missing");
		    echo json_encode($content);
			  exit;
		 }

		  /*new code added by nilesh for stripe payment on 20-11-2015*/
		  /*$customerData=Stripe_Customer::all();

		  $count = count($customerData['data']);
		  $existing = 0;
		  $cust_id = "";
		  for($n=0; $n<$count; $n++)
		  {
		  		if($customerData['data'][$n]['email'] == $email)
		  		{
		  			$existing = "1";
		  			$cust_id = $customerData['data'][$n]['id'];

		  			break;
		  		}
		  }

		  if($existing == "1")
		  {
			      $customer_id  = $cust_id;
		  }else{
				  try {
					    $customer = Stripe_Customer::create(array(
		      				       'email' => $email,
		      				       'source'  => $token));
							$customer_id  = $customer['id'];
					} catch (Exception $e) {
					    $content=array("status"=>"0","response"=>"error","message"=>$e->getMessage());
	        		echo json_encode($content);
						  exit();
					}
		  }*/

					$response = array();
			 		try{
							$customerData=Stripe_Customer::all();
							//print_r($customerData);
							//check if customer exist
					    $customer_id = '';
					    foreach ($customerData['data'] as $customer) {
					        if($customer['email'] == $email){
					            $customer_id =  $customer['id'];
					        }
					    }
							//check if customer exist if not then create new one
							if($customer_id == ''){
								$customer = Stripe_Customer::create(array(
													'email' => $email,
													'card'  => $token));
								$customer_id  = $customer['id'];
							}
							// if($customerData['data'][0]['email']==$email)
							// {
							// 	$customer_id  = $customerData['data'][0]['id'];
							// }else{
							// 	$customer = Stripe_Customer::create(array(
							// 						'email' => $email,
							// 						'card'  => $token));
							// 	$customer_id  = $customer['id'];
							// }
				  	  $charge = Stripe_Charge::create(array(
				               'customer' => $customer_id,
				               'amount'   => $amount,
				               'currency' => "USD"
				      ));


					  //$sqlCC="select *,AES_DECRYPT(Card_Number,'".$EncryptKey."') AS CC_NUMBER from tbl_creditcarddetails where Card_Number=AES_ENCRYPT(".$card_number.",'".$EncryptKey."') AND Card_User_ID=".$User_ID;
				  	$sqlCC="select *,Card_Number AS CC_NUMBER from tbl_creditcarddetails where Card_Number='".$encrypted_card_number."' AND Card_User_ID=".$User_ID;
				  	$exeCC=mysqli_query($con,$sqlCC);
				  	$ResCCNum=mysqli_num_rows($exeCC);

				  	if($ResCCNum<1)
				  	{
				  		$sql="INSERT INTO tbl_creditcarddetails(Stripe_UserId,Card_User_ID,Card_Type,Card_Number,Card_Exp_Month,Card_Exp_Year,Card_FirstName,Card_Street,Card_City,Card_State,Card_Zip,Card_Created)VALUES(".$customer_id.",".$User_ID.",'".mysqli_real_escape_string($con,$_REQUEST['payment_method'])."',AES_ENCRYPT('".mysqli_real_escape_string($con,$card_number)."','".$EncryptKey."'),'".mysqli_real_escape_string($con,$_REQUEST['exp_month'])."','".mysqli_real_escape_string($con,$_REQUEST['exp_date'])."','".mysqli_real_escape_string($con,$_REQUEST['CC_First_Name'])."','".mysqli_real_escape_string($con,$_REQUEST['Street_Address'])."','".mysqli_real_escape_string($con,$_REQUEST['City'])."','".mysqli_real_escape_string($con,$_REQUEST['State'])."','".mysqli_real_escape_string($con,$_REQUEST['Zip_Code'])."',Now())";
				  		$exeCC=mysqli_query($con,$sql);
				  		$Card_ID=mysqli_insert_id($con);
				  	}
				  	else
				  	{
				  		$ResCC=mysqli_fetch_array($exeCC);
				  		$Card_ID=$ResCC['Card_ID'];
				  		$sql="UPDATE tbl_creditcarddetails SET Card_Type='".mysqli_real_escape_string($con,$_REQUEST['payment_method'])."',Card_Number='".$encrypted_card_number."',Card_Exp_Month='".mysqli_real_escape_string($con,$_REQUEST['exp_month'])."',Card_Exp_Year='".mysqli_real_escape_string($con,$_REQUEST['exp_date'])."',Card_FirstName='".mysqli_real_escape_string($con,$_REQUEST['CC_First_Name'])."',Card_Street='".mysqli_real_escape_string($con,$_REQUEST['Street_Address'])."',Card_State='".mysqli_real_escape_string($con,$_REQUEST['State'])."',Card_City='".mysqli_real_escape_string($con,$_REQUEST['City'])."',Card_Zip='".mysqli_real_escape_string($con,$_REQUEST['Zip_Code'])."', Stripe_UserId='".$customer_id."' WHERE Card_ID=".$Card_ID;
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

		      }catch(Stripe_CardError $e) {
					      $response['status']='0';
								$response['response']='error';
					      $response['message'] = $e->getMessage();
					      header('Content-Type: application/json; charset=utf-8');
					      echo json_encode($response);
					      exit();
			    } catch (Stripe_InvalidRequestError $e) {
					      // Invalid parameters were supplied to Stripe's API
					      $response['status']='0';
								$response['response']='error';
					      $response['message'] = $e->getMessage();
					      header('Content-Type: application/json; charset=utf-8');
					      echo json_encode($response);
					      exit();
			    } catch (Stripe_AuthenticationError $e) {
					      // Authentication with Stripe's API failed
					      $response['status']='0';
								$response['response']='error';
					      $response['message'] = $e->getMessage();
					      header('Content-Type: application/json; charset=utf-8');
					      echo json_encode($response);
					      exit();
			    } catch (Stripe_ApiConnectionError $e) {
					      // Network communication with Stripe failed
					      $response['status']='0';
								$response['response']='error';
					      $response['message'] = $e->getMessage();
					      header('Content-Type: application/json; charset=utf-8');
					      echo json_encode($response);
					      exit();
			    } catch (Stripe_Error $e) {
					      // Display a very generic error to the user, and maybe send
					      // yourself an email
					      $response['status']='0';
								$response['response']='error';
					      $response['message'] = $e->getMessage();
					      header('Content-Type: application/json; charset=utf-8');
					      echo json_encode($response);
					      exit();
			    } catch (Exception $e) {
					      // Something else happened, completely unrelated to Stripe
					      $response['status']='0';
								$response['response']='error';
					      $response['message'] = $e->getMessage();
					      header('Content-Type: application/json; charset=utf-8');
					      echo json_encode($response);
					      exit();
			    }

		  /*if(!$charge){
		  	  $content=array("status"=>"0","response"=>"error","message"=>"please enter valid token");
	        echo json_encode($content);
			    exit;
		  } //else{
			  //echo "<pre>"; print_r($charge);exit();
		  //}
		  /* echo "<pre>";

		  print_r($charge);
		  exit();
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

		$ProessorReferenceNumber = $array['SOAP-ENV:Envelope']['SOAP-ENV:Body']['fdggwsapi:FDGGWSApiOrderResponse']['fdggwsapi:ProessorReferenceNumber'];
		$ProcessorResponseMessage = $array['SOAP-ENV:Envelope']['SOAP-ENV:Body']['fdggwsapi:FDGGWSApiOrderResponse']['fdggwsapi:ProcessorResponseMessage'];


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
		*/
		$ErrorMessage = $charge->failure_message;
		$OrderID = $charge->id;
		$TransactionID = $charge->balance_transaction;

	}


	/*$TransactionResult=getTransactionResult($result);
	if($ErrorMessage=='' || $ErrorMessage=='Array')
	{
		$ErrorMessage=$TransactionResult;
	}*/

	if($charge->failure_message != "")
	{
		$ErrorMessage = $charge->failure_message;
	}

	if($charge->paid == "1" && $charge->id != "")
	{

		$rep=1;
		// User details
		$sql2="select firstname,lastname,street,city,state,zipcode,voucher,email_add from tbl_registeration where id=".$User_ID;
		$exe2=mysqli_query($con,$sql2);
		$res2=mysqli_fetch_array($exe2);
		$Buyer_Name=$res2['firstname']." ".$res2['lastname'];



		// Package Cart
		$Package_Total=0;
		$Query="SELECT SUM(Cart_TotalAmount) AS TAMT,Cart_Package from tbl_cartpackages where Cart_Session='".$_REQUEST['device_id']."'";
		$res = mysqli_query($con,$Query);
		$PRes1=mysqli_fetch_array($res);

		$Query="SELECT * FROM tbl_cartpackages WHERE Cart_Session='".$_REQUEST['device_id']."'";
		$res = mysqli_query($con,$Query);
		$Pnum=mysqli_num_rows($res);

		$Package_Total=$PRes1['TAMT'];
		$PTI_TID=0;
		if($Pnum>0)
		{
			$pk=$_REQUEST['Package'];
			if($_REQUEST['Instructions'.$pk]=='Enter special instructions for your order here:')
				$Instructions="";
			else
				$Instructions=mysqli_real_escape_string($con,$_REQUEST['Instructions'.$pk]);

			$Delivery_Address=explode(":",$_REQUEST['DeliveryAddress'.$pk]);
			$Delivery_Email=explode(":",$_REQUEST['Delivery_Email'.$pk]);

			if($Delivery_Address[1]<>'')
				$Delivery_Address=$Delivery_Address[1];
			else
				$Delivery_Address=$_REQUEST['DeliveryAddress'.$pk];

			if($Delivery_Email[1]<>'')
				$Delivery_Email=$Delivery_Email[1];
			else
				$Delivery_Email=$_REQUEST['Delivery_Email'.$pk];

			$rand=genRandomString();
		 	$code=$rand;

		 	$Query1="INSERT INTO tbl_packagetransaction(T_ID,T_Package,T_UsrID,T_TotalAmount,T_Created,Card_Type,Card_Name,Card_Number,Card_Expiry_Month,Card_Expiry_Year,Card_Zip,Card_Address,Card_State,Card_City,DeliveryMethod,DeliveryState,DeliveryCity,DeliveryZip,DeliveryEmail,DeliveryAddress,PaymentDesc,code) VALUES ('',".$PRes1['Cart_Package'].",".$_REQUEST['User_ID'].",".$PRes1['TAMT'].",Now(),'".mysqli_real_escape_string($con,$ResCC['Card_Type'])."','".mysqli_real_escape_string($con,$ResCC['Card_FirstName'])."',AES_ENCRYPT('".$ResCC['CC_NUMBER']."','".$EncryptKey."'),'".mysqli_real_escape_string($con,$ResCC['Card_Exp_Month'])."','".mysqli_real_escape_string($con,$ResCC['Card_Exp_Year'])."','".mysqli_real_escape_string($con,$ResCC['Card_Zip'])."','".mysqli_real_escape_string($con,$ResCC['Card_Street'])."','".mysqli_real_escape_string($con,$ResCC['Card_State'])."','".mysqli_real_escape_string($con,$ResCC['Card_City'])."','".mysqli_real_escape_string($con,$_REQUEST['DeliveryMethod'.$pk])."','".mysqli_real_escape_string($con,$_REQUEST['State'.$pk])."','".mysqli_real_escape_string($con,$_REQUEST['City'.$pk])."','".mysqli_real_escape_string($con,$_REQUEST['Zip_Code'.$pk])."','".mysqli_real_escape_string($con,$Delivery_Email)."','".mysqli_real_escape_string($con,$Delivery_Address)."','".$Instructions."','".$code."')";
			mysqli_query($con,$Query1);

			$PTI_TID=mysqli_insert_id($con);
			$Package_TotalAmount=$PRes1['TAMT'];

			while ($PRes=mysqli_fetch_array($res))
			{
				if($_REQUEST['Instructions'.$pk]=='Enter special instructions for your order here:')
					$Instructions="";
				else
					$Instructions=mysqli_real_escape_string($con,$_REQUEST['Instructions'.$pk]);

				$Delivery_Address=explode(":",$_REQUEST['DeliveryAddress'.$pk]);
				$Delivery_Email=explode(":",$_REQUEST['Delivery_Email'.$pk]);

				if($Delivery_Address[1]<>'')
					$Delivery_Address=$Delivery_Address[1];
				else
					$Delivery_Address=$_REQUEST['DeliveryAddress'.$pk];

				if($Delivery_Email[1]<>'')
					$Delivery_Email=$Delivery_Email[1];
				else
					$Delivery_Email=$_REQUEST['Delivery_Email'.$pk];

				$Query="INSERT INTO tbl_packagetransactionitems(PTI_ID,PTI_TID,PTI_ItemID,PTI_Amount,PTI_Quantity,PTI_TotalAmount,DeliveryMethod,DeliveryEmail,DeliveryAddress,PaymentDesc) VALUES ('',".$PTI_TID.",".$PRes['Cart_Item'].",".$PRes['Cart_Amount'].",".$PRes['Cart_Quantity'].",".$PRes['Cart_TotalAmount'].",'".mysqli_real_escape_string($con,$_REQUEST['DeliveryMethod'.$pk])."','".mysqli_real_escape_string($con,$Delivery_Email)."','".mysqli_real_escape_string($con,$Delivery_Address)."','".$Instructions."')";
				mysqli_query($con,$Query);
			$pk++;
			}
		}



		// end Package
		$rand=genRandomString();
		$code=$rand;

		$Order_Ids="";
		$Pay_TotalAmount=0;
		$Dine_TotalAmount=0;
		$voucher_created=0;
		$pk=1;
		$Pay_TxnID=0;

		//  Dine Transaction
		$Query="SELECT * from tbl_cart where Sess_ID='".$_REQUEST['device_id']."' AND Cart_Type='Dine'";
		$res = mysqli_query($con,$Query);
		$res1 = mysqli_query($con,$Query);
		$result=mysqli_fetch_array($res1);
		$DineTran=mysqli_num_rows($res1);
		// Dine payment transaction
		if($DineTran>0)
		{

			$orderdate=date("Y-m-d",strtotime($_REQUEST['orderdate']));

			if($_REQUEST['pay_type']=='Cash')
				$paymentStatus="Due";
			else
				$paymentStatus="Paid";
			if($_REQUEST['Instructions']=='Enter special instructions for your order here:')
				$Instructions="";

			//$Delivery_Address=$_REQUEST['Delivery_Address'];
			$Delivery_Address= $deliver_address_info;
			$Owner_ID=$result['Owner_ID'];
			// For Miles/Points
			$PointPercentage=round(GetPointPercentage('72'));
			// Points Calculation
			$Points=0;
			//$PointPercentage;
			$Points=round((($_REQUEST['orderDineAmount']*$PointPercentage)/100),2);
			// Insert Order

			if($_REQUEST['deliveryFee']=='')
				$deliveryFee=0;
			else
				$deliveryFee=mysqli_real_escape_string($con,$_REQUEST['deliveryFee']);
			if($_REQUEST['POINTS']>0)
				$sess_points=mysqli_real_escape_string($con,$_REQUEST['POINTS']);
			else
				$sess_points=0;


        $Query="INSERT INTO orders (merchantID,Owner_ID,orderDate,orderTime,customerID,orderPlacedOn,orderType,orderAmount,tip,orderStatus,paymentStatus,paymentType,orderTaxAmount,discount,discount_points,points_earned,deliveryFee,comments,Card_Type,Card_Name,Card_Number,Card_Expiry_Month,Card_Expiry_Year,Card_Zip,Card_Address,Card_State,Card_City,code,DeliveryAddress,PaymentDesc,delivery_lat,delivery_long,parking_total,dine_total)
   			VALUES
   			('".$_REQUEST['MID']."',".$result['Owner_ID'].",'".mysqli_real_escape_string($con,$orderdate)."','".$_REQUEST['ordertime']."','".$User_ID."',NOW(),'".mysqli_real_escape_string($con,$_REQUEST['orderfor'])."','".mysqli_real_escape_string($con,$_REQUEST['orderAmount'])."','".$tip."','Confirmed','".$paymentStatus."','".mysqli_real_escape_string($con,$_REQUEST['pay_type'])."','".mysqli_real_escape_string($con,$_REQUEST['tax_total'])."','".mysqli_real_escape_string($con,$_REQUEST['discount'])."','".$sess_points."','".$Points."','".$deliveryFee."','".mysqli_real_escape_string($con,$_REQUEST['comments'])."','".mysqli_real_escape_string($con,$ResCC['Card_Type'])."','".mysqli_real_escape_string($con,$ResCC['Card_FirstName'])."',AES_ENCRYPT('".$ResCC['CC_NUMBER']."','".$EncryptKey."'),'".mysqli_real_escape_string($con,$ResCC['Card_Exp_Month'])."','".mysqli_real_escape_string($con,$ResCC['Card_Exp_Year'])."','".mysqli_real_escape_string($con,$ResCC['Card_Zip'])."','".mysqli_real_escape_string($con,$ResCC['Card_Street'])."','".mysqli_real_escape_string($con,$ResCC['Card_State'])."','".mysqli_real_escape_string($con,$ResCC['Card_City'])."','".$code."','".mysqli_real_escape_string($con,$Delivery_Address)."','".mysqli_real_escape_string($con,$Instructions)."','".$_REQUEST['delivery_lat']."','".$_REQUEST['delivery_long']."','".$_REQUEST['parking_total']."','".$_REQUEST['dine_total']."')";
   		$Dine_TotalAmount=$_REQUEST['orderDineAmount'];

			mysqli_query($con,$Query);
			$Order_Id=mysqli_insert_id($con);
			$Dine_TxnID=$Order_Id;
			$itemIDs="";
			while ($result11=mysqli_fetch_array($res))
			{
				// Insert Order Items
				$Query="INSERT INTO order_items (orderID,itemID,quantity,price,size,notes,crust)
				VALUES
				('".$Order_Id."','".$result11['Cart_ServiceID']."','".$result11['Cart_Quantity']."','".$result11['Amount']."','".mysqli_real_escape_string($con,$result11['Size'])."','".mysqli_real_escape_string($con,$result11['Notes'])."','".$result11['crust']."')";

				mysqli_query($con,$Query);
				$orderItemID=mysqli_insert_id($con);
				$itemIDs.=",".$result11['Cart_ServiceID'];
				// Insert Sub Items
				$Query2="SELECT * from tbl_cartsubitems where Cart_ID=".$result11['Cart_ID'];
				$res2 = mysqli_query($con,$Query2);
				$SNum=mysqli_num_rows($res2);

				while ($result12=mysqli_fetch_array($res2))
				{
					$SubGdetails=SubItemdetails($result12['subgroup_id']);
					$price_index=$result12['price_index'];

					if($result12['subgroup_value']=='Left Side' || $result12['subgroup_value']=='Right Side')
						$subItemPrice=$SubGdetails[1]/2;
					else if($result12['price_index']>0) {
						$subprices=explode(",",$SubGdetails[1]);
						$subItemPrice=$subprices[$price_index-1];
					} else
						$subItemPrice=$SubGdetails[1];

					if($result12['subgroup_x2']==1)
						$subItemPrice=$subItemPrice*2;
					if($result12['Free_Toppings']==1)
						$subItemPrice=0;
					$Query2="INSERT INTO order_subitems (Order_ID,orderItemID,subItemID,subItemName,subitem_value,subItemPrice,subgroup_x2,Free_Toppings)
					VALUES
					('".$Order_Id."','".$orderItemID."','".$result12['subgroup_id']."','".mysqli_real_escape_string($con,$SubGdetails[0])."','".mysqli_real_escape_string($con,$result12['subgroup_value'])."','".mysqli_real_escape_string($con,$subItemPrice)."','".$result12['subgroup_x2']."','".$result12['Free_Toppings']."')";
					mysqli_query($con,$Query2);
				}
			}



			if($_REQUEST['promo_id'] != '')
			{
				$Query_promo="INSERT INTO tbl_promocodelog(PCL_OrderID,PCL_CategoryName,PCL_CustomerID,PCL_CreatedDate,PCL_PMC_PromoID)
				VALUES
				('".$Order_Id."','DINE','".$User_ID."',NOW(),'".$_REQUEST['promo_id']."')";
				mysqli_query($con,$Query_promo);


				$promo_upsql="UPDATE tbl_promocode SET PMC_occupied= PMC_occupied + 1 WHERE PMC_PromoID = ".$_REQUEST['promo_id'];
				mysqli_query($con,$promo_upsql);
			}

			// ---------------------- Points ----------------------- //
			$sql="INSERT INTO tbl_points(P_UserID,P_Transaction,P_Type,P_Point_Thru,P_points,P_Percentage,P_TransactionAmount,P_Created) VALUES(".$User_ID.",".$Order_Id.",'Dine','Payment Transaction','".$Points."','".$PointPercentage."',".$_REQUEST['orderDineAmount'].",Now())";
			mysqli_query($con,$sql);

			if($_REQUEST['POINTS']>0 && $_REQUEST['discount']>0)
			{
				// Move Points to Redeemed
				$psql="SELECT Points,Points_Redeemed FROM tbl_registeration WHERE id=".$User_ID;
				$pexe=mysqli_query($con,$psql);
				$pres=mysqli_fetch_array($pexe);
				$NewPoints=$pres['Points']-$_REQUEST['POINTS'];
				$NewPoints_Redeemed=$pres['Points_Redeemed']+$_REQUEST['POINTS'];
				$upsql="UPDATE tbl_registeration SET Points=".$NewPoints.",Points_Redeemed=".$NewPoints_Redeemed." WHERE id=".$User_ID;
				mysqli_query($con,$upsql);
			}

			$_REQUEST['POINTS']="";
			// Update Points for Transaction
			$psql="SELECT Points FROM tbl_registeration WHERE id=".$User_ID;
			$pexe=mysqli_query($con,$psql);
			$pres=mysqli_fetch_array($pexe);
			$DBPoints=$pres['Points']+$Points;
			$upsql="UPDATE tbl_registeration SET Points=".$DBPoints." WHERE id=".$User_ID;
			mysqli_query($con,$upsql);
			// End Miles/Points

			// For Message (Inbox)
			/*
			if($result['Cart_Type']=='Parking')
				$Message_Obj->insertParkingmessage($Buyer_Name,$result['Cart_Type'],$result['Owner_ID'],$result['Cart_ServiceID'],$Order_Id);
			else
				$Message_Obj->insertmessageDine($_REQUEST['MID'],$Owner_ID,$itemIDs,$Order_Id,$orderdate,$_REQUEST['ordertime'],$_REQUEST['orderDineAmount'],$_REQUEST['orderfor'],$_REQUEST['deliveryFee'],$_REQUEST['discount']);
			*/
		}
		// End Dine Payment Transaction


		$Query="SELECT * from tbl_cart where Sess_ID='".$_REQUEST['device_id']."' AND Cart_Type!='Dine'";
		$res = mysqli_query($con,$Query);

		while ($result=mysqli_fetch_array($res))
		{
			$ServiceID=$result['Cart_ServiceID'];
			if($result['Cart_Type']=='Activities')
			{
				$Cat_ID=64;$Parking_ID=0;$Ticket_ID=0;$Care_ID=0;$Activity_ID=$result['Cart_ServiceID'];
				$sq="SELECT SUM(CAP_Quantity) AS CAP_Quantity FROM  tbl_cartactivityprice WHERE CAP_CartID=".$result['Cart_ID'];
				$ssexe=mysqli_query($con,$sq);
				$ssres=mysqli_fetch_array($ssexe);
				$CAP_Quantity=$ssres['CAP_Quantity'];
				if($CAP_Quantity<1)
					$CAP_Quantity=$result['Cart_Quantity'];
			}
			else if($result['Cart_Type']=='Movies') {
				$Cat_ID=70;$Parking_ID=0;$Ticket_ID=0;$Care_ID=0;$Activity_ID=0;$Movie_ID=$result['Cart_ServiceID'];
				$sq="SELECT SUM(CAP_Quantity) AS CAP_Quantity FROM tbl_cartmovieprice WHERE CAP_CartID=".$result['Cart_ID'];
				$ssexe=mysqli_query($con,$sq);
				$ssres=mysqli_fetch_array($ssexe);
				$CAP_Quantity=$ssres['CAP_Quantity'];
				if($CAP_Quantity<1)
					$CAP_Quantity=$result['Cart_Quantity'];
			}
			else if($result['Cart_Type']=='Care')
			{
				$Cat_ID=61;$Parking_ID=0;$Ticket_ID=0;$Care_ID=$result['Cart_ServiceID'];$Activity_ID=0;
			}
			else if($result['Cart_Type']=='Tickets')
			{
				$Cat_ID=71;$Parking_ID=0;$Ticket_ID=$result['Cart_ServiceID'];$Activity_ID=0;$Care_ID=0;$Movie_ID=0;
				$ServiceTitle=GetServiceTitle($Ticket_ID,$Cat_ID);
			}
			else if($result['Cart_Type']=='Parking')
			{
				$Cat_ID=45;$Parking_ID=$result['Cart_ServiceID'];$Ticket_ID=0;$Care_ID=0;$Activity_ID=0;
			}

			$ServiceTitle=GetServiceTitle($Activity_ID,$Cat_ID);

			if($_REQUEST['Instructions'.$pk]=='Enter special instructions for your order here:')
				$Instructions="";
			else
				$Instructions=mysqli_real_escape_string($con,$_REQUEST['Instructions'.$pk]);

			$Delivery_Address=explode(":",$_REQUEST['DeliveryAddress'.$pk]);
			$Delivery_Email=explode(":",$_REQUEST['Delivery_Email'.$pk]);
			if($Delivery_Address[1]<>'')
				$Delivery_Address=$Delivery_Address[1];
			else
				$Delivery_Address=$_REQUEST['DeliveryAddress'.$pk];
			if($Delivery_Email[1]<>'')
				$Delivery_Email=$Delivery_Email[1];
			else
				$Delivery_Email=$_REQUEST['Delivery_Email'.$pk];
			$CreatedOn=date("Y-m-d, G:i:s");

			if($Cat_ID=='64')
			{
				$SubCat_ID=GetPlaySubcat($ServiceID,$Cat_ID);

				if($SubCat_ID=='86')
				{
					$PointPercentage=GetSubcatPointPercentage($SubCat_ID);
				}
				else
				{
				 $PointPercentage=GetPointPercentage($Cat_ID);
				}
			}
			else
			{
				$PointPercentage=GetPointPercentage($Cat_ID);
			}
			// Points Calculation
			$Points=0;
			//$PointPercentage;
			$Points=round((($result['TotalAmount']*$PointPercentage)/100),2);
			// Get Additional Charges labels
			if($result['Cart_Type']=='Parking')
				$csql1="SELECT Setting_Name,Setting_Value,format FROM tbl_sell_fee WHERE Setting_Value>0 AND Parking_ID=".$result['Cart_ServiceID'];
			else if($result['Cart_Type']=='Activities')
				$csql1="SELECT Setting_Name,Setting_Value,format FROM tbl_sell_fee WHERE Setting_Value>0 AND Activity_ID=".$result['Cart_ServiceID'];
				$csql2="SELECT Setting_Name,Setting_Value,format FROM tbl_categories_settings WHERE Setting_Value>0 AND Cat_ID=".$Cat_ID;
				$csql=$csql1." UNION ".$csql2;
				$cexe=mysqli_query($con,$csql);
				$extfees=0;
				$extlabels="";
			while($cres=mysqli_fetch_array($cexe))
			{
				if($cres['format']=='%')
				{
					$per_amt=$result['Amount']*$cres['Setting_Value']/100;
					$extfees=round($extfees+$per_amt,2);
					$labels=$cres['Setting_Name']."(".$cres['Setting_Value']."%):$".$per_amt.",";
				}
				else
				{
					$per_amt=$cres['Setting_Value'];
					$extfees=round($extfees+$cres['Setting_Value'],2);
					$labels=$cres['Setting_Name'].":$".$per_amt.",";
				}
				if($extfees>=0)
					$extlabels.=$labels;
			}
			$extlabels=trim($extlabels,",");
			if($result['charges']<=0)
				$extlabels="";

			// New Changes
			$TotalAmount=$result['TotalAmount'];
			$charges=$result['charges'];
			if($result['Cart_Type']=='Activities')
			{
				if($result['Shipping_Cost']>0)
				{
					$labels="Shipping Cost ".":$".$result['Shipping_Cost'].",";
					$extlabels.=$labels;
					$charges=$charges+$result['Shipping_Cost'];
					$TotalAmount=$TotalAmount+$result['Shipping_Cost'];
				}
			}

			$extlabels=trim($extlabels,",");
			// End New Changes

			$Query="INSERT INTO tbl_paymenttransaction (UsrID,Owner_ID,Parking_ID,Care_ID,Activity_ID,Ticket_ID,Cat_ID,from_date,to_date,Show_Time,Movie_Name,Club_Number,TxnDate,PaymentSource,quantity,Amount,charges,charges_details,TotalAmount,code,Status,Ticket_Type,Ticket_Quantity,care_payment_type,Parking_type,Card_Type,Card_Name,Card_Number,Card_Expiry_Month,Card_Expiry_Year,Card_CWCode,Card_Zip,Card_Address,Card_State,Card_City,DeliveryMethod,DeliveryState,DeliveryCity,DeliveryZip,DeliveryEmail,DeliveryAddress,PaymentDesc,points)
			VALUES
			('".$User_ID."','".$result['Owner_ID']."','".$Parking_ID."','".$Care_ID."','".$Activity_ID."','".$Ticket_ID."','".$Cat_ID."','".$result['from_date']."','".$result['to_date']."','".mysqli_real_escape_string($con,$result['Show_Time'])."','".mysqli_real_escape_string($con,$result['Movie_Name'])."','".mysqli_real_escape_string($con,$result['Club_Number'])."','".$CreatedOn."','Credit Card','".$result[Cart_Quantity]."','".$result['Amount']."','".$result[charges]."','".mysqli_real_escape_string($con,$extlabels)."','".$result[TotalAmount]."','".$code."','1','".$result[Ticket_Type]."','".$result[Ticket_Quantity]."','".$result[care_payment_type]."','".$result[Parking_type]."','".mysqli_real_escape_string($con,$ResCC['Card_Type'])."','".mysqli_real_escape_string($con,$ResCC['Card_FirstName'])."','".mysqli_real_escape_string($con,$ResCC['Card_Number'])."','".mysqli_real_escape_string($con,$ResCC['Card_Exp_Month'])."','".mysqli_real_escape_string($con,$ResCC['Card_Exp_Year'])."','".mysqli_real_escape_string($con,$ResCC['Card_Verify_Code'])."','".mysqli_real_escape_string($con,$ResCC['Card_Zip'])."','".mysqli_real_escape_string($con,$ResCC['Card_Street'])."','".mysqli_real_escape_string($con,strtoupper($ResCC['Card_State']))."','".mysqli_real_escape_string($con,$ResCC['Card_City'])."','".mysqli_real_escape_string($con,$_REQUEST['DeliveryMethod'.$pk])."','".mysqli_real_escape_string($con,$_REQUEST['State'.$pk])."','".mysqli_real_escape_string($con,$_REQUEST['City'.$pk])."','".mysqli_real_escape_string($con,$_REQUEST['Zip_Code'.$pk])."','".mysqli_real_escape_string($con,$Delivery_Email)."','".mysqli_real_escape_string($con,$Delivery_Address)."','".$Instructions."','".$Points."')";

			$Query="INSERT INTO tbl_paymenttransaction (UsrID,Owner_ID,Parking_ID,Care_ID,Activity_ID,Ticket_ID,Movie_ID,Cat_ID,from_date,to_date,Ticket_Title,Ticket_EventID,Ticket_EventVenue,Ticket_Section,Ticket_Row,Ticket_Owner,Show_Time,Movie_Name,Club_Number,TxnDate,PaymentSource,quantity,Amount,Discount,charges,Overnight_Fee,charges_details,TotalAmount,code,Status,Ticket_Type,Ticket_Quantity,care_payment_type,Parking_type,Card_Type,Card_Name,Card_Number,Card_Expiry_Month,Card_Expiry_Year,Card_Zip,Card_Address,Card_State,Card_City,DeliveryMethod,DeliveryState,DeliveryCity,DeliveryZip,DeliveryEmail,DeliveryAddress,PaymentDesc,points,NetPark_rate,NetPark_daily_rate)
			VALUES
			('".$User_ID."','".$result['Owner_ID']."','".$Parking_ID."','".$Care_ID."','".$Activity_ID."','".$Ticket_ID."','".$Movie_ID."','".$Cat_ID."','".$result['from_date']."','".$result['to_date']."','".mysqli_real_escape_string($con,$result['Ticket_Title'])."','".mysqli_real_escape_string($con,$result['Ticket_EventID'])."','".mysqli_real_escape_string($con,$result['Ticket_EventVenue'])."','".mysqli_real_escape_string($con,$result['Ticket_Section'])."','".mysqli_real_escape_string($con,$result['Ticket_Row'])."','".mysqli_real_escape_string($con,$result['Ticket_Owner'])."','".mysqli_real_escape_string($con,$result['Show_Time'])."','".mysqli_real_escape_string($con,$result['Movie_Name'])."','".mysqli_real_escape_string($con,$result['Club_Number'])."','".$CreatedOn."','Credit Card','".$result[Cart_Quantity]."','".$result['Amount']."','".$result[Discount]."','".$charges."','".$result[Overnight_Fee]."','".mysqli_real_escape_string($con,$extlabels)."','".$TotalAmount."','".$code."','1','".$result[Ticket_Type]."','".$result[Ticket_Quantity]."','".$result[care_payment_type]."','".$result[Parking_type]."','".mysqli_real_escape_string($con,$ResCC['Card_Type'])."','".mysqli_real_escape_string($con,$ResCC['Card_FirstName'])."',AES_ENCRYPT('".$ResCC['CC_NUMBER']."','".$EncryptKey."'),'".mysqli_real_escape_string($con,$ResCC['Card_Exp_Month'])."','".mysqli_real_escape_string($con,$ResCC['Card_Exp_Year'])."','".mysqli_real_escape_string($con,$ResCC['Card_Zip'])."','".mysqli_real_escape_string($con,$ResCC['Card_Street'])."','".mysqli_real_escape_string($con,strtoupper($ResCC['Card_State']))."','".mysqli_real_escape_string($con,$ResCC['Card_City'])."','".mysqli_real_escape_string($con,$_REQUEST['DeliveryMethod'.$pk])."','".mysqli_real_escape_string($con,$_REQUEST['State'.$pk])."','".mysqli_real_escape_string($con,$_REQUEST['City'.$pk])."','".mysqli_real_escape_string($con,$_REQUEST['Zip_Code'.$pk])."','".mysqli_real_escape_string($con,$Delivery_Email)."','".mysqli_real_escape_string($con,$Delivery_Address)."','".$Instructions."','".$Points."','".mysqli_real_escape_string($con,$result['NetPark_rate'])."','".mysqli_real_escape_string($con,$result['NetPark_daily_rate'])."')";
			mysqli_query($con,$Query);

			$Order_Id=mysqli_insert_id($con);
			// For Message
			if($result['Cart_Type']=='Parking')
				$Message_Obj->insertParkingmessage($Buyer_Name,$result['Cart_Type'],$result['Owner_ID'],$result['Cart_ServiceID'],$Order_Id);
			else
				$Message_Obj->insertmessage($Buyer_Name,$result['Cart_Type'],$result['Owner_ID'],$result['Cart_ServiceID'],$Order_Id);
			// Send Order to Ticket Evolution
			if($result['Cart_Type']=='Tickets')
			{
				include("ticketevolution-sendorder.php");
			}
			// For Tickets Quantity
			if($Ticket_ID>0)
			{
				$sq="SELECT Booked_Ticket_Quantity,Available_Ticket_Quantity,Ticket_Quantity FROM tbl_tickets WHERE Ticket_ID=".$Ticket_ID;
				$ex=mysqli_query($con,$sq);
				$re=mysqli_fetch_array($ex);
				$Booked_Ticket_Quantity=$re['Booked_Ticket_Quantity']+$result['Cart_Quantity'];
				$Available_Ticket_Quantity=$re['Available_Ticket_Quantity']-$result['Cart_Quantity'];

				$sq="UPDATE tbl_tickets SET Booked_Ticket_Quantity=".$Booked_Ticket_Quantity.",Available_Ticket_Quantity=".$Available_Ticket_Quantity." WHERE Ticket_ID=".$Ticket_ID;
				mysqli_query($con,$sq);
			}


			// For Miles/Points
			if($PointPercentage=='')
				$PointPercentage=0;
			if($Points=='')
				$Points=0;
			$sql="INSERT INTO tbl_points(P_UserID,P_Transaction,P_Point_Thru,P_points,P_Percentage,P_TransactionAmount,P_Created) VALUES(".$User_ID.",".$Order_Id.",'Payment Transaction','".round($Points,2)."','".round($PointPercentage)."',".$TotalAmount.",Now())";
			mysqli_query($con,$sql);
			// Update Points for Transaction
			$psql="SELECT Points FROM tbl_registeration WHERE id=".$User_ID;
			$pexe=mysqli_query($con,$psql);
			$pres=mysqli_fetch_array($pexe);
			$DBPoints=$pres['Points']+$Points;
			$upsql="UPDATE tbl_registeration SET Points=".$DBPoints." WHERE id=".$User_ID;
			mysqli_query($con,$upsql);
			// End Miles/Points

			if($_REQUEST['DeliveryMethod'.$pk]=='Electronic Confirmation Code' && $voucher_created==0)
			{

				// Create PDF
				if($result['$resCart_Type']=='Activities')
				{

					/* PDF Creation
					include "qrcode.php";
					$pdffilename="WayTicket".$Order_Id.$result['Cart_ServiceID'].strtotime(date("F j, Y, g:i a")).".pdf";
					include_once("html2pdf/about.php");
					$voucher_created=$Order_Id;

					$sql="UPDATE tbl_paymenttransaction SET Voucher_File='".$pdffilename."' WHERE TxnID=".$Order_Id;
					mysqli_query($con,$sql);	End PDF Creation*/
					$filename="WayTicket".$Order_Id.$result['Cart_ServiceID'].strtotime(date("F j, Y, g:i a"));

					$htmlfilename=$filename.".html";
					$pngfilename=$filename.".png";

					//include "qrcode.php";

					//include_once("createhtml.php");

					$voucher_created=$Order_Id;

					$sql="UPDATE tbl_paymenttransaction SET Voucher_File='".$htmlfilename."' WHERE TxnID=".$Order_Id;
					mysqli_query($con,$sql);
				}
			}

			$Order_Ids.=",".$Order_Id;
			$Pay_TotalAmount=$Pay_TotalAmount+$TotalAmount;

			$pk++;

		}

		$Order_Ids=trim($Order_Ids,",");


		$Pay_TotalAmount=$Pay_TotalAmount+$Package_TotalAmount+$Dine_TotalAmount;

		if($Dine_TxnID=='')
			$Dine_TxnID=0;
		if($Order_Ids=='')
			$Order_Ids=0;
		if($PTI_TID=='')
			$PTI_TID=0;
		if($User_ID=='')
			$User_ID=0;

		//--------------- Empty Cart -------------------//
		$queryemp="DELETE FROM tbl_cart where Sess_ID='".$_REQUEST['device_id']."'";
		mysqli_query($con,$queryemp);

		$sql="INSERT INTO tbl_payment(Dine_TxnID,Pay_TxnID,Package_TxnID,UsrID,Pay_TotalAmount,Pay_Created,Pay_Status) VALUES('".$Dine_TxnID."','".$Order_Ids."','".$PTI_TID."','".$User_ID."','".$Pay_TotalAmount."',NOW(),1)";
		mysqli_query($con,$sql);
		$Payment_Id=mysqli_insert_id($con);



		//$Qry="DELETE FROM tbl_cartpackages where Cart_Session='".session_id()."'";
		//mysqli_query($con,$Qry);http://letsnurture.co.uk/demo/dinning/api/payment.php?User_ID=40&payment_method=VISA&Card_Number=hlOEgR8Un+Sk5b0ZLTkDVAOKIjSGIXC4jnT0IvLi6a8=&exp_month=05&exp_date=19&CC_First_Name=Jennelle&Street_Address=Sunnyvale&City=California&State=CA&Zip_Code=94088&pay_type=CreditCard&orderAmount=9.79&DeliveryAddress=Sunnyvale,California&Pending_Amount=&Delivery_Email=jebaraj.h@gmail.com&Package=&Instructions=thanks..&orderdate=2015-07-07&Delivery_address=&orderDineAmount=&MID=211&ordertime=14:45:00&orderfor=Delivery&tax_total=0.79&comments=&quantity=1&discount=

		//Insert First Data values into Database
		if (getenv(HTTP_X_FORWARDED_FOR))
		{
			$visit_ip = getenv(HTTP_X_FORWARDED_FOR);
		}
		else
		{
			$visit_ip = getenv(REMOTE_ADDR);
		}

		if($CalculatedTax=='')
			$CalculatedTax=0;
		if($CalculatedShipping=='')
			$CalculatedShipping=0;
		if($TransactionID=='')
			$TransactionID=0;
		if($TransactionScore=='')
			$TransactionScore=0;
		if($ProessorReferenceNumber=='')
			$ProessorReferenceNumber=0;
		if($ProessorReferenceNumber=='')
			$ProessorReferenceNumber=0;
		$sql="INSERT INTO tbl_firstdatatransaction(TxnID,UserID,Amount,CardType,CardName,CardNumber,Card_Epx_Month,Card_Exp_Year,Card_Address,Card_State,Card_City,Card_Zip,FDT_CommercialServiceProvider,FDT_TransactionTime,FDT_TransactionID,FDT_ProessorReferenceNumber,FDT_ProcessorResponseMessage,FDT_ErrorMessage,FDT_OrderID,FDT_ApprovalCode,FDT_AVSResponse,FDT_TDate,FDT_TransactionResult,FDT_ProcessorApprovalCode,FDT_CalculatedTax,FDT_CalculatedShipping,FDT_TransactionScore,FDT_AuthenticationResponseCode,Created_On,Ip_Address) VALUES('".$Payment_Id."','".$User_ID."','".$_REQUEST['orderAmount']."','".mysqli_real_escape_string($con,$ResCC['Card_Type'])."','".mysqli_real_escape_string($con,$ResCC['Card_FirstName'])."',AES_ENCRYPT('".$ResCC['CC_NUMBER']."','".$EncryptKey."'),'".mysqli_real_escape_string($con,$ResCC['Card_Exp_Month'])."','".mysqli_real_escape_string($con,$ResCC['Card_Exp_Year'])."','".mysqli_real_escape_string($con,$ResCC['Card_Street'])."','".mysqli_real_escape_string($con,$ResCC['Card_State'])."','".mysqli_real_escape_string($con,$ResCC['Card_City'])."','".mysqli_real_escape_string($con,$ResCC['Card_Zip'])."','".$CommercialServiceProvider."','".$TransactionTime."','".$TransactionID."','".$ProessorReferenceNumber."','".$ProcessorResponseMessage."','".$ErrorMessage."','".$OrderID."','".$ApprovalCode."','".$AVSResponse."','".$TDate."','".$TransactionResult."','".$ProcessorApprovalCode."','".$CalculatedTax."','".$CalculatedShipping."','".$TransactionScore."','".$AuthenticationResponseCode."',Now(),'".$visit_ip."')";

		mysqli_query($con,$sql);

		$fp_quantity=$rec['fp_quantity']-$_REQUEST['quantity'];
		if($_REQUEST['pay_type']=='Cash')
		$resp=1;
		else
			$resp=$TransactionResult;
		if($TransactionResult=='APPROVED')
			$rep=1;

		//print "<script language=javascript>window.location='thankyou.php?rep=".$rep."&resp=".$TransactionResult."&ac=".$_REQUEST["ac"]."&id=".$_REQUEST["id"]."&TxnID=".$Payment_Id."&PTxnID=".$PTI_TID."&err=".$ErrorMessage."';</script>";
        $content=array("status"=>"1","message"=>"success","rep"=>$rep,"resp"=>$TransactionResult,"TxnID"=>$Payment_Id,"PTxnID"=>$PTI_TID,"err"=>$ErrorMessage);
        echo json_encode($content);
		exit;
	}
	else
	{
		//Insert First Data values into Database
		if (getenv(HTTP_X_FORWARDED_FOR)) {
		$visit_ip = getenv(HTTP_X_FORWARDED_FOR);
		} else {
			$visit_ip = getenv(REMOTE_ADDR);
		}
		if($CalculatedTax=='')
			$CalculatedTax=0;
		if($CalculatedShipping=='')
			$CalculatedShipping=0;
		if($OrderID=='')
			$OrderID=0;
		if($Payment_Id=='')
			$Payment_Id=0;
		$sql="INSERT INTO tbl_firstdatatransaction(TxnID,UserID,Amount,CardType,CardName,CardNumber,Card_Epx_Month,Card_Exp_Year,Card_Address,Card_State,Card_City,Card_Zip,FDT_CommercialServiceProvider,FDT_TransactionTime,FDT_TransactionID,FDT_ProessorReferenceNumber,FDT_ProcessorResponseMessage,FDT_ErrorMessage,FDT_OrderID,FDT_ApprovalCode,FDT_AVSResponse,FDT_TDate,FDT_TransactionResult,FDT_ProcessorApprovalCode,FDT_CalculatedTax,FDT_CalculatedShipping,FDT_TransactionScore,FDT_AuthenticationResponseCode,Created_On,Ip_Address) VALUES('".$Payment_Id."','".$User_ID."','".$_REQUEST['orderAmount']."','".mysqli_real_escape_string($con,$ResCC['Card_Type'])."','".mysqli_real_escape_string($con,$ResCC['Card_FirstName'])."',AES_ENCRYPT('".$ResCC['CC_NUMBER']."','".$EncryptKey."'),'".mysqli_real_escape_string($con,$ResCC['Card_Exp_Month'])."','".mysqli_real_escape_string($con,$ResCC['Card_Exp_Year'])."','".mysqli_real_escape_string($con,$ResCC['Card_Street'])."','".mysqli_real_escape_string($con,$ResCC['Card_State'])."','".mysqli_real_escape_string($con,$ResCC['Card_City'])."','".mysqli_real_escape_string($con,$ResCC['Card_Zip'])."','".$CommercialServiceProvider."','".$TransactionTime."','".$TransactionID."','".$ProessorReferenceNumber."','".$ProcessorResponseMessage."','".$ErrorMessage."','".$OrderID."','".$ApprovalCode."','".$AVSResponse."','".$TDate."','".$TransactionResult."','".$ProcessorApprovalCode."','".$CalculatedTax."','".$CalculatedShipping."','".$TransactionScore."','".$AuthenticationResponseCode."',Now(),'".$visit_ip."')";
		mysqli_query($con,$sql);
		//print "<script language=javascript>window.location='thankyou.php?rep=0&resp=".$TransactionResult."&ac=".$_REQUEST["ac"]."&id=".$_REQUEST["id"]."&err=".$ErrorMessage."';</script>";
	    $content=array("status"=>0,"message"=>"Declined","rep"=>$rep,"resp"=>$TransactionResult,"err"=>$ErrorMessage);
	    echo json_encode($content);
		exit;
	}
}

?>
