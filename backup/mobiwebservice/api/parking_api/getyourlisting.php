<?php
include ('config.php');
include ('function.php');
$data = json_decode(file_get_contents('php://input'), TRUE);
$userid = $data['data']['userid'];

if($userid == '')
{
	$content = array("response" => ERROR, "message" => PARAMETER_MSG);
    echo json_encode($content);
    exit;
}
else
{
	$query = "select l.*,p.* from tbl_parking as p left join tbl_parkinglocations as l on l.Park_ID = p.P_Location where P_UserID='".$userid."' order by p.P_ID desc";
    $res = mysql_query($query);
	$count = mysql_num_rows($res);
	if($count > 0)
    {
		while($info = mysql_fetch_assoc($res))
		{
			$all[] = $info;
		}

		$content = array(
                "response" => SUCCESS,
                "message" => SUCCESS,
                "userid" => $userid,
                "data" => $all,
               );
        echo json_encode($content);
        exit;
	}
	else
	{
		$content = array("response" => ERROR, "message" => 'No Records Found');
		echo json_encode($content);
		exit;
	}

}
?>
