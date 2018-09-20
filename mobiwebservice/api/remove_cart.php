<?php
//include("../common/config.php");
//include("../include/functions.php");
include("config.php");
include("function.php");
$device_id = sanitize($_REQUEST['device_id']);
$user_id = sanitize($_REQUEST['user_id']);
$flag=0;
if($user_id !="" || $device_id!=''){
	if($user_id !=""){
		 $sql="select * from tbl_cart where Cart_Type = 'Dine' and Cart_UserID='".$user_id."'";
	}
	else{
		$sql="select count(*) as total_cart from tbl_cart where Cart_Type = 'Dine' and Sess_ID='".$_device_id."'";
	}
	$res=mysqli_query($con,$sql);
	if (mysqli_num_rows($res))
	{
		while($rec=mysqli_fetch_array($res)){
			$query="DELETE FROM tbl_cart WHERE Cart_ID=".$rec['Cart_ID'];
			$res1 = mysqli_query($con,$query);
			$sql="DELETE FROM tbl_cartsubitems WHERE Cart_ID=".$rec['Cart_ID'];
			$result=mysqli_query($con,$sql);
			$flag=	$res1 && $result ? 1:0;
		}
	}
	else {
		$content=array("status"=>0,"message"=>"Your cart is empty");
		echo json_encode($content);
		exit;
	}
	if($flag==1)
	{
	  $content=array("status"=>1,"message"=>"success");
	  echo json_encode($content);
	  exit;
	}
	else
	{
		$content=array("status"=>0,"message"=>"error");
		echo json_encode($content);
		exit;
	}
}
else{
   $content=array("status"=>1,"message"=>PARAMETER_MSG);
   echo json_encode($content);
   exit;
}
?>
