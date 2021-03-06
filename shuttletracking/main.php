<?php

/**
 * File to handle api calls from pi component.
  Hashval - Z+zUrN0Xgp1rTH9zKTgzfQ0GJtwdORF4Bgqdpl4y+74=
  text - wayapppi
  encyption key - smartparking
  bit - 256 bit
 */
header('Content-Type: application/json');
include("config.php");

class mainService {

    var $dbMsg;

    function __construct() {
        GLOBAL $con;
        $flag = 0;
        $response = array();
        $message = '';
        $mainErrorMessage = 'Something went wrong here';
        $param = json_decode(file_get_contents('php://input'), TRUE);
        
        $_REQUEST['data']=$param['data'];
        $data=$param['data'];
        if (!empty($_REQUEST['method'])) {
                $parkingid = !empty($data['parkingid'])?$data['parkingid']:'';
                $parkingid = $this->CheckParkingMapping($parkingid);
                if ($parkingid <= "0") {
                $message = "No Mapping Parking Id";
                }else{
                    if($_REQUEST['method'] == 'getDriversByParkingID'){
                        if(empty($parkingid)) {
                            $message = "parkingid is required";
                        } else {
        		    
        		            $flag = 1;
        		            $response['driverInfo'] = $this->getDriversByParkingID($parkingid);
        		            if(empty($response['driverInfo'])){
        		                $message = "No driver available";
        		            }
                             
                        }
                    } else if($_REQUEST['method'] == 'updateDriverStatusByParkingID'){
                        $userid = !empty($data['userid'])?$data['userid']:'';
                        $parkingid = $this->CheckParkingMapping($data['parkingid']);
                        $driverStatus = isset($data['driverstatus'])?$data['driverstatus']:'';
                        if(empty($userid)) {
                            $message = "userid is required";
                        } else if(empty($parkingid)) {
                            $message = "parkingid is required";
                        } else if($driverStatus == '') {
                            $message = "driverstatus is required";
                        } else {
                            $flag = 1;
                            $response['driverId'] = $this->updateDriverStatusByParkingID($userid, $parkingid, $driverStatus);
                        }
                    } else {
                        $message = $mainErrorMessage;
                    }
            }

        } else {
            $message = $mainErrorMessage;
        }
        
        echo $this->finalOutput($flag, $response, $message);
    }

    function CheckParkingMapping($map_userID)
    {   
        GLOBAL $con;
        $query = mysqli_query($con,"select `Old_parkingID`,`LST_ListingID` from tbl_OldNewParkingLotIDMap where LST_ListingID =".$map_userID);    
        $num = mysqli_num_rows($query);
        if($num > 0){ 
            $results = mysqli_fetch_assoc($query);
            return $results['Old_parkingID'];
        }else{  
            return $num;
        } 
        
    }

    function getDriversByParkingID($parkingid) {
        GLOBAL $con;
        $response = array();
        $check_driver = mysqli_query($con,"select tsd.DVR_DriverID, tsd.DVR_ParkingID, tsd.DVR_UserID, tr.firstname, tr.lastname, tr.display_name, tr.email_add from tbl_shuttle_drivers tsd LEFT JOIN tbl_registeration tr ON tr.id = tsd.DVR_UserID where DVR_ParkingID = '".$parkingid."' and DVR_Status = '1'");
	$count = mysqli_num_rows($check_driver);
	if($count > 0)
	{
            $info = array();
            while($aRow=mysqli_fetch_assoc($check_driver))
            {
                    $info[] = $aRow;
            }
           $response = $info;
        }
        return $response;
    }

    function updateDriverStatusByParkingID($userid, $parkingid, $driverStatus) {
        GLOBAL $con;
        
        $response = array();
        $check_driver = mysqli_query($con,"select * from tbl_shuttledrivers tsd where DVR_ParkingID = '".$parkingid."' and DVR_UserID = '".$userid."'");
	$count = mysqli_num_rows($check_driver);
	if($count > 0){
           $sql="UPDATE tbl_shuttledrivers set DVR_Status = '".$driverStatus."' ,"
                                            . "DVR_LastUpdatedDate = NOW()"
                                            ." WHERE DVR_ParkingID = '".$parkingid."' and DVR_UserID = '".$userid."'";
           mysqli_query($con,$sql);
           $driversRecord = mysqli_fetch_assoc($check_driver);
           $response = $driversRecord['DVR_DriverID'];
        } else {
           $sql="INSERT INTO tbl_shuttledrivers(DVR_ParkingID, DVR_UserID, DVR_Status, DVR_CreatedDate, DVR_LastUpdatedDate)VALUES(".$parkingid.",'".$userid."','".$driverStatus."', Now(), Now())";
           mysqli_query($con,$sql);
           $DVR_ParkingID = mysqli_insert_id($con);
           $response = $DVR_ParkingID;
        }
        return $response;
    }

    function finalOutput($flag = 0, $response = array(), $message = '') {
        $retArr = array();
        
        /*Response Flag*/
        if ($flag == 1) {
            $retArr['status'] = $flag;
        } else {
            $retArr['error'] = $flag;
        }
        
        /*API Message*/
        if (!empty($message)) {
            $retArr['message'] = $message;
        }
        
        /*Response details*/
        if (!empty($response)) {
            $retArr['data'] = $response;
        }
        
        return json_encode($retArr);
    }

}

$main = new mainService();
