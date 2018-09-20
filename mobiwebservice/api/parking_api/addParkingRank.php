<?php
error_reporting(1);
header('Content-Type: application/json');
include ('../config.php');
include ('../function.php');
GLOBAL $con;
$data = json_decode(file_get_contents('php://input'), TRUE);


if(isset($data['data']['user_id']) && !empty($data['data']['user_id'])){
  $park_id=sanitize($data['data']['parking_id']);
  $user_id=sanitize($data['data']['user_id']);
  $owner_id=sanitize($data['data']['owner_id']);
	$rating=sanitize($data['data']['rating']);
  if(!isset($owner_id) && empty($owner_id)){
    $owner_id=0;
  }
  if(!isset($rating) && empty($rating))
    $rating=0;
  $sql="SELECT * FROM tbl_reviews WHERE user_id='".$user_id."'  AND list_id=".$park_id." AND R_Type='Parking' ORDER BY Review_ID DESC LIMIT 1";
  $exe=mysqli_query($con,$sql);
  if($user_id>0) {
    $num=mysqli_num_rows($exe);
    $res=mysqli_fetch_array($exe);
  }
  if($num>0)
    $sql="UPDATE tbl_reviews SET txt0=".$rating.",txt1=".$rating.",txt2=".$rating.",txt3=".$rating.",txt4=".$rating.",txt5=".$rating.",txt6=".$rating.",Average=".$rating.",Owner_ID=".$owner_id." WHERE Review_ID=".$res['Review_ID'];
  else
    $sql = "INSERT into tbl_reviews(user_id,list_id,R_Type,txt0,txt1,txt2,txt3,txt4,txt5,txt6,Average,status,Date_Created,Owner_ID) VALUES('".$user_id."','".$park_id."','Parking','".$rating."','".$rating."','".$rating."','".$rating."','".$rating."','".$rating."','".$rating."','".$rating."',1, now(),'".$owner_id."')";
  //echo $sql ;die;
  $rec=mysqli_query($con,$sql);
  $info=array();
  $info['Total_Rating']=getAllAverageRatings($park_id,'Parking');
  $info['My_Rating']=getAllAverageRatings($park_id,'Parking',$user_id);
  $content = array("status" => "1", "message"=> 'Rank added successfully',"data"=>$info);
  echo $json = json_encode($content);
  exit;
}else{
  $content = array("response" => ERROR, "message" => 'Bad Request');
  echo json_encode($content);
  exit;
}

 ?>
