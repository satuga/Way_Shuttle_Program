<?php
ini_set('memory_limit','1256M');
error_reporting(0);
header('Content-Type: application/json');
include ('../config.php');
include ('../function.php');
include '../encrypt/security.php';
//include("../message_class.php");


$user_id = sanitize($_REQUEST['user_id']);

$CC_First_Name = sanitize($_REQUEST['CC_First_Name']);
$Street_Address = sanitize($_REQUEST['Street_Address']);
$City = sanitize($_REQUEST['City']);
$State = sanitize($_REQUEST['State']);
$Zip_Code = sanitize($_REQUEST['Zip_Code']);
$Phone =sanitize( $_REQUEST['Phone']);               ///---- do not send
$BLDG_No = sanitize($_REQUEST['BLDG_No']);           ///---- do not send

$pay_type = sanitize($_REQUEST['pay_type']);

$Card_Number1 = sanitize($_REQUEST["Card_Number"]);
$Card_Number=Security::decrypt($Card_Number1,CARDENCRYPT);

$payment_method = sanitize($_REQUEST['payment_method']);
$exp_month =sanitize( $_REQUEST['exp_month']);
$exp_date = sanitize($_REQUEST['exp_date']);
$cardcvv=sanitize($_REQUEST['ver_code']);


$orderAmount = sanitize($_REQUEST['orderAmount']);
$Pending_Amount =sanitize( $_REQUEST['Pending_Amount']);

$DeliveryAddress1 = sanitize($_REQUEST['DeliveryAddress']);

$device_id = sanitize($_REQUEST['device_id']);
$quantity = sanitize($_REQUEST['quantity']);

$rep=0;
$PTI_TID=0;
$Package_TotalAmount=0;

$EncryptKey = ENCRYPTKEY;
$adminmail = ADMINMAIL;


//--------------Payment related function--------------//
function getTransactionResult($result)
{
    $varPos = strpos($result, '<fdggwsapi:TransactionResult>');
    $varPos2 = strpos($result, '</fdggwsapi:TransactionResult>');
    if($varPos !== false)
    {
        $varPos = $varPos + 29;
        $varLen = $varPos2 - $varPos;
        return substr($result,$varPos,$varLen);
    }
    else
    {
        return 'FAILED';
    }
}

function genRandomString() {
    $length = 7;
    $characters = "0123456789";
    $string = "";
    for ($p = 0; $p < $length; $p++) {
        $string .= $characters[mt_rand(0, strlen($characters))];
    }
    return $string;
}

function fetch_Cust_fulldetails($user_id)
{
	$sql = "select street,city,state,zipcode from tbl_registeration where id=".$user_id;
	$res = @mysqli_query($con,$sql);
	$row = @mysqli_fetch_array($res);
	if($row['street']<>'')
		$Address=$row['street'];
	if($row['city']<>'')
		$Address.=','.$row['city'];
	if($row['state']<>'')
		$Address.=','.$row['state'];
	if($row['zipcode']<>'')
		$Address.=','.$row['zipcode'];
	$Address=trim($Address,",");
	return $Address;
}

function GetDeliveryAddress($User_ID)
{
	$sql="SELECT DE_Address,DE_City,DE_State,DE_Zipcode FROM tbl_deliveryaddress WHERE DE_Address<>'' AND DE_City<>'' AND DE_State<>'' AND DE_UserID=".$User_ID." ORDER BY DE_ID DESC LIMIT 1";
	$exe=mysqli_query($con,$sql);
	$res=mysqli_fetch_array($exe);
	$DeliveryAddress=$res['DE_Address'].",".$res['DE_City'].",".$res['DE_State'];
	if($res['DE_Zipcode']<>'')
		$DeliveryAddress.=",".$res['DE_Zipcode'];
	return $DeliveryAddress;
}

//--------------XML to ARRAY conversion function --------------//
function xml2array($contents, $get_attributes=1, $priority = 'tag')
{
	if(!$contents)
		return array();

	if(!function_exists('xml_parser_create'))
	{
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
	foreach($xml_values as $data)
	{
		unset($attributes,$value);//Remove existing values, or there will be trouble

		//This command will extract these variables into the foreach scope
		// tag(string), type(string), level(int), attributes(array).
		extract($data);//We could use the array by itself, but this is cooler.

		$result = array();
		$attributes_data = array();

		if(isset($value))
		{
			if($priority == 'tag')
				$result = $value;
			else
				$result['value'] = $value; //Put the value in a assoc array if we are in the 'Attribute' mode
		}

		//Set the attributes too.
		if(isset($attributes) and $get_attributes)
		{
			foreach($attributes as $attr => $val)
			{
				if($priority == 'tag')
					$attributes_data[$attr] = $val;
				else
					$result['attr'][$attr] = $val; //Set all the attributes in a array called 'attr'
			}
		}

		//See tag status and do the needed.
		if($type == "open")
		{
			//The starting of the tag '<tag>'
			$parent[$level-1] = &$current;
			if(!is_array($current) or (!in_array($tag, array_keys($current))))
			{
				//Insert New tag
				$current[$tag] = $result;
				if($attributes_data)
				{
					$current[$tag. '_attr'] = $attributes_data;
					$repeated_tag_index[$tag.'_'.$level] = 1;
					$current = &$current[$tag];

				}
				else
				{
					//There was another element with the same tag name
					if(isset($current[$tag][0]))
					{//If there is a 0th element it is already an array
						$current[$tag][$repeated_tag_index[$tag.'_'.$level]] = $result;
						$repeated_tag_index[$tag.'_'.$level]++;
					}
					else
					{//This section will make the value an array if multiple tags with the same name appear together
						$current[$tag] = array($current[$tag],$result);//This will combine the existing item and the new item together to make an array
						$repeated_tag_index[$tag.'_'.$level] = 2;
					}

					if(isset($current[$tag.'_attr']))
					{ //The attribute of the last(0th) tag must be moved as well
						$current[$tag]['0_attr'] = $current[$tag.'_attr'];
						unset($current[$tag.'_attr']);
					}

				}

				$last_item_index = $repeated_tag_index[$tag.'_'.$level]-1;
				$current = &$current[$tag][$last_item_index];
			}
		}
		elseif($type == "complete")
		{
			//Tags that ends in 1 line '<tag />'
			//See if the key is already taken.
			if(!isset($current[$tag]))
			{ //New Key
				$current[$tag] = $result;
				$repeated_tag_index[$tag.'_'.$level] = 1;
				if($priority == 'tag' and $attributes_data)
					$current[$tag. '_attr'] = $attributes_data;
			}
			else
			{ //If taken, put all things inside a list(array)
				if(isset($current[$tag][0]) and is_array($current[$tag]))
				{//If it is already an array...

					// ...push the new element into that array.
					$current[$tag][$repeated_tag_index[$tag.'_'.$level]] = $result;

					if($priority == 'tag' and $get_attributes and $attributes_data)
					{
						$current[$tag][$repeated_tag_index[$tag.'_'.$level] . '_attr'] = $attributes_data;
					}
					$repeated_tag_index[$tag.'_'.$level]++;

				}
				else
				{
					//If it is not an array...
					$current[$tag] = array($current[$tag],$result); //...Make it an array using using the existing value and the new value
					$repeated_tag_index[$tag.'_'.$level] = 1;
					if($priority == 'tag' and $get_attributes)
					{
						if(isset($current[$tag.'_attr']))
						{ //The attribute of the last(0th) tag must be moved as well

							$current[$tag]['0_attr'] = $current[$tag.'_attr'];
							unset($current[$tag.'_attr']);
						}

						if($attributes_data)
						{
							$current[$tag][$repeated_tag_index[$tag.'_'.$level] . '_attr'] = $attributes_data;
						}
					}
					$repeated_tag_index[$tag.'_'.$level]++; //0 and 1 index is already taken
				}
			}

		}
		elseif($type == 'close')
		{ //End of tag '</tag>'
			$current = &$parent[$level-1];
		}
	}

	return($xml_array);
}


if ($user_id !="" && $device_id != '')
{
	$rsql="SELECT id,firstname,street,city,state,zipcode,mobile_phone FROM tbl_registeration WHERE id=".$user_id;
	$rexe=mysqli_query($con,$rsql);
	$rres=mysqli_fetch_array($rexe);
	if($rres['mobile_phone']=='' || $rres['firstname']=='' || $rres['street']=='' || $rres['city']=='' || $rres['state']=='' || $rres['zipcode']=='')
	{
		$rsql="UPDATE tbl_registeration SET firstname='".mysqli_real_escape_string($con,$CC_First_Name)."',street='".mysqli_real_escape_string($con,$Street_Address)."',city='".mysqli_real_escape_string($con,$City)."',state='".mysqli_real_escape_string($con,$State)."',zipcode='".mysqli_real_escape_string($con,$Zip_Code)."',mobile_phone='".mysqli_real_escape_string($con,$Phone)."',BLDG_No='".mysqli_real_escape_string($con,$BLDG_No)."' WHERE id=".$user_id;
		mysqli_query($con,$rsql);
	}

	//----------Get the Credit card details ----------//
	$sqlCC="select *,AES_DECRYPT(Card_Number,'".$EncryptKey."') AS CC_NUMBER from tbl_creditcarddetails where Card_Number=AES_ENCRYPT(".$Card_Number.",'".$EncryptKey."') AND Card_User_ID=".$user_id;
	$exeCC=mysqli_query($con,$sqlCC);
	$ResCCNum=mysqli_num_rows($exeCC);


	if($ResCCNum<1)
	{
		$sql="INSERT INTO tbl_creditcarddetails(Card_User_ID,Card_Type,Card_Number,Card_Exp_Month,Card_Exp_Year,Card_FirstName,Card_Street,Card_City,Card_State,Card_Zip,Card_Created)VALUES(".$user_id.",'".mysqli_real_escape_string($con,$payment_method)."',AES_ENCRYPT('".mysqli_real_escape_string($con,$Card_Number)."','".$EncryptKey."'),'".mysqli_real_escape_string($con,$exp_month)."','".mysqli_real_escape_string($con,$exp_date)."','".mysqli_real_escape_string($con,$CC_First_Name)."','".mysqli_real_escape_string($con,$Street_Address)."','".mysqli_real_escape_string($con,$City)."','".mysqli_real_escape_string($con,$State)."','".mysqli_real_escape_string($con,$Zip_Code)."',Now())";
		//$exeCC=mysqli_query($con,$sql);
		$Card_ID=mysqli_insert_id($con);
	}
	else
	{
		$ResCC=mysqli_fetch_array($exeCC);
		$Card_ID=$ResCC['Card_ID'];
		$sql="UPDATE tbl_creditcarddetails SET Card_Type='".mysqli_real_escape_string($con,$payment_method)."',Card_Number=AES_ENCRYPT('".mysqli_real_escape_string($con,$Card_Number)."','".$EncryptKey."'),Card_Exp_Month='".mysqli_real_escape_string($con,$exp_month)."',Card_Exp_Year='".mysqli_real_escape_string($con,$exp_date)."',Card_FirstName='".mysqli_real_escape_string($con,$CC_First_Name)."',Card_Street='".mysqli_real_escape_string($con,$Street_Address)."',Card_State='".mysqli_real_escape_string($con,$State)."',Card_City='".mysqli_real_escape_string($con,$City)."',Card_Zip='".mysqli_real_escape_string($con,$Zip_Code)."' WHERE Card_ID=".$Card_ID;
		//mysqli_query($con,$sql);
	}

	//------ Get your Credit card details------------//
	$sqlCC="select *,AES_DECRYPT(Card_Number,'".$EncryptKey."') AS CC_NUMBER from tbl_creditcarddetails where Card_ID=".$Card_ID;
	$exeCC=mysqli_query($con,$sqlCC);
	$ResCC=mysqli_fetch_array($exeCC);


	if($pay_type=='CreditCard')
	{
		//-------------- First Data Transaction Code-----------//

		//---------- Firstdata Payment Integration-----------------
		$wsdl = "https://ws.firstdataglobalgateway.com/fdggwsapi/services/order.wsdl";              	// PROD WSDL
		//$wsdl = "https://ws.merchanttest.firstdataglobalgateway.com/fdggwsapi/services/order.wsdl";   // CTE WSDL - Note lines 74 and 75 regarding CTE

		$userid = "";                      						// "Username" from WS000000._.1.auth.txt
		$password = "";                              			// "Password" from WS000000._.1.auth.txt
		$pemlocation = realpath("fdcode/WS1001310340._.1.pem"); // Location of "WS000000._.1.pem"
		$kslocation = realpath("fdcode/WS1001310340._.1.key");  // Location of "WS000000._.1.key"
		$keyname = "ckp_1414348609";                  			// From WS000000._.1.key.pw.txt

		//  AVS Verification
		$transactiontype = "sale";
		$creditcardnumber = $ResCC['CC_NUMBER'];
		$cardexpirationmonth = $ResCC['Card_Exp_Month'];
		$cardexpirationyear = substr($ResCC['Card_Exp_Year'],(strlen($ResCC['Card_Exp_Year'])-2),2);
		$orderAmounttest=0;

		$chargetotal=preg_replace('#[^0-9.]+#','',(number_format(($orderAmounttest),2)));
		// Billing Address
		$Billing_Name=mysqli_real_escape_string($con,$CC_First_Name);
		$Billing_Address=mysqli_real_escape_string($con,$Street_Address);
		$Billing_City=mysqli_real_escape_string($con,$City);
		$Billing_State=mysqli_real_escape_string($con,$State);
		$Billing_Zip=mysqli_real_escape_string($con,$Zip_Code);


		$body = "<SOAP-ENV:Envelope xmlns:SOAP-ENV=\"http://schemas.xmlsoap.org/soap/envelope/\"><SOAP-ENV:Header/><SOAP-ENV:Body><ns4:FDGGWSApiOrderRequest xmlns:ns2=\"http://secure.linkpt.net/fdggwsapi/schemas_us/v1\" xmlns:ns3=\"http://secure.linkpt.net/fdggwsapi/schemas_us/a1\" xmlns:ns4=\"http://secure.linkpt.net/fdggwsapi/schemas_us/fdggwsapi\"><ns2:Transaction><ns2:CreditCardTxType><ns2:Type>";
		$body .= $transactiontype;  				//  Transaction Type
		$body .= "</ns2:Type></ns2:CreditCardTxType>";
		$body .= "<ns2:CreditCardData>";
		$body .= "<ns2:CardNumber>";
		$body .= $creditcardnumber;  				//  Credit Card Number
		$body .= "</ns2:CardNumber>";
		$body .= "<ns2:ExpMonth>";
		$body .= $cardexpirationmonth;  			//  Card Expiration Month
		$body .= "</ns2:ExpMonth><ns2:ExpYear>";
		$body .= $cardexpirationyear;  				//  Card Expiration Year
		$body .= "</ns2:ExpYear>";
		$body .= "<ns2:CardCodeValue>";
		$body .= $cardcvv;  						//  CVV
		$body .= "</ns2:CardCodeValue>";
		$body .= "<ns2:CardCodeIndicator>PROVIDED</ns2:CardCodeIndicator>";
		$body .= "</ns2:CreditCardData><ns2:Payment><ns2:ChargeTotal>";
		$body .= $chargetotal;  					//  Charge Total
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

		$result = curl_exec($ch);

		// closing cURL:
		curl_close($ch);

		$array = xml2array($result);

		//print_r($array);

		//print("<br><br>$result");
		//print "<br><br>";
		//exit;
		// This is a way you can extract data from the $array sent back to you.


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

		/*$message="output000: \n\n<br><br>";
		$message.="CommercialServiceProvider: " . $CommercialServiceProvider . "\n<br>";
		$message.="TransactionTime: " . $TransactionTime. "\n<br>";
		$message.="TransactionID: " . $TransactionID. "\n<br>";
		$message.="ProessorReferenceNumber: " . $ProessorReferenceNumber. "\n<br>";
		$message.="ProcessorResponseMessage: " . $ProcessorResponseMessage. "\n<br>";
		$message.="ErrorMessage: " . $ErrorMessage. "\n<br>";
		$message.="OrderID: " . $OrderID. "\n<br>";
		$message.="ApprovalCode: " . $ApprovalCode. "\n<br>";
		$message.="AVSResponse: " . $AVSResponse. "\n<br>";
		$message.="TDate: " . $TDate. "\n<br>";
		$message.="TransactionResult: " . $TransactionResult. "\n<br>";
		$message.="ProessorResponseCode: " . $ProessorResponseCode. "\n<br>";
		$message.="ProcessorApprovalCode: " . $ProcessorApprovalCode. "\n<br>";
		$message.="CalculatedTax: " . $CalculatedTax. "\n<br>";
		$message.="CalculatedShipping: " . $CalculatedShipping. "\n<br>";
		$message.="TransactionScore: " . $TransactionScore. "\n<br>";
		$message.="AuthenticationResponseCode: " . $AuthenticationResponseCode. "\n<br>";

		GLOBAL $adminmail;
		$headers = "From: ".$adminmail."\r\n" .
		   'Reply-To: '.$adminmail."\r\n" .
				   'X-Mailer: PHP/' . phpversion();
		$headers .= 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		mail('jebaraj.h@gmail.com', 'Payment Info', $message, $headers);*/
		//NYZM- only zip code matches

		if($AVSResponse<>'YYYM' && $AVSResponse<>'YYAM' && $AVSResponse<>'YYDM' && $AVSResponse<>'YYFM' && $AVSResponse<>'YYMM')
		{
			$Payment_Id1=0;
			$orderAmount1=preg_replace('#[^0-9.]+#','',($orderAmount));
			$Pending_Amount1=preg_replace('#[^0-9.]+#','',($Pending_Amount));
			$chargetotal1=preg_replace('#[^0-9.]+#','',(number_format(($orderAmount1-$Pending_Amount1),2)));
			if($AVSResponse=='NYZM' && $chargetotal1<=250)
			{

			}
			else
			{
				$visit_ip = getenv(REMOTE_ADDR);
				$sql="INSERT INTO tbl_firstdatatransaction(TxnID,UserID,Amount,CardType,CardName,CardNumber,Card_Epx_Month,Card_Exp_Year,Card_Address,Card_State,Card_City,Card_Zip,FDT_CommercialServiceProvider,FDT_TransactionTime,FDT_TransactionID,FDT_ProessorReferenceNumber,FDT_ProcessorResponseMessage,FDT_ErrorMessage,FDT_OrderID,FDT_ApprovalCode,FDT_AVSResponse,FDT_TDate,FDT_TransactionResult,FDT_ProcessorApprovalCode,FDT_CalculatedTax,FDT_CalculatedShipping,FDT_TransactionScore,FDT_AuthenticationResponseCode,Created_On,Ip_Address) VALUES('".$Payment_Id1."','".$User_ID."','".$chargetotal1."','".mysqli_real_escape_string($con,$ResCC['Card_Type'])."','".mysqli_real_escape_string($con,$ResCC['Card_FirstName'])."',AES_ENCRYPT('".$ResCC['CC_NUMBER']."','".$EncryptKey."'),'".mysqli_real_escape_string($con,$ResCC['Card_Exp_Month'])."','".mysqli_real_escape_string($con,$ResCC['Card_Exp_Year'])."','".mysqli_real_escape_string($con,$ResCC['Card_Street'])."','".mysqli_real_escape_string($con,$ResCC['Card_State'])."','".mysqli_real_escape_string($con,$ResCC['Card_City'])."','".mysqli_real_escape_string($con,$ResCC['Card_Zip'])."','".$CommercialServiceProvider."','".$TransactionTime."','".$TransactionID."','".$ProessorReferenceNumber."','".$ProcessorResponseMessage."','".$ErrorMessage."','".$OrderID."','".$ApprovalCode."','".$AVSResponse."','".$TDate."','".$TransactionResult."','".$ProcessorApprovalCode."','".$CalculatedTax."','".$CalculatedShipping."','".$TransactionScore."','".$AuthenticationResponseCode."',Now(),'".$visit_ip."')";
				mysqli_query($con,$sql);
				print "<script language=javascript>window.location='checkout.php?rep=0&resp=".$TransactionResult."&AVS=".$AVSResponse."&Card_ID=".$Card_ID."&ErrorMessage=".$ErrorMessage."';</script>";
				exit;
			}
		}
	}


	$TransactionResult=getTransactionResult($result);
	$TransactionResult = "APPROVED";
	$TransactionID = '123456';

	if($ErrorMessage=='' || $ErrorMessage=='Array')
	{
		$ErrorMessage=$TransactionResult;
	}
	//if($TransactionResult!='')
	//if($TransactionResult=='APPROVED' && $TransactionID>0)
	//if($TransactionResult=='APPROVED' && $TransactionID>0)

	if($TransactionResult=='APPROVED' && $TransactionID>0)
	{
		$rep=1;
		// User details
		$sql2="select * from tbl_registeration where id=".$user_id;
		$exe2=mysqli_query($con,$sql2);
		$res2=mysqli_fetch_array($exe2);
		$Buyer_Name=$res2['firstname']." ".$res2['lastname'];

		$rand=genRandomString();
		$code=$rand;

		$Order_Ids="";
		$Pay_TotalAmount=0;
		$Dine_TotalAmount=0;
		$voucher_created=0;
		$Pay_TxnID=0;

		$Query="SELECT * from tbl_cart where Sess_ID='".$device_id."' AND Cart_Type!='Dine'";
		$res = mysqli_query($con,$Query);
		$pk=1;

		while ($result=mysqli_fetch_array($res))
		{
			$ServiceID=$result['Cart_ServiceID'];

			if($result['Cart_Type']=='Parking')
			{
				$Cat_ID=45;
				$Parking_ID=$result['Cart_ServiceID'];
				$Ticket_ID=0;
				$Care_ID=0;
				$Activity_ID=0;
			}

			if(sanitize($_REQUEST['Instructions'.$pk])=='Enter special instructions for your order here:')
				$Instructions="";
			else
				$Instructions=mysqli_real_escape_string($con,sanitize($_REQUEST['Instructions'.$pk]));


			$Delivery_Address=explode(":",sanitize($_REQUEST['DeliveryAddress'.$pk]));
			$Delivery_Email=explode(":",sanitize($_REQUEST['Delivery_Email'.$pk]));

			if($Delivery_Address[1]<>'')
				$Delivery_Address=$Delivery_Address[1];
			else
				$Delivery_Address=sanitize($_REQUEST['DeliveryAddress'.$pk]);
			$Delivery_Address=trim($Delivery_Address);

			if($Delivery_Address=='' || $Delivery_Address==',' || $Delivery_Address==', , ,' || $Delivery_Address==', ,')
			{
				$Delivery_Address=GetDeliveryAddress($user_id);
			}
			$Delivery_Address=trim($Delivery_Address);

			if($Delivery_Address=='' || $Delivery_Address==',' || $Delivery_Address==', , ,' || $Delivery_Address==', ,')
			{
				$Delivery_Address=fetch_Cust_fulldetails($User_ID);
			}

			if($Delivery_Email[1]<>'')
				$Delivery_Email=$Delivery_Email[1];
			else
				$Delivery_Email=sanitize($_REQUEST['Delivery_Email'.$pk]);
			$CreatedOn=date("Y-m-d, G:i:s");


			//--------- Points Calculation ------------//
			$Points=0;
			$resvenue = @mysqli_fetch_array(mysqli_query($con,"SELECT Point_Percentage FROM tbl_categories WHERE Cat_ID=".$Cat_ID));
	        $PointPercentage = $resvenue['Point_Percentage'];
			$Points=round((($result['TotalAmount']*$PointPercentage)/100),2);

			//--------- Get Additional Charges labels -------//
			$csql1="SELECT Setting_Name,Setting_Value,format FROM tbl_sell_fee WHERE Setting_Value>0 AND status=1 AND Parking_ID=".$result['Cart_ServiceID'];
			$csql2="SELECT Setting_Name,Setting_Value,format FROM tbl_categories_settings WHERE Setting_Value>0 AND status=1 AND Cat_ID=".$Cat_ID;

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
			$extlabels=trim($extlabels,",");

			$Query="INSERT INTO tbl_paymenttransaction (UsrID,Owner_ID,Parking_ID,Care_ID,Activity_ID,Ticket_ID,Movie_ID,Cat_ID,from_date,to_date,Ticket_Title,Ticket_EventID,RetailAmount,TotalRetailAmount,Ticket_EventVenue,Ticket_Section,Ticket_Row,Ticket_Owner,Show_Time,Movie_Name,Club_Number,TxnDate,PaymentSource,quantity,Amount,Discount,charges,Overnight_Fee,Way_Fee,charges_details,TotalAmount,Payat_Lot,Payment_Type,code,Status,Ticket_Type,Ticket_Quantity,care_payment_type,Parking_type,Card_Type,Card_Name,Card_Number,Card_Expiry_Month,Card_Expiry_Year,Card_Zip,Card_Address,Card_State,Card_City,DeliveryMethod,DeliveryState,DeliveryCity,DeliveryZip,DeliveryEmail,DeliveryAddress,PaymentDesc,points,NetPark_rate,NetPark_daily_rate,Instant_MovieTicket,Theater_Chain,Movie_Tickets,Amount_Code,Parking_Event_PE_ID)
			VALUES ('".$user_id."','".$result['Owner_ID']."','".$Parking_ID."','".$Care_ID."','".$Activity_ID."','".$Ticket_ID."','".$Movie_ID."','".$Cat_ID."','".$result['from_date']."','".$result['to_date']."','".mysqli_real_escape_string($con,$result['Ticket_Title'])."','".mysqli_real_escape_string($con,$result['Ticket_EventID'])."','".mysqli_real_escape_string($con,$result['RetailAmount'])."','".mysqli_real_escape_string($con,$result['TotalRetailAmount'])."','".mysqli_real_escape_string($con,$result['Ticket_EventVenue'])."','".mysqli_real_escape_string($con,$result['Ticket_Section'])."','".mysqli_real_escape_string($con,$result['Ticket_Row'])."','".mysqli_real_escape_string($con,$result['Ticket_Owner'])."','".mysqli_real_escape_string($con,$result['Show_Time'])."','".mysqli_real_escape_string($con,$result['Movie_Name'])."','".mysqli_real_escape_string($con,$result['Club_Number'])."','".$CreatedOn."','Credit Card','".$result[Cart_Quantity]."','".$result['Amount']."','".$result['Discount']."','".$charges."','".$result[Overnight_Fee]."','".$result['Way_Fee']."','".mysqli_real_escape_string($con,$extlabels)."','".$TotalAmount."','".$result['Payat_Lot']."','".$result['Payment_Type']."','".$code."','1','".$result[Ticket_Type]."','".$result[Ticket_Quantity]."','".$result['care_payment_type']."','".$result[Parking_type]."','".mysqli_real_escape_string($con,$ResCC['Card_Type'])."','".mysqli_real_escape_string($con,$ResCC['Card_FirstName'])."',AES_ENCRYPT('".$ResCC['CC_NUMBER']."','".$EncryptKey."'),'".mysqli_real_escape_string($con,$ResCC['Card_Exp_Month'])."','".mysqli_real_escape_string($con,$ResCC['Card_Exp_Year'])."','".mysqli_real_escape_string($con,$ResCC['Card_Zip'])."','".mysqli_real_escape_string($con,$ResCC['Card_Street'])."','".mysqli_real_escape_string($con,strtoupper($ResCC['Card_State']))."','".mysqli_real_escape_string($con,$ResCC['Card_City'])."','".mysqli_real_escape_string($con,sanitize($_REQUEST['DeliveryMethod'.$pk]))."','".mysqli_real_escape_string($con,sanitize($_REQUEST['State'.$pk]))."','".mysqli_real_escape_string($con,sanitize($_REQUEST['City'.$pk]))."','".mysqli_real_escape_string($con,sanitize($_REQUEST['Zip_Code'.$pk]))."','".mysqli_real_escape_string($con,$Delivery_Email)."','".mysqli_real_escape_string($con,$Delivery_Address)."','".$Instructions."','".$Points."','".mysqli_real_escape_string($con,$result['NetPark_rate'])."','".mysqli_real_escape_string($con,$result['NetPark_daily_rate'])."','".$result['Instant_MovieTicket']."','".$result['Theater_Chain']."','".$result['Movie_Tickets']."',AES_ENCRYPT('".$cardcvv."','".$EncryptKey."'),'".$result['Parking_Event_PE_ID']."')";
			//mysqli_query($con,$Query);
			//$Order_Id=mysqli_insert_id($con);

			if($result['Cart_Type']=='Parking')
			{
				//$Message_Obj->insertParkingmessage($Buyer_Name,$result['Cart_Type'],$result['Owner_ID'],$result['Cart_ServiceID'],$Order_Id);
			}



			// For Miles/Points
			if($PointPercentage=='')
				$PointPercentage=0;
			if($Points=='')
				$Points=0;

			$sql="INSERT INTO tbl_points(P_UserID,P_Transaction,P_Point_Thru,P_points,P_Percentage,P_TransactionAmount,P_Created) VALUES(".$user_id.",".$Order_Id.",'Payment Transaction','".round($Points,2)."','".round($PointPercentage)."','".$TotalAmount."',Now())";
			//mysqli_query($con,$sql);

			// Update Points for Transaction
			$psql="SELECT Points FROM tbl_registeration WHERE id=".$user_id;
			$pexe=mysqli_query($con,$psql);
			$pres=mysqli_fetch_array($pexe);
			$DBPoints=$pres['Points']+$Points;
			$upsql="UPDATE tbl_registeration SET Points=".$DBPoints." WHERE id=".$user_id;
			//mysqli_query($con,$upsql);

			$Order_Ids.=",".$Order_Id;
			$Pay_TotalAmount=$Pay_TotalAmount+$TotalAmount;


			$pk++;
		}

		$Order_Ids=trim($Order_Ids,",");
		$Pay_TotalAmount=$Pay_TotalAmount+$Package_TotalAmount+$Dine_TotalAmount;

		// ------ After payment success Empty Cart-----------//
		$queryemp="DELETE FROM tbl_cart where Sess_ID='".session_id()."'";
		//mysqli_query($con,$queryemp);


		if($Dine_TxnID=='')
			$Dine_TxnID=0;
		if($Order_Ids=='')
			$Order_Ids=0;
		if($PTI_TID=='')
			$PTI_TID=0;
		if($user_id=='')
			$user_id=0;

		$sql="INSERT INTO tbl_payment(Dine_TxnID,Pay_TxnID,Package_TxnID,UsrID,Pay_TotalAmount,Pay_Created,Pay_Status) VALUES('".$Dine_TxnID."','".$Order_Ids."','".$PTI_TID."','".$User_ID."','".$Pay_TotalAmount."',NOW(),1)";
		//mysqli_query($con,$sql);
		$Payment_Id=mysqli_insert_id($con);

		// -------------inserting the values to the firstdata to database ----------//
		if (getenv(HTTP_X_FORWARDED_FOR)) {
			$visit_ip = getenv(HTTP_X_FORWARDED_FOR);
		} else {
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
		$sql="INSERT INTO tbl_firstdatatransaction(TxnID,UserID,Amount,CardType,CardName,CardNumber,Card_Epx_Month,Card_Exp_Year,Card_Address,Card_State,Card_City,Card_Zip,FDT_CommercialServiceProvider,FDT_TransactionTime,FDT_TransactionID,FDT_ProessorReferenceNumber,FDT_ProcessorResponseMessage,FDT_ErrorMessage,FDT_OrderID,FDT_ApprovalCode,FDT_AVSResponse,FDT_TDate,FDT_TransactionResult,FDT_ProcessorApprovalCode,FDT_CalculatedTax,FDT_CalculatedShipping,FDT_TransactionScore,FDT_AuthenticationResponseCode,Created_On,Ip_Address) VALUES('".$Payment_Id."','".$User_ID."','".sanitize($_REQUEST['orderAmount'])."','".mysqli_real_escape_string($con,$ResCC['Card_Type'])."','".mysqli_real_escape_string($con,$ResCC['Card_FirstName'])."',AES_ENCRYPT('".$ResCC['CC_NUMBER']."','".$EncryptKey."'),'".mysqli_real_escape_string($con,$ResCC['Card_Exp_Month'])."','".mysqli_real_escape_string($con,$ResCC['Card_Exp_Year'])."','".mysqli_real_escape_string($con,$ResCC['Card_Street'])."','".mysqli_real_escape_string($con,$ResCC['Card_State'])."','".mysqli_real_escape_string($con,$ResCC['Card_City'])."','".mysqli_real_escape_string($con,$ResCC['Card_Zip'])."','".$CommercialServiceProvider."','".$TransactionTime."','".$TransactionID."','".$ProessorReferenceNumber."','".$ProcessorResponseMessage."','".$ErrorMessage."','".$OrderID."','".$ApprovalCode."','".$AVSResponse."','".$TDate."','".$TransactionResult."','".$ProcessorApprovalCode."','".$CalculatedTax."','".$CalculatedShipping."','".$TransactionScore."','".$AuthenticationResponseCode."',Now(),'".$visit_ip."')";
		//mysqli_query($con,$sql);


		if($TransactionResult=='APPROVED')
		{
			$rep=1;
		}

        $content=array("status"=>1,"message"=>"success","rep"=>$rep,"resp"=>$TransactionResult,"TxnID"=>$Payment_Id,"PTxnID"=>$PTI_TID,"err"=>$ErrorMessage);
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
		$sql="INSERT INTO tbl_firstdatatransaction(TxnID,UserID,Amount,CardType,CardName,CardNumber,Card_Epx_Month,Card_Exp_Year,Card_Address,Card_State,Card_City,Card_Zip,FDT_CommercialServiceProvider,FDT_TransactionTime,FDT_TransactionID,FDT_ProessorReferenceNumber,FDT_ProcessorResponseMessage,FDT_ErrorMessage,FDT_OrderID,FDT_ApprovalCode,FDT_AVSResponse,FDT_TDate,FDT_TransactionResult,FDT_ProcessorApprovalCode,FDT_CalculatedTax,FDT_CalculatedShipping,FDT_TransactionScore,FDT_AuthenticationResponseCode,Created_On,Ip_Address) VALUES('".$Payment_Id."','".$User_ID."','".$orderAmount."','".mysqli_real_escape_string($con,$ResCC['Card_Type'])."','".mysqli_real_escape_string($con,$ResCC['Card_FirstName'])."',AES_ENCRYPT('".$ResCC['CC_NUMBER']."','".$EncryptKey."'),'".mysqli_real_escape_string($con,$ResCC['Card_Exp_Month'])."','".mysqli_real_escape_string($con,$ResCC['Card_Exp_Year'])."','".mysqli_real_escape_string($con,$ResCC['Card_Street'])."','".mysqli_real_escape_string($con,$ResCC['Card_State'])."','".mysqli_real_escape_string($con,$ResCC['Card_City'])."','".mysqli_real_escape_string($con,$ResCC['Card_Zip'])."','".$CommercialServiceProvider."','".$TransactionTime."','".$TransactionID."','".$ProessorReferenceNumber."','".$ProcessorResponseMessage."','".$ErrorMessage."','".$OrderID."','".$ApprovalCode."','".$AVSResponse."','".$TDate."','".$TransactionResult."','".$ProcessorApprovalCode."','".$CalculatedTax."','".$CalculatedShipping."','".$TransactionScore."','".$AuthenticationResponseCode."',Now(),'".$visit_ip."')";
		//mysqli_query($con,$sql);

		$content=array("status"=>0,"message"=>"error","rep"=>$rep,"resp"=>$TransactionResult,"err"=>$ErrorMessage);
		echo json_encode($content);
		exit;
	}

}
else
{
	$content = array("status" => 0,"message" => "Please Pass Correct Parameters");
    echo json_encode($content);
    exit;
}
echo "dddd";
exit;

?>
