<?php
include('config.php');
include('function.php');

$data = json_decode(file_get_contents('php://input'), TRUE);
$id = sanitize($data['data']['address_id']);
$user_id = sanitize($data['data']['user_id']);

$query="delete from tbl_deliveryaddress where DE_ID='".$id."' and DE_UserID = '".$user_id."'";
$res = mysqli_query($con,$query);

$output=array("status"=>"1","data"=>"Address Deleted Successfully");
echo json_encode($output);exit;

?>