<?php  
       header('Content-Type: application/json; charset=utf-8');
       GLOBAL $EncryptKey;
       error_reporting(E_ALL);
       error_reporting(1);
       include('../config.php');
       include('function.php');      
       $users = isset($_REQUEST['userId'])?sanitize($_REQUEST['userId']):'';        
       $Reg_count = getUsersBYid($users);
       if($Reg_count > 0)
       {
               $c_count = getDriverSingleRecordBYuserid($users);
              if($c_count['id'] > 0 )
           {   
                  
               $driver_pic = $c_count['logo'];
               $DVR_UserID = $c_count['DVR_UserID'];
                if($driver_pic != ''){
                    $driver_pics = $Host_Path."admin/upload/users/".$driver_pic;
                }else{
                    $driver_pics = '';
                }
                
               $output=array("status"=>"1","Driver"=>"Yes","DriverImage"=>$driver_pics,"DriverId"=>$DVR_UserID);
               echo json_encode($output);
               exit;
           }else{
                   $output=array("status"=>"0","Driver"=>"No","data"=>"Customer.");
                   echo json_encode($output);
                   exit;    

                      }
                }else{
                   $output=array("status"=>"0","Driver"=>"No","msg"=>"User is not exits.");
                   echo json_encode($output);
                   exit;
               }
?>
