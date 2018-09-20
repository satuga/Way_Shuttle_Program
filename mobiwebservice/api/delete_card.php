<?php
include('config.php');
include('function.php');

$data = json_decode(file_get_contents('php://input'), TRUE);
$id = sanitize($data['data']['card_id']);

$query="delete from tbl_creditcarddetails where Card_ID=".$id;
$res = mysqli_query($con,$query);

$output=array("status"=>"1","data"=>"Card Deleted Successfully");
echo json_encode($output);exit;

?>