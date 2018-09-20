<?php
ini_set('display_errors', 1);
error_reporting(1);
header('Content-Type: application/json; charset=utf-8');
include("config.php");
include("model/common.class.php");

class stopService {

    var $dbMsg;
    private $parking_id;

    function __construct() {
        $this->dbMsg = "Something went wrong. Please try again.";
        $commonCls = new CommonClass();
        $this->fields_val = $commonCls->fnAssignval();
        $this->parking_id = isset($this->fields_val['parking_id']) ? $this->fields_val['parking_id'] : '';
        if ($this->parking_id == "") {
            echo $this->fnAuthInvalidParam();
            exit();
        }
        $res = $this->fnGetStopsDetail($this->parking_id);
    }

    function __destruct() {
        unset($this);
    }

    function fnAuthInvalidParam() {
        ob_end_clean();
        $content = array("status" => "0", "response" => "error", "message" => 'Invalid Parameters');
        return json_encode($content);
    }

    function fnGetStopsDetail($parking_id) {
        GLOBAL $con;

        /* Drivers - Start */
        $stop_array = $this->getStopsInfo($parking_id);
        /* Drivers - End */

        ob_end_clean();
        $info = array();
        $info['parking_id'] = $parking_id;
        $info['stops'] = $stop_array;

        $content = array("status" => 1, "data" => $info);
        mysqli_close($con);
        jsonResponse($content);
    }

    function getStopsInfo($parking_id){
        GLOBAL $con;
        $stops = array();

        $sqlDriverQuery = "SELECT * FROM `tbl_shuttle_parkinglot_drivers` where SPD_ParkingLotID = '".$parking_id."' and SPD_IsActive = '1' group by SPD_DLG_DriverTripLogID";
        $sqlDriverResult = mysqli_query($con, $sqlDriverQuery);
        $countDriverResult = mysqli_num_rows($sqlDriverResult);
        if ($countDriverResult > 0) {
            $j = 0;
            while ($results = mysqli_fetch_assoc($sqlDriverResult)) {
                    $sqlDriverSubQuery = "SELECT tsts.*, tsdt.DLG_TripName,tsdt.DLG_Phone, tspd.SPD_DVR_DriverID FROM `tbl_shuttle_parkinglot_drivers` tspd "
                        . " INNER JOIN tbl_shuttle_trip_stops tsts ON tspd.SPD_DLG_DriverTripLogID = tsts.DTP_DLG_DriverTripLogID "
                        . " INNER JOIN tbl_shuttle_driver_trip tsdt ON tsdt.DLG_DriverTripLogID = tsts.DTP_DLG_DriverTripLogID "
                        . " WHERE tspd.SPD_ParkingLotID = '".$parking_id."' and tspd.SPD_DLG_DriverTripLogID = '".$results['SPD_DLG_DriverTripLogID']."' and tspd.SPD_IsActive = '1' Group by tsts.DTP_TripStopID";
                       
                    $sqlDriverSubResult = mysqli_query($con, $sqlDriverSubQuery);
                    $countDriverSubResult = mysqli_num_rows($sqlDriverSubResult);
                    if ($countDriverSubResult > 0) {
                        $stops_new = array();
                        while ($subResults = mysqli_fetch_assoc($sqlDriverSubResult)) {
                            $stops_new[] = $subResults;
                        }
                        if(!empty($stops_new)){
                            $stops[$j]['SPD_DLG_DriverTripLogID'] = $results['SPD_DLG_DriverTripLogID'];
                            $stops[$j]['DLG_Phone'] = !empty($stops_new[0]['DLG_Phone'])?$stops_new[0]['DLG_Phone']:'';
                            $stops[$j]['DLG_TripName'] = !empty($stops_new[0]['DLG_TripName'])?$stops_new[0]['DLG_TripName']:'';
                            $stops[$j]['totalStopsCount'] = (string)$countDriverSubResult;
                            $stops[$j]['StopDetails'] = $this->setPreviousNextStopIDs($stops_new);
                            $j++;
                        }
                    }
            }
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