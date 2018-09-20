<?php 
//$url = 'http://bi.way.com/node';
$url = 'http://www.way.com/node';

$checkAliveStatus = check_alive($url, $timeout = 90);

if(!$checkAliveStatus){
	$currentdate = date('Y-m-d');
    $mailto = !empty($_GET['emailto']) ? $_GET['emailto'] : 'ujash.letsnurture@gmail.com, Praveen.Kommuri@way.com';
    $subject = 'Need to restart Shuttle server - ' . $currentdate;
    $message = 'Kindly, I request you to restart shuttle server as soon as possible.';
    $message .= '<br/><br/>';
    $message .= 'Thank you';
    //restartService();
    sendmail($mailto, $subject, $message);
	echo "testing";
}

function restartService(){
    
}

function check_alive($url, $timeout = 10) {
  $ch = curl_init($url);

  // Set request options
  curl_setopt_array($ch, array(
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_NOBODY => true,
    CURLOPT_TIMEOUT => $timeout,
    CURLOPT_USERAGENT => "page-check/1.0" 
  ));

  // Execute request
  curl_exec($ch);

  // Check if an error occurred
  if(curl_errno($ch)) {
    curl_close($ch);
    return false;
  }

  // Get HTTP response code
  $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  curl_close($ch);

  // Page is alive if 200 OK is received
  return $code === 200;
}

function sendmail($mailto, $subject, $message) {
    $email_from = "support@way.com"; // Who the email is from
    $headers = "From: ".$email_from."\r\n" .
	'Reply-To: '.$email_from."\r\n" .
	'X-Mailer: PHP/' . phpversion();
    $headers .= 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
    mail($mailto,$subject, $message, $headers);
}
?>
