<?php

Global $con;

function checksum() {
    $length = 30;
    $characters = "0123456789abcdefghijklmnopqrstuvwxyz";
    $string = "";
    for ($p = 0; $p < $length; $p++) {
        $string .= $characters[mt_rand(0, strlen($characters))];
    }
    $checksum_register = $string;
    return $checksum_register;
}

function SubItemdetails($gid) {
    Global $con;
    $sql = @mysqli_query($con, "select subitemName,subitemPrice from subitems where id=" . $gid);
    $res = mysqli_fetch_array($sql);
    return array($res['subitemName'], $res['subitemPrice']);
}

function Sendmail($email, $id, $chk_sum) {
    //	GLOBAL $adminmail,$Host_Path,$reply_mail;
    $Host_Path = 'https://www.way.com/responsive/mobiwebservice/api/';
    $id = $id;
    $checksum_register = $chk_sum;
    $reply_mail = 'support@way.com';
    $reguser = explode("@", $email);
    if ($display_name == "")
        $display_name = $reguser[0];
    $Subject = "Welcome to Way.com, " . $display_name . "! ";
    $message = '<table width="564" border="0" align="center" cellpadding="0" cellspacing="0" >
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
							<td height="26" scope="col"><div align="left"><font style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold; color:#6a6a6a;">Hello</font><font style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold; color:#be3917;">&nbsp;&nbsp;' . $display_name . '</font>
							</div></td>
						  </tr>
						  <tr><td height="10" scope="col"></td></tr>

						   <tr>
							<td height="19" scope="col" align="center">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold; color:#6a6a6a;"><a href="' . $Host_Path . 'confirm.php?id=' . $id . '&checksum_register=' . $checksum_register . '" style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold; color:#0b9fd8;">Click here to activate account</font></td>
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
							<td height="19" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#6a6a6a;text-decoration:none">If you did not recently update your email address on <a href="' . $Host_Path . '">Way.com</a>, please let us know by forwarding this email to mail@way.com. </font></td>
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
    $headers = "From: " . $reply_mail . "\r\n" .
            'Reply-To: ' . $reply_mail . "\r\n" .
            'X-Mailer: PHP/' . phpversion();
    $headers .= 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
    include ("template/email-template.php");
    $template1 = str_replace('DETAILS', $message . "", $template);
    $message = $template1;
    mail($email, $Subject, $message, $headers);
}

function getMerchantTaxRate($rest) {
    Global $con;
    $rescity = @mysqli_fetch_array(mysqli_query($con, "select tax from merchant where id=" . $rest));
    return $rescity['tax'];
}

function GetRestaurantname($rest) {
    Global $con;
    $rescity = @mysqli_fetch_array(mysqli_query($con, "select merchantName from merchant where id=" . $rest));
    return $rescity['merchantName'];
}

function GetRestaurantCity($rest) {
    Global $con;
    $rescity = @mysqli_fetch_array(mysqli_query($con, "select city from merchant where id=" . $rest));
    return $rescity['city'];
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

function mail_attachmentnew($filename, $path, $mailto, $from_mail, $from_name, $replyto, $subject, $message) {
    $fileatt = $path . $filename; // Path to the file

    $fileatt_type = "application/html"; // File Type
    $fileatt_name = $filename; // Filename that will be used for the file as the attachment

    $email_from = "support@way.com"; // Who the email is from
    $email_subject = $subject; // The Subject of the email
    $email_message = $message;
    // Message that the email has in it

    $email_to = $mailto; // Who the email is to

    $headers = "From: " . $email_from;

    $file = fopen($fileatt, 'rb');
    $data = fread($file, filesize($fileatt));
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

    if ($ok) {
        return '1';
    } else {
        return '0';
    }
}

function getTransactionResult($result) {
    $varPos = strpos($result, '<fdggwsapi:TransactionResult>');
    $varPos2 = strpos($result, '</fdggwsapi:TransactionResult>');
    if ($varPos !== false) {
        $varPos = $varPos + 29;
        $varLen = $varPos2 - $varPos;
        return substr($result, $varPos, $varLen);
    } else {
        return 'FAILED';
    }
}

function get_rand_id($length) {
    if ($length > 0) {
        $rand_id = "";
        for ($i = 1; $i <= $length; $i++) {
            mt_srand((double) microtime() * 1000000);
            $num = mt_rand(1, 36);
            $rand_id .= assign_rand_value($num);
        }
    }
    return $rand_id;
}

function assign_rand_value($num) {
    // accepts 1 - 36
    switch ($num) {
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

function SubGroupname($gid) {
    Global $con;
    $sql = mysqli_query($con, "select subgroup_name from subgroups where id=" . $gid);
    //var_dump($sql); die;
    $res = mysqli_fetch_array($sql);

    return $res['subgroup_name'];
}

function getMerchantTodaysOpenTimes($merchantID, $time = "", $orderType = "", $day = "") {
    Global $con;
    if ($day <> '') {
        $day = $day;
    } else {
        if ($time == "") {
            $day = date("w");
            $currTime = date("H:i:s");
        } else {
            $day = date("w", strtotime($time));
            $currTime = date("H:i:s", strtotime($time));
        }
    }

    $sql = "SELECT * FROM merchant_hours WHERE merchantID=" . $merchantID . " AND weekDay=" . $day;
    $exe = mysqli_query($con, $sql);
    $num = mysqli_num_rows($exe);
    $row = mysqli_fetch_array($exe);
    /* $ci =& get_instance();
      $ci->db->where("merchantID", $merchantID);
      $ci->db->where("weekDay", $day);
      $query = $ci->db->get("merchant_hours"); */

    // $row = $query->row(0);

    if ($num > 0) {
        return $row['startTime'] . " to " . $row['endTime'] . " and " . $row['startTimeOther'] . " to " . $row['endTimeOther'];
    }

    return "";
}

function RestaurantStatus($MID, $time = '') {
    GLOBAL $Time_Zone, $con;
    if ($time == "") {
        $day = date("w");
        $currTime = date("H:i:s");
    } else {
        $day = date("w", strtotime($time));
        $currTime = date("H:i:s", strtotime($time));
    }

    // get Merchant Info
    $msql = "SELECT * FROM merchant WHERE id=" . $MID;
    $mexe = mysqli_query($con, $msql) or die(mysqli_error($con));
    $merchantInfo = mysqli_fetch_array($mexe);

    $currentTime = strtotime(date("F j, Y, g:i a"));

    $diff = $merchantInfo['timezone'] - $Time_Zone;
    $currentTime += ($diff * 60 * 60);

    $sql = "SELECT * FROM merchant_hours WHERE merchantID=" . $MID . " AND weekDay=" . $day;
    $exe = mysqli_query($con, $sql) or die(mysqli_error($con));
    $num = mysqli_num_rows($exe);
    if ($num > 0) {
        $row = mysqli_fetch_array($exe);
        if ($row['24hours'] == "Yes")
            return "Open";
        else if ($row['closed'] == "Yes")
            return "Close";
        else if (isset($row['startTime']) && isset($row['endTime']) && ((strtotime($row['endTime']) >= strtotime($row['startTime']) && $currentTime >= strtotime($row['startTime']) && $currentTime <= strtotime($row['endTime'])) || (strtotime($row['endTime']) < strtotime($row['startTime']) && $currentTime >= strtotime($row['startTime']) && $currentTime <= (strtotime($row['endTime']))))) {
            return "Open";
        } else if (isset($row['startTimeOther']) && isset($row['endTimeOther']) && (($row['endTimeOther'] >= $row['startTimeOther'] && $currentTime >= strtotime($row['startTimeOther']) && $currentTime <= strtotime($row['endTimeOther'])) || ($row['endTimeOther'] < $row['startTimeOther'] && $currentTime >= strtotime($row['startTimeOther']) && $currentTime <= (strtotime($row['endTimeOther']))))) {
            return "Open";
        } else {
            return 'Close';
        }
    } else {
        return 'Close';
    }

    return 'Close';
}

function GetOpenORClose($merchantID, $day) {
    GLOBAL $con;
    $sql = "SELECT closed FROM merchant_hours WHERE merchantID=" . $merchantID . " AND weekDay=" . $day;
    $exe = mysqli_query($con, $sql) or die(mysqli_error($con));
    $row = mysqli_fetch_array($exe);
    return $row['closed'];
}

function GetServiceTitle($Service_ID, $cat) {
    GLOBAL $con;
    //echo $cat; die;
    if ($cat == 45) {
        $resvenue = @mysqli_fetch_array(mysqli_query($con, "select tbl_parkinglocations.Park_Address AS Title,tbl_parkinglocations.Park_Name from tbl_parking INNER JOIN tbl_parkinglocations ON tbl_parkinglocations.Park_ID=tbl_parking.P_Location where P_ID=" . $Service_ID));
        //echo "select tbl_parkinglocations.Park_Address AS Title,tbl_parkinglocations.Park_Name from tbl_parking INNER JOIN tbl_parkinglocations ON tbl_parkinglocations.Park_ID=tbl_parking.P_Location where P_ID=".$Service_ID;die;
    } else if ($cat == 64) {
        $sql = "select tbl_activities.Act_Title AS Title,Act_Cat_ID,tbl_activitycompany.C_CompanyName from tbl_activities INNER JOIN tbl_activitycompany ON tbl_activitycompany.C_ID=tbl_activities.Act_CompanyID where Act_ID=" . $Service_ID;
        $resvenue = @mysqli_fetch_array(mysqli_query($con, $sql));
    } else if ($cat == 61) {
        $resvenue = @mysqli_fetch_array(mysqli_query($con, "SELECT Title AS Title FROM tbl_category_care where ID=" . $Service_ID));
    } else if ($cat == 75) {
        $sql = "SELECT M_MovieName AS Title FROM tbl_movies where M_ID=" . $Service_ID;
        $resvenue = @mysqli_fetch_array(mysqli_query($con, $sql));
    } else if ($cat == 72) {
        $resvenue = @mysqli_fetch_array(mysqli_query($con, "SELECT merchantName AS Title FROM merchant where id=" . $Service_ID));
    } else if ($cat == 71 || $cat == 'Tickets') {
        $sql = "SELECT tevoevents.eventName AS Title FROM tevoevents where eventId=" . $Service_ID;
        $resvenue = @mysqli_fetch_array(mysqli_query($con, $sql));
    } else if ($cat == 'Package') {
        $resvenue = @mysqli_fetch_array(mysqli_query($con, "SELECT Package_Name AS Title FROM tbl_package where Package_ID=" . $Service_ID));
    }
    /* if($cat==64 && $resvenue['Act_Cat_ID']==86)
      return array ($resvenue['C_CompanyName']);
      else */
    if ($resvenue['Park_Name'] <> '') {
        return array($resvenue['Park_Name'], $resvenue['Title']); //patch due to  odd DB structure
        //var_dump($testArray);
    } else {
        return $resvenue['Title'];
    }
}

function getrestaurantinfo($Service_ID, $cat) {
    GLOBAL $con;
    $resvenue = @mysqli_fetch_array(mysqli_query($con, "SELECT id as R_Id,logo,geoLong,geoLat,contactAddress as address,telephone as phone, merchantName AS title FROM merchant where id=" . $Service_ID));
    return $resvenue;
}

function sanitize($input) {
    GLOBAL $con;
    if (is_array($input)) {
        foreach ($input as $var => $val) {
            $output[$var] = sanitize($val);
        }
    } else {
        if (get_magic_quotes_gpc()) {
            $input = stripslashes($input);
        }
        $input = cleanInput($input);
        $output = mysqli_real_escape_string($con, $input);
    }
    return $output;
}

function cleanInput($input) {
    $search = array(
        '@<script[^>]*?>.*?</script>@si', // Strip out javascript
        '@<[\/\!]*?[^<>]*?>@si', // Strip out HTML tags
        '@<style[^>]*?>.*?</style>@siU', // Strip style tags properly
        '@<![\s\S]*?--[ \t\n\r]*>@'         // Strip multi-line comments
    );

    $output = preg_replace($search, '', $input);
    return $output;
}

function GetPointPercentage($cat) {
    GLOBAL $con;
    $resvenue = @mysqli_fetch_array(mysqli_query($con, "SELECT Point_Percentage FROM tbl_categories WHERE Cat_ID=" . $cat));
    return ($resvenue['Point_Percentage']);
}

function getDriverinfo($order_id) {
    Global $con;
    $driver_info = @mysqli_fetch_array(mysqli_query($con, "SELECT d.driver_id,u.firstname,u.lastname,u.mobile_phone FROM driver_accept_ride as d left join tbl_registeration as u ON u.id = d.driver_id where d.order_id = " . $order_id));
    return $driver_info;
}

function isDriver($userid) {
    Global $con;
    $driver_info = @mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM `tbl_shuttle_drivers` WHERE DVR_UserID = '" . $userid . "'"));
    return $driver_info;
}

/* ------------ sending android notification on driver pickup and deliverd ------------ */

function android_notification($device_token, $message) {
    if (is_array($device_token)) {
        $registrationIds = $device_token;
    } else {
        $registrationIds = explode(',', $device_token);
    }

    $msg = array
        (
        'message' => $message,
        'pushType' => $check,
        'title' => 'This is a title. title',
        'subtitle' => 'This is a subtitle. subtitle',
        'tickerText' => 'hellloo',
        'vibrate' => 1,
        'sound' => 0
    );

    $fields = array
        (
        'registration_ids' => $registrationIds,
        'data' => $msg
    );

    $headers = array
        (
        'Authorization: key=AIzaSyDCACagLTloP91ZGTC493mVKZ5tkxngk2c',
        'Content-Type: application/json'
    );

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://android.googleapis.com/gcm/send');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
    $result = curl_exec($ch);
    curl_close($ch);
    //echo "<pre>";
    //echo $result;
    //exit;
    //echo '1   ';
}

function IOS_notification($deviceToken, $message) {
    // Put your private key's passphrase here:
    $passphrase = 'pushchat';
    $path = $_SERVER['DOCUMENT_ROOT'] . '/demo/dinning/';

    //$deviceToken = 'd39d26c2abea5cfd8fd186f2ac7f1d62a2f2a905127baee399e9fe1a2207a5b9';
    if (file_exists($path . 'ck.pem')) {

        $ctx = stream_context_create();
        stream_context_set_option($ctx, 'ssl', 'local_cert', $path . 'ck.pem');
        stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);

        // Open a connection to the APNS server
        $fp = stream_socket_client(
                'ssl://gateway.sandbox.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);

        if (!$fp)
            exit("Failed to connect: $err $errstr" . PHP_EOL);

        //	echo 'Connected to APNS' . PHP_EOL;
        // Create the payload body
        $body['aps'] = array(
            'alert' => $message,
            'pushType' => $check,
            'sound' => 'default'
        );

        // Encode the payload as JSON
        $payload = json_encode($body);

        // Build the binary notification
        $msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;

        // Send it to the server
        $result = fwrite($fp, $msg, strlen($msg));

        /* if (!$result)
          echo 'Message not delivered' . PHP_EOL;
          else
          echo 'Message successfully delivered' . PHP_EOL;
         */

        // Close the connection to the server
        fclose($fp);
        //exit;
    }
    else {
        echo 'ck.pem file does not exist';
    }
}

/* * ******************
 * Purvesh
 * removeNull function
 * Description - for remove null keyword and replace it with blank
 * 28-1-2016
 * ****************** */

function removeNull($array) {

    foreach ($array as $key => $value) {
        if (is_array($value)) {
            $array[$key] = removeNull($value);
        } else {
            if (is_null($value)) {
                $array[$key] = "";
            }
        }
    }

    return $array;
}

/* By Developer : Hitesh Tank
  Date: 5-Feb-2016
 */

function GetParkingfee($Park_ID) {
    Global $con;
    $sqll = "select *,tbl_parkinglocations.*,tbl_parking.* from tbl_parking
		INNER JOIN tbl_parkinglocations ON tbl_parkinglocations.Park_ID=tbl_parking.P_Location
		where P_ID=" . $Park_ID;
    $exe = mysqli_query($con, $sqll);
    $res = mysqli_fetch_array($exe);
    $Park_AirportVenue = $res['Park_AirportVenue'];
    if ($Park_AirportVenue == 1) {
        $sql2 = "SELECT Setting_Name,Setting_Value,format FROM tbl_categories_settings WHERE Cat_ID=45 AND Parking_ID=" . $Park_ID;
        $exe = mysqli_query($con, $sql2);
        $num = mysqli_num_rows($exe);
        if ($num > 0)
            $sql2 = "SELECT Setting_Name,Setting_Value,format FROM tbl_categories_settings WHERE Cat_ID=45 AND Parking_ID=" . $Park_ID;
        else
            $sql2 = "SELECT Setting_Name,Setting_Value,format FROM tbl_categories_settings WHERE Setting_Value>0 AND Cat_ID=45 AND Parking_ID=0";

        $sql1 = "SELECT Setting_Name,Setting_Value,format FROM tbl_sell_fee WHERE Setting_Value>0 AND Parking_ID=" . $Park_ID;

        $sql = $sql1 . " UNION " . $sql2;
    }
    else {
        $sql = "SELECT Setting_Name,Setting_Value,format FROM tbl_sell_fee WHERE Setting_Value>0 AND Parking_ID=" . $Park_ID;
    }

    $exe = mysqli_query($con, $sql);
    $num = mysqli_num_rows($exe);
    $Fee = array();
    $i = 0;
    while ($res = mysqli_fetch_array($exe)) {
        $Fee[$i]['Setting_Name'] = $res['Setting_Name'];
        $Fee[$i]['Setting_Value'] = $res['Setting_Value'];
        $Fee[$i]['format'] = $res['format'];
        $i++;
    }
    return array($Fee, $num);
}

function getMerchantDeliveryFee($rest) {
    GLOBAL $con;
    $rescity = mysqli_fetch_array(mysqli_query($con, "select deliveryFee from merchant where id=" . $rest));
    return $rescity['deliveryFee'];
}

function getCalDailyPrice($fromData, $toDate, $MaxAmount) {


    // HOURLY PRICING
    // tbl_parking.P_FAmt   - HOURLY CHARGE
    $price = number_format($MaxAmount, 2);

    $pr = "hourly";

    $fdate = strtotime($fromData);
    $tdate = strtotime($toDate);
    $diff = $tdate - $fdate;
    $d1 = ceil(($diff) / 60 / 60 / 24);
    $diff = ceil(($diff - $dl * 60 * 60 * 24) / 60 / 60);
    $totalprice = 0;

    if ($MaxAmount == 1 && $MaxAmount > 0) {
        $P_MaxMin = $MaxAmount;
        if ($P_MaxMin < 1) {
            $P_MaxMin = 24;
        }
        $Total_Hours = $diff;
        $Avg_Hours = $Total_Hours / $P_MaxMin;

        if ($Avg_Hours >= 1) {
            $otherprice = floor($Avg_Hours) * $MaxAmount;
            $Remaining_Hours = $Total_Hours % $P_MaxMin;
            $Remaining_Price = $Remaining_Hours * $price;
            if ($Remaining_Price > $MaxAmount)
                $Remaining_Price = $MaxAmount;

            $totalprice = $otherprice + $Remaining_Price;
        }
        else {
            $otherprice = $Total_Hours * $price;
            if ($otherprice > $MaxAmount) {
                $otherprice = $MaxAmount;
            }
            $totalprice = $otherprice;
        }
    } else {

        $totalprice = $price * $diff;
    }
    return $totalprice;
}

/* Developer Name : HItesh Tank
 * Created Date : 25 March 2016
 * Function name : getDineAverageRatings
 */

function getDineAverageRatings($dine_id, $category) {
    GLOBAL $con;
    if (empty($dine_id))
        $dine_id = 0;


    $sql = "SELECT * FROM tbl_reviews WHERE R_Type='" . $category . "' AND list_id=" . $dine_id;
    $result = mysqli_query($con, $sql);
    $Total = 0;
    $Average = 0;
    $num_rows = mysqli_num_rows($result);
    $one_start = 0;
    $two_star = 0;
    $three_star = 0;
    $four_star = 0;
    $five_star = 0;
    $i = 0;

    while ($rows = mysqli_fetch_array($result)) {
        $Total+=$rows['Average'];
        $i++;
    }

    if ($Total > 0)
        $Average = round($Total / $i, 2);
    else
        $Average = 0;

    if ($Average > 0)
        $Average = round($Average * 2, 0) / 2;
    else
        $Average = 0;

    if ($Average > 5)
        $Average = 5;

    $response = array();
    $response['average_rating'] = $Average;
    $response['average_rating_count'] = $i;
    return $response;
}

function GetAvailablePoints($User_ID) {
    GLOBAL $con;
    $sql = "select Points from tbl_registeration where id=" . $User_ID;
    $res = mysqli_query($con, $sql);
    $row = mysqli_fetch_array($res);
    return $row['Points'];
}

function GetTotalTransaction($TxnID) {
    GLOBAL $con;
    $sql = "SELECT * FROM tbl_payment WHERE Pay_ID=" . $TxnID;
    $exe = mysqli_query($con, $sql)or die(mysqli_error());
    $aRow = mysqli_fetch_array($exe);
    return array($aRow['Pay_TxnID'], $aRow['Pay_TotalAmount'], $aRow['Pay_ID'], $aRow['Pay_Created'], $aRow['Dine_TxnID']);
}

function GetTransactionReport($Transactions) {
    GLOBAL $con;
    $query = "SELECT tbl_registeration.firstname AS Name, tbl_paymenttransaction.from_date, tbl_paymenttransaction.to_date,tbl_registeration.email_add,tbl_registeration.voucher,PaymentSource,Item_ID,TxnID,TotalAmount,UsrID,PaymentDesc,Voucher_File,tbl_paymenttransaction.Status,date_format( tbl_paymenttransaction.TxnDate,'%d/%m/%Y') as regdate,tbl_paymenttransaction.TxnDate AS TxnDate,1 AS Main,tbl_paymenttransaction.Activity_ID,tbl_paymenttransaction.Care_ID,tbl_paymenttransaction.Ticket_ID,tbl_paymenttransaction.Parking_ID,tbl_paymenttransaction.Cat_ID,OrderStatus,orderType,Redeem,TxnDate AS ORDERCREATED,tbl_paymenttransaction.*,RIGHT(AES_DECRYPT(tbl_paymenttransaction.Card_Number,'" . ENCRYPTKEY . "'),4) AS Card_No FROM tbl_paymenttransaction
    INNER JOIN tbl_registeration ON  tbl_registeration.id=tbl_paymenttransaction.UsrID
    WHERE TxnID IN(" . $Transactions . ") AND UsrID=" . $_REQUEST['User_ID'];


    $res = mysqli_query($con, $query);
    $contact = array();
    $i = 0;
    while ($aRow = @mysqli_fetch_array($res)) {
        $contact[$i]['TxnID'] = $aRow['TxnID'];
        $contact[$i]['UsrID'] = $aRow['UsrID'];
        $contact[$i]['Activity_ID'] = $aRow['Activity_ID'];
        $contact[$i]['Owner_ID'] = $aRow['Owner_ID'];
        $contact[$i]['Parking_ID'] = $aRow['Parking_ID'];
        $contact[$i]['Ticket_ID'] = $aRow['Ticket_ID'];
        $contact[$i]['Movie_ID'] = $aRow['Movie_ID'];
        $contact[$i]['TxnDate'] = $aRow['TxnDate'];
        $contact[$i]['from_date'] = $aRow['from_date'];
        $contact[$i]['to_date'] = $aRow['to_date'];
        $contact[$i]['Ticket_Title'] = $aRow['Ticket_Title'];
        $contact[$i]['Ticket_EventID'] = $aRow['Ticket_EventID'];
        $contact[$i]['Ticket_EventVenue'] = $aRow['Ticket_EventVenue'];
        $contact[$i]['Ticket_Section'] = $aRow['Ticket_Section'];
        $contact[$i]['Ticket_Row'] = $aRow['Ticket_Row'];
        $contact[$i]['Ticket_Owner'] = $aRow['Ticket_Owner'];
        $contact[$i]['PaymentPlanID'] = $aRow['PaymentPlanID'];
        $contact[$i]['PaymentSource'] = $aRow['PaymentSource'];
        $contact[$i]['RefBy'] = $aRow['RefBy'];
        $contact[$i]['PaymentDesc'] = $aRow['PaymentDesc'];
        $contact[$i]['Ticket_Type'] = $aRow['Ticket_Type'];
        $contact[$i]['quantity'] = $aRow['quantity'];
        $contact[$i]['Amount'] = $aRow['Amount'];
        $contact[$i]['TotalAmount'] = $aRow['TotalAmount'];
        $contact[$i]['Payat_Lot'] = $aRow['Payat_Lot'];
        $contact[$i]['Voucher_File'] = $aRow['Voucher_File'];
        $contact[$i]['Redeem'] = $aRow['Redeem'];
        $contact[$i]['Status'] = $aRow['Status'];
        $contact[$i]['regdate'] = $aRow['regdate'];
        $contact[$i]['Discount'] = $aRow['Discount'];
        $contact[$i]['charges'] = $aRow['charges'];
        $contact[$i]['Item_ID'] = $aRow['Item_ID'];
        $contact[$i]['firstname'] = $aRow['firstname'];
        $contact[$i]['lastname'] = $aRow['lastname'];
        $contact[$i]['email_add'] = $aRow['email_add'];
        $contact[$i]['code'] = $aRow['code'];
        $contact[$i]['Shipping_Method'] = $aRow['Shipping_Method'];
        $contact[$i]['id'] = $aRow['id'];
        $contact[$i]['user_id'] = $aRow['user_id'];
        $contact[$i]['Name'] = $aRow['Name'];
        $contact[$i]['description'] = $aRow['description'];
        $contact[$i]['DeliveryMethod'] = $aRow['DeliveryMethod'];
        $contact[$i]['DeliveryAddress'] = $aRow['DeliveryAddress'];
        $contact[$i]['DeliveryEmail'] = $aRow['DeliveryEmail'];
        $contact[$i]['OrderStatus'] = $aRow['OrderStatus'];
        $contact[$i]['Card_Number'] = $aRow['Card_No'];
        $contact[$i]['Card_Type'] = $aRow['Card_Type'];
        $contact[$i]['Card_Name'] = $aRow['Card_Name'];
        $contact[$i]['orderType'] = $aRow['orderType'];
        $contact[$i]['points'] = $aRow['points'];
        $contact[$i]['PNF_Confirmation'] = $aRow['PNF_Confirmation'];
        $contact[$i]['PNF_TxnID'] = $aRow['PNF_TxnID'];
        $contact[$i]['Payat_Lot'] = $aRow['Payat_Lot'];
        $i++;
    }
    return array($contact, $TotalRecordCount);
}

function distanceNew($lat1, $lon1, $lat2, $lon2) {
    $request_url = "https://maps.googleapis.com/maps/api/distancematrix/xml?origins=" . $lat1 . "+" . $lon1 . "&destinations=" . $lat2 . "+" . $lon2 . "&sensor=false";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $request_url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    //Change this to a 1 to return headers
    // telling cURL to verify the server certificate:
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER["HTTP_USER_AGENT"]);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    $data = curl_exec($ch);
    curl_close($ch);
    $obj = json_decode($data, TRUE);

    $ObjDis = new SimpleXMLElement($data);
    $miles = 0;
    $kilo = $ObjDis->row->element->{'distance'}->text;
    $kkilM = explode(" ", $kilo);
    $KKMM = $kkilM[0];
    $KKMM = str_replace(',', '', $KKMM);
    $miles = $KKMM / 1.609344;
    return $miles;
}

function distance($lat1, $lon1, $lat2, $lon2, $unit) {

    $theta = $lon1 - $lon2;
    $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
    $dist = acos($dist);
    $dist = rad2deg($dist);
    $miles = $dist * 60 * 1.1515;
    $unit = strtoupper($unit);

    if ($unit == "K") {
        return ($miles * 1.609344);
    } else if ($unit == "N") {
        return ($miles * 0.8684);
    } else {
        return $miles;
    }
}

function getlatandlon($address) {
    GLOBAL $con;
    $address = trim($address, ',');
    $sql = "SELECT GL_lat,GL_lon FROM tbl_googlelocationlist WHERE GL_location='" . $address . "'";
    $exe = mysqli_query($con, $sql);
    $res = mysqli_fetch_array($exe);
    if ($res['GL_lat'] <> '' && $res['GL_lon'] <> '') {
        return array($res['GL_lat'], $res['GL_lon'], $res['GL_lat'], $res['GL_lon']);
    } else {

        $address1 = $address;
        $address = urlencode($address);

        //If you want an extended data set, change the output to "xml" instead of csv
        //$local = "ABQIAAAAyizmwBcajGaC_qcNJvVBQRTyRwIAU8UGfeJZL2Ig9l_2M8J71hS9YSoZ5mn1Sd0q4L3J2ENS6F2OWw";
        //$url = "http://maps.google.com/maps/geo?q=".$address."&output=csv&key=".$local;
        //$url = "http://maps.googleapis.com/maps/api/geocode/json?sensor=false&address=".$address."";
        $url = "https://maps.googleapis.com/maps/api/geocode/json?sensor=false&address=" . $address . "&key=AIzaSyCykyjaKTC42PSGYqOlogZgVQy2q-RgDtg";
        //Set up a CURL request, telling it not to spit back headers, and to throw out a user agent.
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        //Change this to a 1 to return headers
        // telling cURL to verify the server certificate:
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER["HTTP_USER_AGENT"]);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $data = curl_exec($ch);
        curl_close($ch);
        $obj = json_decode($data, TRUE);

        $lat = $obj['results'][0]['geometry']['location']['lat'];
        $long = $obj['results'][0]['geometry']['location']['lng'];


        $sql = "INSERT INTO tbl_googlelocationlist(GL_location,GL_lat,GL_lon,GL_Created) VALUES('" . $address1 . "','" . $lat . "','" . $long . "',now())";
        mysqli_query($con, $sql);
        //$data=explode(",",$data);
        return array($lat, $long, $lat, $long);
    }
}

function ParkingDetails($Service_ID) {
    GLOBAL $con;
    $resvenue = mysqli_fetch_array(mysqli_query($con, "SELECT tbl_parkinglocations.Park_Address,
      tbl_parkinglocations.Park_City,tbl_parkinglocations.Park_State,tbl_parkinglocations.Park_Zip,
      tbl_parkinglocations.Park_Logo,
      tbl_parkinglocations.Park_Image,
      tbl_parkinglocations.Park_Name,tbl_parkinglocations.smart_lot, tbl_parkinglocations.Park_Phone from tbl_parking INNER JOIN tbl_parkinglocations ON  tbl_parkinglocations.Park_ID=tbl_parking.P_Location  WHERE P_ID=" . $Service_ID));
    $smart_lot = $resvenue['smart_lot'] == 'yes' ? 1 : 0;
    $resvenue['smart_lot'] = $smart_lot;
    return array($resvenue['Park_Address'], $resvenue['Park_City'], $resvenue['Park_State'], $resvenue['Park_Zip'],
        $resvenue['Park_Logo'], $resvenue['Park_Image'], $resvenue['Park_Name'], $resvenue['smart_lot'], $resvenue['Park_Phone']);
}

function isParkingHasShuttle($parking_ID) {
    GLOBAL $con;
    $sqlDriverQuery = "SELECT tspd.SPD_MappingID FROM `tbl_shuttle_parkinglot_drivers` tspd WHERE tspd.SPD_ParkingLotID = '" . $parking_ID . "' AND tspd.SPD_IsActive = '1'";
    $sqlDriverResult = mysqli_query($con, $sqlDriverQuery);
    $countDriverResult = mysqli_num_rows($sqlDriverResult);
    $isShuttle = 0;
    if ($countDriverResult > 0) {
        $isShuttle = 1;
    }
    return $isShuttle;
}

function getParkingLongLat($parking_ID) {
    GLOBAL $con;
    $resvenue = @mysqli_fetch_array(mysqli_query($con, "select tbl_parkinglocations.lat, tbl_parkinglocations.lon from tbl_parking INNER JOIN tbl_parkinglocations ON tbl_parkinglocations.Park_ID=tbl_parking.P_Location where P_ID=" . $parking_ID));
    return $resvenue;
}

function GetAParkingWeekdayLowPrice($Service_ID) {
    GLOBAL $con;
    /* $sql="SELECT P_Sunday_Price,P_Monday_Price,P_Tuesday_Price,P_Wednesday_Price, P_Thursday_Price, P_Friday_Price,P_Saturday_Price FROM tbl_parking WHERE P_ID=".$Service_ID;
      $v = mysqli_fetch_array(mysqli_query($con,$sql));
      $prices=$v['P_Sunday_Price'].",".$v['P_Monday_Price'].",".$v['P_Tuesday_Price'].",".$v['P_Wednesday_Price'].",".$v['P_Thursday_Price'].",".$v['P_Friday_Price'].",".$v['P_Saturday_Price'].",0";
      $price=explode(",",$prices);
      $remove = array(0);
      $price = array_diff($price, $remove);

      return MIN($price); */
    $Yesterday = date("Y-m-d");
    $e_date = date("Y-m-t", strtotime($Yesterday));
    //$sql = "SELECT MIN(PA_P_Dailyprice) AS PRICE FROM tbl_parkingweekdayavailability WHERE P_fromDate>='" . $Yesterday . "' AND P_ID=" . $Service_ID;
    $sql="SELECT MIN(PA_P_Dailyprice) AS PRICE FROM tbl_parkingweekdayavailability WHERE P_fromDate between :Yesterday and '$e_date' AND PA_P_Dailyprice>0 AND P_ID=:id";
    $v = mysqli_fetch_array(mysqli_query($con, $sql));
    return $v['PRICE'];
}

function GetParkingDailyPrice($Service_ID) {
    GLOBAL $con;
    $start = date("Y-m-d");
    $v = @mysqli_fetch_array(mysqli_query($con, "SELECT PA_P_Dailyprice FROM tbl_parkingavailability WHERE ('" . $start . "' BETWEEN P_fromDate AND PA_toDate) AND P_ID=" . $Service_ID));
    if ($v['PA_P_Dailyprice'] > 0)
        $PA_P_Dailyprice = $v['PA_P_Dailyprice'];
    else {
        $v = @mysqli_fetch_array(mysqli_query($con, "SELECT MIN(PA_P_Dailyprice) AS PA_P_Dailyprice FROM tbl_parkingavailability WHERE P_ID=" . $Service_ID));
        if ($v['PA_P_Dailyprice'] > 0)
            $PA_P_Dailyprice = $v['PA_P_Dailyprice'];
        else {
            $v = @mysqli_fetch_array(mysqli_query($con, "SELECT Average_Price AS PA_P_Dailyprice FROM tbl_parking WHERE P_ID=" . $Service_ID));
            if ($v['PA_P_Dailyprice'] > 0)
                $PA_P_Dailyprice = $v['PA_P_Dailyprice'];
        }
    }
    return $PA_P_Dailyprice;
}

function getAllAverageRatings($list_id, $cat, $user_id = '') {
    GLOBAL $con;
    if ($list_id == '') {
        $list_id = 0;
    }
    if ($user_id != '')
        $sql = "select * from tbl_reviews where user_id='" . $user_id . "' AND R_Type='" . $cat . "' AND list_id=" . $list_id;
    else
        $sql = "select * from tbl_reviews where R_Type='" . $cat . "' AND list_id=" . $list_id;
    $execity = mysqli_query($con, $sql);
    $Total = 0;
    $Average = 0;
    $num = mysqli_num_rows($execity);
    $i = 0;
    while ($v = mysqli_fetch_array($execity)) {
        $Total+=$v['Average'];
        $i++;
    }
    if ($Total > 0)
        $Average = round($Total / $i, 2);
    else
        $Average = 0;

    if ($Average > 0)
        $Average = round($Average * 2, 0) / 2;
    else
        $Average = 0;
    //$Average = floor($Total*$i)/$i;

    if ($Average > 5)
        $Average = 5;
    return $Average;
}

function getParkingAverageRatings($list_id, $cat, $user_id = '') {
    GLOBAL $con;
    if ($list_id == '') {
        $list_id = 0;
    }
    if ($user_id != '')
        $sql = "select * from tbl_reviews where user_id='" . $user_id . "' AND R_Type='" . $cat . "' AND list_id=" . $list_id;
    else
        $sql = "select * from tbl_reviews where R_Type='" . $cat . "' AND list_id=" . $list_id;
    $execity = mysqli_query($con, $sql);
    $Total = 0;
    $Average = 0;
    $num = mysqli_num_rows($execity);
    $i = 0;
    while ($v = mysqli_fetch_array($execity)) {
        $Total+=$v['Average'];
        $i++;
    }
    if ($Total > 0)
        $Average = round($Total / $i, 2);
    else
        $Average = 0;

    if ($Average > 0)
        $Average = round($Average * 2, 0) / 2;
    else
        $Average = 0;
    //$Average = floor($Total*$i)/$i;

    if ($Average > 5)
        $Average = 5;

    $response = array();
    $response['average_rating'] = $Average;
    $response['average_rating_count'] = $i;
    return $response;
}

function getParkingPriceAndTotal($flag, $fromdate, $todate, $P_ID, $pe_id = '') {
    GLOBAL $con;
    // echo $fromdate;
    // echo $todate; die;

    $sqll = "select *,tbl_parkinglocations.*,tbl_parking.* from tbl_parking
    INNER JOIN tbl_parkinglocations ON tbl_parkinglocations.Park_ID=tbl_parking.P_Location
    where P_ID=" . $P_ID;
    $exe = mysqli_query($con, $sqll);
    $res = mysqli_fetch_assoc($exe);
    $Park_AirportVenue = $res['Park_AirportVenue'];

    //print_r($res); die;
    if ($res['min_reservation'] > 0)
        $min_reservation = $res['min_reservation'];
    else
        $min_reservation = 0;

    $fdate = strtotime($fromdate);
    $tdate = strtotime($todate);
    if ($min_reservation > 0 && $flag == 1)
        $tdate = strtotime(date('Y-m-d g:i A', strtotime("+$min_reservation day", $fdate)));
    $totalprice = $price = $diff = 0;
    //  echo $res['P_Pricingtype'];die;
    if ($res['P_Pricingtype'] == 'daily') {
        $start = date("Y-m-d", $fdate);
        $end = date("Y-m-d", $tdate);
        $num = 1;
        $error = "";
        $diff = abs($tdate - $fdate);
        $years = floor($diff / (365 * 60 * 60 * 24));
        $months = floor(($diff - $years * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));
        $days = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24) / (60 * 60 * 24));
        $hours = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24 - $days * 60 * 60 * 24) / (60 * 60));
        $minuts = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24 - $days * 60 * 60 * 24 - $hours * 60 * 60) / 60);
        if ($years > 0) {
            $days = $days + ($years * 364);

            $days--;
            $days = $days;
        }
        if ($months > 0) {
            $days = $days + ($months * 30);

            //$days--;
            $days = $days;
        }

        if ($hours > 0 || $minuts > 0)
            $days++;

        $diff = $days;

        if ($min_reservation > 0 && $min_reservation > $diff && $flag == 0) {
            $closed = 1;
            $content = array("response" => ERROR, "message" => $min_reservation . "-day minimum stay required.");
            echo json_encode($content);
            exit;
        }
        $ik = 1;
        $closed = 0;
        $closeddates = "";
        $PayatLotdaysAmt = 0;
        $enddate = $end;

        if ($diff < 7) {
            $enddate = date('Y-m-d', strtotime("+7 day", $fdate));
        }
        $parkingData = parkingData($P_ID, $start, $enddate, 'daily', '');
        if (!empty($parkingData)) {
            $temp = array();
            foreach ($parkingData as $records) {
                $lot = $records['PA_No_Spaces'] - $records['occupiedSpaces'];
                $temp['spaces'] = ($lot > 0) ? $lot : '0';
                $temp['sold_out'] = ($lot <= 0) ? '1' : '0';
                $temp['date'] = $records['P_fromDate'];
                $temp['price'] = $records['PA_P_Dailyprice'];
                $price = $records['PA_P_Dailyprice'];
                $price_rate[] = $temp;
                if ($ik <= $days) {
                    $totalprice = $totalprice + $records['PA_P_Dailyprice'];
                }
                $ik++;
            }
        }
    }
    if ($res['P_Pricingtype'] == 'weekly') {
        $price = number_format($res['P_Weeklyprice'], 2);
        $pr = "Week";

        $diff = ceil(($tdate - $fdate) / ((3600 * 24) * 7));
        $totalprice = $price * $diff;
    } else if ($res['P_Pricingtype'] == 'event') {
        $price = number_format($res['Event_price'], 2);
        $pr = "Event";

        //$diff = ceil(($tdate - $fdate)/ 3600/24);
        $diff = 1;
        $totalprice = $price * $diff;
        $dt2 = date('Y-m-d H:i:s');
        $pesql = "SELECT PE_Start,PE_End FROM tbl_parkingevents WHERE PE_ParkID='" . $P_ID . "' AND tbl_parkingevents.PE_Start>'" . $dt2 . "'"; // and PE_ID='".$pe_id."'";
        $peexe = mysqli_query($con, $pesql);
        $peres = mysqli_fetch_assoc($peexe);
        //  print_r($peres); die;
        $fdate = strtotime($peres['PE_Start']);
        $tdate = strtotime($peres['PE_End']);
    } else if ($res['P_Pricingtype'] == 'special') {
        $price = number_format($res['Event_price'], 2);
        $pr = "Special";

        $diff = ceil(($tdate - $fdate) / 3600 / 24);
        $totalprice = $price * $diff;
    } else if ($res['P_Pricingtype'] == 'monthly') {
        $price = number_format($res['P_Monthlyprice'], 2);
        $pr = "Month";

        $diff = ceil(($tdate - $fdate) / ((3600 * 24) * 31));
        $totalprice = $price * $diff;
    } else if ($res['P_Pricingtype'] == 'hourly') {
        $Overnight_Fee = 0;
        if ($flag == 0) {
            //Check Lot opened for selected arrival date
            $weekfday = date("l", $fdate);

            if ($weekfday == 'Monday') {
                $Park_timing = $res['P_OP_Monday'];
                $Park_Closed = $res['P_OP_Monday_Closed'];
            } else if ($weekfday == 'Tuesday') {
                $Park_timing = $res['P_OP_Tuesday'];
                $Park_Closed = $res['P_OP_Tuesday_Closed'];
            } else if ($weekfday == 'Wednesday') {
                $Park_timing = $res['P_OP_Wednesday'];
                $Park_Closed = $res['P_OP_Wednesday_Closed'];
            } else if ($weekfday == 'Thursday') {
                $Park_timing = $res['P_OP_Thursday'];
                $Park_Closed = $res['P_OP_Thursday_Closed'];
            } else if ($weekfday == 'Friday') {
                $Park_timing = $res['P_OP_Friday'];
                $Park_Closed = $res['P_OP_Friday_Closed'];
            } else if ($weekfday == 'Saturday') {
                $Park_timing = $res['P_OP_Saturday'];
                $Park_Closed = $res['P_OP_Saturday_Closed'];
            } else if ($weekfday == 'Sunday') {
                $Park_timing = $res['P_OP_Sunday'];
                $Park_Closed = $res['P_OP_Sunday_Closed'];
            }

            if ($Park_Closed == 1 || $Park_timing == 'closed') {
                echo 'Parking lot is closed during the time you have selected.';
                exit;
            } else if ($Park_timing == '24hr') {
                echo '';
            } else {
                $Park_timings = explode("-", $Park_timing);

                //echo "<br>From Time:".$Current_Time=date("g:i A",$fdate);
                $Current_Date = date("Y-m-d", $fdate);
                $today_timestamp = $fdate;
                //echo "<br>open Time:".$Park_timings[0];
                $start_timestamp = strtotime($Current_Date . ' ' . $Park_timings[0]);
                //echo "<br>Close Time:".$Park_timings[1];
                if (strtotime($Park_timings[1]) < strtotime($Park_timings[0]))
                    $Current_Date = date("Y-m-d", strtotime("+1 day", strtotime($Current_Date)));
                $end_timestamp = strtotime($Current_Date . ' ' . $Park_timings[1]);
                if (($today_timestamp >= $start_timestamp) && ($today_timestamp <= $end_timestamp)) {

                } else {
                    //Check for Over Night Parking
                    if ($res['P_Overnight'] == 1) {
                        // Calculate Over Night Rate
                        $Overnight_Fee = $Overnight_Fee + $res['P_Overnight_Fee'];
                    }
                }
            }

            //Check Lot opened for selected arrival date
            $weektday = date("l", $tdate);

            if ($weektday == 'Monday') {
                $Park_timing = $res['P_OP_Monday'];
                $Park_Closed = $res['P_OP_Monday_Closed'];
            } else if ($weektday == 'Tuesday') {
                $Park_timing = $res['P_OP_Tuesday'];
                $Park_Closed = $res['P_OP_Tuesday_Closed'];
            } else if ($weektday == 'Wednesday') {
                $Park_timing = $res['P_OP_Wednesday'];
                $Park_Closed = $res['P_OP_Wednesday_Closed'];
            } else if ($weektday == 'Thursday') {
                $Park_timing = $res['P_OP_Thursday'];
                $Park_Closed = $res['P_OP_Thursday_Closed'];
            } else if ($weektday == 'Friday') {
                $Park_timing = $res['P_OP_Friday'];
                $Park_Closed = $res['P_OP_Friday_Closed'];
            } else if ($weektday == 'Saturday') {
                $Park_timing = $res['P_OP_Saturday'];
                $Park_Closed = $res['P_OP_Saturday_Closed'];
            } else if ($weektday == 'Sunday') {
                $Park_timing = $res['P_OP_Sunday'];
                $Park_Closed = $res['P_OP_Sunday_Closed'];
            }
            if ($Park_Closed == 1 || $Park_timing == 'closed') {
                echo 'Parking lot is closed during the time you have selected.';
                exit;
            }
            // else if($Park_timing=='24hr')
            // {
            //  echo '';
            // }
            else {
                $Park_timings = explode("-", $Park_timing);

                //echo "<br>From Time:".$Current_Time=date("g:i A",$tdate);
                $Current_Date = date("Y-m-d", $tdate);
                $today_timestamp = $tdate;
                //echo "<br>open Time:".$Park_timings[0];
                $start_timestamp = strtotime($Current_Date . ' ' . $Park_timings[0]);
                //echo "<br>Close Time:".$Park_timings[1];
                if (strtotime($Park_timings[1]) < strtotime($Park_timings[0]))
                    $Current_Date = date("Y-m-d", strtotime("+1 day", strtotime($Current_Date)));
                $end_timestamp = strtotime($Current_Date . ' ' . $Park_timings[1]);

                if (($today_timestamp >= $start_timestamp) && ($today_timestamp <= $end_timestamp)) {

                } else {
                    //Check for Over Night Parking
                    if ($res['P_Overnight'] == 1) {
                        // Calculate Over Night Rate
                        $Overnight_Fee = $Overnight_Fee + $res['P_Overnight_Fee'];
                    } else {
                        // echo $Park_Closed;
                        // echo $Park_timing;
                        //  echo 'Overnight parking is not permitted in this location';
                        //echo 'Parking Closed on Selected Arrival Time';
                        //  exit;
                    }
                }
            }
        }
        $price = number_format($res['P_FAmt'], 2);
        //$totalprice=$price;
        $pr = "Hourly";
        $diff = $tdate - $fdate;
        $d1 = ceil(($diff) / 60 / 60 / 24);
        $diff = ceil(($diff - $dl * 60 * 60 * 24) / 60 / 60);

        if ($res['P_MaxMinEnable'] == 1 && $res['P_MaxAmt'] > 0) {
            $P_MaxMin = $res['P_MaxMin'];
            if ($P_MaxMin < 1)
                $P_MaxMin = 24;
            $Total_Hours = $diff;
            $Avg_Hours = $Total_Hours / $P_MaxMin;
            if ($Avg_Hours >= 1) {
                $otherprice = floor($Avg_Hours) * $res['P_MaxAmt'];
                $Remaining_Hours = $Total_Hours % $P_MaxMin;
                $Remaining_Price = $Remaining_Hours * $price;
                if ($Remaining_Price > $res['P_MaxAmt'])
                    $Remaining_Price = $res['P_MaxAmt'];
                $totalprice = $otherprice + $Remaining_Price;
            }
            else {
                $otherprice = $Total_Hours * $price;
                if ($otherprice > $res['P_MaxAmt'])
                    $otherprice = $res['P_MaxAmt'];
                $totalprice = $otherprice;
            }
        }
        else {
            $totalprice = getNewMainHourPrice($price, $diff);
            //$totalprice=$price*$diff;
        }
    } else if ($res['P_Pricingtype'] == 'minute') {
        $otherprice = 0;
        $price = number_format($res['P_FAmt'], 2);
        $totalprice = $price;
        $pr = "Hourly";


        $diff = round(abs($tdate - $fdate) / 60, 2);

        //echo "<br>".$diff = ceil($diff/ 1440);
        //$hl = ceil(($diff - $dl*60*60*24)/60/60);
        //echo "<br>".$diff = ceil(($diff - $dl*60*60*24 - $hl*60*60)/60);

        if ($diff > $res['P_FMIN']) {
            if ($res['P_MaxMinEnable'] == 1) {
                $diff1 = $diff - $res['P_FMIN'];
                if ($diff1 >= $res['P_MaxMin']) {
                    $othermin = floor($diff1 / $res['P_MaxMin']);
                    $otherprice = $othermin * $res['P_MaxAmt'];
                    $othermin1 = $diff1 % $res['P_MaxMin'];
                    if ($othermin1 > 0) {
                        $othermin1 = ceil($othermin1 / $res['P_IncMin']);
                        $otherprice1 = $othermin1 * $res['P_IncAmt'];
                        $otherprice = $otherprice + $otherprice1;
                    }
                } else {
                    $diff1 = ceil($diff1 / $res['P_IncMin']);
                    $otherprice = $diff1 * $res['P_IncAmt'];
                }
            } else {
                if ($res['P_IncMin'] == 0)
                    $P_IncMin = $res['P_FMIN'];
                else
                    $P_IncMin = $res['P_IncMin'];
                if ($res['P_IncAmt'] == 0)
                    $P_IncAmt = $res['P_FAmt'];
                else
                    $P_IncAmt = $res['P_IncAmt'];
                $diff1 = $diff - $res['P_FMIN'];
                $diff1 = ceil($diff1 / $P_IncMin);
                $otherprice = $diff1 * $P_IncAmt;
            }
        }
        $diff = $diff / $res['P_FMIN'];
        $totalprice = $totalprice + $otherprice;
    }
    // get tax and fees
    $extfees = 0;
    $Way_Fee = 0;
    // Admin External Fee based on Individual Listing
    if ($Park_AirportVenue == 1) {
        $csql = "SELECT * FROM tbl_categories_settings WHERE Cat_ID=45 AND Parking_ID=" . $P_ID;
        $cexe = mysqli_query($con, $csql);
        $ext_setting = 0;
        while ($cres = mysqli_fetch_array($cexe)) {
            $ext_setting = 1;
            if ($cres['format'] == '%') {
                $per_amt = $totalprice * $cres['Setting_Value'] / 100;
                $extfees = $extfees + $per_amt;
            } else {
                $per_amt = $cres['Setting_Value'];
                $extfees = $extfees + $cres['Setting_Value'];
            }
        }
        $Way_Fee = $extfees;
    }
    // Admin External Fees
    if ($Park_AirportVenue == 1) {
        if ($ext_setting == 0) {
            $csql = "SELECT * FROM tbl_categories_settings WHERE Setting_Value>0 AND Cat_ID=45 AND Parking_ID=0";
            $cexe = mysqli_query($con, $csql);
            while ($cres = mysqli_fetch_array($cexe)) {
                if ($cres['format'] == '%') {
                    $per_amt = $totalprice * $cres['Setting_Value'] / 100;
                    $extfees = $extfees + $per_amt;
                    $Way_Fee = $Way_Fee + $per_amt;
                } else {
                    $per_amt = $cres['Setting_Value'];
                    $extfees = $extfees + $cres['Setting_Value'];
                    $Way_Fee = $Way_Fee + $cres['Setting_Value'];
                }
            }
        }
    }

    // Get Additional Charges
    $csql = "SELECT * FROM tbl_sell_fee WHERE Parking_ID=" . mysqli_real_escape_string($con, $P_ID);
    $cexe = mysqli_query($con, $csql);

    while ($cres = mysqli_fetch_array($cexe)) {
        if ($cres['format'] == '%') {
            $per_amt = round($totalprice * $cres['Setting_Value'] / 100, 2);
            $extfees = $extfees + $per_amt;
        } else {
            $per_amt = $cres['Setting_Value'];
            $extfees = $extfees + $cres['Setting_Value'];
        }
    }
    $total = $totalprice + $extfees + $Overnight_Fee;
    return array('price_rate' => $price_rate, 'price' => $price, 'subtotal' => $totalprice, 'total' => $total, 'tax' => $extfees, 'overnight_fee' => $Overnight_Fee, 'days' => $diff, 'fdate' => $fdate, 'tdate' => $tdate, 'min_reservation' => $min_reservation);
}

function parkingData($id, $startdate, $enddate, $park_type, $park_price_type) {
    GLOBAL $con;
    if ($park_type == 'daily')
        $query = "SELECT P_fromDate,PA_No_Spaces,PA_P_Dailyprice,occupiedSpaces FROM tbl_parkingweekdayavailability where P_ID='$id' AND P_fromDate BETWEEN '$startdate' AND '$enddate' order by '$startdate' ";
    else if ($park_type = 'monthly' || $park_type = 'hourly')
        $query = "SELECT Available_From,P_Spots FROM tbl_parking where P_ID='$id'";
     echo $query;
    $res = mysqli_query($con, $query);
    while ($row = mysqli_fetch_assoc($res))
        $data[] = $row;
    return $data;
}

/*function parkingOccupiedData($id, $startdate, $enddate, $park_type, $park_price_type) {

    GLOBAL $con;
    $query = "SELECT tbl_registeration.display_name as Name,tbl_paymenttransaction.OrderStatus,tbl_paymenttransaction.from_date FROM tbl_paymenttransaction
    INNER JOIN tbl_registeration ON tbl_registeration.id=tbl_paymenttransaction.UsrID
    INNER JOIN tbl_parking ON tbl_parking.P_ID=tbl_paymenttransaction.Parking_ID
    INNER JOIN tbl_parkinglocations ON tbl_parkinglocations.Park_ID=tbl_parking.P_Location
    WHERE Parking_ID='$id' AND from_date BETWEEN '$startdate' AND '$enddate'";
    $res = mysqli_query($con, $query);
    while ($row = mysqli_fetch_assoc($res))
        $data[] = $row;
    return $data;
}*/

function getNewMainHourPrice($price, $diff) {
    $minDiffCal = 4;
    $maxDiffCal = 5;
    $totalHoursCal = 24;
    $diffCal = $diff % $totalHoursCal;
    if ($diff >= $totalHoursCal) {
        $TotalHoursLoop = number_format($diff / $totalHoursCal);
    } else {
        $TotalHoursLoop = 0;
    }
    $totalprice = 0;
    for ($i = 0; $i <= $TotalHoursLoop; $i++) {
        if ($i == $TotalHoursLoop) {
            $totalprice += getNewSubHourPrice($price, $diffCal, $minDiffCal, $maxDiffCal);
        } else {
            $totalprice += getNewSubHourPrice($price, $maxDiffCal, $minDiffCal, $maxDiffCal);
        }
    }
    return $totalprice;
}

function getNewSubHourPrice($price, $diffCal, $minDiffCal, $maxDiffCal) {
    $totalprice = '';
    if ($diffCal <= $minDiffCal) {
        $totalprice = $price * $diffCal;
    }
    if ($diffCal >= $maxDiffCal) {
        $totalprice = $price * $maxDiffCal;
    }
    return $totalprice;
}

function getParkingTypeByPid($P_ID, $statusQuery) {
    GLOBAL $con;
    $sql = "SELECT * FROM `tbl_parking` tp where " . $statusQuery . " tp.P_Location = (select tp1.P_Location from `tbl_parking` tp1 where tp1.P_ID = '" . $P_ID . "') AND (tp.P_Pricingtype = 'hourly' OR tp.P_Pricingtype = 'daily') ORDER BY tp.`P_ID`";
    //echo $sql;
    $result = mysqli_query($con, $sql);
    $parkingTypeStatus = '';
    $countParkingResult = mysqli_num_rows($result);
    if ($countParkingResult > 0) {
        while ($res = mysqli_fetch_assoc($result)) {
            if ($res['P_Pricingtype'] == 'daily' || $res['P_Pricingtype'] == 'hourly') {
                if ($res['P_Pricingtype'] == 'hourly') {
                    $parkingTypeStatus['h'] = 'h';
                } else if ($res['P_Pricingtype'] == 'daily') {
                    $parkingTypeStatus['d'] = 'd';
                }
            }
        }
        asort($parkingTypeStatus);
        $parkingTypeStatus = implode('', $parkingTypeStatus);
    }
    return preg_replace("/(.)\\1+/", "$1", $parkingTypeStatus);
}

function getParkingTypeByPidWithDetail($P_ID, $statusQuery = '') {
    GLOBAL $con;
    $sql = "SELECT P_ID, P_Location, P_Pricingtype  FROM `tbl_parking` tp where " . $statusQuery . " tp.P_Location = (select tp1.P_Location from `tbl_parking` tp1 where tp1.P_ID = '" . $P_ID . "') AND (tp.P_Pricingtype = 'hourly' OR tp.P_Pricingtype = 'daily') ORDER BY tp.`P_ID`";
    //echo $sql;
    $result = mysqli_query($con, $sql);
    $parkingTypeStatus = '';
    $mainDetail = array();
    $detailArray = array();
    $countParkingResult = mysqli_num_rows($result);
    if ($countParkingResult > 0) {
        while ($res = mysqli_fetch_assoc($result)) {
            if ($res['P_Pricingtype'] == 'daily' || $res['P_Pricingtype'] == 'hourly') {
                if ($res['P_Pricingtype'] == 'hourly') {
                    $parkingTypeStatus['h'] = 'h';
                    $detailArray['hourly'] = $res;
                } else if ($res['P_Pricingtype'] == 'daily') {
                    $parkingTypeStatus['d'] = 'd';
                }
            }
        }
        asort($parkingTypeStatus);
        $parkingTypeStatus = implode('', $parkingTypeStatus);
    }
    $parkingTypeStatus = preg_replace("/(.)\\1+/", "$1", $parkingTypeStatus);
    $mainDetail['parkingTypeStatus'] = $parkingTypeStatus;
    $mainDetail['parkingDetails'] = $detailArray;
    return $mainDetail;
}

function encryptor($action, $string) {
    GLOBAL $EncryptKey;
    $output = false;

    $encrypt_method = "AES-256-CBC";
    //pls set your unique hashing key
    $secret_key = $EncryptKey;
    $secret_iv = 'WAY@2211';

    // hash
    $key = hash('sha256', $secret_key);

    // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
    $iv = substr(hash('sha256', $secret_iv), 0, 16);

    //do the encyption given text/string/number
    if ($action == 'encrypt') {
        $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
        $output = base64_encode($output);
    } else if ($action == 'decrypt') {
        //decrypt the given text/string/number
        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
    }

    return $output;
}

function parkingOccupiedData($id, $startdate, $enddate, $park_type, $park_price_type) {
    GLOBAL $con;
    $query = "SELECT tbl_registeration.display_name as Name,tbl_registeration.mobile_phone as Mobile,tbl_paymenttransaction.TxnID as TransactionId,tbl_paymenttransaction.to_date as CheckoutDate,tbl_paymenttransaction.from_date as CheckinDate,tbl_paymenttransaction.OrderStatus FROM tbl_paymenttransaction
    INNER JOIN tbl_registeration ON tbl_registeration.id=tbl_paymenttransaction.UsrID
    INNER JOIN tbl_parking ON tbl_parking.P_ID=tbl_paymenttransaction.Parking_ID
    INNER JOIN tbl_parkinglocations ON tbl_parkinglocations.Park_ID=tbl_parking.P_Location
    WHERE Parking_ID='$id' AND tbl_paymenttransaction.from_date BETWEEN '$startdate' AND '$enddate'";
    $res = mysqli_query($con, $query);
    while ($row = mysqli_fetch_assoc($res))
        $data[] = $row;
    return $data;
}

function GetOrderAmount($OrderNo){
  GLOBAL $con;
  $query = "SELECT orders.orderAmount FROM  `orders` WHERE  `id` = '".$OrderNo."'";
  $res = mysqli_query($con, $query);
  $row = mysqli_fetch_assoc($res);
  return $row['orders.orderAmount'];
}

function GetOrdertaxAmount($OrderNo){
  GLOBAL $con;
  $query = "SELECT orders.orderTaxAmount FROM  `orders` WHERE  `id` = '".$OrderNo."'";
  $res = mysqli_query($con, $query);
  $row = mysqli_fetch_assoc($res);
  return $row['orders.orderTaxAmount'];
}

function GetOrderdiscountAmount($OrderNo){
  GLOBAL $con;
  $query = "SELECT orders.discount FROM  `orders` WHERE  `id` = '".$OrderNo."'";
  $res = mysqli_query($con, $query);
  $row = mysqli_fetch_assoc($res);
  return $row['orders.discount'];
}

function GetOrderdeliveryFee($OrderNo){
  GLOBAL $con;
  $query = "SELECT orders.deliveryFee FROM  `orders` WHERE  `id` = '".$OrderNo."'";
  $res = mysqli_query($con, $query);
  $row = mysqli_fetch_assoc($res);
  return $row['orders.deliveryFee'];
}

function GetOrderDetailByOrderNo($OrderNo){
  GLOBAL $con;
  $array = array();
  $query = "SELECT orders.orderAmount, orders.orderTaxAmount, orders.discount, orders.deliveryFee FROM  `orders` WHERE  `id` = '".$OrderNo."'";
  $res = mysqli_query($con, $query);
  $row = mysqli_fetch_assoc($res);
  if(!empty($row)){
    $array = $row;
  }
  return $array;
}

?>
