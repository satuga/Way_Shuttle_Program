<?php
//error_reporting(0);
include ('config.php');
include ('function.php');
$EncryptKey = ENCRYPTKEY;

$device_type = $_REQUEST['device_type'];
$access_token = $_REQUEST['access_token'];
$device_id = $_REQUEST['device_id'];

if (isset($_REQUEST['isfbuser']) == 1)
{
    $email = $_REQUEST['email'];
    $password = $_REQUEST['password'];
    $isfbuser = $_REQUEST['isfbuser'];
    $facebook_id = $_REQUEST['facebook_id'];
    $fullname=$_REQUEST['fullname'];
    $parameter = $email . ':' . $password . ':' . $facebook_id.':'.$fullname;
    $response = chk_parameter($parameter, 4);
}
else
{
    $email = $_REQUEST['email'];
    $password = $_REQUEST['password'];
    $parameter = $email . ':' . $password;
    $response = chk_parameter($parameter, 2);
	
}
if ($response == 0)
{
    $content = array("status" => "0","response" => ERROR, "message" => PARAMETER_MSG);
    echo json_encode($content);
    exit;
}
else
{
    if ($isfbuser == '1')
    {
        $query = "select * from tbl_registeration where email_add='" . $email ."' and encrypt_password='' and facebook='1' and facebook_id='".$facebook_id."'";
        $res = mysql_query($query);
        if (mysql_num_rows($res))
        {
            $update=mysql_query("update tbl_registeration set last_login=now() where email_add='" . $email ."' and encrypt_password='' and facebook='1' and facebook_id='".$facebook_id."'");
            $data = mysql_fetch_array($res);
			
            $fullname = $data['firstname'];
            $lastname = $data['lastname'];
            $school = $data['school'];
            $displayname = $data['display_name'];
            $email = $data['email_add'];
            $id = $data['id'];
            
            
            	//--------------- check device type and accesstoken for the user ---------------//
	            $sql_device="select * from tbl_user_device where user_id=".$id." AND access_token = '".$access_token."'";
	            $exe_device = mysql_query($sql_device);
	        	$num_device = mysql_num_rows($exe_device);
	        	if($num_device > 0)
	        	{
	        		
	        	}
	        	else 
	        	{
	        		$del_ext_devices = mysql_query("delete from tbl_user_device where access_token = '".$access_token."'");
	        		
	        		$ins_device = "insert into tbl_user_device(user_id,device_type,access_token,created_at) values('".$id."','".$device_type."','".$access_token."',now())";
        			mysql_query($ins_device);
	        	}
	        	//------------------------ End of the device token ---------------//
            
	        	$sql_add="select * from tbl_deliveryaddress where DE_UserID='".$id."' AND DE_default = '1'";
	            $exe_add = mysql_query($sql_add);
	        	$num_add = mysql_num_rows($exe_add);
	        	if($num_add > 0)
	        	{
	        		while($add1 = mysql_fetch_assoc($exe_add))
	        		{
	        			$address[] = $add1;
	        		}
	        		$address_all = array("DE_ID"=>$address[0]['DE_ID'],"DE_Name" => $address[0]['DE_Name'],"DE_Address"=>$address[0]['DE_Address'],"BLDG_No"=>$address[0]['BLDG_No'],"DE_State"=>$address[0]['DE_State'],"DE_City"=>$address[0]['DE_City'],"DE_Zipcode"=>$address[0]['DE_Zipcode'],"DE_Phone"=>$address[0]['DE_Phone'],"DE_lat"=>$address[0]['DE_lat'],"DE_long"=>$address[0]['DE_long'],"DE_default" =>$address[0]['DE_default']);
	        	}
	        	else 
	        	{
	        		$address_all = "";
	        	}
	        	
	        	
            $sql2="select * from tbl_creditcarddetails where Card_User_ID=".$id." AND Card_Type!='AMERICAN EXPRESS' ORDER BY Card_Type DESC";
            
	        $Cardexe=mysql_query($sql2);
	    	//$num2=mysql_num_rows($exe2);
	    	$Cardnum=mysql_num_rows($Cardexe);
	    	if($Cardnum >0)
	    	{
	    		while($res2=mysql_fetch_array($Cardexe)){
	            $card_array[]=array("Card_Type"=>$res2['Card_Type'],"CARD_NO"=>$res2['Card_Number'],"Card_Exp_Year"=>$res2['Card_Exp_Year'],
	                            "Card_Exp_Month"=>$res2['Card_Exp_Month'],"Card_FirstName"=>$res2['Card_FirstName'],"Card_Street"=>$res2['Card_Street'],
	                            "Card_State"=>$res2['Card_State'],"Card_City"=>$res2['Card_City'],"Card_Zip"=>$res2['Card_Zip']);
	            }                                
	    	}
	        else{
	            $card_array=array();
	        }
            
	        
        		/*--------------update the cart ------------------*/
	            if($device_id != '')
	            {
	            	$update_cart=mysql_query("update tbl_cart set Cart_UserID = '".$id."' where Sess_ID = '".$device_id."'");
            		//$data_cart = mysql_fetch_array($update_cart);
	            }
	        
            $content = array(
			    "status" => "1",
                "response" => SUCCESS,
                "message" => SUCCESS,
                "userid" => $id,
                "fullname" => $fullname,
				"school" => $school,
				"city" => $data['city'],
				"state" => $data['state'],
				"birthday" => $data['birthday'],
				"street" => $data['street'],
            	"zipcode" => $data['zipcode'],
            	"building" => $data['BLDG_No'],
            	"dine_driver" => $data['dine_driver'],
				"push_notification" => $data['push_notification'],
				"mobile_phone" => $data['mobile_phone'],
				"gender" => $data['gender'],
                "Lastname" => $lastname,
                "displayname" => $data['display_name'],
                "email" => $email,
                "default_address" => $address_all,
             	"logo" => $data['logo'],
                "card_array"=>$card_array);
            echo json_encode($content);
            
            
            
            /*$content = array(
				"status" => "1",
                "response" => SUCCESS,
                "message" => SUCCESS,
                "userid" => $id,
                "fullname" => $fullname,
                "Lastname" => $lastname,
                "displayname" => $data['display_name'],
                "email" => $email);
            echo json_encode($content);*/
            exit;
        }
        else
        {
             $chk = mysql_query("select id from tbl_registeration where email_add ='".$email ."' ");
   
			   // echo mysql_num_rows($chk);  
				/*if (mysql_num_rows($chk))
				{
					$content = array("response" => ERROR, "message" => "Email id " . $email .
							" already registered Please login with password");
					echo json_encode($content);
					exit;
				}*/
            $EncryptKey = ENCRYPTKEY;

        $chk_sum = checksum();
        $sql="insert into tbl_registeration (firstname,display_name,email_add,encrypt_password,cdate,checksum_register,status,facebook,facebook_id)values('".$fullname."','".$fullname."','".$email."','',now(),'".$chk_sum."','1','1','".$facebook_id."')";
            $res=mysql_query($sql);
        $id = mysql_insert_id();
      
       // Company table
        $sql11 = "insert into companies set id='" . $id . "',email='" . $email .
            "',joinedOn=now()";
        mysql_query($sql11);

        // Alias Email, Address
        $reguser = explode("@", $email);
        $aliasemail = $reguser[0] . $id . "@members.way.com";

        $sql = "UPDATE tbl_registeration SET alias_email='" . $aliasemail ."' ,last_login=now(),device_type = '".$device_type."',access_token = '".$access_token."' WHERE id =" . $id;
        mysql_query($sql);
        
        
        		//--------------- check device type and accesstoken for the user ---------------//
	            $sql_device="select * from tbl_user_device where user_id=".$id." AND access_token = '".$access_token."'";
	            $exe_device = mysql_query($sql_device);
	        	$num_device = mysql_num_rows($exe_device);
	        	if($num_device > 0)
	        	{
	        		
	        	}
	        	else 
	        	{
	        		$del_ext_devices = mysql_query("delete from tbl_user_device where access_token = '".$access_token."'");
	        		
	        		$ins_device = "insert into tbl_user_device(user_id,device_type,access_token,created_at) values('".$id."','".$device_type."','".$access_token."',now())";
        			mysql_query($ins_device);
	        	}
	        	//------------------------ End of the device token ---------------//
	        	
	        	
	        	//------------------------ End of the device token ---------------//
        		$sql_add="select * from tbl_deliveryaddress where DE_UserID='".$id."' AND DE_default = '1'";
	            $exe_add = mysql_query($sql_add);
	        	$num_add = mysql_num_rows($exe_add);
	        	if($num_add > 0)
	        	{
	        		while($add1 = mysql_fetch_assoc($exe_add))
	        		{
	        			$address[] = $add1;
	        		}
	        		$address_all = array("DE_ID"=>$address[0]['DE_ID'],"DE_Name" => $address[0]['DE_Name'],"DE_Address"=>$address[0]['DE_Address'],"BLDG_No"=>$address[0]['BLDG_No'],"DE_State"=>$address[0]['DE_State'],"DE_City"=>$address[0]['DE_City'],"DE_Zipcode"=>$address[0]['DE_Zipcode'],"DE_Phone"=>$address[0]['DE_Phone'],"DE_lat"=>$address[0]['DE_lat'],"DE_long"=>$address[0]['DE_long'],"DE_default" =>$address[0]['DE_default']);
	        	}
	        	else 
	        	{
	        		$address_all = "";
	        	}
	        	
	        	
        //**getting card details
        //$sql2="select *,AES_DECRYPT(Card_Number,'".$EncryptKey."') AS CARD_NO from tbl_creditcarddetails where Card_User_ID=".$id." AND Card_Type!='AMERICAN EXPRESS' ORDER BY Card_Type DESC";
    	$sql2="select * from tbl_creditcarddetails where Card_User_ID=".$id." AND Card_Type!='AMERICAN EXPRESS' ORDER BY Card_Type DESC";
        $exe2=mysql_query($sql2);
    	
    	$Cardexe=mysql_query($sql2);
    	$num2=mysql_num_rows($exe2);
    	$Cardnum=mysql_num_rows($Cardexe);
    	if($num2>0)
    	{
    		while($res2=mysql_fetch_array($exe2)){
            $card_array[]=array("Card_Type"=>$res2['Card_Type'],"CARD_NO"=>$res2['Card_Number'],"Card_Exp_Year"=>$res2['Card_Exp_Year'],
                            "Card_Exp_Month"=>$res2['Card_Exp_Month'],"Card_FirstName"=>$res2['Card_FirstName'],"Card_Street"=>$res2['Card_Street'],
                            "Card_State"=>$res2['Card_State'],"Card_City"=>$res2['Card_City'],"Card_Zip"=>$res2['Card_Zip']);
            }                                
    	}
        else{
            $card_array=array();
        }
        
        /*--------------update the cart ------------------*/
	            if($device_id != '')
	            {
	            	$update_cart=mysql_query("update tbl_cart set Cart_UserID = '".$id."' where Sess_ID = '".$device_id."'");
            		//$data_cart = mysql_fetch_array($update_cart);
	            }
        
        
        if ($id)
        {
            //Sendmail($email,$id,$chk_sum);
             $content = array(
			    "status" => "1",
                "response" => SUCCESS,
                "message" => SUCCESS,
                "userid" => $id,
                "fullname" => $fullname,
				"school" => $school,
				"city" => $data['city'],
				"state" => $data['state'],
				"birthday" => $data['birthday'],
				"street" => $data['street'],
             	"zipcode" => $data['zipcode'],
            	"building" => $data['BLDG_No'],
             	"dine_driver" => $data['dine_driver'],
				"push_notification" => $data['push_notification'],
				"mobile_phone" => $data['mobile_phone'],
				"gender" => $data['gender'],
                "Lastname" => $lastname,
                "displayname" => $data['display_name'],
                "email" => $email,
                "default_address" => $address_all,
             	"logo" => $data['logo'],
                "card_array"=>$card_array);
            echo json_encode($content);
            exit;
            
        }
        }
    }
    else
    {
        $query = "select * from tbl_registeration where email_add='".$email."' and encrypt_password=AES_ENCRYPT('".$password."','".ENCRYPTKEY."')";
        $res = mysql_query($query);
        if (mysql_num_rows($res))
        {
        	
    	
	        $data = mysql_fetch_array($res);
	        if($data['status'] == '1')
        	{
	            $fullname = $data['firstname'];
	            $lastname = $data['lastname'];
				$school = $data['school'];
	            $displayname = $data['display_name'];
	            $email = $data['email_add'];
	            $id = $data['id'];
	            
	            //--------------- check device type and accesstoken for the user ---------------//
	            $sql_device="select * from tbl_user_device where user_id=".$id." AND access_token = '".$access_token."'";
	            $exe_device = mysql_query($sql_device);
	        	$num_device = mysql_num_rows($exe_device);
	        	if($num_device > 0)
	        	{
	        		
	        	}
	        	else 
	        	{
	        		$del_ext_devices = mysql_query("delete from tbl_user_device where access_token = '".$access_token."'");
	        		
	        		$ins_device = "insert into tbl_user_device(user_id,device_type,access_token,created_at) values('".$id."','".$device_type."','".$access_token."',now())";
        			mysql_query($ins_device);
	        	}
	        	//------------------------ End of the device token ---------------//
        		$sql_add="select * from tbl_deliveryaddress where DE_UserID='".$id."' AND DE_default = '1'";
	            $exe_add = mysql_query($sql_add);
	        	$num_add = mysql_num_rows($exe_add);
	        	if($num_add > 0)
	        	{
	        		while($add1 = mysql_fetch_assoc($exe_add))
	        		{
	        			$address[] = $add1;
	        		}
	        		$address_all = array("DE_ID"=>$address[0]['DE_ID'],"DE_Name" => $address[0]['DE_Name'],"DE_Address"=>$address[0]['DE_Address'],"BLDG_No"=>$address[0]['BLDG_No'],"DE_State"=>$address[0]['DE_State'],"DE_City"=>$address[0]['DE_City'],"DE_Zipcode"=>$address[0]['DE_Zipcode'],"DE_Phone"=>$address[0]['DE_Phone'],"DE_lat"=>$address[0]['DE_lat'],"DE_long"=>$address[0]['DE_long'],"DE_default" =>$address[0]['DE_default']);
	        	}
	        	else 
	        	{
	        		$address_all = "";
	        	}
	        	
	        	//----------------- Get the Default Delivery Address of the user -----------//
	        	// --------------- End of Default Address --------------//
	        	
	        	
	            /*if($data['logo']){
	                $logo=IMAGEPATH.$data['logo'];
	            }
	            else{
	                $logo="";
	            }*/
	            
	            //**getting card details
	            //$sql2="select *,AES_DECRYPT(Card_Number,'".$EncryptKey."') AS CARD_NO from tbl_creditcarddetails where Card_User_ID=".$id." AND Card_Type!='AMERICAN EXPRESS' ORDER BY Card_Type DESC";
	        	$sql2="select * from tbl_creditcarddetails where Card_User_ID=".$id." AND Card_Type!='AMERICAN EXPRESS'";
	            $exe2=mysql_query($sql2);
	        	
	        	$Cardexe=mysql_query($sql2);
	        	$num2=mysql_num_rows($exe2);
	        	$Cardnum=mysql_num_rows($Cardexe);
	        	if($num2>0)
	        	{
	        		while($res2=mysql_fetch_array($exe2)){
	                $card_array[]=array("Card_ID"=>$res2['Card_ID'],"Card_Type"=>$res2['Card_Type'],"CARD_NO"=>$res2['Card_Number'],"Card_Exp_Year"=>$res2['Card_Exp_Year'],
	                                "Card_Exp_Month"=>$res2['Card_Exp_Month'],"Card_FirstName"=>$res2['Card_FirstName'],"Card_Street"=>$res2['Card_Street'],
	                                "Card_State"=>$res2['Card_State'],"Card_City"=>$res2['Card_City'],"Card_Zip"=>$res2['Card_Zip']);
	                }                                
	        	}
	            else{
	                $card_array=array();
	            }
	            
	            
	            $content = array(
					"status" => "1",
	                "response" => SUCCESS,
	                "message" => SUCCESS,
					"status" => "1",
	                "userid" => $id,
	                "fullname" => $fullname,
					"school" => $school,
					"city" => $data['city'],
					"state" => $data['state'],
					"birthday" => $data['birthday'],
					"street" => $data['street'],
	            	"zipcode" => $data['zipcode'],
            	    "building" => $data['BLDG_No'],
	            	"dine_driver" => $data['dine_driver'],
					"push_notification" => $data['push_notification'],
					"mobile_phone" => $data['mobile_phone'],
					"gender" => $data['gender'],
	                "Lastname" => $lastname,
	                "displayname" => $data['display_name'],
	                "email" => $email,
	                "default_address" => $address_all,
	                "logo"=>$data['logo'],
	                "card_array"=>$card_array);
	            
	            
	            /*--------------update the cart ------------------*/
	            if($device_id != '')
	            {
	            	$update_cart=mysql_query("update tbl_cart set Cart_UserID = '".$id."' where Sess_ID = '".$device_id."'");
            		//$data_cart = mysql_fetch_array($update_cart);
	            }
	            
	           
	            echo json_encode($content);
	            exit;
        	}
        	else 
        	{
        		$content = array("status" => "0","response" => ERROR, "message" => 'Please activate your account');
           		echo json_encode($content);
                exit;
        	}
        }
        else
        {
            $content = array("status" => "0","response" => ERROR, "message" => INCORRECT_MSG);
            echo json_encode($content);
            exit;
        }
    }
}

?>