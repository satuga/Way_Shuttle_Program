<?php

error_reporting(0);
header('Content-Type: application/json');
include ('../config.php');
include ('../function.php');

$data = json_decode(file_get_contents('php://input'), TRUE);
$list_id = sanitize($data['data']['listid']);
$user_id = sanitize($data['data']['userid']);

if ($list_id == '') {
    $content = array("status" => "0", "response" => ERROR, "data" => PARAMETER_MSG);
    echo json_encode($content);
    exit;
} else {
    $query = "select reg.firstname,reg.lastname,reg.logo,rev.* from tbl_reviews as rev left join tbl_registeration as reg on rev.user_id = reg.id where rev.list_id ='" . $list_id . "' and rev.user_id ='" . $user_id . "' ORDER BY rev.Review_ID DESC";
    $res = mysqli_query($con, $query);
    $count = mysqli_num_rows($res);
    if ($count > 0) {
        while ($info = mysqli_fetch_assoc($res)) {
            $all[] = $info;
        }
        $content = array(
            "status" => "1",
            "response" => SUCCESS,
            "data" => $all,
        );
        echo json_encode($content);
        exit;
    } else {
        $content = array("status" => "0", "response" => ERROR, "message" => 'No Records Found');
        echo json_encode($content);
        exit;
    }
}
?>
