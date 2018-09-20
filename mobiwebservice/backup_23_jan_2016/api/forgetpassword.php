<?php
include ('config.php');
include 'function.php';
if (isset($_REQUEST['email'])) 
{
    $email=$_REQUEST['email'];
    $message="";
    $query="SELECT id,firstname,email_add,AES_DECRYPT(encrypt_password,'".ENCRYPTKEY."') AS password FROM tbl_registeration WHERE email_add='".$email."'";
	$result=mysql_query($query);
    $count=mysql_numrows($result);
	if ($count>0) 
	{
	$rec=mysql_fetch_array($result);
	$to =$rec['email_add'];
	$toname=explode("@",$to);
	// Create Checksum Value
	$checksumstatus=0;
	$checksum=get_rand_id(30);
	while($checksumstatus==0)
	{
		$query="SELECT checksum FROM tbl_registeration WHERE checksum='".$checksum."'"; 
		$res=mysql_query($query);
		if (mysql_affected_rows())
			$checksumstatus=0;
		else
			$checksumstatus=1;
	}
	if($checksumstatus==1)
		$sql="UPDATE tbl_registeration SET checksum='".$checksum."' WHERE email_add='".$email."'"; 
		mysql_query($sql);
	
	$message='<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
	<td  valign="top" scope="col"><table width="100%" border="0" cellspacing="0" cellpadding="0">
	  <tr>
		<td valign="top" scope="col"><table width="94%" border="0" align="center" cellpadding="0" cellspacing="0">
		<tr>
			<td height="36" scope="col"><div align="left"><font style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold; color:#6a6a6a;">Dear</font><font style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold; color:#be3917;">&nbsp;&nbsp;'.$toname[0].'</font><br /><br /><font style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#6a6a6a; line-height:20px;padding-left:120px;">We received a request to reset your password</font>.<br><font style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#6a6a6a; line-height:20px;padding-left:120px;">If you didn\'t make this request, simply ignore this email.</font>
			</div></td>
		  </tr>
		  <tr><td height="10"></td></tr>
		  <tr>
			<td height="19" scope="col" align="center">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold; color:#6a6a6a;"><a href="http://letsnurture.co.uk/demo/dinning/change_password.php?checksum='.$checksum.'" style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold; color:#0b9fd8;"><img src="'.HOSTPATH.'images/btn_reset_password.gif"></font></td>
		  </tr>
		  <tr><td height="10"></td></tr>
		 
		  <tr><td height="10" align="center"><font style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold; color:#6a6a6a;">OR</font></td></tr>
		  <tr>
			<td height="19" scope="col" align="center">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold; color:#6a6a6a;"><a href="http://letsnurture.co.uk/demo/dinning/change_password.php?checksum='.$checksum.'" style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold; color:#0b9fd8;">Click here to reset your Password.</font></td>
		  </tr>
		</table>
				 </td>
	  </tr>
	</table></td>
  </tr>
</table>';

	$headers = "From: ".ADMINMAIL."\r\n" .
	'Reply-To: '.ADMINMAIL."\r\n" .
	'X-Mailer: PHP/' . phpversion();
	$headers .= 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	include ("template/email-template.php");
	$template1=str_replace('DETAILS',$message."",$template);
	$message=$template1;
	
    	if(mail($to,"Your Password Reset Code", $message, $headers)){
    	   $content=array("status" => "1","response"=>SUCCESS,"message"=>SUCCESS);
           echo json_encode($content);
           exit;
    	}
        else{
            $content=array("status" => "0","response"=>ERROR,"message"=>ERROR);
            echo json_encode($content);
            exit;
        }	
    } 
    else {
	    $content=array("status" => "0","response"=>ERROR,"message"=>"Invalid Email");
        echo json_encode($content);
        exit;
	}
}
else{
     $content=array("status" => "0","response"=>ERROR,"message"=>PARAMETER_MSG);
        echo json_encode($content);
        exit;
} 

?>