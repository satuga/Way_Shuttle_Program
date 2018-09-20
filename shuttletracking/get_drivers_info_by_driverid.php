<?php

error_reporting(1);
header('Content-Type: application/json; charset=utf-8');
include("config.php");
include("model/common.class.php");

class driverService {

    var $dbMsg;
    private $driver_id;

    function __construct() {
        $this->dbMsg = "Something went wrong. Please try again.";
        $commonCls = new CommonClass();
        $this->fields_val = $commonCls->fnAssignval();
        $this->driver_id = isset($this->fields_val['driver_id']) ? $this->fields_val['driver_id'] : '';
        if ($this->driver_id == "") {
            echo $this->fnAuthInvalidParam();
            exit();
        }
        $res = $this->fnGetDriverDetails($this->driver_id);
    }

    function __destruct() {
        unset($this);
    }

    function fnAuthInvalidParam() {
        ob_end_clean();
        $content = array("status" => "0", "response" => "error", "message" => 'Invalid Parameters');
        return json_encode($content);
    }

    function fnGetDriverDetails($driver_id) {
        GLOBAL $con, $geoFancingRange;

        /* Drivers - Start */
        $driver_array = $this->getDriverInfo($driver_id);
        /* Drivers - End */

        ob_end_clean();
        $info = array();
        $info['driver_id'] = $driver_id;
		$info['geo_fencing_range'] = $geoFancingRange;
        $info['DVR_driver_idle_time'] = !empty($driver_array['0']['DVR_driver_idle_time'])?$driver_array['0']['DVR_driver_idle_time']:"10";
        $info['drivers'] = $driver_array;
        $content = array("status" => 1, "data" => $info);
        jsonResponse($content);
    }

    function getDriverInfo($driver_id){
        GLOBAL $con;
        $drivers = array();
        $sqlDriverQuery = "SELECT tspd.SPD_ParkingLotID as Parking_id, tspd.SPD_breaktime, tsd.* FROM tbl_shuttle_parkinglot_drivers AS tspd LEFT JOIN tbl_shuttle_drivers AS tsd ON tsd.DVR_DriverID = tspd.SPD_DVR_DriverID where tsd.DVR_DriverID = '".$driver_id."' AND tspd.SPD_IsActive = '1' ORDER BY tspd.SPD_CreatedDate DESC";
        $sqlDriverResult = mysqli_query($con, $sqlDriverQuery);
        $countDriverResult = mysqli_num_rows($sqlDriverResult);
        if ($countDriverResult > 0) {
            while ($results = mysqli_fetch_assoc($sqlDriverResult)) {
                $drivers[] = $results;
            }
        }
        return $drivers;
    }
}

$driver = new driverService();
?>
