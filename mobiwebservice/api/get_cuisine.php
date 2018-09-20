<?php
include 'config.php';
include ('function.php');
$cuisines = array();
$query = "select Cuisine_ID,Cuisine_Name from tbl_cuisine where Cuisine_Status='1' group by Cuisine_Name order by Cuisine_ID asc";
$result = mysqli_query($con,$query) or die(mysqli_error($con));
$count = mysqli_num_rows($result);
if($count > 0) 
{
    $i = 0;
    while ($row = mysqli_fetch_assoc($result)) {
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