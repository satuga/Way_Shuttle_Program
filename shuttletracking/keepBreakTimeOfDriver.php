<?php

header('Content-Type: application/json; charset=utf-8');
include 'config.php';

define("SUCCESS","success");
define("SUCCESS_MSG","Success");
define("PARAMETER_CODE","error");
define("PARAMETER_MSG","Parameter Missing");
define("ERROR","error");
define("ERROR_MSG","Something Went Wrong");

$data = json_decode(file_get_contents('php://input'), TRUE);
$ITS_SPD_ParkingLotID = isset($data['data']['parkingid']) ? $data['data']['parkingid'] : '';
$ITS_SPD_DVR_DriverID = isset($data['data']['driverid']) ? $data['data']['driverid'] : '';
$SDL_BreakLogID = isset($data['data']['itsid']) ? $data['data']['itsid'] : '';

$paramCondition = '';
if (empty($SDL_BreakLogID)) {
    $paramCondition = !empty($ITS_SPD_ParkingLotID) && !empty($ITS_SPD_DVR_DriverID);
} else {
    $paramCondition = !empty($SDL_BreakLogID);
}

if ($paramCondition) {
    if (empty($SDL_BreakLogID)) {
        $sql = "insert into tbl_shuttle_driver_break_log set "
                . " SDL_DriverID='" . $ITS_SPD_ParkingLotID . "', "
                . " SDL_ParkingID='" . $ITS_SPD_DVR_DriverID . "', "
                . " SDL_BreakStartDateTime='" . date('Y-m-d H:i:s') . "'";
        $rec = mysqli_query($con, $sql);
        $stopLatestinsertedid = mysqli_insert_id($con);
    } else {
        $sqlIdleQuery = "SELECT * FROM `tbl_shuttle_driver_break_log` where SDL_BreakLogID = '".$SDL_BreakLogID."'";
        $sqlIdleResult = mysqli_query($con, $sqlIdleQuery);
        $countIdleResult = mysqli_num_rows($sqlIdleResult);
        if ($countIdleResult > 0) {
            $results = mysqli_fetch_assoc($sqlIdleResult);

            $sql = "update tbl_shuttle_driver_break_log set "
                    . " SDL_BreakEndDateTime='" . date('Y-m-d H:i:s') . "' "
                    . " where SDL_BreakLogID='" . $SDL_BreakLogID . "' " ;
            $rec = mysqli_query($con, $sql);
        }
        $stopLatestinsertedid = $SDL_BreakLogID;
    }
    if ($rec) {
        $content = array("response" => SUCCESS, "status" => "1", "message" => SUCCESS, "BreakLogID" => (string)$stopLatestinsertedid);
        echo json_encode($content);
        exit;
    } else {
        $content = array("response" => ERROR, "status" => "0", "message" => ERROR_MSG);
        echo json_encode($content);
        exit;
    }
} else {
    $content = array("response" => ERROR, "status" => "0", "message" => PARAMETER_MSG);
    echo json_encode($content);
    exit;
}

function getMinutes($createdTime, $updatedTime){
    //Convert it into a timestamp.
    $createdTime = strtotime($createdTime);

    //Get the current timestamp.
    $updatedTime = strtotime($updatedTime);

    //Calculate the difference.
    $difference = $updatedTime - $createdTime;

    //Convert seconds into minutes.
    return $minutes = floor($difference / 60 );
}

?>
