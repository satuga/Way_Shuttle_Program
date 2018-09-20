<?php
include 'config.php';
	$sql2="SELECT * FROM tbl_reviewlabel WHERE La_Cat=72 AND La_Status=1 LIMIT 5";
	$re2=mysql_query($sql2);
	$num2=mysql_num_rows($re2);
    if($num2>0){
        while($row=mysql_fetch_array($re2)){
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