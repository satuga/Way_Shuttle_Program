<?php
include 'config.php';
include ('function.php');
$cuisines = array();
$query = "select Cuisine_ID,Cuisine_Name from tbl_cuisine where Cuisine_Status='1' order by Cuisine_ID asc";
$result = mysql_query($query) or die(mysql_error());
$count = mysql_num_rows($result);
if($count > 0) 
{
    $i = 0;
    while ($row = mysql_fetch_assoc($result)) {
        $cuisines[$i] = array('cuisine_id' => $row['Cuisine_ID'], 'cuisine_name' => $row['Cuisine_Name']);
        $i++;
    }
    $output=array("status"=>"1","cuisine"=>$cuisines);
    echo json_encode($output);
	exit;
}
else
{
	$output=array("status"=>"0","cuisine"=>"No cuisine found");
	echo json_encode($output);exit;
}
?>