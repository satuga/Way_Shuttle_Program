<?php 
        header('Content-Type: application/json; charset=utf-8');
        GLOBAL $EncryptKey;
        error_reporting(E_ALL);
        error_reporting(1);
        include('../config.php');
        include('function.php');	
	$users = isset($_GET['userid'])?sanitize($_GET['userid']):'';  
        if($users == '')
        {
		$output=array("status"=>"0","message"=>"Missing required fields.");
		echo json_encode($output);
                exit;
        }else{  
                $c_count = getUsersBYid($users);   
		if($c_count > 0 )
		{  	
                        deleteDriver($users);			        
		        $output=array("status"=>"1","message"=>"Driver deleted successfully.","userId"=>$users);
			echo json_encode($output);
		        exit; 
		}else{ 
                    	$output=array("status"=>"0","message"=>"Driver not existing.");
			echo json_encode($output);
		        exit;
		}
        } 
?>
