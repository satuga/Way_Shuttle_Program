<?php  
        header('Content-Type: application/json; charset=utf-8');
        error_reporting(E_ALL);
        error_reporting(1);
        include('../config.php');
        include('function.php');
	$data = json_decode(file_get_contents('php://input'), TRUE);        
	$users = isset($data['userId'])?sanitize($data['userId']):'';        
	$firstName = isset($data['firstName'])?sanitize($data['firstName']):'';
        $lastName = isset($data['lastName'])?sanitize($data['lastName']):'';        
        $username = isset($data['username'])?sanitize($data['username']):'';        
        $email = isset($data['email'])?sanitize($data['email']):'';        
        $street = isset($data['address']['street'])?sanitize($data['address']['street']):'';
        $city = isset($data['address']['city']['cityName'])?sanitize($data['address']['city']['cityName']):'';
        $state = isset($data['address']['state']['stateName'])?sanitize($data['address']['state']['stateName']):'';
        $country = isset($data['address']['country']['countryName'])?sanitize($data['address']['country']['countryName']):'';
        $zipcode = isset($data['address']['zipcode']['zipcode'])?sanitize($data['address']['zipcode']['zipcode']):'';
        $contact = isset($data['contact']['contactValue'])?sanitize($data['contact']['contactValue']):''; 
	$password = isset($data['data']['password'])?sanitize($data['data']['password']):'welcome'; 
        $gender = isset($data['gender'])?sanitize($data['gender']):'';	
        if(isset($data['birthDate']) && $data['birthDate'] != ''){
             $birthDate = sanitize(date('Y-m-d H:i:s',strtotime($data['birthDate'])));
        }else{
             $birthDate = 'NULL';
        }  
        if($gender == 'M'){
            $gen = 'Male';
        }else if($gender == 'F'){
            $gen = 'Female';
        }
        if($users == '')
        { 
		$output=array("status"=>"0","message"=>"Missing required fields.");
		echo json_encode($output);
                exit;
        }else{                  
                $r_count = getUsersBYid($users);               
		if($r_count > 0)
		{  
		        GLOBAL $con;
			$query = "UPDATE tbl_registeration SET   firstName='".$firstName."',
                                                                                         lastName='".$lastName."',
                                                                                         display_name='".$username."',  
                                                                                         email_add='".$email."',
                                                                                         street='".$street."',
                                                                                         city='".$city."',
                                                                                         state='".$state."',
                                                                                         country='".$country."',
                                                                                         zipcode='".$zipcode."',
                                                                                         gender='".$gen."',
                                                                                         status='1',
                                                                                         mobile_phone='".$contact."',
                                                                                         cdate=now(),
                                                                                         birthday = '".$birthDate."'
                                                           WHERE id ='".$users."'";
			$res = mysqli_query($con, $query);
		        $output=array("status"=>"1","message"=>"Driver updated successfully.","userId"=>$users);
                        echo json_encode($output);
		        exit; 
		}else{ 
                    	$output=array("status"=>"0","message"=>"Driver not existing.");
			echo json_encode($output);
		        exit;
		}
        } 
?>
