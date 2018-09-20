<?php
include("config.php");
if(isset($_REQUEST['Cart_ID']) && isset($_REQUEST['quantity']))
{
    $quantity=$_REQUEST['quantity'];
    $Cart_ID=$_REQUEST['Cart_ID'];
    $query="UPDATE tbl_cart SET Cart_Quantity=$quantity where Cart_ID=$Cart_ID";
	$res = mysql_query($query);
	if($res){
	   $content=array("response"=>"success","message"=>"success");
       echo json_encode($content);
       exit;
	}
    else{
       $content=array("response"=>"error","message"=>"error");
       echo json_encode($content);
       exit;
    }
}
else{
    $content=array("response"=>"error","message"=>"parameter missing");
       echo json_encode($content);
       exit;
}

?>