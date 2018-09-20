
<?php
//header('Content-Type: application/json');
error_reporting(E_ALL);
include('config.php');
include('function.php');
require_once('test-stripe-config.php');
$EncryptKey = ENCRYPTKEY;
$data = json_decode(file_get_contents('php://input'), TRUE);
$user_id = sanitize($data['data']['user_id']);
$card_type = sanitize($data['data']['card_type']);
$card_number = sanitize($data['data']['card_number']);
$card_name = sanitize($data['data']['card_name']);
$exp_month = sanitize($data['data']['exp_month']);
$exp_year = sanitize($data['data']['exp_year']);
$street = sanitize($data['data']['street']);
$city = sanitize($data['data']['city']);
$state = sanitize($data['data']['state']);
$zip = sanitize($data['data']['zip']);
$type = sanitize($data['data']['type']);
$card_id = sanitize($data['data']['card_id']);
$token  = sanitize($data['data']['stripe_token']);
$response = array();
if($user_id!='' && $user_id>0 && $token !='' && $card_number!='' && $exp_month!='' && $exp_year!='' && $card_type!='')
{
	$query = "select * from tbl_registeration where id=".$user_id;
  	$res = mysqli_query($con,$query);
  	if (mysqli_num_rows($res))
  	{
    	$rec = mysqli_fetch_assoc($res);
    	$email  = $rec['email_add'];
  	}
  	else {
    	$response = array("status" => 0, "message" => "User does not exist");
    	echo json_encode($response);
    	exit;
  	}
	$currency = "USD";
	if($email == ""){
		$content=array("status"=>"0","response"=>"error","message"=>"Email id does not found");
		echo json_encode($content);
		exit;
	}
	try{
		$customerData=Stripe_Customer::all();
		$customer_id = '';
		foreach ($customerData['data'] as $customer) {
			if($customer['email'] == $email){
				$customer_id =  $customer['id'];
			}
		}
		//check if customer exist if not then create new one
		if($customer_id == ''){
			$customer = Stripe_Customer::create(array(
			'email' => $email,
			'card'  => $token));
			$customer_id  = $customer['id'];
		}
		if($customer_id!='')
		{
			$card_number = substr(base64_decode($card_number),-4);
			// Renameing cart type for sync web and app - 28-apr-2016 Bindra Shah
			if($card_type == 'VISA')
				$card_type = 'Visa';
			else if($card_type == 'MASTER CARD')
				$card_type = 'MasterCard';
			else if($card_type == 'AMEX')
				$card_type = 'American Express';
			else if($card_type == 'DISCOVER')
				$card_type = 'Discover';
			else if($card_type == 'DINERSCLUB')
				$card_type = 'Dinners Club';
			else
				$card_type = $card_type;
			if($type == 'add')
			{
				
			//	echo "select Card_ID from tbl_creditcarddetails where Card_Number = AES_ENCRYPT('".$card_number."','".$EncryptKey."')";
			//echo "select Card_ID from tbl_creditcarddetails where Card_User_ID =".$user_id." AND Card_Number = AES_ENCRYPT('".$card_number."','".$EncryptKey."')"; die;
			$check_card = mysqli_query($con,"select Card_ID from tbl_creditcarddetails where Card_User_ID =".$user_id." AND Card_Number = AES_ENCRYPT('".$card_number."','".$EncryptKey."')");
				$count = mysqli_num_rows($check_card);
				if($count > 0)
				{
					$mycard = mysqli_fetch_array($check_card);
					$response=array("status"=>"0","data"=>"Card Already Exist with ".$mycard['Card_ID']);
					echo json_encode($response);exit;
				}
				else
				{
					// Set by default card if it is first
					$Card_Default='0';					
					$CardTotal=mysqli_num_rows(mysqli_query($con,"select Card_ID from tbl_creditcarddetails where Card_User_ID =$user_id"));
					if($CardTotal==0)
						$Card_Default='1';
					$sql="INSERT INTO tbl_creditcarddetails(Card_User_ID,Card_Type,Card_StripeCustID,Card_Number,Card_Exp_Month,Card_Exp_Year,Card_FirstName,Card_Street,Card_City,Card_State,Card_Zip,Card_Created,Card_Default)VALUES(".$user_id.",'".$card_type."','".$customer_id."',AES_ENCRYPT('".$card_number."','".$EncryptKey."'),'".mysqli_real_escape_string($con,$exp_month)."','".mysqli_real_escape_string($con,$exp_year)."','".mysqli_real_escape_string($con,$card_name)."','".mysqli_real_escape_string($con,$street)."','".mysqli_real_escape_string($con,$city)."','".mysqli_real_escape_string($con,$state)."','".mysqli_real_escape_string($con,$zip)."',Now(),'".$Card_Default."')";
					$exeCC=mysqli_query($con,$sql);
					$Card_ID=mysqli_insert_id($con);
					if($Card_ID != '')
					{
						$response=array("status"=>"1","data"=>"Card Added Successfully","card_id"=>$Card_ID);
						echo json_encode($response);exit;
					}
					else
					{
						$response=array("status"=>"0","data"=>"Card Not Added");
						echo json_encode($response);exit;
					}
				}
			}

			else
			{
				if($card_id == '')
				{
					$response=array("status"=>"0","data"=>"Please enter card id");
					echo json_encode($response);exit;
				}
				else
				{
					$sql="UPDATE tbl_creditcarddetails SET Card_Type='".mysqli_real_escape_string($con,$card_type)."',Card_Number=AES_ENCRYPT('".$card_number."','".$EncryptKey."'),Card_StripeCustID='".mysqli_real_escape_string($con,$customer_id)."',Card_Exp_Month='".mysqli_real_escape_string($con,$exp_month)."',Card_Exp_Year='".mysqli_real_escape_string($con,$exp_year)."',Card_FirstName='".mysqli_real_escape_string($con,$card_name)."',Card_Street='".mysqli_real_escape_string($con,$street)."',Card_State='".mysqli_real_escape_string($con,$state)."',Card_City='".mysqli_real_escape_string($con,$city)."',Card_Zip='".mysqli_real_escape_string($con,$zip)."' WHERE Card_ID=".$card_id;
					$exeCC=mysqli_query($con,$sql);
					$response=array("status"=>"1","data"=>"Card Updated Successfully");
					echo json_encode($response);exit;
				}
			}
		} // End checking customer id

	} // End try block
	catch(Stripe_CardError $e) {
		$response['status']='0';
		$response['response']='error';
		$response['message'] = $e->getMessage();
		header('Content-Type: application/json; charset=utf-8');
		echo json_encode($response);
		exit;
	} catch (Stripe_InvalidRequestError $e) {
		// Invalid parameters were supplied to Stripe's API
		$response['status']='0';
		$response['response']='error';
		$response['message'] = $e->getMessage();
		header('Content-Type: application/json; charset=utf-8');
		echo json_encode($response);
		exit;
	} catch (Stripe_AuthenticationError $e) {
		// Authentication with Stripe's API failed
		$response['status']='0';
		$response['response']='error';
		$response['message'] = $e->getMessage();
		header('Content-Type: application/json; charset=utf-8');
		echo json_encode($response);
		exit;
	} catch (Stripe_ApiConnectionError $e) {
		// Network communication with Stripe failed
		$response['status']='0';
		$response['response']='error';
		$response['message'] = $e->getMessage();
		header('Content-Type: application/json; charset=utf-8');
		echo json_encode($response);
		exit;
	} catch (Stripe_Error $e) {
		// Display a very generic error to the user, and maybe send
		// yourself an email
		$response['status']='0';
		$response['response']='error';
		$response['message'] = $e->getMessage();
		header('Content-Type: application/json; charset=utf-8');
		echo json_encode($response);
		exit;
	} catch (Exception $e) {
		// Something else happened, completely unrelated to Stripe
		$response['status']='0';
		$response['response']='error';
		$response['message'] = $e->getMessage();
		header('Content-Type: application/json; charset=utf-8');
		echo json_encode($response);
		exit;
	}
}
else {
	$content=array("status"=>"0","response"=>"error","message"=>"Parameter Missing");
	echo json_encode($content);
	exit;
}
?>
