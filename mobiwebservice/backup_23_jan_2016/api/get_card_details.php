<?php
include('config.php');
include('function.php');
$EncryptKey = ENCRYPTKEY;

$data = json_decode(file_get_contents('php://input'), TRUE);
$id = $data['data']['user_id'];

//$query="select *,AES_DECRYPT(Card_Number,'".$EncryptKey."') AS Card_Number from tbl_creditcarddetails where Card_User_ID=".$id." AND Card_Type!='AMERICAN EXPRESS'";
$query="select * from tbl_creditcarddetails where Card_User_ID=".$id." AND Card_Type!='AMERICAN EXPRESS'";

$res = mysql_query($query);
$count = mysql_num_rows($res);

	if($count>0)
	{
		while($res2=mysql_fetch_array($res))
		{
			$card_array[]=array("Card_ID"=>$res2['Card_ID'],"Card_Type"=>$res2['Card_Type'],"CARD_NO"=>$res2['Card_Number'],"Card_Exp_Year"=>$res2['Card_Exp_Year'],
						"Card_Exp_Month"=>$res2['Card_Exp_Month'],"Card_FirstName"=>$res2['Card_FirstName'],"Card_Street"=>$res2['Card_Street'],
						"Card_State"=>$res2['Card_State'],"Card_City"=>$res2['Card_City'],"Card_Zip"=>$res2['Card_Zip']);
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
