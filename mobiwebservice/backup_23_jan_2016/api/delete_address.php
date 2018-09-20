<?php
include('config.php');
include('function.php');

$data = json_decode(file_get_contents('php://input'), TRUE);
$id = $data['data']['address_id'];
$user_id = $data['data']['user_id'];

$query="delete from tbl_deliveryaddress where DE_ID='".$id."' and DE_UserID = '".$user_id."'";
$res = mysql_query($query);

$output=array("status"=>"1","data"=>"Address Deleted Successfully");
echo json_encode($output);exit;

?>