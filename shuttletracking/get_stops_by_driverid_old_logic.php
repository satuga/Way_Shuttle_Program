<?php

error_reporting(1);
header('Content-Type: application/json; charset=utf-8');
include("config.php");
include("model/common.class.php");

class stopService {

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
        $res = $this->fnGetStopsDetail($this->driver_id);
    }

    function __destruct() {
        unset($this);
    }

    function fnAuthInvalidParam() {
        ob_end_clean();
        $content = array("status" => "0", "response" => "error", "message" => 'Invalid Parameters');
        return json_encode($content);
    }

    function fnGetStopsDetail($driver_id) {
        GLOBAL $con;
        
        /* Drivers - Start */
        $stop_array = $this->getStopsInfo($driver_id);
        /* Drivers - End */

        ob_end_clean();
        $info = array();
        $info['driver_id'] = $driver_id;
        $info['stops'] = $stop_array;
        $content = array("status" => 1, "data" => $info);
        jsonResponse($content);
    }
    
    function getStopsInfo($driver_id){
        GLOBAL $con;
        $stops = array();
        $sqlParkingQuery = "SELECT GROUP_CONCAT(tspd.SPD_ParkingLotID) as SPD_ParkingLotIDs, tspd.* FROM `tbl_shuttle_parkinglot_drivers` tspd "
                . " Left join tbl_shuttle_drivers tsd ON tsd.DVR_DriverID = tspd.SPD_DVR_DriverID "
                . " Left join tbl_shuttle_driver_trip tsdt ON tsdt.DLG_DriverTripLogID = tspd.SPD_DLG_DriverTripLogID "
                //. " Left join tbl_shuttle_trip_stops tsts ON tsts.DTP_DLG_DriverTripLogID = tspd.SPD_DLG_DriverTripLogID "
                . " Where tspd.SPD_DVR_DriverID = '".$driver_id."' AND tspd.SPD_IsActive "
                . " GROUP BY tspd.SPD_DLG_DriverTripLogID" ;
        $sqlParkingResult = mysqli_query($con, $sqlParkingQuery);
        $countParkingResult = mysqli_num_rows($sqlParkingResult);
        if ($countParkingResult > 0) {
            $i = 0;
            while ($parkingResults = mysqli_fetch_assoc($sqlParkingResult)) {
                $sqlDriverQuery = "SELECT tsts.*, tsdt.DLG_TripName FROM tbl_shuttle_driver_trip tsdt INNER JOIN tbl_shuttle_trip_stops tsts ON tsts.DTP_DLG_DriverTripLogID = tsdt.DLG_DriverTripLogID where tsdt.DLG_DriverTripLogID = '".$parkingResults['SPD_DLG_DriverTripLogID']."'";
                $sqlDriverResult = mysqli_query($con, $sqlDriverQuery);
                $countDriverResult = mysqli_num_rows($sqlDriverResult);
                if ($countDriverResult > 0) {
                    while ($results = mysqli_fetch_assoc($sqlDriverResult)) {
                        $stops[$i] = $results;
                        $stops[$i]['SPD_ParkingLotIDs'] = $parkingResults['SPD_ParkingLotIDs'];
                        $i++;
                    }
                }
            }
        }
        if(!empty($stops)){
            $stops = $this->setPreviousNextStopIDs($stops);
        }
        
        return $stops;
    }
    function setPreviousNextStopIDs($stops){
        $stops_new = $stops;
        $i = 1;
        foreach ($stops_new as $stopk => $stop){
            $totalArrayKeys = count($stops);
            if($i == 1){
                $stops[$stopk]['DTP_TripPreviousStopID'] = $this->getLastKey($totalArrayKeys, $stops);
                $nextArray = $this->getNextKey($stopk, $stops);
                $stops[$stopk]['DTP_TripNextStopID'] = $nextArray;
            } elseif($totalArrayKeys == $i) {
                $previousArray = $this->getPrevKey($stopk, $stops);
                $stops[$stopk]['DTP_TripPreviousStopID'] = $previousArray;
                $nextArray = $this->getFirstKey(0, $stops);
                $stops[$stopk]['DTP_TripNextStopID'] = $nextArray;
            } else {
                $previousArray = $this->getPrevKey($stopk, $stops);
                $stops[$stopk]['DTP_TripPreviousStopID'] = $previousArray;
                $nextArray = $this->getNextKey($stopk, $stops);
                $stops[$stopk]['DTP_TripNextStopID'] = $nextArray;
            }
            $i++;
        }
        return $stops;
    }
    function getPrevKey($key, $hash = array())
    {
        $keys = array_keys($hash);
        $found_index = array_search($key, $keys);
        if ($found_index === false)
            return false;
        
        return $hash[$found_index-1]['DTP_TripStopID'];
    }
    
    function getNextKey($key, $hash = array())
    {
        if(count($hash) == 1){
            return "1";
        } else {
            $keys = array_keys($hash);
            $found_index = array_search($key, $keys);
            if ($found_index === false)
                return false;

            return $hash[$found_index+1]['DTP_TripStopID'];
        }
    }
    
    function getLastKey($totalArrayKeys, $hash)
    {
        $totalArrayKeys = $totalArrayKeys-1;
        return $hash[$totalArrayKeys]['DTP_TripStopID'];
    }
    
    function getFirstKey($totalArrayKeys, $hash)
    {
        return $hash[$totalArrayKeys]['DTP_TripStopID'];
    }
}

$stop = new stopService();
?>
