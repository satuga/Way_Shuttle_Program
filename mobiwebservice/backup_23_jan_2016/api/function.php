<?php

function checksum()
{
    $length = 30;
    $characters = "0123456789abcdefghijklmnopqrstuvwxyz";
    $string = "";
    for ($p = 0; $p < $length; $p++)
    {
        $string .= $characters[mt_rand(0, strlen($characters))];
    }
    $checksum_register = $string;
    return $checksum_register;
}

function SubItemdetails($gid)
{
	$sql = @mysql_query("select subitemName,subitemPrice from subitems where id=".$gid);
	$res = mysql_fetch_array($sql);
	return array($res['subitemName'],$res['subitemPrice']);
}

function Sendmail($email,$id,$chk_sum)
{
    	//	GLOBAL $adminmail,$Host_Path,$reply_mail;
        $Host_Path='http://letsnurture.co.uk/demo/dinning/';
        $id=$id;
        $checksum_register=$chk_sum;
        $reply_mail='support@way.com';
				$reguser=explode("@",$email);
				if($display_name=="")
					$display_name=$reguser[0];
				$Subject="Welcome to Way.com, ".$display_name."! ";
				 $message='<table width="564" border="0" align="center" cellpadding="0" cellspacing="0" >
				  <tr>
					<td  valign="top" scope="col"><table width="100%" border="0" cellspacing="0" cellpadding="0">
					 <tr>
						<td height="5" scope="col">&nbsp;</td>
					  </tr>
					  <tr>
						<td scope="col" align="left"><font style="font-family:Arial, Helvetica, sans-serif; font-size:16px; font-weight:bold; color:#6a6a6a;">Thank You For Registering And Welcome To Way.com!</font></td>
					  </tr>
					  <tr>
						<td height="5" scope="col">&nbsp;</td>
					  </tr>
					  <tr>
						<td valign="top" scope="col"><table width="98%" border="0" align="center" cellpadding="0" cellspacing="0">
						<tr>
							<td height="26" scope="col"><div align="left"><font style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold; color:#6a6a6a;">Hello</font><font style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold; color:#be3917;">&nbsp;&nbsp;'.$display_name.'</font>
							</div></td>
						  </tr>
						  <tr><td height="10" scope="col"></td></tr>
						 
						   <tr>
							<td height="19" scope="col" align="center">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold; color:#6a6a6a;"><a href="'.$Host_Path.'confirm.php?id='.$id.'&checksum_register='.$checksum_register.'" style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold; color:#0b9fd8;">Click here to activate account</font></td>
						  </tr>

						  <tr><td height="20" scope="col"></td></tr>
						  <tr>
							<td height="19" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#6a6a6a;">Connect with Way.com! Follow Way.com on Twitter, Facebook and the Way.com Blog.</font></td>
						  </tr>
						  <tr><td height="10" scope="col"></td></tr>
						  <tr>
							<td height="19" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#6a6a6a;"><b>Not you?</b></font></td>
						  </tr>
						  <tr><td height="10" scope="col"></td></tr>
						  <tr>
							<td height="19" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#6a6a6a;text-decoration:none">If you did not recently update your email address on <a href="'.$Host_Path.'">Way.com</a>, please let us know by forwarding this email to mail@way.com. </font></td>
						  </tr>
						  <tr><td height="20" scope="col"></td></tr>
						  <tr>
							<td height="19" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#6a6a6a;">This is an account-related message. Responses to this email will not go to a customer service representative.</font></td>
						  </tr>
						  
						  <tr>
							<td height="19" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#6a6a6a;">To contact our Customer Care team directly, please visit the help section of our website.</font></td>
						  </tr>
						 
						   <tr>
						<td height="19" scope="col">&nbsp;</td>
					  </tr>
						</table>
								 </td>
					  </tr>
					  <tr><td height="2" scope="col"></td></tr>
					</table></td>
				  </tr>
				</table>';
				$headers = "From: ".$reply_mail."\r\n" .
						   'Reply-To: '.$reply_mail."\r\n" .
						   'X-Mailer: PHP/' . phpversion();
				$headers .= 'MIME-Version: 1.0' . "\r\n";
				$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
				include ("template/email-template.php"); 
			$template1=str_replace('DETAILS',$message."",$template);
			$message=$template1;
			mail($email, $Subject, $message, $headers);
			
}
function getMerchantTaxRate($rest)
{
	$rescity = @mysql_fetch_array(mysql_query("select tax from merchant where id=".$rest));
	return $rescity['tax'];
}
function GetRestaurantname($rest)
{
	$rescity = @mysql_fetch_array(mysql_query("select merchantName from merchant where id=".$rest));
	return $rescity['merchantName'];
}
function GetRestaurantCity($rest)
{
	$rescity = @mysql_fetch_array(mysql_query("select city from merchant where id=".$rest));
	return $rescity['city'];
}
function genRandomString() 
{
    $length = 7;
    $characters = "0123456789";
    $string = "";    
    for ($p = 0; $p < $length; $p++) 
	{
        $string .= $characters[mt_rand(0, strlen($characters))];
    }
    return $string;
}
function mail_attachmentnew($filename, $path, $mailto, $from_mail, $from_name, $replyto, $subject, $message) {
	$fileatt = $path.$filename; // Path to the file
   
	$fileatt_type = "application/html"; // File Type 
	$fileatt_name = $filename; // Filename that will be used for the file as the attachment

	$email_from = "support@way.com"; // Who the email is from 
	$email_subject = $subject; // The Subject of the email 
	$email_message = $message;
	 // Message that the email has in it

	$email_to = $mailto; // Who the email is to

	$headers = "From: ".$email_from;

	$file = fopen($fileatt,'rb'); 
	$data = fread($file,filesize($fileatt)); 
	fclose($file);

	$semi_rand = md5(time()); 
	$mime_boundary = "==Multipart_Boundary_x{$semi_rand}x"; 

	$headers .= "\nMIME-Version: 1.0\n" . 
	"Content-Type: multipart/mixed;\n" . 
	" boundary=\"{$mime_boundary}\";Return-Path: support@way.com\n";

	$email_message .= "This is a multi-part message in MIME format.\n\n" . 
	"--{$mime_boundary}\n" . 
	"Content-Type:text/html; charset=\"iso-8859-1\"\n" . 
	"Content-Transfer-Encoding: 7bit\n\n" . 
	$email_message .= "\n\n";

	$data = chunk_split(base64_encode($data));

	$email_message .= "--{$mime_boundary}\n" . 
	"Content-Type: {$fileatt_type};\n" . 
	" name=\"{$fileatt_name}\"\n" . 
	"Content-Disposition: attachment;\n" . 
	" filename=\"{$fileatt_name}\"\n" . 
	"Content-Transfer-Encoding: base64\n\n" . 
	$data .= "\n\n" . 
	"--{$mime_boundary}--\n";

	//$ok = @mail($email_to, $email_subject, $email_message, $headers);
	$ok = @mail($email_to, '', $email_message, $headers);

	if($ok) { 
		return '1';
	} else { 
	return '0';
	}
}
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
function get_rand_id($length)
{
  if($length>0) 
  { 
  $rand_id="";
   for($i=1; $i<=$length; $i++)
   {
   mt_srand((double)microtime() * 1000000);
   $num = mt_rand(1,36);
   $rand_id .= assign_rand_value($num);
   }
  }
return $rand_id;
}

function assign_rand_value($num)
{
// accepts 1 - 36
  switch($num)
  {
    case "1":
     $rand_value = "a";
    break;
    case "2":
     $rand_value = "b";
    break;
    case "3":
     $rand_value = "c";
    break;
    case "4":
     $rand_value = "d";
    break;
    case "5":
     $rand_value = "e";
    break;
    case "6":
     $rand_value = "f";
    break;
    case "7":
     $rand_value = "g";
    break;
    case "8":
     $rand_value = "h";
    break;
    case "9":
     $rand_value = "i";
    break;
    case "10":
     $rand_value = "j";
    break;
    case "11":
     $rand_value = "k";
    break;
    case "12":
     $rand_value = "l";
    break;
    case "13":
     $rand_value = "m";
    break;
    case "14":
     $rand_value = "n";
    break;
    case "15":
     $rand_value = "o";
    break;
    case "16":
     $rand_value = "p";
    break;
    case "17":
     $rand_value = "q";
    break;
    case "18":
     $rand_value = "r";
    break;
    case "19":
     $rand_value = "s";
    break;
    case "20":
     $rand_value = "t";
    break;
    case "21":
     $rand_value = "u";
    break;
    case "22":
     $rand_value = "v";
    break;
    case "23":
     $rand_value = "w";
    break;
    case "24":
     $rand_value = "x";
    break;
    case "25":
     $rand_value = "y";
    break;
    case "26":
     $rand_value = "z";
    break;
    case "27":
     $rand_value = "0";
    break;
    case "28":
     $rand_value = "1";
    break;
    case "29":
     $rand_value = "2";
    break;
    case "30":
     $rand_value = "3";
    break;
    case "31":
     $rand_value = "4";
    break;
    case "32":
     $rand_value = "5";
    break;
    case "33":
     $rand_value = "6";
    break;
    case "34":
     $rand_value = "7";
    break;
    case "35":
     $rand_value = "8";
    break;
    case "36":
     $rand_value = "9";
    break;
  }
return $rand_value;
}

function SubGroupname($gid)
{
	$sql = @mysql_query("select subgroup_name from subgroups where id=".$gid);
	$res = mysql_fetch_array($sql);
	return $res['subgroup_name'];
}


function getMerchantTodaysOpenTimes($merchantID, $time = "", $orderType = "", $day = "") {
	
	if($day<>'')
	{
	$day=$day;
	}
	else
	{
	if($time == "") {
		$day = date("w");
		$currTime = date("H:i:s");
	} else {
		$day = date("w", strtotime($time));
		$currTime = date("H:i:s",strtotime($time));
	}
	}
		
	$sql="SELECT * FROM merchant_hours WHERE merchantID=".$merchantID." AND weekDay=".$day;
	$exe=mysql_query($sql);
	$num=mysql_num_rows($exe);
	$row=mysql_fetch_array($exe);
	/*$ci =& get_instance();
	$ci->db->where("merchantID", $merchantID);
	$ci->db->where("weekDay", $day);
	$query = $ci->db->get("merchant_hours");*/

      // $row = $query->row(0);

	if($num>0) {
            return $row['startTime']." to ".$row['endTime']." and ".$row['startTimeOther']." to ".$row['endTimeOther'];
        }

        return "";
}


function RestaurantStatus($MID,$time='')
	{
		GLOBAL $Time_Zone;
		if($time == "") {
		$day = date("w");
		$currTime = date("H:i:s");
		} else {
			$day = date("w", strtotime($time));
			$currTime = date("H:i:s",strtotime($time));
		}
		
		// get Merchant Info 
		$msql="SELECT * FROM merchant WHERE id=".$MID;
		$mexe=mysql_query($msql) or die(mysql_error());
		$merchantInfo=mysql_fetch_array($mexe);
		
		$currentTime = strtotime(date("F j, Y, g:i a"));

		$diff = $merchantInfo['timezone'] - $Time_Zone;               
		$currentTime += ($diff * 60 * 60);

		$sql="SELECT * FROM merchant_hours WHERE merchantID=".$MID." AND weekDay=".$day;
		$exe=mysql_query($sql) or die(mysql_error());
		$num=mysql_num_rows($exe);
		if($num>0)
		{
			$row=mysql_fetch_array($exe);
			if($row['24hours'] == "Yes")
					return "Open";
			else if($row['closed'] == "Yes")
			return "Close";
            else if( isset($row['startTime']) && isset($row['endTime']) && ((strtotime($row['endTime']) >= strtotime($row['startTime']) && $currentTime >= strtotime($row['startTime']) && $currentTime<= strtotime($row['endTime'])) || (strtotime($row['endTime']) < strtotime($row['startTime']) && $currentTime >= strtotime($row['startTime']) && $currentTime<= (strtotime($row['endTime']))))) {
			return "Open";
                    } else if(isset($row['startTimeOther']) && isset($row['endTimeOther']) && (($row['endTimeOther'] >= $row['startTimeOther'] && $currentTime >= strtotime($row['startTimeOther']) && $currentTime<= strtotime($row['endTimeOther'])) || ($row['endTimeOther'] < $row['startTimeOther'] && $currentTime >= strtotime($row['startTimeOther']) && $currentTime<= (strtotime($row['endTimeOther']))))) {
			return "Open";
                    }
					else {
					return 'Close';
					}
		}
		else
		{
			return 'Close';
		}
		
		return 'Close';
	}
function GetOpenORClose($merchantID,$day)
{
	$sql="SELECT closed FROM merchant_hours WHERE merchantID=".$merchantID." AND weekDay=".$day;
	$exe=mysql_query($sql) or die(mysql_error());
	$row=mysql_fetch_array($exe);
	return $row['closed'];
}
function GetServiceTitle($Service_ID,$cat)
{
	//echo $cat;
	if($cat==45) {
		$resvenue = @mysql_fetch_array(mysql_query("select tbl_parkinglocations.Park_Address AS Title,tbl_parkinglocations.Park_Name from tbl_parking INNER JOIN tbl_parkinglocations ON tbl_parkinglocations.Park_ID=tbl_parking.P_Location where P_ID=".$Service_ID)); }
	else if($cat==64) {
		$sql="select tbl_activities.Act_Title AS Title,Act_Cat_ID,tbl_activitycompany.C_CompanyName from tbl_activities INNER JOIN tbl_activitycompany ON tbl_activitycompany.C_ID=tbl_activities.Act_CompanyID where Act_ID=".$Service_ID;
		$resvenue = @mysql_fetch_array(mysql_query($sql)); }
	else if($cat==61) {
		$resvenue = @mysql_fetch_array(mysql_query("SELECT Title AS Title FROM tbl_category_care where ID=".$Service_ID));}
	else if($cat==75) {
	$sql="SELECT M_MovieName AS Title FROM tbl_movies where M_ID=".$Service_ID;
		$resvenue = @mysql_fetch_array(mysql_query($sql));}
	else if($cat==72) {
		$resvenue = @mysql_fetch_array(mysql_query("SELECT merchantName AS Title FROM merchant where id=".$Service_ID));}
	else if($cat==71 || $cat=='Tickets') {
		$sql="SELECT tevoevents.eventName AS Title FROM tevoevents where eventId=".$Service_ID;
		$resvenue = @mysql_fetch_array(mysql_query($sql));}
	else if($cat=='Package') {
		$resvenue = @mysql_fetch_array(mysql_query("SELECT Package_Name AS Title FROM tbl_package where Package_ID=".$Service_ID));}
	/*if($cat==64 && $resvenue['Act_Cat_ID']==86)
		return array ($resvenue['C_CompanyName']);
	else*/
		if($resvenue['Park_Name']<>'')
		return array ($resvenue['Park_Name']);
		else
		return $resvenue['Title'];
}
function getrestaurantinfo($Service_ID,$cat)
{
	$resvenue = @mysql_fetch_array(mysql_query("SELECT id as R_Id,logo,contactAddress as address,telephone as phone FROM merchant where id=".$Service_ID));
	return $resvenue;
}
function sanitize($input)
{
    if (is_array($input)) 
    {
        foreach($input as $var=>$val) 
        {
            $output[$var] = sanitize($val);
        }
    }
    else 
    {
        if (get_magic_quotes_gpc()) 
        {
            $input = stripslashes($input);
        }
        $input  = cleanInput($input);
        $output = mysql_real_escape_string($input);
    }
    return $output;
}

function cleanInput($input) 
{
  $search = array(
    '@<script[^>]*?>.*?</script>@si',   // Strip out javascript
    '@<[\/\!]*?[^<>]*?>@si',            // Strip out HTML tags
    '@<style[^>]*?>.*?</style>@siU',    // Strip style tags properly
    '@<![\s\S]*?--[ \t\n\r]*>@'         // Strip multi-line comments
  );

    $output = preg_replace($search, '', $input);
    return $output;
}
function GetPointPercentage($cat)
{
	$resvenue = @mysql_fetch_array(mysql_query("SELECT Point_Percentage FROM tbl_categories WHERE Cat_ID=".$cat));
	return ($resvenue['Point_Percentage']);
}
function getDriverinfo($order_id)
{
	$driver_info = @mysql_fetch_array(mysql_query("SELECT d.driver_id,u.firstname,u.lastname,u.mobile_phone FROM driver_accept_ride as d left join tbl_registeration as u ON u.id = d.driver_id where d.order_id = ".$order_id));
	return $driver_info;
}

/*------------ sending android notification on driver pickup and deliverd ------------*/
function android_notification($device_token,$message)
{
		if(is_array($device_token))
		{
				$registrationIds=$device_token;
		}
		else{	
				$registrationIds=explode(',',$device_token);
		}

		$msg = array
		(
			'message' 		=> $message,
			'pushType' => $check,
			'title'			=> 'This is a title. title',
			'subtitle'		=> 'This is a subtitle. subtitle',
			'tickerText'	=> 'hellloo',
			'vibrate'	=> 1,
			'sound'		=> 0
		);
		 
		$fields = array
		(
			'registration_ids' 	=> $registrationIds,
			'data'				=> $msg
		);
		 
		$headers = array
		(
			'Authorization: key=AIzaSyDCACagLTloP91ZGTC493mVKZ5tkxngk2c',
			'Content-Type: application/json'
		);
		 
		$ch = curl_init();
		curl_setopt( $ch,CURLOPT_URL, 'https://android.googleapis.com/gcm/send' );
		curl_setopt( $ch,CURLOPT_POST, true );
		curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
		curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
		$result = curl_exec($ch );
		curl_close( $ch );
		//echo "<pre>";
		//echo $result;
		//exit;
		//echo '1   ';
}

function IOS_notification($deviceToken,$message)
{
    	// Put your private key's passphrase here:
		$passphrase = 'Letsdoit@123';
		$path = $_SERVER['DOCUMENT_ROOT'].'/demo/dinning/';
		
		//$deviceToken = 'd39d26c2abea5cfd8fd186f2ac7f1d62a2f2a905127baee399e9fe1a2207a5b9';
		if (file_exists($path.'ck.pem') )
		{
		  
			$ctx = stream_context_create();
			stream_context_set_option($ctx, 'ssl', 'local_cert',$path.'ck.pem');
			stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);
		
			// Open a connection to the APNS server
			$fp = stream_socket_client(
				'ssl://gateway.sandbox.push.apple.com:2195', $err,
				$errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);

			if (!$fp)
				exit("Failed to connect: $err $errstr" . PHP_EOL);

		    //	echo 'Connected to APNS' . PHP_EOL;
            
			// Create the payload body
			$body['aps'] = array(
				'alert' => $message,
			    'pushType'=>$check,
				'sound' => 'default'
				);

			// Encode the payload as JSON
			$payload = json_encode($body);

			// Build the binary notification
			$msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;

			// Send it to the server
			$result = fwrite($fp, $msg, strlen($msg));

			/*if (!$result)
				echo 'Message not delivered' . PHP_EOL;
			else
				echo 'Message successfully delivered' . PHP_EOL;
			*/

			// Close the connection to the server
			fclose($fp);
			//exit;
		}
		else
		{
			echo 'ck.pem file does not exist';
		}
}
?>