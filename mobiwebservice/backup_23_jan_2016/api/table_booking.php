<?php
include 'config.php';
include("function.php"); 
include "../dine/dineclass.php";

$adminmail=ADMINMAIL;
$Host_Path=HOSTPATH;
$contactmail=CONTACTMAIL;
$EncryptKey=ENCRYPTKEY;
if(isset($_REQUEST['id']) && isset($_REQUEST['book_date']) && isset($_REQUEST['book_time']) && isset($_REQUEST['size']) && isset($_REQUEST['Diner_Name'])
    && isset($_REQUEST['Diner_LName']) && isset($_REQUEST['Diner_Email'])){
	$id=mysql_real_escape_string($_REQUEST["id"]);
	$book_date=date("Y-m-d",strtotime($_REQUEST["book_date"]));
	$Start_Time=date("g:i a",$_REQUEST["book_time"]);
	$book_fulldate=date("Y-m-d G:i:s",strtotime($_REQUEST["book_date"]." ".$Start_Time));
	
	$End_Time=date("G:i:s",$_REQUEST["book_totime"]);
	//$Look_Ahead=mysql_real_escape_string($_REQUEST["Look_Ahead"]);
	$Size=mysql_real_escape_string($_REQUEST["size"]);
	$Book_Name=mysql_real_escape_string($_REQUEST["Diner_Name"]);
	if($Book_Name=='First Name')
		$Book_Name="";
		
	$Book_LName=mysql_real_escape_string($_REQUEST["Diner_LName"]);
	if($Book_LName=='Last Name')
		$Book_LName="";
	if($Book_LName<>'')
		$Book_Name.=" ".$Book_LName;
	$Book_Email=mysql_real_escape_string($_REQUEST["Diner_Email"]);
	if($Book_Email=='For Reservation Confirmation')
		$Book_Email="";
	$Book_Phone=mysql_real_escape_string($_REQUEST["Diner_Phone"]);
	if($Book_Phone=='555-555-5555')
		$Book_Phone="";
	$Book_Phone_Contact=mysql_real_escape_string($_REQUEST["contact_principle"]);
	//$Book_FirstTime=mysql_real_escape_string($_REQUEST["first_time"]);
	$Book_Notes=mysql_real_escape_string($_REQUEST["Special_Request"]);
	if($Book_Notes=='Please note that not all requests can be accommodated')
		$Book_Notes="";
	$special_events=$_REQUEST["special_events"];
	
	$User_ID=$_REQUEST['User_ID'];
	if($_REQUEST['User_ID']=='')
	{
		// Create an New User
		// get random values
		$query="SELECT id,email_add FROM tbl_registeration WHERE email_add='".$Book_Email."'";
		$qexe=mysql_query($query);
		$qnum=mysql_num_rows($qexe);
		$qres=mysql_fetch_array($qexe);
		if($qnum>0)
		{
			$book_date1=date("m/d/Y",$_REQUEST["book_date"]);
			$book_time1=date("g:i a",$_REQUEST["book_time"]);
			$User_ID=$qres['id'];
			//echo "<script language='javascript'>location.href='table_confirm.php?id=".$_REQUEST['id']."&err=email&book_date=".$book_date1."&meal=".$_REQUEST['meal']."&size=".$_REQUEST['size']."&book_time=".$book_time1."&book_totime=".$_REQUEST['book_totime']."&Diner_Name=".$_REQUEST['Diner_Name']."&Diner_Phone=".$_REQUEST['Diner_Phone']."&Diner_Email=".$_REQUEST['Diner_Email']."&lastname=".$Book_LName."&Special_Request=".$Book_Notes."';</script>";
			//exit;
		}
		else
		{
			$length = 30;
			$characters = "0123456789abcdefghijklmnopqrstuvwxyz";
			$string = "";    
			for ($p = 0; $p < $length; $p++) {
				$string .= $characters[mt_rand(0, strlen($characters))];
			}
			$checksum_register=$string;
			$password=get_rand_letters('6');
			$sql ="insert into tbl_registeration(email_add,encrypt_password,firstname,display_name,mobile_phone,contact_principle,checksum_register,status,cdate) VALUES('".$Book_Email."',AES_ENCRYPT('".$password."','".$EncryptKey."'),'".$_REQUEST['Diner_Name']."','".$_REQUEST['Diner_Name']."','".$_REQUEST['Diner_Phone']."','Mobile','".$checksum_register."',1,now())";
			
			$rec=mysql_query($sql);
			$User_ID=mysql_insert_id();
		
		
		// Send Registeration details to email
		$Subject="Welcome to Way.com, ".$_REQUEST['Diner_Name']."! ";
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
						<td height="26" scope="col"><div align="left"><font style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold; color:#6a6a6a;">Hello</font><font style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold; color:#be3917;">&nbsp;&nbsp;'.$_REQUEST['Diner_Name'].'</font>
						</div></td>
					  </tr>
					  <tr><td height="10" scope="col"></td></tr>
					 <tr>
						<td height="26" scope="col"><div align="left"><font style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold; color:#6a6a6a;">Your Password:</font><font style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold; color:#be3917;">&nbsp;&nbsp;'.$password.'</font>
						</div></td>
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
			$headers = "From: ".$adminmail."\r\n" .
					   'Reply-To: '.$adminmail."\r\n" .
					   'X-Mailer: PHP/' . phpversion();
			$headers .= 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
			include ("template/email-template.php"); 
		$template1=str_replace('DETAILS',$message."",$template);
		$message=$template1;
		
		mail($Book_Email, $Subject, $message, $headers);
	}
	}
	$rand=genRandomString();
	$code=$rand;
	$sql="INSERT INTO tbl_tablebooking_bookings(Book_Owner,Book_UserID,Book_Restaurant,Book_date,Book_datetime,Book_Meal,Book_Start_Time,Book_End_Time,Book_Size,Book_Name,Book_Email,Book_Phone,Book_Contact,Book_FirstTime,special_events,Book_Notes,code,Book_Created) 
	VALUES (".$_REQUEST['Res_UserID'].",'".$User_ID."',".$id.",'".$book_date."','".$book_fulldate."','".$meal."','".$Start_Time."','".$End_Time."','".$Size."','".$Book_Name."','".$Book_Email."','".$Book_Phone."','".$Book_Phone_Contact."','".$Book_FirstTime."','".$special_events."','".$Book_Notes."','".$code."',now())";
	
	mysql_query($sql);
	$Booking_ID=mysql_insert_id();
	if($Booking_ID>0)
	{
	
	$sql="SELECT email,contactName,merchantName,contactAddress,state,city,postalCode,telephone,faxNumber FROM merchant WHERE id=".$_REQUEST['id'];
	$exe=mysql_query($sql);
	$res=mysql_fetch_array($exe);
	$Seller_Email =$res['email'];
	$faxNumber=$res['faxNumber'];
	
	// Send Confirmation mail to seller
		$Subject="An Table was booked through Way.com";
		$message='<table width="564" border="0" align="center" cellpadding="0" cellspacing="0" >
			  <tr>
				<td  valign="top" scope="col"><table width="100%" border="0" cellspacing="0" cellpadding="0">
				 <tr>
					<td height="5" scope="col"></td>
				  </tr>
				   <tr>
					<td scope="col" width="30%" style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold; color:#4e4e4e; line-height:18px;" colspan="2">Hi '.$res['contactName'].',</td>
				  </tr>
				  <tr>
					<td height="5" scope="col"></td>
				  </tr>
				   <tr>
					<td scope="col" width="30%" style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold; color:#4e4e4e; line-height:18px;" colspan="2">An table was Booked on '.$res['merchantName'].'. See details below:</td>
				  </tr>
				  <tr>
					<td height="5" scope="col">&nbsp;</td>
				  </tr>
				  <tr>
					<td scope="col"  width="30%" style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#4e4e4e; line-height:18px;">Restaurant Name</td><td style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#4e4e4e; line-height:18px;" >: '.$res['merchantName'].'</td>
				  </tr>
				  <tr>
					<td scope="col" valign="top" width="30%" style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#4e4e4e; line-height:18px;">Restaurant Address</td><td style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#4e4e4e; line-height:18px;" >: &nbsp;'.$res['contactAddress'].'<br>&nbsp;&nbsp;'.$res['city'].', '.$res['state'].'&nbsp;'.$res['postalCode'].'</td>
				  </tr>';
				  if($res['telephone']<>'')
				   $message.='<tr>
					<td scope="col" width="30%" style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#4e4e4e; line-height:18px;">Restaurant Phone</td><td style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#4e4e4e; line-height:18px;" >: &nbsp;'.$res['telephone'].'</td>
				  </tr>';
				  $message.='<tr>
					<td scope="col" width="30%" style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#4e4e4e; line-height:18px;">Booking ID</td><td style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#4e4e4e; line-height:18px;">: TB'.$Booking_ID.'</td>
				  </tr>
				  <tr>
					<td scope="col" width="30%" style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#4e4e4e; line-height:18px;">Dinner Name</td><td style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#4e4e4e; line-height:18px;">: '.$Book_Name.'</td>
				  </tr>
				  <tr>
					<td scope="col" width="30%" style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#4e4e4e; line-height:18px;">Dinner Phone</td><td style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#4e4e4e; line-height:18px;">: '.$Book_Phone.'</td>
				  </tr>';
				  if($Book_Notes<>'')
				  $message.='<tr>
					<td scope="col" width="30%" style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#4e4e4e; line-height:18px;" >Special Request: </td><td style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#4e4e4e; line-height:18px;" >: '.$Book_Notes.'</td>
				  </tr>';
				  
				  $message.='<tr>
					<td scope="col" width="30%" style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#4e4e4e; line-height:18px;" >Booked for </td><td style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#4e4e4e; line-height:18px;" >: '.$book_date.'</td>
				  </tr>
				   <tr>
					<td scope="col" style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#4e4e4e; line-height:18px;" >Start Time </td><td style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#4e4e4e; line-height:18px;" >: '.$Start_Time.'</td>
				  </tr>
				  
				   <tr>
					<td scope="col" style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#4e4e4e; line-height:18px;" >Party Size </td><td style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#4e4e4e; line-height:18px;" >: '.$Size.'</td>
				  </tr>
				  
				  <tr>
					<td height="5" scope="col">&nbsp;</td>
				  </tr>
				  <tr>
					<td height="5" colspan="2" scope="col" style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold; color:#4e4e4e; line-height:18px;" >Please check your way.com Account for more details.</td>
				  </tr>
				  </table>
				 </td></tr>
				</table>';
		$faxmessage=$message;
		$headers = "From: ".$adminmail."\r\n" .
					   'Reply-To: '.$adminmail."\r\n" .
					   'X-Mailer: PHP/' . phpversion();
			$headers .= 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
			include ("template/email-template.php"); 
		$template1=str_replace('DETAILS',$message."",$template);
		$message=$template1;
		mail($Seller_Email, $Subject, $message, $headers);
		// Send a copy to Support
		mail($adminmail, $Subject, $message, $headers);
		// Fax using EMAIL with Provider Ringcentral
	$my_file = 'fax/TB-8885194198-'.$Booking_ID.'.html';
	$my_file1 = 'TB-8885194198-'.$Booking_ID.'.html';
	$handle = fopen($my_file, 'w');
	fwrite($handle, $faxmessage);
		
	$my_path = $_SERVER['DOCUMENT_ROOT']."/demo/dinning/api/fax/";
	$my_name = "way";
	$my_mail = "wayfax@way.com";
	$my_replyto = "wayfax@way.com";
	$my_subject = "Way FAX";
	$my_message = "FAX";
	
	if($faxNumber=='')
		$faxNumber1='888-781-8138';
	else
		$faxNumber1=$faxNumber;
	if($faxNumber=='')
		$faxNumber='8887818138';
	
	$faxNumber=str_replace("-","",$faxNumber);
	$faxNumber=str_replace("(","",$faxNumber);
	$faxNumber=str_replace(")","",$faxNumber);
	$faxNumber=str_replace(" ","",$faxNumber);
	//$faxNumber='8887818138';
	// Enable Below when make it Live
	
	$status=mail_attachmentnew($my_file1, $my_path, $faxNumber."@rcfax.com", $my_mail, $my_name, $my_replyto, $my_subject, $my_message);
	
	// Store in DB
	$sql="INSERT INTO tbl_fax(Fax_From,Fax_To,Fax_Restaurant,Fax_User,Fax_Content,Fax_Status,Fax_Created) VALUES('888-781-8138','".$faxNumber1."','".$id."',".$User_ID.",'".$my_file1."',".$status.",Now())";
	
	mysql_query($sql);
		// Send Confirmation mail to Buyer
		
			//$Customer_email=fetch_customers_email($_SESSION['User_ID']);
			//$Customer_Name=fetch_customers_name($_SESSION['User_ID']);
			
			$headers = "From: ".$adminmail."\r\n" .
					   'Reply-To: '.$adminmail."\r\n" .
					   'X-Mailer: PHP/' . phpversion();
			$headers .= 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
			$Subject="Your Table was booked through Way.com";
			$message='<table width="564" border="0" align="center" cellpadding="0" cellspacing="0" >
			  <tr>
				<td  valign="top" scope="col"><table width="100%" border="0" cellspacing="0" cellpadding="0">
				 <tr>
					<td height="5" scope="col"></td>
				  </tr>
				   <tr>
					<td scope="col" width="30%" style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold; color:#4e4e4e; line-height:18px;" colspan="2">Hi '.$Book_Name.',</td>
				  </tr>
				  <tr>
					<td height="5" scope="col"></td>
				  </tr>
				   <tr>
					<td scope="col" width="30%" style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold; color:#4e4e4e; line-height:18px;" colspan="2">An table was Booked on '.$res['merchantName'].'. See details below:</td>
				  </tr>
				  <tr>
					<td height="5" scope="col">&nbsp;</td>
				  </tr>
				  <tr>
					<td scope="col" width="30%" style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#4e4e4e; line-height:18px;">Restaurant Name</td><td style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#4e4e4e; line-height:18px;" >: '.$res['merchantName'].'</td>
				  </tr>
				  <tr>
					<td scope="col" valign="top" width="30%" style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#4e4e4e; line-height:18px;">Restaurant Address</td><td style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#4e4e4e; line-height:18px;" >: '.$res['contactAddress'].'<br>&nbsp;&nbsp;'.$res['city'].', '.$res['state'].'&nbsp;'.$res['postalCode'].'</td>
				  </tr>';
				  if($res['telephone']<>'')
				   $message.='<tr>
					<td scope="col" width="30%" style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#4e4e4e; line-height:18px;">Restaurant Phone</td><td style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#4e4e4e; line-height:18px;" >: &nbsp;'.$res['telephone'].'</td>
				  </tr>';
				  $message.='<tr>
					<td scope="col" width="30%" style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#4e4e4e; line-height:18px;">Booking ID</td><td style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#4e4e4e; line-height:18px;">: TB'.$Booking_ID.'</td>
				  </tr>
				  <tr>
					<td scope="col" width="30%" style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#4e4e4e; line-height:18px;">Dinner Name</td><td style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#4e4e4e; line-height:18px;">: '.$Book_Name.'</td>
				  </tr>
				  <tr>
					<td scope="col" width="30%" style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#4e4e4e; line-height:18px;">Dinner Phone</td><td style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#4e4e4e; line-height:18px;">: '.$Book_Phone.'</td>
				  </tr>
				  <tr>
					<td scope="col" width="30%" style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#4e4e4e; line-height:18px;">Dinner Email</td><td style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#4e4e4e; line-height:18px;">: '.$Book_Email.'</td>
				  </tr>';
				  if($Book_Notes<>'')
				  $message.='<tr>
					<td scope="col" width="30%" style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#4e4e4e; line-height:18px;" >Special Request: </td><td style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#4e4e4e; line-height:18px;" >: '.$Book_Notes.'</td>
				  </tr>';
				  
				  $message.='<tr>
					<td scope="col" width="30%" style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#4e4e4e; line-height:18px;" >Booked for </td><td style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#4e4e4e; line-height:18px;" >: '.$book_date.'</td>
				  </tr>
				   <tr>
					<td scope="col" style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#4e4e4e; line-height:18px;" >Start Time </td><td style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#4e4e4e; line-height:18px;" >: '.$Start_Time.'</td>
				  </tr>
				  <tr>
					<td scope="col" style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#4e4e4e; line-height:18px;" >Party Size </td><td style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#4e4e4e; line-height:18px;" >: '.$Size.'</td>
				  </tr>
				  
				  <tr>
					<td height="5" scope="col">&nbsp;</td>
				  </tr>
				  <tr>
					<td height="5" colspan="2" scope="col" style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold; color:#4e4e4e; line-height:18px;" >Please check your way.com Account for more details.</td>
				  </tr>
				  </table>
				 </td></tr>
				</table>';
				
			include ("template/email-template.php"); 
			$template1=str_replace('DETAILS',$message."",$template);
			$message=$template1;
			
			mail($Book_Email, $Subject, $message, $headers);
			//changed by logictreeit solutions
			/*if($mobile==1){
			 	return  $Booking_ID;
			}	
			else{
				print "<script language=javascript>window.location='table_book_confirm.php?rep=sucess&TxnID=".$Booking_ID."&id=".$_REQUEST['id']."';</script>";
				exit;
			}*/
            $content=array("status"=>1,"message"=>"success","booking_id"=>$Booking_ID);
            echo json_encode($content);
            exit;
			//end
		}
		else
		{
			/*if($mobile==1){
			 	return  0;
			}	
			else{
				// falied to Book
				print "<script language=javascript>window.location='table_book_confirm.php?rep=fail&TxnID=0&id=".$_REQUEST['id']."';</script>";
				exit;
			}*/
            $content=array("status"=>0,"message"=>"fail");
            echo json_encode($content);
            exit;
		}
}
else{
    $content=array("status"=>0,"message"=>PARAMETER_MSG);
    echo json_encode($content);
    exit;
}        

?>