<?php
header('Content-Type: application/json');
include 'config.php';
include 'function.php';

$data = json_decode(file_get_contents('php://input'), TRUE);
$user_id = sanitize($data['data']['user_id']);
$status =isset($data['data']['status'])? sanitize($data['data']['status']):'';

if (isset($user_id) && isset($status)) {
    $sql = "update tbl_registeration set isFingerprint='".$status."' where id=" . $user_id;
    $rec = mysqli_query($con, $sql);
    if ($rec) {
        $content = array("response" => SUCCESS, "status" => "1", "message" => SUCCESS);
        echo json_encode($content);
        exit;
    } else {
        $content = array("response" => ERROR, "status" => "0", "message" => ERROR);
        echo json_encode($content);
        exit;
    }
} else {
    $content = array("response" => ERROR, "status" => "0", "message" => PARAMETER_MSG);
    echo json_encode($content);
    exit;
}
?>