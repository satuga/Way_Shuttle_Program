<?php
header("Content-Type : Application/JSON");
$id = sanitize($_REQUEST['id']);

if ($id == "" || $id < 0) {
    $output = array("status" => 0, "message" => "id is required");
    echo json_encode($output);
    exit;

} else {
    include ("../common/config.php");
    include ("../include/functions.php");
    require_once ('../Yelp/v2/php/lib/OAuth.php');
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
    // yelp
    $rest = str_replace("!", "", str_replace(" ", "-", str_replace("&", "and",
        str_replace(".", "", str_replace("`", "", str_replace("'", "", str_replace("(",
        "", str_replace(")", "", GetRestaurantname($id)))))))));
    $restcity = str_replace(" ", "-", GetRestaurantCity($id));
    $link = $rest . "-" . $restcity;
    // For example, request business with id 'the-waterboy-sacramento'
    $unsigned_url = "https://api.yelp.com/v2/business/" . trim($link, '-');

    // Build OAuth Request using the OAuth PHP library. Uses the consumer and token object created above.
    $oauthrequest = OAuthRequest::from_consumer_and_token($consumer, $token, 'GET',
        $unsigned_url);

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
    $reviews = json_decode($data, true);
    if (sizeof($reviews)) {
        $data = array();
        $data['rating'] = $reviews['rating'];
        $data['review_count'] = $reviews['review_count'];
        $data['reviews'] = $reviews['reviews'];
        $output = array("status" => 1, "review" => $data);
        echo json_encode($output);
        exit;
    } else {
        $output = array("status" => 0, "message" => "No Reviews found");
        echo json_encode($output);
        exit;
    }
}
?>