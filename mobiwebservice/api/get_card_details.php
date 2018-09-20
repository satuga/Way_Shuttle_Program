<?php
header('Content-Type: application/json');
include('config.php');
include('function.php');
$EncryptKey = ENCRYPTKEY;

$data = json_decode(file_get_contents('php://input'), TRUE);
// print_r($data); die;
$id = sanitize($data['data']['user_id']);

//$query="select *,AES_DECRYPT(Card_Number,'".$EncryptKey."') AS Card_Number from tbl_creditcarddetails where Card_User_ID=".$id." AND Card_Type!='AMERICAN EXPRESS'";
$query="select *,RIGHT(AES_DECRYPT(Card_Number,'".$EncryptKey."'),4) AS Card_No from tbl_creditcarddetails where Card_User_ID=".$id." AND Card_StripeCustID<>''";

$res = mysqli_query($con,$query);
$count = mysqli_num_rows($res);

	if($count>0)
	{
		while($res2=mysqli_fetch_array($res))
		{	// Renameing cart type for sync web and app - 28-apr-2016 Bindra Shah
			$card_type = $res2['Card_Type'];
			if($card_type == 'Visa')
				$card_type = 'VISA';
			else if($card_type == 'MasterCard')
				$card_type = 'MASTER CARD';
			else if($card_type == 'American Express')
				$card_type = 'AMEX';
			else if($card_type == 'Discover')
				$card_type = 'DISCOVER';
			else if($card_type == 'Dinners Club')
				$card_type = 'DINERSCLUB';
			else
				$card_type = $card_type;
			$card_exp_year = substr($res2['Card_Exp_Year'],-2);
			$card_array[]=array("Card_ID"=>$res2['Card_ID'],"Card_Type"=>$card_type,"CARD_NO"=>base64_encode($res2['Card_No']),"Card_Exp_Year"=>$card_exp_year,
						"Card_Exp_Month"=>$res2['Card_Exp_Month'],"Card_FirstName"=>$res2['Card_FirstName'],"Card_Street"=>$res2['Card_Street'],
						"Card_State"=>$res2['Card_State'],"Card_City"=>$res2['Card_City'],"Card_Zip"=>$res2['Card_Zip'],"Stripe_UserId"=>(isset($res2['Stripe_UserId'])?$res2['Stripe_UserId']:'0'),"Card_StripeCustID"=>(isset($res2['Card_StripeCustID'])?$res2['Card_StripeCustID']:'0'),"Card_Default"=>$res2['Card_Default']);
			//$card_array[] = $res2;
			//echo "DDD".html($res2['Card_Number']);
			//echo "dd".iconv('Latin1', 'UTF-8', Hex2String($res['Card_Number']));
		}
		
		$output=array("status"=>"1","data"=>$card_array);
		echo json_encode($output);exit;
	}
    else
	{
		$output=array("status"=>"0","data"=>"No Records found");
		echo json_encode($output);exit;
    }


?>
