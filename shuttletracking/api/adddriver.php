<?php   
        header('Content-Type: application/json; charset=utf-8');
        GLOBAL $EncryptKey;
        error_reporting(E_ALL);
        error_reporting(1);
        include('../config.php');
        include('function.php');       
        $EncryptKey = ENCRYPTKEY;
	$data = json_decode(file_get_contents('php://input'), TRUE);           
        // print_r($data['address']['country']['countryName']); exit;
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
	$createdUserID = 0;
	$createdDate = date('Y-m-d H:i:s');
	$active = '1';
        if(isset($data['birthDate']) && $data['birthDate'] != ''){
             $birthDate = sanitize(date('Y-m-d H:i:s',strtotime($data['birthDate'])));
        }else{
             $birthDate = 'NULL';
        }
        if($email != ''){
            $reguser=explode("@",$email);
	    $aliasemail =$reguser[0].$users."@members.way.com";
        }else{
            $aliasemail = '';
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
                     if($username == '' && $email == ''){                                
                                $output=array("status"=>"0","message"=>"Username and email required.");
				echo json_encode($output);
				exit;
                     }else{    
                                 $NUM_ROW = getUsersBYemail($email);
				 if($NUM_ROW > 0)
				 {
					        
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
				                $chksum = checksum();
						$sql ="insert into tbl_registeration set firstName='".$firstName."',
                                                                                         lastName='".$lastName."',
                                                                                         display_name='".$username."',  
                                                                                         email_add='".$email."',
                                                                                         alias_email='".$aliasemail."',
                                                                                         street='".$street."',
                                                                                         city='".$city."',
                                                                                         state='".$state."',
                                                                                         country='".$country."',
                                                                                         zipcode='".$zipcode."',
                                                                                         gender='".$gen."',
                                                 encrypt_password=AES_ENCRYPT('".$password."','".$EncryptKey."'),
                                                                                         status='1',
                                                                                         mobile_phone='".$contact."',
                                                                                         cdate=now(),
                                                                                         checksum_register='".$chksum."',
                                                                                         birthday = '".$birthDate."'
                                                                                         ";
						$rec=mysqli_query($con, $sql);
						$id=mysqli_insert_id($con); 
						$output=array("status"=>"1","User"=>$id,"message"=>"Driver added successfully.","userId"=>$id);
						echo json_encode($output);
						exit;
				      }
                     }
                           
       } 
?>
