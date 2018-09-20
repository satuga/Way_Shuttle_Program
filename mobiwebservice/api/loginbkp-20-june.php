<?php
 header('Content-Type: application/json; charset=utf-8');
 //error_reporting(0);
include ('config.php');
include ('function.php');
$EncryptKey = ENCRYPTKEY;
//$data_request = json_decode(file_get_contents('php://input'), TRUE);
//$_REQUEST  = $data_request;
//$data = json_decode(file_get_contents('php://input'), TRUE);
//$_REQUEST  = $data;
//var_dump($data);
$device_type = sanitize($_REQUEST['device_type']);
$access_token = sanitize($_REQUEST['access_token']);
$device_id = sanitize($_REQUEST['device_id']);
//var_dump($_REQUEST); echo "above is requet";
if (isset($_REQUEST['isfbuser']) == 1)
{
    $email = sanitize($_REQUEST['email']);
    $password = sanitize($_REQUEST['password']);
    $isfbuser = sanitize($_REQUEST['isfbuser']);
    $facebook_id = sanitize($_REQUEST['facebook_id']);
    $fullname=sanitize($_REQUEST['fullname']);
    $parameter = $email . ':' . $password . ':' . $facebook_id.':'.$fullname;
    $response = chk_parameter($parameter, 4);
}
else
{
    $email = sanitize($_REQUEST['email']);
    $password = sanitize($_REQUEST['password']);
    $parameter = $email . ':' . $password;
	//var_dump($parameter); die;
    $response = chk_parameter($parameter, 2);
	//var_dump($response); die;

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
        //$query = "select * from tbl_registeration where email_add='" . $email ."' and encrypt_password='' and facebook='1' and facebook_id='".$facebook_id."'";
        //$query = "select * from tbl_registeration where email_add='" . $email ."' and encrypt_password='' and facebook='1' and facebook_id='".$facebook_id."'";
        $query = "select * from tbl_registeration where email_add='" . $email ."'";
        $res = mysqli_query($con,$query);
        if (mysqli_num_rows($res))
        {
            $update=mysqli_query($con,"update tbl_registeration set last_login=now() where email_add='" . $email ."' and encrypt_password='' and facebook='1' and facebook_id='".$facebook_id."'");
            $data = mysqli_fetch_array($res);

            $fullname = $data['firstname'];
            $lastname = $data['lastname'];
            $school = $data['school'];
            $displayname = $data['display_name'];
            $email = $data['email_add'];
            $id = $data['id'];


            	//--------------- check device type and accesstoken for the user ---------------//
	            $sql_device="select * from tbl_user_device where user_id=".$id." AND access_token = '".$access_token."'";
	            $exe_device = mysqli_query($con,$sql_device);
	        	$num_device = mysqli_num_rows($exe_device);
	        	if($num_device > 0)
	        	{

	        	}
	        	else
	        	{
	        		$del_ext_devices = mysqli_query($con,"delete from tbl_user_device where access_token = '".$access_token."'");

	        		$ins_device = "insert into tbl_user_device(user_id,device_type,access_token,created_at) values('".$id."','".$device_type."','".$access_token."',now())";
        			mysqli_query($con,$ins_device);
	        	}
	        	//------------------------ End of the device token ---------------//

	        	$sql_add="select * from tbl_deliveryaddress where DE_UserID='".$id."' AND DE_default = '1'";
	            $exe_add = mysqli_query($con,$sql_add);
	        	$num_add = mysqli_num_rows($exe_add);
	        	if($num_add > 0)
	        	{
	        		while($add1 = mysqli_fetch_assoc($exe_add))
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

	        $Cardexe=mysqli_query($con,$sql2);
	    	//$num2=mysqli_num_rows($exe2);
	    	$Cardnum=mysqli_num_rows($Cardexe);
	    	if($Cardnum >0)
	    	{
	    		while($res2=mysqli_fetch_array($Cardexe)){
	            $card_array[]=array("Card_Type"=>$res2['Card_Type'],"CARD_NO"=>base64_encode($res2['Card_Number']),"Card_Exp_Year"=>$res2['Card_Exp_Year'],
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
	            	$update_cart=mysqli_query($con,"update tbl_cart set Cart_UserID = '".$id."' where Sess_ID = '".$device_id."'");
            		//$data_cart = mysqli_fetch_array($update_cart);
	            }

              /* ------------------ Get total cart items before login --------------------------*/
              $sql_count="select count(*) as total_cart from tbl_cart where Cart_UserID='".$id."'";
               $query_count=mysqli_query($con,$sql_count);
               if (mysqli_num_rows($query_count))
               {
                $counts= mysqli_fetch_array($query_count);
                $total_cart=$counts['total_cart'];
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
             	"total_cart" => $total_cart,
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
             $chk = mysqli_query($con,"select id from tbl_registeration where email_add ='".$email ."' ");

			   // echo mysqli_num_rows($chk);
				/*if (mysqli_num_rows($chk))
				{
					$content = array("response" => ERROR, "message" => "Email id " . $email .
							" already registered Please login with password");
					echo json_encode($content);
					exit;
				}*/
            $EncryptKey = ENCRYPTKEY;

        $chk_sum = checksum();
        $sql="insert into tbl_registeration (firstname,display_name,email_add,encrypt_password,cdate,checksum_register,status,facebook,facebook_id)values('".$fullname."','".$fullname."','".$email."','',now(),'".$chk_sum."','1','1','".$facebook_id."')";
            $res=mysqli_query($con,$sql);
        $id = mysqli_insert_id($con);

       // Company table
        $sql11 = "insert into companies set id='" . $id . "',email='" . $email .
            "',joinedOn=now()";
        mysqli_query($con,$sql11);

        // Alias Email, Address
        $reguser = explode("@", $email);
        $aliasemail = $reguser[0] . $id . "@members.way.com";

        $sql = "UPDATE tbl_registeration SET alias_email='" . $aliasemail ."' ,last_login=now(),device_type = '".$device_type."',access_token = '".$access_token."' WHERE id =" . $id;
        mysqli_query($con,$sql);


        		//--------------- check device type and accesstoken for the user ---------------//
	            $sql_device="select * from tbl_user_device where user_id=".$id." AND access_token = '".$access_token."'";
	            $exe_device = mysqli_query($con,$sql_device);
	        	$num_device = mysqli_num_rows($exe_device);
	        	if($num_device > 0)
	        	{

	        	}
	        	else
	        	{
	        		$del_ext_devices = mysqli_query($con,"delete from tbl_user_device where access_token = '".$access_token."'");

	        		$ins_device = "insert into tbl_user_device(user_id,device_type,access_token,created_at) values('".$id."','".$device_type."','".$access_token."',now())";
        			mysqli_query($con,$ins_device);
	        	}
	        	//------------------------ End of the device token ---------------//


	        	//------------------------ End of the device token ---------------//
        		$sql_add="select * from tbl_deliveryaddress where DE_UserID='".$id."' AND DE_default = '1'";
	            $exe_add = mysqli_query($con,$sql_add);
	        	$num_add = mysqli_num_rows($exe_add);
	        	if($num_add > 0)
	        	{
	        		while($add1 = mysqli_fetch_assoc($exe_add))
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
        $exe2=mysqli_query($con,$sql2);

    	$Cardexe=mysqli_query($con,$sql2);
    	$num2=mysqli_num_rows($exe2);
    	$Cardnum=mysqli_num_rows($Cardexe);
    	if($num2>0)
    	{
    		while($res2=mysqli_fetch_array($exe2)){
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
	            	$update_cart=mysqli_query($con,"update tbl_cart set Cart_UserID = '".$id."' where Sess_ID = '".$device_id."'");
            		//$data_cart = mysqli_fetch_array($update_cart);
	            }
              /* ------------------ Get total cart items before login --------------------------*/
              $sql_count="select count(*) as total_cart from tbl_cart where Cart_UserID='".$id."'";
               $query_count=mysqli_query($con,$sql_count);
               if (mysqli_num_rows($query_count))
               {
                $counts= mysqli_fetch_array($query_count);
                $total_cart=$counts['total_cart'];
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
             	"total_cart" => $total_cart,
                "card_array"=>$card_array);
            echo json_encode($content);
            exit;

        }
        }
    }
    else
    {
       // $query = "select * from tbl_registeration where email_add='".$email."' and encrypt_password=AES_ENCRYPT('".$password."','".ENCRYPTKEY."')";
        $query = "select * from tbl_registeration where email_add='".$email."'";
		//var_dump($query); die;
        $res = mysqli_query($con,$query);
        if (mysqli_num_rows($res))
        {


	        $data = mysqli_fetch_array($res);
			//var_dump($data); die;
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
	            $exe_device = mysqli_query($con,$sql_device);
	        	$num_device = mysqli_num_rows($exe_device);
				//var_dump($num_device); die;
	        	if($num_device > 0)
	        	{

	        	}
	        	else
	        	{
	        		$del_ext_devices = mysqli_query($con,"delete from tbl_user_device where access_token = '".$access_token."'");

	        		$ins_device = "insert into tbl_user_device(user_id,device_type,access_token,created_at) values('".$id."','".$device_type."','".$access_token."',now())";
        			mysqli_query($con,$ins_device);
	        	}
	        	//------------------------ End of the device token ---------------//
        		$sql_add="select * from tbl_deliveryaddress where DE_UserID='".$id."' AND DE_default = '1'";
	            $exe_add = mysqli_query($con,$sql_add);
	        	$num_add = mysqli_num_rows($exe_add);
				//var_dump($sql_add); die;
	        	if($num_add > 0)
	        	{
	        		while($add1 = mysqli_fetch_assoc($exe_add))
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
	            $exe2=mysqli_query($con,$sql2);

	        	$Cardexe=mysqli_query($con,$sql2);
	        	$num2=mysqli_num_rows($exe2);
	        	$Cardnum=mysqli_num_rows($Cardexe);
				//var_dump($Cardnum); die;
	        	if($num2>0)
	        	{
	        		while($res2=mysqli_fetch_array($exe2)){
	                $card_array[]=array("Card_ID"=>$res2['Card_ID'],"Card_Type"=>$res2['Card_Type'],"CARD_NO"=>base64_encode($res2['Card_Number']),"Card_Exp_Year"=>$res2['Card_Exp_Year'],
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
                $update_cart=mysqli_query($con,"update tbl_cart set Cart_UserID = '".$id."' where Sess_ID = '".$device_id."'");
                //$data_cart = mysqli_fetch_array($update_cart);
              }
              /* ------------------ Get total cart items before login --------------------------*/
               $sql_count="select count(*) as total_cart from tbl_cart where Cart_Type in('Dine','Parking') and Cart_UserID='".$id."'";
               $query_count=mysqli_query($con,$sql_count);
               if (mysqli_num_rows($query_count))
               {
                $counts= mysqli_fetch_array($query_count);
                $total_cart=$counts['total_cart'];
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
	            	"dine_driver" => isset($data['dine_driver'])? $data['dine_driver'] :"0",
					"push_notification" => $data['push_notification'],
					"mobile_phone" => $data['mobile_phone'],
					"gender" => $data['gender'],
	                "Lastname" => $lastname,
	                "displayname" => $data['display_name'],
	                "email" => $email,
	                "default_address" => $address_all,
                  "logo"=>$data['logo'],
	                "total_cart"=>$total_cart,
	                "card_array"=>$card_array);
					
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
