<?php
//include("../common/config.php");
//include("../include/functions.php"); 
include("config.php");
include("function.php"); 
if($_REQUEST['id'])
{
	$query="DELETE FROM tbl_cart WHERE Cart_ID=".$_REQUEST['id'];	
	$res = mysql_query($query);
	$sql="DELETE FROM tbl_cartsubitems WHERE Cart_ID=".$_REQUEST['id'];
	$result=mysql_query($sql);
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