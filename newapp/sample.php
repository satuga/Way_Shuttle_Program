<?php

$bearerToken = "f081e61b-ee8f-4f95-8210-31e3fa05976c";
        
$url = "https://uat.way.com/way-service/security/userProfileManagement/user";

$headers = array( 
    //"accept: application/json, text/plain, *//*",
    //"accept-encoding: gzip, deflate, br",
    //"Content-Type: application/json;charset=UTF-8", 
    //"accept-language: en-US,en;q=0.8",
    "Authorization:Bearer ".$bearerToken
);  

$ch = curl_init(); 
curl_setopt($ch, CURLOPT_URL,$url); 
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);  
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

$data = curl_exec($ch); 

if (curl_errno($ch)) { 
    print "Error: " . curl_error($ch); 
} else { 
    // Show me the result 
    echo "<pre>";
    print_r($data);
    exit;
    curl_close($ch); 
} 
?>

