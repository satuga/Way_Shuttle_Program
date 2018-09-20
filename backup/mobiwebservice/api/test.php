<?php 

function _recaptcha_aes_encrypt($val,$ky) {
	if (! function_exists ("mcrypt_encrypt")) {
		die ("To use reCAPTCHA Mailhide, you need to have the mcrypt php module installed.");
	}
	$mode=MCRYPT_MODE_CBC;   
	$enc=MCRYPT_RIJNDAEL_128;
	$val=_recaptcha_aes_pad($val);
	return mcrypt_encrypt($enc, $ky, $val, $mode, "\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0");
}
	// get random values
		$length = 30;
		$characters = "0123456789abcdefghijklmnopqrstuvwxyz";
		$string = "";    
		for ($p = 0; $p < $length; $p++) {
			$string .= $characters[mt_rand(0, strlen($characters))];
		}
		$checksum_register=$string;
        
	$reguser=explode("@",$email);
		$aliasemail =$reguser[0].$id."@members.way.com";
?>