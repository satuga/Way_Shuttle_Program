<?php
include ('config.php');
include ('function.php');

$user_id = $_REQUEST['id'];
if ($user_id == '')
{
    $content = array("status" => 0,"response" => ERROR, "message" => PARAMETER_MSG);
    echo json_encode($content);
    exit;
}
else
{
	$query = "select * from tbl_registeration where id='".$user_id."' and status = 1";
    $res = mysql_query($query);
	$count = mysql_num_rows($res);
    if ($count > 0)
	{
		$data = mysql_fetch_array($res);
        $content = array(
                "status" => 1,
                "message" => SUCCESS,
                "userid" => $data['id'],
                "firstname" => $data['firstname'],
                "Lastname" => $data['lastname'],
                "displayname" => $data['display_name'],
				"email" => $data['email_add'],
				"image" => $data['logo'],
				"school" => $data['school'],
				"gender" => $data['gender'],
				"city" => $data['city'],
				"state" => $data['state'],
				"home_phone" => $data['home_phone'],
				"work_phone" => $data['work_phone'],
				"mobile_phone" => $data['mobile_phone'],
				"about" => $data['MessageBody'],
				"birthday" => $data['birthday'],
				"push_notification" => $data['push_notification']
				);
        echo json_encode($content);
        exit;
    }
    else
    {
        $content = array("status" => 0,"message" => "No Records Found");
        echo json_encode($content);
        exit;
    }
}

?>