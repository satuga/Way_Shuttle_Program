<?php
error_reporting(1);
header('Content-Type: application/json');
include ('../config.php');
include ('../function.php');

//--------- Check the parameters coming from the API ------//
if(isset($_REQUEST['beacon_distance']) && isset($_REQUEST['park_id']) )
{
	$beacon_distance = sanitize($_REQUEST['beacon_distance']);
	$park_id = sanitize($_REQUEST['park_id']);
	$distance = explode(",",$beacon_distance);
	$beacon_distance_min = $distance[0];
	$beacon_distance_max = $distance[1];
	if($beacon_distance_min!='' && $park_id!='')
	{
		/*if($beacon_distance_min > $beacon_distance_max){
			$content=array("response"=>ERROR,"status"=>"0","message"=>"Minimum value could not greater than maximum value");
			echo json_encode($content);
			exit;
		}*/
		
		if(($beacon_distance_min >= 0 && $beacon_distance_min <= 100))
		{
			$sql = "select P_ID from tbl_parking where P_ID='".$park_id."'";
			$rec=mysqli_query($con,$sql);
			$num= mysqli_num_rows($rec);
			if($num>0){
				$sql ="update tbl_parkinglocations A INNER JOIN tbl_parking B ON A.Park_ID = B.P_Location set beacon_distance = '".$beacon_distance."' where P_ID ='".$park_id."'";
				$rec=mysqli_query($con,$sql);
				if($rec){
					$content=array("response"=>SUCCESS,"status"=>"1","message"=>"Beacon distance has been updated successfully");
					echo json_encode($content);
					exit;
				}
				else{
					$content=array("response"=>ERROR,"status"=>"0","message"=>"Something went to wrong");
					echo json_encode($content);
					exit;
				}
			}
			else{
				$content=array("response"=>ERROR,"status"=>"0","message"=>"We coundn't found parking");
				echo json_encode($content);
				exit;
			}

		}
		else{
			$content=array("response"=>ERROR,"status"=>"0","message"=>"Enter valid distance value between 0 to 100");
			echo json_encode($content);
			exit;
		}
	}
	else{
		$content=array("response"=>ERROR,"status"=>"0","message"=>PARAMETER_MSG);
		echo json_encode($content);
		exit;
	}
}
else{
	$content=array("response"=>ERROR,"status"=>"0","message"=>PARAMETER_MSG);
		echo json_encode($content);
		exit;
}
?>
