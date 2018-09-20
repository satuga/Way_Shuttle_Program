<?php
//include("../common/config.php");
//include("../include/functions.php");
include("config.php");
include("function.php");
$id=isset($_REQUEST['id']) && $_REQUEST['id']!='' ? sanitize($_REQUEST['id']):'';
if($id!='' && $id>0)
{
	$query="DELETE FROM tbl_cart WHERE Cart_ID=".$id;
	$res = mysqli_query($con,$query);
	$sql="DELETE FROM tbl_cartsubitems WHERE Cart_ID=".$id;
	$result=mysqli_query($con,$sql);
	if($res && $result)
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
else
{
   $content=array("status"=>1,"message"=>PARAMETER_MSG);
   echo json_encode($content);
   exit;
}
?>
