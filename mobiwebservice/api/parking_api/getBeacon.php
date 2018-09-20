<?php
error_reporting(0);
header('Content-Type: application/json');
include ('../config.php');
include ('../function.php');
$beacon_id=sanitize($_REQUEST['beacon_id']);
$beacon_major=sanitize($_REQUEST['beacon_major']);
$beacon_minor=sanitize($_REQUEST['beacon_minor']);

if(isset($beacon_id) && !empty($beacon_id) && isset($beacon_major) && !empty($beacon_major) && isset($beacon_minor) && !empty($beacon_minor))
{
  $sql="SELECT * FROM beacon WHERE Beacon_ID='".trim($beacon_id)."' AND Beacon_Major='".trim($beacon_major)."' AND Beacon_Minor='".trim($beacon_minor)."'";
  $res = mysqli_query($con,$sql);
  $count = mysqli_num_rows($res);
  if($count > 0){
    $info=mysqli_fetch_assoc($res);
    $content = array("status" => 1,"data" => $info);
    echo json_encode($content);
    exit;
  }else{
      $content = array("status" => 0,"response" => array(), "message" => 'No Records Found');
      echo json_encode($content);
      exit;
  }
}else{
  $content = array("status" => 0,"response" => ERROR, "message" => 'No Records Found');
  echo json_encode($content);
  exit;
}



?>
