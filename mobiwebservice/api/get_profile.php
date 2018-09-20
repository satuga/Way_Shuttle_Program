<?php
//***getting User Profile
header('Content-Type: application/json');
$id = sanitize($_REQUEST['user_id']);
if ($id == "" || $id < 0) {
    $output = array("status" => 0, "message" => "User id is required");
    echo json_encode($output);
    exit;

}
else
{
  include ("config.php");
 include('function.php'); 
  GLOBAL $con;
  $query = "select * from tbl_registeration where id=".$id;
  $res = mysqli_query($con,$query);
  if (mysqli_num_rows($res))
  {
   while($rec = mysqli_fetch_assoc($res)){
	$rec1=array();
	$rec1['id']=$rec['id'];
	$rec1['firstname']=$rec['firstname'];
	$rec1['lastname']=$rec['lastname'];
	$rec1['mobile_number']=$rec['id'];
	$rec1['logo']=$rec['logo'];
	$rec1['email_add']=$rec['email_add'];
	$rec1['home_phone']=$rec['home_phone'];
	$rec1['work_phone']=$rec['_phone'];
	$data[]=$rec1;
   }
    $content=array("status" => "1","data"=>$rec1);
    echo json_encode($content);
    exit;
  }
  else {
    $output = array("status" => 0, "message" => "User does not exist");
    echo json_encode($output);
    exit;
  }
}
?>
