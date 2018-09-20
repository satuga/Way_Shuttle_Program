<?php

function checksum()
{
    $length = 30;
    $characters = "0123456789abcdefghijklmnopqrstuvwxyz";
    $string = "";
    for ($p = 0; $p < $length; $p++)
    {
        $string .= $characters[mt_rand(0, strlen($characters))];
    }
    $checksum_register = $string;
    return $checksum_register;
}

function Sendmail($email,$id,$chk_sum)
{
    	//	GLOBAL $adminmail,$Host_Path,$reply_mail;
        $Host_Path='http://www.steelengine.com/';
        $id=$id;
        $checksum_register=$chk_sum;
        $reply_mail='support@way.com';
				$reguser=explode("@",$email);
				if($display_name=="")
					$display_name=$reguser[0];
				$Subject="Welcome to Way.com, ".$display_name."! ";
				 $message='<table width="564" border="0" align="center" cellpadding="0" cellspacing="0" >
				  <tr>
					<td  valign="top" scope="col"><table width="100%" border="0" cellspacing="0" cellpadding="0">
					 <tr>
						<td height="5" scope="col">&nbsp;</td>
					  </tr>
					  <tr>
						<td scope="col" align="left"><font style="font-family:Arial, Helvetica, sans-serif; font-size:16px; font-weight:bold; color:#6a6a6a;">Thank You For Registering And Welcome To Way.com!</font></td>
					  </tr>
					  <tr>
						<td height="5" scope="col">&nbsp;</td>
					  </tr>
					  <tr>
						<td valign="top" scope="col"><table width="98%" border="0" align="center" cellpadding="0" cellspacing="0">
						<tr>
							<td height="26" scope="col"><div align="left"><font style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold; color:#6a6a6a;">Hello</font><font style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold; color:#be3917;">&nbsp;&nbsp;'.$display_name.'</font>
							</div></td>
						  </tr>
						  <tr><td height="10" scope="col"></td></tr>

						   <tr>
							<td height="19" scope="col" align="center">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold; color:#6a6a6a;"><a href="'.$Host_Path.'confirm.php?id='.$id.'&checksum_register='.$checksum_register.'" style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold; color:#0b9fd8;">Click here to activate account</font></td>
						  </tr>

						  <tr><td height="20" scope="col"></td></tr>
						  <tr>
							<td height="19" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#6a6a6a;">Connect with Way.com! Follow Way.com on Twitter, Facebook and the Way.com Blog.</font></td>
						  </tr>
						  <tr><td height="10" scope="col"></td></tr>
						  <tr>
							<td height="19" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#6a6a6a;"><b>Not you?</b></font></td>
						  </tr>
						  <tr><td height="10" scope="col"></td></tr>
						  <tr>
							<td height="19" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#6a6a6a;text-decoration:none">If you did not recently update your email address on <a href="'.$Host_Path.'">Way.com</a>, please let us know by forwarding this email to mail@way.com. </font></td>
						  </tr>
						  <tr><td height="20" scope="col"></td></tr>
						  <tr>
							<td height="19" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#6a6a6a;">This is an account-related message. Responses to this email will not go to a customer service representative.</font></td>
						  </tr>

						  <tr>
							<td height="19" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#6a6a6a;">To contact our Customer Care team directly, please visit the help section of our website.</font></td>
						  </tr>

						   <tr>
						<td height="19" scope="col">&nbsp;</td>
					  </tr>
						</table>
								 </td>
					  </tr>
					  <tr><td height="2" scope="col"></td></tr>
					</table></td>
				  </tr>
				</table>';
				$headers = "From: ".$reply_mail."\r\n" .
						   'Reply-To: '.$reply_mail."\r\n" .
						   'X-Mailer: PHP/' . phpversion();
				$headers .= 'MIME-Version: 1.0' . "\r\n";
				$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
				include ("template/email-template.php");
			$template1=str_replace('DETAILS',$message."",$template);
			$message=$template1;
			mail($email, $Subject, $message, $headers);

}


// ----------- getting the random if from the API ----//
function get_rand_id($length)
{
	if($length>0)
	{
		$rand_id="";
		for($i=1; $i<=$length; $i++)
		{
			mt_srand((double)microtime() * 1000000);
			$num = mt_rand(1,36);
			$rand_id .= assign_rand_value($num);
		}
	}
	return $rand_id;
}

function assign_rand_value($num)
{
// accepts 1 - 36
  switch($num)
  {
    case "1":
     $rand_value = "a";
    break;
    case "2":
     $rand_value = "b";
    break;
    case "3":
     $rand_value = "c";
    break;
    case "4":
     $rand_value = "d";
    break;
    case "5":
     $rand_value = "e";
    break;
    case "6":
     $rand_value = "f";
    break;
    case "7":
     $rand_value = "g";
    break;
    case "8":
     $rand_value = "h";
    break;
    case "9":
     $rand_value = "i";
    break;
    case "10":
     $rand_value = "j";
    break;
    case "11":
     $rand_value = "k";
    break;
    case "12":
     $rand_value = "l";
    break;
    case "13":
     $rand_value = "m";
    break;
    case "14":
     $rand_value = "n";
    break;
    case "15":
     $rand_value = "o";
    break;
    case "16":
     $rand_value = "p";
    break;
    case "17":
     $rand_value = "q";
    break;
    case "18":
     $rand_value = "r";
    break;
    case "19":
     $rand_value = "s";
    break;
    case "20":
     $rand_value = "t";
    break;
    case "21":
     $rand_value = "u";
    break;
    case "22":
     $rand_value = "v";
    break;
    case "23":
     $rand_value = "w";
    break;
    case "24":
     $rand_value = "x";
    break;
    case "25":
     $rand_value = "y";
    break;
    case "26":
     $rand_value = "z";
    break;
    case "27":
     $rand_value = "0";
    break;
    case "28":
     $rand_value = "1";
    break;
    case "29":
     $rand_value = "2";
    break;
    case "30":
     $rand_value = "3";
    break;
    case "31":
     $rand_value = "4";
    break;
    case "32":
     $rand_value = "5";
    break;
    case "33":
     $rand_value = "6";
    break;
    case "34":
     $rand_value = "7";
    break;
    case "35":
     $rand_value = "8";
    break;
    case "36":
     $rand_value = "9";
    break;
  }
return $rand_value;
}

function SubGroupname($gid)
{
	$sql = @mysqli_query($con,"select subgroup_name from subgroups where id=".$gid);
	$res = mysqli_fetch_array($sql);
	return $res['subgroup_name'];
}

function GetParkingfee($Park_ID)
	{
	    $sqll="select *,tbl_parkinglocations.*,tbl_parking.* from tbl_parking
		INNER JOIN tbl_parkinglocations ON tbl_parkinglocations.Park_ID=tbl_parking.P_Location
		where P_ID=".$Park_ID;
		$exe=mysqli_query($con,$sqll);
		$res=mysqli_fetch_array($exe);
		$Park_AirportVenue=$res['Park_AirportVenue'];
		if($Park_AirportVenue==1)
		{
	   $sql2="SELECT Setting_Name,Setting_Value,format FROM tbl_categories_settings WHERE Cat_ID=45 AND Parking_ID=".$Park_ID;
	   $exe=mysqli_query($con,$sql2);
	   $num=mysqli_num_rows($exe);
	   if($num>0)
			$sql2="SELECT Setting_Name,Setting_Value,format FROM tbl_categories_settings WHERE Cat_ID=45 AND Parking_ID=".$Park_ID;
	    else
		   $sql2="SELECT Setting_Name,Setting_Value,format FROM tbl_categories_settings WHERE Setting_Value>0 AND Cat_ID=45 AND Parking_ID=0";

	   $sql1="SELECT Setting_Name,Setting_Value,format FROM tbl_sell_fee WHERE Setting_Value>0 AND Parking_ID=".$Park_ID;

	   $sql=$sql1." UNION ".$sql2;
	   }
	   else {
	   $sql="SELECT Setting_Name,Setting_Value,format FROM tbl_sell_fee WHERE Setting_Value>0 AND Parking_ID=".$Park_ID;
	   }

	   $exe=mysqli_query($con,$sql);
	   $num=mysqli_num_rows($exe);
	   $Fee=array();
	   $i=0;
		while($res=mysqli_fetch_array($exe))
		{
			$Fee[$i]['Setting_Name']=$res['Setting_Name'];
			$Fee[$i]['Setting_Value']=$res['Setting_Value'];
			$Fee[$i]['format']=$res['format'];
			$i++;
		}
		return array($Fee,$num);
	}






?>
