<?php
include 'config.php';
require_once ('Yelp/v2/php/lib/OAuth.php');
require_once ('function.php');
if($_REQUEST['id'])
{
    $rest=str_replace("!","",str_replace(" ","-",str_replace("&","and",str_replace(".","",str_replace("`","",str_replace("'","",str_replace("(","",str_replace(")","",GetRestaurantname($_REQUEST['id'])))))))));
    //$rest=str_replace("--","-",(str_replace(" ","-",GetRestaurantname($_REQUEST['id']))));
    $restcity=str_replace(" ","-",GetRestaurantCity($_REQUEST['id']));
    $link=$rest."-".$restcity;
    // For example, request business with id 'the-waterboy-sacramento'
    $unsigned_url = "http://api.yelp.com/v2/business/".trim($link,'-');
    
    // For examaple, search for 'tacos' in 'sf'
    // $unsigned_url = "http://api.yelp.com/v2/search?term=".GetRestaurantname($_REQUEST['id'])."&location=".$restcity;
    //$unsigned_url = "http://api.yelp.com/business_review_search?term=Spice%20Hut,594 E. El Camino Real,Sunnyvale, CA";
    
    
    // Set your keys here
    $consumer_key = "X634ql-jnAs6QBuohg4UHA";
    $consumer_secret = "GvO6267DYlJCvDLAQhxtKV8XxK0";
    $token = "_bVAYenTlrbRtfJs1QrCpS6Vz9vH4aqk";
    $token_secret = "ls-G4u15n6AqbAp7smU0wadvrxg";
    
    // Token object built using the OAuth library
    $token = new OAuthToken($token, $token_secret);
    
    // Consumer object built using the OAuth library
    $consumer = new OAuthConsumer($consumer_key, $consumer_secret);
    
    // Yelp uses HMAC SHA1 encoding
    $signature_method = new OAuthSignatureMethod_HMAC_SHA1();
    
    // Build OAuth Request using the OAuth PHP library. Uses the consumer and token object created above.
    $oauthrequest = OAuthRequest::from_consumer_and_token($consumer, $token, 'GET', $unsigned_url);
    
    // Sign the request
    $oauthrequest->sign_request($signature_method, $consumer, $token);
    
    // Get the signed URL
    $signed_url = $oauthrequest->to_url();
    
    // Send Yelp API Call
    $ch = curl_init($signed_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    $data = curl_exec($ch); // Yelp response
    curl_close($ch);
    $object=json_decode($data,TRUE);
    $object['status']=1;
    $object['message']='success';
    echo json_encode($object);
    exit;
}
else{
    $content=array("status"=>0,"message"=>PARAMETER_MSG);
    echo json_encode($content);
    exit;
}
?>