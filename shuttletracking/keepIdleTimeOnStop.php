<?php

header('Content-Type: application/json; charset=utf-8');
include 'config.php';
//include 'function.php';

define("SUCCESS","success");
define("SUCCESS_MSG","Success");
define("PARAMETER_CODE","error");
define("PARAMETER_MSG","Parameter Missing");
define("ERROR","error");
define("ERROR_MSG","Something Went Wrong");

$data = json_decode(file_get_contents('php://input'), TRUE);

$ITS_SPD_ParkingLotID = isset($data['data']['parkingid']) ? $data['data']['parkingid'] : '';
$ITS_DTP_TripStopID = isset($data['data']['tripstopiD']) ? $data['data']['tripstopiD'] : '';
$ITS_SPD_DLG_DriverTripLogID = isset($data['data']['tripid']) ? $data['data']['tripid'] : '';
$ITS_SPD_DVR_DriverID = isset($data['data']['driverid']) ? $data['data']['driverid'] : '';
$ITS_Idle_Time = isset($data['data']['idletime']) ? $data['data']['idletime'] : '';
$itsid = isset($data['data']['itsid']) ? $data['data']['itsid'] : '';
$paramCondition = '';
if (empty($itsid)) {
    $paramCondition = !empty($ITS_SPD_ParkingLotID) && !empty($ITS_DTP_TripStopID) 
                   && !empty($ITS_SPD_DLG_DriverTripLogID) && !empty($ITS_SPD_DVR_DriverID);
} else {
    $paramCondition = !empty($itsid);
}

if (!empty($paramCondition)) {
    if (empty($itsid)) {
        $sql = "insert into tbl_shuttle_idle_time_on_stop set "
                . " ITS_SPD_ParkingLotID='" . $ITS_SPD_ParkingLotID . "', "
                . " ITS_DTP_TripStopID='" . $ITS_DTP_TripStopID . "', "
                . " ITS_SPD_DLG_DriverTripLogID='" . $ITS_SPD_DLG_DriverTripLogID . "', "
                . " ITS_SPD_DVR_DriverID='" . $ITS_SPD_DVR_DriverID . "', "
                . " ITS_CreatedDate='" . date('Y-m-d H:i:s') . "'";
        $rec = mysqli_query($con, $sql);
        $stopLatestinsertedid = mysqli_insert_id($con);
    } else {
        $sqlIdleQuery = "SELECT * FROM `tbl_shuttle_idle_time_on_stop` where ITS_ID = '".$itsid."'";
        $sqlIdleResult = mysqli_query($con, $sqlIdleQuery);
        $countIdleResult = mysqli_num_rows($sqlIdleResult);
        if ($countIdleResult > 0) {
            $results = mysqli_fetch_assoc($sqlIdleResult);
            $createdTime = $results['ITS_CreatedDate'];
            $updatedTime = date('Y-m-d H:i:s');
            $ITS_Idle_Time   = getMinutes($createdTime, $updatedTime);
            
            $sql = "update tbl_shuttle_idle_time_on_stop set "
                    . " ITS_Idle_Time='" . $ITS_Idle_Time . "', "
                    . " ITS_LastUpdatedDate='" . date('Y-m-d H:i:s') . "' "
                    . " where ITS_ID=" . $itsid;
            $rec = mysqli_query($con, $sql);
        }
        $stopLatestinsertedid = $itsid;
    }
    if ($rec) {
        $content = array("response" => SUCCESS, "status" => "1", "message" => SUCCESS, "itsid" => (string)$stopLatestinsertedid);
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