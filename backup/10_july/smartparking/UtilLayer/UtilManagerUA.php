<?php

/************************************************************
FILE :	UtilManagerUA.php
PURPOSE: parking database transactions
AUTHOR: Gopalan Mani
DATE  : 05 FEB 2015
**************************************************************/
 

class UtilManagerUA 
{

  // Function to get the client IP address
  function get_client_ip() {
    $ipaddress = '';
    if ($_SERVER['HTTP_CLIENT_IP'])
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if($_SERVER['HTTP_X_FORWARDED_FOR'])
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if($_SERVER['HTTP_X_FORWARDED'])
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if($_SERVER['HTTP_FORWARDED_FOR'])
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if($_SERVER['HTTP_FORWARDED'])
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if($_SERVER['REMOTE_ADDR'])
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}


function cleanInput($input) {
  $search = array(
    '@<script[^>]*?>.*?</script>@si',   // Strip out javascript
    '@<[\/\!]*?[^<>]*?>@si',            // Strip out HTML tags
    '@<style[^>]*?>.*?</style>@siU',    // Strip style tags properly
    '@<![\s\S]*?--[ \t\n\r]*>@'         // Strip multi-line comments
  );

    $output = preg_replace($search, '', $input);
	//$output = preg_replace($search, '', $input);
	$output = preg_replace("(^https?://)", "", $output );
	$output = preg_replace("(^www.)", "", $output );
	$output = preg_replace("(^ftp://)", "", $output );
	$output = preg_replace("(^sftp://)", "", $output );
	//$output = preg_replace("(^telnet)", "", $output );
	//$output = preg_replace("(^/)", "", $output );
	//$output = str_replace('/', '', $output); 
	$output = preg_replace('~[\\\\*?"<>|£$^%]~', ' ', $output);
    return $output;
 }
  function sanitize($input) {
    if (is_array($input)) {
        foreach($input as $var=>$val) {
            $output[$var] = sanitize($val);
        }
    }
    else {
        if (get_magic_quotes_gpc()) {
            $input = stripslashes($input);
        }
		  
        $input  = $this->cleanInput($input) ;
       //$output = stripslashes(mysqli_real_escape_string(stripslashes($input)));
	    $output = stripslashes(stripslashes($input));
		$output = addslashes($output);

    }
    return $output;
}
   
 
}

?>
