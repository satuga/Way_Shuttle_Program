<?php
error_reporting(0);
header('Content-Type: application/json');
include ('../config.php');
include ('../function.php');

$data = json_decode(file_get_contents('php://input'), TRUE);
$list_id = sanitize($data['data']['listid']);

if ($list_id == '') {
    $content = array("status" => "0", "response" => ERROR, "data" => PARAMETER_MSG);
    echo json_encode($content);
    exit;
} else {
    $query = "select reg.firstname,reg.lastname,reg.logo,rev.* from tbl_reviews as rev left join tbl_registeration as reg on rev.user_id = reg.id where rev.list_id ='" . $list_id . "' AND rev.status=1 AND Review<>'' ORDER BY rev.Date_Created DESC";
    $res = mysqli_query($con, $query);
    $count = mysqli_num_rows($res);
    if ($count > 0) {
        while ($info = mysqli_fetch_assoc($res)) {
            if(!empty($info['firstname']) || !empty($info['lastname']) ){
            $all[] = $info;
        }
        }
        $content = array(
            "status" => "1",
            "response" => SUCCESS,
            "data" => $all,
        );
        $content = removeNull($content);
        echo json_encode($content);
        exit;
    } else {
        $content = array("status" => "0", "response" => ERROR, "message" => 'No Records Found');
        echo json_encode($content);
        exit;
    }
}
?>
