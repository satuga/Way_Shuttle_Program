<?php
error_reporting(0);
header('Content-Type: application/json');
include ('../config.php');
include ('../function.php');

$data = json_decode(file_get_contents('php://input'), TRUE);
$list_id = sanitize($data['data']['listid']);
$pe_id =isset($data['data']['pe_id'])? sanitize($data['data']['pe_id']):'';
$fdate = sanitize($data['data']['fromdate']);
$tdate= sanitize($data['data']['todate']);
if($list_id == '')
{
	$content = array("status" => "0","response" => ERROR, "data" => PARAMETER_MSG);
    echo json_encode($content);
    exit;
}
else
{
$fdate=  $fdate!=''? $fdate : date("Y-m-d g:i A");
  $tdate= $tdate!=''? $tdate : date('Y-m-d g:i A',strtotime("+2 day",strtotime($fdate)));
  $parkingData=getParkingPriceAndTotal($flag=0,$fdate,$tdate,$list_id,$pe_id);
	$parking_types['price_rate']=$parkingData['price_rate'];
  $parking_types['min_reservation']=$parkingData['min_reservation'];
  $parking_types['price']=$parkingData['price'];
  $parking_types['subtotal']=$parkingData['subtotal'];
  $parking_types['tax']=$parkingData['tax'];
  $parking_types['overnight_fee']=$parkingData['overnight_fee'];
  $parking_types['total']=$parkingData['total'];
  $parking_types['diff']=$parkingData['days'];
  $parking_types['checkIn']=date('m d, Y g:i A',$parkingData['fdate']);
  $parking_types['checkOut']=date('m d, Y g:i A',$parkingData['tdate']);
  $all['parking_types']=!empty($parking_types) ? $parking_types : array();
  array_walk_recursive($all, function(&$item, $key) {
      if(is_string($item)) {
          $item = @strip_tags($item);

      }
  });
  $all=removeNull($all);
  $content = array("status" => "1", "data" => $all);
  echo $json = json_encode($content);
  exit;
}
 ?>
