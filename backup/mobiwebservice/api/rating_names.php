<?php
include 'config.php';
	$sql2="SELECT * FROM tbl_reviewlabel WHERE La_Cat=72 AND La_Status=1 LIMIT 5";
	$re2=mysqli_query($con,$sql2);
	$num2=mysqli_num_rows($re2);
    if($num2>0){
        while($row=mysqli_fetch_array($re2)){
            $name[]=$row['La_Name'];
        }
    }
    else{
        $name=array();
    }
    $content=array("status"=>1,"message"=>"success","names"=>$name);
    echo json_encode($content);
    exit;

?>