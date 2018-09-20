<?php
include("config.php");
 $quantity=sanitize($_REQUEST['quantity']);
 $Cart_ID=sanitize($_REQUEST['Cart_ID']);
if(isset($Cart_ID) && isset($quantity))
{
   
    $query="UPDATE tbl_cart SET Cart_Quantity=$quantity where Cart_ID=$Cart_ID";
	$res = mysqli_query($con,$query);
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