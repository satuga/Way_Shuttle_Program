<?php
class AES {
	

private $iv = ' '; #Same as in iOS
private $skey = 'L0M35r1j3mqcntZL'; #Same as in iOS

public function encrypt($sStr) {
  return base64_encode(
    mcrypt_encrypt(
        MCRYPT_RIJNDAEL_128, 
        base64_decode($sKey),
        $sStr,
        MCRYPT_MODE_ECB
    )
  );
}

public function decrypt($sStr) {
  return mcrypt_decrypt(
    MCRYPT_RIJNDAEL_128, 
    base64_decode($sKey), 
    base64_decode($sStr), 
    MCRYPT_MODE_ECB
  );
}
/*
public function encrypt($str) {

$iv = $this->iv;
$td = mcrypt_module_open('rijndael-128', '', 'ecb', $iv);
mcrypt_generic_init($td, $this->key, $iv);
$encrypted = mcrypt_generic($td, $str);
mcrypt_generic_deinit($td);
mcrypt_module_close($td);

return bin2hex($encrypted);
}
public function decrypt($code) {
$code = $this->hex2bin($code);
$iv = $this->iv;
$td = mcrypt_module_open('rijndael-128', '', 'ecb', $iv);
mcrypt_generic_init($td, $this->key, $iv);
$decrypted = mdecrypt_generic($td, $code);
mcrypt_generic_deinit($td);
mcrypt_module_close($td);

return utf8_encode(trim($decrypted));
}
*/
protected function hex2bin($hexdata) {
$bindata = '';
for ($i = 0; $i < strlen($hexdata); $i += 2) {
$bindata .= chr(hexdec(substr($hexdata, $i, 2)));
}
return $bindata;
}
}
?>
