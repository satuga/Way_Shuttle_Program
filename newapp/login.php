
<?php
header('Content-Type: application/json; charset=utf-8');
GLOBAL $EncryptKey;
error_reporting(E_ALL);
error_reporting(1);
include('./../../config.php');
include('./../function.php');

$parkingdata = loginAPI();
$park_result = json_decode($parkingdata);
if(!empty($park_result->response)){
    $park = $park_result->response;
    foreach ($park as $key => $value) {        
        CheckParkingMapping($value);
    }
}

function CheckParkingMapping($value)
   {  
            $parkID = $value->listingId;
            $listingName = $value->listingName;            
            $parkStatus = $value->listingStatus;
            $longitude = $value->longitude;
            $latitude = $value->latitude;         
            if($parkStatus == 'Active')
                $status = 1;
            else
                $status = 0;
            
            $newparkingId = isset($parkID)?sanitize($parkID):'';
            $parkingName = isset($listingName)?sanitize($listingName):'';
            $lat = isset($latitude)?sanitize($latitude):'';
            $long = isset($longitude)?sanitize($longitude):'';
            $createdUserID = 40;
            $createdDate = date('Y-m-d H:i:s');
            $active = $status;

            if($newparkingId == '')
        {
            $output=array("status"=>"0","message"=>"Missing new parking id.");
            echo json_encode($output);
            exit;
        } else if($parkingName == '')
        {
            $output=array("status"=>"0","message"=>"Missing parking name.");
            echo json_encode($output);
            exit;
        } else if($lat == '' || $long == '')
        {
            $output=array("status"=>"0","message"=>"Missing longitude or latitude.");
            echo json_encode($output);
            exit;
        }else{
            $parkingId = CheckParkingMapping($newparkingId);
            if (Empty($parkingId)) {
                        $sql ="insert into tbl_parkinglocations set Park_Name='".$parkingName."',
                                                                    Park_UserID='".$createdUserID."',
                                                                    lat='".$lat."',
                                                                    lon='".$long."'";
                        $rec=mysqli_query($con, $sql);
                        $id=mysqli_insert_id($con);
                        $sql1 ="insert into tbl_parking set P_Location='".$id."'";
                        $rec1=mysqli_query($con, $sql1);
                        $id=mysqli_insert_id($con);
                        $sql1 ="insert into tbl_OldNewParkingLotIDMap set Legacy_ID='PKLT".$id."',
                                                                    LST_ListingID='".$newparkingId."',
                                                                    LSt_ListingName='".$parkingName."'";
                        $rec1=mysqli_query($con, $sql1);
                        $output=array("status"=>"1","message"=>"Parking added successfully.","oldparkingId"=>$id);
                        echo json_encode($output);
                        exit;
            }else{
                        $sql ="update tbl_parkinglocations set Park_Name='".$parkingName."',
                                                                    Park_UserID='".$createdUserID."',
                                                                    lat='".$lat."',
                                                                    lon='".$long."' where Park_ID = '".$parkingId."'";
                        $rec=mysqli_query($con, $sql);
                        $id=mysqli_insert_id($con);

                        $sql1 ="update tbl_OldNewParkingLotIDMap set LSt_ListingName='".$parkingName."'
                                                                    where LST_ListingID = '".$newparkingId."'";

                        $output=array("status"=>"1","message"=>"Parking updated successfully.","oldparkingId"=>$parkingId);
                        echo json_encode($output);
                        exit;
            }

            
        }else{  
            echo 'No';
        }
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


function loginAPI(){

    $bearerToken = "d2F5X2NsaWVudDpzZWNyZXQ=";        
    $url = "http://45.55.110.142:8080/way-service/security/login";
    $headers = array( 
        //"accept: application/json, text/plain, *//*",
        //"accept-encoding: gzip, deflate, br",
        //"Content-Type: application/json;charset=UTF-8", 
        //"accept-language: en-US,en;q=0.8",
        "Authorization:Basic ".$bearerToken
    );  
    $ch = curl_init(); 
    curl_setopt($ch, CURLOPT_URL,$url); 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);  
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS,"grant_type=password&username=adminqa@way.com&password=panipuri");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true); 
    $output = curl_exec($ch); 
    $headers=array();
    $data=explode("\n",$output);
    $headers['status']=$data[0];
    array_shift($data);
    foreach($data as $part){
        $middle=explode(":",$part);
        $headers[trim($middle[0])] = trim($middle[1]);
    }
     
    if (curl_errno($ch)) { 
        print "Error: " . curl_error($ch); 
    } else { 
        $token = $headers['access_token'];   
        if($token){ 
            return ParkingAPI($token);
        }
    }   

}

function ParkingAPI($token){

    if($token){

            $parking_url = "http://45.55.110.142:8080/way-service/parkingListings/newAndModified?from=2017-10-25&to=2017-10-27&serviceName=Parking";

            $parking_headers = array( 
                //"accept: application/json, text/plain, *//*",
                //"accept-encoding: gzip, deflate, br",
                //"Content-Type: application/json;charset=UTF-8", 
                //"accept-language: en-US,en;q=0.8",
                "Authorization:Bearer ".$token
            );  

            $parking_ch = curl_init(); 
            curl_setopt($parking_ch, CURLOPT_URL,$parking_url); 
            curl_setopt($parking_ch, CURLOPT_RETURNTRANSFER, 1); 
            curl_setopt($parking_ch, CURLOPT_HTTPHEADER, $parking_headers);  
            curl_setopt($parking_ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($parking_ch, CURLOPT_SSL_VERIFYPEER, 0);

            $parking_data = curl_exec($parking_ch); 

            if (curl_errno($parking_ch)) { 
                print "Error: " . curl_error($parking_ch); 
            } else { 
                return $parking_data;
            } 

        } 
        curl_close($parking_ch); 
     
} 


?>

