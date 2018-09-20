<?php

error_reporting(1);
header('Content-Type: application/json; charset=utf-8');
include("config.php");
include("model/common.class.php");

class parkingService {

    var $dbMsg;
    private $parking_id;

    function __construct() {
       $this->dbMsg = "Something went wrong. Please try again.";
       $commonCls = new CommonClass();
       $this->fields_val = $commonCls->fnAssignval();        
       $parkid = isset($this->fields_val['parking_id']) ? $this->fields_val['parking_id'] : '';
        $parkingid = $this->CheckParkingMapping($parkid);
        if ($parkingid <= "0") {            
            echo $this->fnAuthInvalidParam1();
            $this->parking_id = '';
           exit();
        }else{
            $this->parking_id = $parkingid;
        }
        
       if ($this->parking_id == "") {
           echo $this->fnAuthInvalidParam();
           exit();
       }
       $res = $this->fnGetDriverDetails($this->parking_id);
   }

   function CheckParkingMapping($map_userID)
   {  
       GLOBAL $con;
       $query = mysqli_query($con,"select `Legacy_ID`,`LST_ListingID` from tbl_OldNewParkingLotIDMap where LST_ListingID =".$map_userID);    
      $num = mysqli_num_rows($query);
       if($num > 0){
           $results = mysqli_fetch_assoc($query);
            $String = preg_replace("/[^0-9,.]/", "", $results['Legacy_ID']);
           return $String;
       }else{  
          return $num;
       }
       
  }

    function __destruct() {
        unset($this);
    }

    function fnAuthInvalidParam() {
        ob_end_clean();
        $content = array("status" => "0", "response" => "error", "message" => 'Invalid Parameters');
        return json_encode($content);
    }

    function fnGetDriverDetails($parking_id) {
        GLOBAL $con;
        
        /* Drivers - Start */
        $driver_array = $this->getDriverInfo($parking_id);
        /* Drivers - End */

        ob_end_clean();
        $info = array();
        $info['parking_id'] = $parking_id;
        $info['drivers'] = $driver_array;
        
        $content = array("status" => 1, "data" => $info);
        jsonResponse($content);
    }
    
    function getDriverInfo($parking_id){
        GLOBAL $con;
        $drivers = array();
        $sqlDriverQuery = "SELECT tsd.* FROM tbl_shuttle_parkinglot_drivers AS tspd LEFT JOIN tbl_shuttle_drivers AS tsd ON tsd.DVR_DriverID = tspd.SPD_DVR_DriverID where tspd.SPD_ParkingLotID = '".$parking_id."' AND tspd.SPD_IsActive = '1' ORDER BY tspd.SPD_CreatedDate DESC";
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

$parking = new parkingService();
?>
