<?php
class message
{
	function insertmessage($Buyer_Name,$Cart_Type,$Owner_ID,$Cart_ServiceID,$Order_Id,$UsrID)
	{
		if($_SESSION['User_ID']=='')
			$User_ID=$UsrID;
		else
			$User_ID=$_SESSION['User_ID'];
		$reply_mail="support@way.com";
		$headers = "From: ".$reply_mail."\r\n" .
				   'Reply-To: '.$reply_mail."\r\n" .
				   'X-Mailer: PHP/' . phpversion();
		$headers .= 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		
		GLOBAL $Base_Path,$Host_Path;
		if($Cart_Type=='Activities') {
			$typeID=64;
			$T_ID="PL";
		} else if($Cart_Type=='Tickets') {
			$typeID=71;
			$T_ID="TI";
		} 
		else if($Cart_Type=='Care') {
			$typeID=61;
			$T_ID="CA";
		} else if($Cart_Type=='Parking')  {
			$typeID=45;
			$T_ID="PA";
		}
		// Owner details
		$sql1="select firstname,lastname,street,city,state,email_add from tbl_registeration where id=".$Owner_ID;
		$exe1=mysql_query($sql1);
		$res1=mysql_fetch_array($exe1);
		$Seller_Name=$res1['firstname']." ".$res1['lastname'];
		$Seller_Email=$res1['email_add'];
		// Buyer details
		$sql2="select firstname,lastname,street,city,state,email_add,contact_principle,home_phone,mobile_phone,work_phone,other_phone from tbl_registeration where id=".$User_ID;
		$exe2=mysql_query($sql2);
		$res2=mysql_fetch_array($exe2);
		$Customer_Name=$res2['firstname']." ".$res2['lastname'];
		$Customer_Email=$res2['email_add'];
		if($res2['home_phone']<>'')
			$Customer_Phone=$res2['home_phone'];
		else if($res2['mobile_phone']<>'')
			$Customer_Phone=$res2['mobile_phone'];
		else if($res2['work_phone']<>'')
			$Customer_Phone=$res2['work_phone'];
			
		if($res2['contact_principle']=='Home')
			$Customer_Phone=$res2['home_phone'];
		else if($res2['contact_principle']=='Mobile')
			$Customer_Phone=$res2['mobile_phone'];
		else if($res2['contact_principle']=='Work')
			$Customer_Phone=$res2['work_phone'];
		
		if($Cart_Type=='Tickets') 	
		$Title= GetServiceEventTitle($Order_Id);
		else
		$Title= GetServiceTitle($Cart_ServiceID,$typeID);	
				
		$subject=$Buyer_Name." has purchased ".$Cart_Type." - ".$Title[0];
		//$subject="Re:".$Cart_Type." - ".$Title[0];
		
		// Store Inbox Message for Buyer
		$Msg_Body='<table width="600" border="0" align="center" cellpadding="0" cellspacing="0" >
		<tr><td align="left">
		<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
		<tr><td align="left" width="15%">
		<img src="'.$Host_Path.'images/img_logo_small1.png" border="0" alt="Way" title="Way"></td>
		<td style="padding-left:15px;padding-top:5px;" valign="top">
		<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
		<tr><td align="left" height="20">
		<font style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold; color:#4e4e4e;">Way.com sent this message to '.$Customer_Name.' ('.$Customer_Email.').</font></td></tr>
		<tr><td align="left" height="20"><font style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#4e4e4e;">Your registered full name and email id is included to show this message originated from Way.com.</font></td></tr>
		</table>
		</td></tr>
		</table>
		</td></tr>
		<tr><td height="20"></td></tr>
		<tr bgcolor="#FFED97"><td height="30" align="left" style="padding-left:10px;"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:bold; color:#4e4e4e;">Thanks for your order!</font></td></tr>
		<tr><td>
		<table width="98%" border="0" align="center" cellpadding="0" cellspacing="0">
		<tr><td height="25" align="left"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:bold; color:#4e4e4e;">Hi '.$Customer_Name.',</font></td></tr>
		<tr><td height="10"></td></tr>
		<tr><td height="25" align="left"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:bold; color:#4e4e4e;">Thank you for your order. You can visit My Way any time to view your <a href="'.$Host_Path.'activity.php?act=transaction" class="quicklinks" target="_BLANK">order details</a>.</font></td></tr>
		<tr><td height="10"></td></tr>
		
		<tr><td>
		<table width="98%" border="0" align="center" cellpadding="0" cellspacing="0">
		<tr><td width="50%" valign="top">
		<table width="98%" border="0" align="center" cellpadding="0" cellspacing="0">
		<tr><td height="25" align="left"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:bold; color:#4e4e4e;">Order Details:</font></td></tr>
		<tr><td height="25" align="left"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;"><b>e-Delivery To:</b></font></td></tr>
		<tr><td height="25" align="left"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">'.$Customer_Email.'</font></td></tr>
		<tr><td height="5"></td></tr>
		</table>
		</td><td width="50%" valign="top">
		<table width="98%" border="0" align="center" cellpadding="0" cellspacing="0">
		<tr><td height="25" align="left"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:bold; color:#4e4e4e;">Payment details</font></td></tr>
		<tr><td height="25" align="left"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">Credit Card</font></td></tr>
		<tr><td height="25" align="left"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;"><b>Seller:</b> '.$Seller_Name.'</font></td></tr>
		</table>
		</td></tr>
		</table>
		</td></tr>
		</table>
		</td></tr>
		</table>
		';
		
		$sql="Insert into tbl_message(Msg_FromID,Service_ID,Service_Type,Msg_ToID,Msg_Subject,Msg_Body,Msg_LastViewed_In,Msg_LastViewed_Out,Msg_Approved,Msg_PostedDate) 
		 VALUES 
		 ('$Owner_ID','$Cart_ServiceID','$Cart_Type','$User_ID','".mysql_real_escape_string($subject)."','".mysql_real_escape_string($Msg_Body)."','N','N',1,Now())"; 
		
		$re=mysql_query($sql);
		
		// Store Inbox Message for Seller
		$Query="SELECT * FROM tbl_paymenttransaction WHERE TxnID=".$Order_Id;
			$res = mysql_query($Query);
			$rec1 = mysql_fetch_array($res);
			$to= fetch_customers_email($Owner_ID);	
			$fromemail= fetch_customers_email($User_ID);
			$toname= fetch_customers_name($Owner_ID);
			$fromname= fetch_customers_name($User_ID);
			$fromphone= fetch_customer_phone($User_ID);
			
			//if($Cart_Type=='Tickets')
				//$office_email= fetch_EventOffice_email($Owner_ID);
				
			$TxnDate= date("F j, Y, g:i a",strtotime($rec1['TxnDate']));
			
			if($rec1['from_date']=='0000-00-00 00:00:00' || $rec1['from_date']=='1970-01-01 00:00:00' || $rec1['from_date']=='' || date("F j, Y",strtotime($rec1['from_date']))=='December 31, 1969')
				$from_date='Open';
			else
				$from_date= date("F j, Y ",strtotime($rec1['from_date']));
			$from_date1= date("F j, Y g:i a",strtotime($rec1['from_date']));
			$to_date= date("F j, Y g:i a",strtotime($rec1['to_date']));
			$tomail1=$to;
			
			// Get Parking Details
			if($rec1['Parking_ID']>0)
			{
				$sql="SELECT tbl_parkinglocations.Park_Address,tbl_parkinglocations.Park_City,tbl_parkinglocations.Park_State,tbl_parkinglocations.Park_Zip,P_Lot_Type,tbl_parkinglocations.Additional_Email FROM tbl_parking INNER JOIN tbl_parkinglocations ON tbl_parkinglocations.Park_ID=tbl_parking.P_Location
				WHERE tbl_parking.P_ID=".$rec1['Parking_ID'];
				$parkexe=mysql_query($sql);
				$parkres=mysql_fetch_array($parkexe);
				$Additional_Email=$parkres['Additional_Email'];
				
			}
		$subject="Congratulations, you have received an order!";
		$Msg_Body='<table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
		<tr><td align="left">
		<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
		<tr><td align="left" width="15%">
		<img src="'.$Host_Path.'images/img_logo_small1.png" border="0" alt="Way" title="Way"></td>
		<td style="padding-left:15px;padding-top:5px;" valign="top">
		
		</td></tr>
		</table>
		</td></tr>
		<tr><td height="20"></td></tr>
		<tr bgcolor="#FFED97"><td height="30" align="left" style="padding-left:10px;"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:bold; color:#4e4e4e;">Thanks for your order!</font></td></tr>
		<tr><td>
		<table width="98%" border="0" align="center" cellpadding="0" cellspacing="0">
		<tr><td height="10"></td></tr>
		<tr><td height="25" align="left"><font style="font-family:Arial, Helvetica, sans-serif; font-size:16px; font-weight:bold; color:#4e4e4e;">Congratulations, you have received an order!</font></td></tr>
		<tr><td height="5"></td></tr>
		<tr><td height="25" align="left"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:bold; color:#4e4e4e;">Hi '.$Seller_Name.',</font></td></tr>
		<tr><td height="5"></td></tr>
		<tr><td height="25" align="left"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">You did it! You have received a new order. You can visit My Way any time to view the ';
		if($rec1['Parking_ID']>0)
			$Msg_Body.='<a href="'.$Host_Path.'parkings/parking.php" class="quicklinks" target="_BLANK">order details</a>.';
		else if($rec1['Activity_ID']>0)
			$Msg_Body.='<a href="'.$Host_Path.'play/index.php" class="quicklinks" target="_BLANK">order details</a>.';
		else if($rec1['Ticket_ID']>0)
			$Msg_Body.='<a href="'.$Host_Path.'tickets/index.php" class="quicklinks" target="_BLANK">order details</a>.';
		else if($rec1['Movie_ID']>0)
			$Msg_Body.='<a href="'.$Host_Path.'movies/index.php" class="quicklinks" target="_BLANK">order details</a>.';
		else
			$Msg_Body.='<a href="'.$Host_Path.'play/index.php" class="quicklinks" target="_BLANK">order details</a>.';
		$Msg_Body.='</font></td></tr>
		<tr><td height="5"></td></tr>
		<tr><td colspan="4">
		<table width="100%" border="0">
		<tr>
		<td height="30" width="30%" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">Order No</font><td colspan="3" align="left"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">:&nbsp;'.$T_ID.$Order_Id.'</font></td></td>
	  </tr>	
		<tr>
		<td height="30" width="30%" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">Customer Name</font></td><td align="left" colspan="3"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">:&nbsp;'.$fromname.'</font></td>
	  </tr>
	  <tr>
		<td height="30" width="30%" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">Customer Email</font></td><td align="left" colspan="3"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">:&nbsp;'.$fromemail.'</font></td>
	  </tr>
	  <tr>
		<td height="30" width="30%" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">Customer Phone</font></td><td align="left" colspan="3"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">:&nbsp;'.$fromphone.'</font></td>
	  </tr>
	   <tr>
		<td height="30" width="30%" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">Booked Date</font></td><td align="left" colspan="3"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">:&nbsp;'.$TxnDate.'</font></td>
	  </tr>';
	if($rec1['Activity_ID']>0)
	{
	$Msg_Body.='<tr>
	<td height="30" width="30%" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">Booked for</font></td><td align="left" colspan="3"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">:&nbsp;';
	//if(strtotime($rec1['from_date']) < strtotime("now"))
	if($rec1['from_date']=='0000-00-00 00:00:00' || $rec1['from_date']=='1970-01-01 00:00:00' || $rec1['from_date']=='')
	$Msg_Body.='Open';
	else 	
	$Msg_Body.=date("F j, Y",strtotime($rec1['from_date']));
	$Msg_Body.='</font></td>
	</tr>';
	}
	  if($rec1['Parking_ID']>0)
	  {
		$Msg_Body.='<tr>
		<td height="30" width="30%" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">Parking Arrival Date</font></td><td align="left" colspan="3"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">:&nbsp;'.$from_date.'</font></td>
	  </tr>
	  <tr>
		<td height="30" width="30%" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">Parking Departure Date</font></td><td align="left" colspan="3"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">:&nbsp;'.$to_date.'</font></td>
	  </tr>
	  
	  <tr>
		<td height="30" width="30%" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">Parking Company</font></td><td align="left" colspan="3"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">:&nbsp;';
		if($parkres['C_CompanyName']<>'')
		$Msg_Body.=$parkres['C_CompanyName'];
		else $Msg_Body.=$parkres['C_Person'];
		$Msg_Body.'</font></td>
	  </tr>
	  <tr>
		<td height="30" width="30%" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">Parking Type</font></td><td align="left" colspan="3"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">:&nbsp;'.$parkres['P_Lot_Type'].'</font></td>
	  </tr>
	   <tr>
		<td height="30" width="30%" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">Parking venue</font></td><td align="left" colspan="3"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">:&nbsp;'.$parkres['Park_Address'].',<br>&nbsp;&nbsp;'.$parkres['Park_City'].', '.$parkres['Park_State'].' '.$parkres['Park_Zip'].'</font></td>
	  </tr>';
	  }
	  else
	  {
	  $Msg_Body.='<tr>
		<td height="30" width="30%" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">Book For Date</font></td><td align="left" colspan="3"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">:&nbsp;'.$from_date.'</font></td>
	  </tr>';
	  }
	  $Msg_Body.='<tr>
		<td height="30" width="30%" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">Delivery Type</font></td><td align="left" colspan="3"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">:&nbsp;';
		if($rec1['DeliveryMethod']=='Electronic Confirmation Code')
		$Msg_Body.='Electronic Ticket <a href="#"><img src="'.$Host_Path.'images/info1.png" width="18"  align="top" border="0" alt="Seller Will Instantly Process Your Order and Send a Voucher Electronically" title="Seller Will Instantly Process Your Order and Send a Voucher Electronically"/></a>';
		else if($rec1['DeliveryMethod']=='Electronic Ticket')
		$Msg_Body.='Electronic Ticket <a href="#"><img src="'.$Host_Path.'images/info1.png" width="18"  align="top" border="0" alt="Seller Will Manually Process Your Order and Send a Voucher Electronically." title="Seller Will Manually Process Your Order and Send a Voucher Electronically."/></a>';
		else
		$Msg_Body.=$rec1['DeliveryMethod'];
		$Msg_Body.='</font></td>
	  </tr>';
	  if($rec1['DeliveryMethod']=='Mail')
	  {
		$Msg_Body.='<tr>
		<td height="30" width="30%" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">Delivery Address</font></td><td align="left" colspan="3"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">:&nbsp;';
		$Loc=explode(",",$rec1['DeliveryAddress']);
		$Strlen=strlen($rec1['DeliveryAddress']);
		$cnt=count($Loc);
		$Strlen1=strlen($Loc[$cnt-2]);
		$Strlen2=strlen($Loc[$cnt-1]);
		$Strdiff=$Strlen-($Strlen1+$Strlen2);
		$Loc=ucwords(strtolower(trim($Loc[$cnt-2]))).", ".strtoupper(trim($Loc[$cnt-1]));
		$Street=substr($rec1['DeliveryAddress'], 0,$Strdiff);
		$Street=trim($Street);
		$Street = substr($Street, 0, strlen($Street)-1);
		$Msg_Body.=$Street.'<br>&nbsp;&nbsp;'.$Loc;
		$Msg_Body.='</font></td>
	  </tr>';
	  }
	else if($rec1['DeliveryMethod']=='Electronic Ticket')
		$Msg_Body.='<tr>
		<td height="30" width="30%" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">Email</font></td><td align="left" colspan="3"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">:&nbsp;'.$rec1['DeliveryEmail'].'</font></td>
	  </tr>
		</table>
		</td></tr>
		<tr><td height="5"></td></tr>
		 <tr><td height="25" align="left"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:bold; color:#4e4e4e;">Email reference id: [#'.$Order_Id.'#]</font></td></tr>
		 <tr><td height="5"></td></tr>
		 <tr><td height="25" align="left"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">Please don\'t remove this number. Way.com customer support may ask you for this number, if you should need assistance.</font></td></tr>
		 <tr><td height="5"></td></tr>
		 <tr><td height="25" align="left"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">Way sent this email to '.$Seller_Name.' ('.$Seller_Email.') about your account registered on www.bi.way.com.</font></td></tr>
		 <tr><td height="25" align="left"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">Way.com will periodically send you required emails about the site and your transactions. Visit our Privacy Policy and User Agreement if you have any questions.</font></td></tr>
		 <tr><td height="25" align="left"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">Copyright © 2012 Way.com Inc. All Rights Reserved. Designated trademarks and brands are the property of their respective owners. Way.com and the Way.com logo are trademarks of Way.com Inc. Way.com Inc. is located at 830 Stewart Dr, Sunnyvale, CA 94085.</font></td></tr>
		<tr><td height="5"></td></tr>
		</table>
		</td></tr>
		</table>
		';
		
		$sql="Insert into tbl_message(Msg_FromID,Service_ID,Service_Type,Msg_ToID,Msg_Subject,Msg_Body,Msg_LastViewed_In,Msg_LastViewed_Out,Msg_Approved,Msg_PostedDate) 
		 VALUES 
		 ('$User_ID','$Cart_ServiceID','$Cart_Type','$Owner_ID','".mysql_real_escape_string($subject)."','".mysql_real_escape_string($Msg_Body)."','N','N',1,Now())";
		
		$re=mysql_query($sql);
		$re=1;
		if($re==1)
		{	
			
			$Query="SELECT * FROM tbl_paymenttransaction WHERE TxnID=".$Order_Id;
			$res = mysql_query($Query);
			$rec1 = mysql_fetch_array($res);
			$to= fetch_customers_email($Owner_ID);	
			$toname= fetch_customers_name($Owner_ID);
			$fromname= fetch_customers_name($User_ID);
			$fromphone= fetch_customer_phone($User_ID);
			$TxnDate= date("F j, Y, g:i a",strtotime($rec1['TxnDate']));
			
			if($rec1['from_date']=='0000-00-00 00:00:00' || $rec1['from_date']=='1970-01-01 00:00:00' || $rec1['from_date']=='')
				$from_date='Open';
			else
				$from_date= date("F j, Y ",strtotime($rec1['from_date']));
			$from_date1= date("F j, Y g:i a",strtotime($rec1['from_date']));
			$to_date= date("F j, Y g:i a",strtotime($rec1['to_date']));
			$tomail1=$to;
			
			// Get Parking Details
			if($rec1['Parking_ID']>0)
			{
				$sql="SELECT tbl_parkinglocations.Park_Address,tbl_parkinglocations.Park_City,tbl_parkinglocations.Park_State,tbl_parkinglocations.Park_Zip,P_Lot_Type,tbl_parking.* FROM tbl_parking INNER JOIN tbl_parkinglocations ON tbl_parkinglocations.Park_ID=tbl_parking.P_Location
				WHERE tbl_parking.P_ID=".$rec1['Parking_ID'];
				$parkexe=mysql_query($sql);
				$parkres=mysql_fetch_array($parkexe);
				
			}
			// Get Activity Details
			if($rec1['Activity_ID']>0)
			{
				$sql="SELECT  tbl_activitycompany.*,tbl_activities.Act_Title FROM  tbl_activities 
				INNER JOIN  tbl_activitycompany ON  tbl_activitycompany.C_ID= tbl_activities.Act_CompanyID
				WHERE tbl_activities.Act_ID=".$rec1['Activity_ID'];
				$activitiesexe=mysql_query($sql);
				$activitiesres=mysql_fetch_array($activitiesexe);
				
			}
			// Get Ticket Details
			if($rec1['Ticket_ID']>0)
			{
				$sql="SELECT tbl_tickets.*,tevoevents.*,tevovenues.* FROM tbl_tickets INNER JOIN tevoevents ON tevoevents.eventId=tbl_tickets.Ticket_Event_ID INNER JOIN tevovenues ON tevovenues.venueId=tevoevents.venueId WHERE tbl_tickets.Ticket_ID=".$rec1['Ticket_ID'];
				$ticketexe=mysql_query($sql);
				$ticketres=mysql_fetch_array($ticketexe);
				
			}
			// Get Movie Details
			if($rec1['Movie_ID']>0)
			{
				$sql="Select tbl_movies.*,tbl_movietheater.*,tbl_movies.clicks AS E_Clicks,tbl_movies.views AS T_Views from tbl_movies INNER JOIN tbl_movietheater On tbl_movietheater.MT_ID=tbl_movies.M_Theater WHERE tbl_movies.M_ID=".$rec1['Movie_ID'];
				$movieexe=mysql_query($sql);
				$movieres=mysql_fetch_array($movieexe);
				
			}
			// Seller Confimation mail details
			 $message='<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
				  <tr>
					<td  valign="top" scope="col"><table width="100%" border="0" cellspacing="0" cellpadding="0">
					
					  <tr>
						<td valign="top" scope="col"><table width="94%" border="0" align="center" cellpadding="0" cellspacing="0">
						  <tr>
							<td colspan="4" height="19" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">Hi '.$toname.' &nbsp;&nbsp;</font></td>
						  </tr>
						  <tr>
						   <td height="10" scope="col"></td>
					     </tr>
						<tr>
						<td height="19" scope="col" colspan="5">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">&nbsp;&nbsp;You received an order!</font></td>
					    </tr>					  
						   <tr>
							<td height="19" scope="col">&nbsp;</td>
						  </tr>
						  <tr>
							<td height="30" width="30%" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">Order No</font><td colspan="3" align="left"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">:&nbsp;'.$T_ID.$Order_Id.'</font></td></td>
						  </tr>	
							<tr>
							<td height="30" width="30%" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">Customer Name</font></td><td align="left" colspan="3"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">:&nbsp;'.$fromname.'</font></td>
						  </tr>
						  <tr>
							<td height="30" width="30%" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">Customer Email</font></td><td align="left" colspan="3"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">:&nbsp;'.$fromemail.'</font></td>
						  </tr>
						  <tr>
							<td height="30" width="30%" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">Customer Phone</font></td><td align="left" colspan="3"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">:&nbsp;'.$fromphone.'</font></td>
						  </tr>
						   <tr>
							<td height="30" width="30%" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">Booked Date</font></td><td align="left" colspan="3"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">:&nbsp;'.$TxnDate.'</font></td>
						  </tr>';
						if($rec1['Activity_ID']>0)
						{
						$message.='<tr>
						<td height="30" width="30%" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">Booked for</font></td><td align="left" colspan="3"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">:&nbsp;';
						if($rec1['from_date']=='0000-00-00 00:00:00' || $rec1['from_date']=='1970-01-01 00:00:00' || $rec1['from_date']=='' || date("F j, Y",strtotime($rec1['from_date']))=='December 31, 1969')
						$message.='Open';
						else 	
						$message.=date("F j, Y",strtotime($rec1['from_date']));
						$message.='</font></td>
						</tr>';
							if($rec1['Show_Time']<>'')
							{
								$message.='<tr>
								<td height="30" width="30%" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">Show Time</font></td><td align="left" colspan="3"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">:&nbsp;'.$rec1['Show_Time'].'</font></td>
								</tr>';
							}
							if($rec1['Movie_Name']<>'')
							{
								$message.='<tr>
								<td height="30" width="30%" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">Movie Name</font></td><td align="left" colspan="3"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">:&nbsp;'.$rec1['Movie_Name'].'</font></td>
								</tr>';
							}
							if($rec1['Club_Number']<>'')
							{
								$message.='<tr>
								<td height="30" width="30%" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">Regal or AMC
or Club Number</font></td><td align="left" colspan="3"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">:&nbsp;'.$rec1['Club_Number'].'</font></td>
								</tr>';
							}
						}
						  if($rec1['Parking_ID']>0)
						  {
							$message.='<tr>
							<td height="30" width="30%" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">Expected Arrival Date and Time</font></td><td align="left" colspan="3"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">:&nbsp;'.$from_date.'</font></td>
						  </tr>
						  <tr>
							<td height="30" width="30%" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">Expected Departure Date and Time</font></td><td align="left" colspan="3"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">:&nbsp;'.$to_date.'</font></td>
						  </tr>
						   <tr>
							<td height="30" width="30%" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">Parking Venue</font></td><td align="left" colspan="3"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">:&nbsp;'.$parkres['Park_Address'].',<br>&nbsp;&nbsp;'.$parkres['Park_City'].', '.$parkres['Park_State'].' '.$parkres['Park_Zip'].'</font></td>
						  </tr>
						   <tr>
							<td height="30" width="30%" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">Parking Type</font></td><td align="left" colspan="3"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">:&nbsp;'.$parkres['P_Lot_Type'].'</font></td>
						  </tr>
						   <tr>
							<td height="30" width="30%" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">Pricing Type</font></td><td align="left" colspan="3"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">:&nbsp;'.ucwords($parkres['P_Pricingtype']).'</font></td>
						  </tr>
						   <tr>
							<td height="30" width="30%" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">Rate</font></td><td align="left" colspan="3"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">:&nbsp;';
							if($parkres['P_Pricingtype']=='daily')
								$price=number_format($parkres['P_Dailyprice'],2)." Daily";
							else if($parkres['P_Pricingtype']=='weekly')
								$price=number_format($parkres['P_Weeklyprice'],2)." Weekly";
							else if($parkres['P_Pricingtype']=='monthly')
								$price=number_format($parkres['P_Monthlyprice'],2)." Monthly";
							else if($parkres['P_Pricingtype']=='hourly')
								$price=number_format($parkres['P_FAmt'],2)." Hourly";
							else if($parkres['P_Pricingtype']=='minute')
								$price=number_format($parkres['P_FAmt'],2)." Minute";
							else if($parkres['P_Pricingtype']=='event')
								$price=number_format($parkres['Event_price'],2)." Event";
							$message.=$price.'</font></td>
						  </tr>
						   <tr>
							<td height="30" width="30%" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">Qty</font></td><td align="left" colspan="3"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">:&nbsp;'.$rec1['quantity'].'</font></td>
						  </tr>';
						  }
						  else if($rec1['Activity_ID']>0)
						  {
							$message.='<tr>
							<td height="30" width="30%" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">Activity Name</font></td><td align="left" colspan="3"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">:&nbsp;<a href="'.$Host_Path.'play/activitydetail.php?id='.$rec1['Activity_ID'].'">'.$activitiesres['Act_Title'].'</a></font></td>
						  </tr>';
						  $message.='<tr>
							<td height="30" width="30%" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">Address</font></td><td align="left" colspan="3"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">:&nbsp;'.$activitiesres['C_CompanyName'].'<br>&nbsp;&nbsp;'.$activitiesres['C_Address'].'<br>&nbsp;&nbsp;'.$activitiesres['C_City'].','.$activitiesres['C_State'].' '.$activitiesres['C_Zipcode'].'</font></td>
						  </tr>';
						  }
						   else if($rec1['Ticket_ID']>0)
						  {
							$message.='<tr>
							<td height="30" width="30%" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">Event Date</font></td><td align="left" colspan="3"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">:&nbsp;'.$from_date1.'</font></td>
						  </tr>
						  <tr>
							<td height="30" width="30%" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">Event Venue</font></td><td align="left" colspan="3"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">:&nbsp;'.$rec1['Ticket_EventVenue'].'</font></td>
						  </tr>
						  <tr>
							<td height="30" width="30%" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">Event Section</font></td><td align="left" colspan="3"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">:&nbsp;'.$rec1['Ticket_Section'].'</font></td>
						  </tr>
						  <tr>
							<td height="30" width="30%" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">Event Row</font></td><td align="left" colspan="3"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">:&nbsp;'.$rec1['Ticket_Row'].'</font></td>
						  </tr>
						  ';
						  }
						  else if($rec1['Movie_ID']>0)
						  {
							$message.='<tr>
							<td height="30" width="30%" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">Movie Name</font></td><td align="left" colspan="3"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">:&nbsp;'.$movieres['M_MovieName'].'</font></td>
						  </tr>
						  <tr>
							<td height="30" width="30%" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">Theater</font></td><td align="left" colspan="3"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">:&nbsp;'.$movieres['MT_TheaterName'].'</font></td>
						  </tr>
						  <tr>
							<td height="30" width="30%" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">Address</font></td><td align="left" colspan="3"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">:&nbsp;'.$movieres['MT_Location'].'<br>'.$movieres['MT_City'].','.$movieres['MT_State'].' '.$movieres['MT_Zip'].'</font></td>
						  </tr>
						  <tr>
							<td height="30" width="30%" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">Contact Name</font></td><td align="left" colspan="3"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">:&nbsp;'.$movieres['MT_ContactName'].'</font></td>
						  </tr>
						  <tr>
							<td height="30" width="30%" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">Contact Phone</font></td><td align="left" colspan="3"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">:&nbsp;'.$movieres['MT_ContactPhone'].'</font></td>
						  </tr>
						  <tr>
							<td height="30" width="30%" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">Show Time</font></td><td align="left" colspan="3"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">:&nbsp;'.$from_date1.'</font></td>
						  </tr>
						  ';
						  }
						  else
						  {
						  $message.='<tr>
							<td height="30" width="30%" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">Book For Date</font></td><td align="left" colspan="3"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">:&nbsp;'.$from_date.'</font></td>
						  </tr>';
						  }
						  $message.='<tr>
							<td height="30" width="30%" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">Delivery Type</font></td><td align="left" colspan="3"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">:&nbsp;';
							if($rec1['DeliveryMethod']=='Electronic Confirmation Code')
		$message.='Electronic Ticket <a href="#"><img src="'.$Host_Path.'images/info1.png" width="18"  align="top" border="0" alt="Seller Will Instantly Process Your Order and Send a Voucher Electronically" title="Seller Will Instantly Process Your Order and Send a Voucher Electronically"/></a>';
		else if($rec1['DeliveryMethod']=='Electronic Ticket')
		$message.='Electronic Ticket <a href="#"><img src="'.$Host_Path.'images/info1.png" width="18"  align="top" border="0" alt="Seller Will Manually Process Your Order and Send a Voucher Electronically." title="Seller Will Manually Process Your Order and Send a Voucher Electronically."/></a>';
		else
		$message.=$rec1['DeliveryMethod'];
							$message.='</font></td>
						  </tr>';
						   if($rec1['DeliveryMethod']=='Electronic Ticket' && $rec1['DeliveryEmail']<>'')
						   $message.='<tr>
							<td height="35" width="25%" scope="col">&nbsp;<span style="font-family:din-mediumregular, Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#000;">Delivery Email</span></td><td align="left" colspan="3"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">:&nbsp;&nbsp;&nbsp;'.$rec1['DeliveryEmail'].'</span></td>
						  </tr>';
						  if($rec1['DeliveryMethod']=='Mail')
						  {
							$message.='<tr>
							<td height="30" width="30%" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">Delivery Address</font></td><td align="left" colspan="3"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">:&nbsp;';
							$Loc=explode(",",$rec1['DeliveryAddress']);
							$Strlen=strlen($rec1['DeliveryAddress']);
							$cnt=count($Loc);
							$Strlen1=strlen($Loc[$cnt-2]);
							$Strlen2=strlen($Loc[$cnt-1]);
							$Strdiff=$Strlen-($Strlen1+$Strlen2);
							$Loc=ucwords(strtolower(trim($Loc[$cnt-2]))).", ".strtoupper(trim($Loc[$cnt-1]));
							$Street=substr($rec1['DeliveryAddress'], 0,$Strdiff);
							$Street=trim($Street);
							$Street = substr($Street, 0, strlen($Street)-1);
							$message.=$Street.'<br>&nbsp;&nbsp;'.$Loc;
							$message.='</font></td>
						  </tr>';
						  }
					
						$message.='<tr><td height="5" colspan="5" bgcolor="#cccccc"></td></tr> <tr>
							<td height="25" align="left" scope="col"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:bold; color:#4e4e4e;">Item Name</font></td>';
							if($typeID==64) {
							$message.='<td align="left" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:bold; color:#4e4e4e;">Ticket Type</font></td>';
							}
							$message.='<td align="left" scope="col"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:bold; color:#4e4e4e;">Price</font></td>
							<td align="left" scope="col"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:bold; color:#4e4e4e;"> Quantity</font></td>
							<td align="right" scope="col" style="padding-right:10px;">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:bold; color:#4e4e4e;">Total</font></td>

						  </tr>	
							<tr><td height="5" colspan="5" bgcolor="#cccccc"></td></tr>						  
						   ';	
				 $Query="SELECT tbl_paymenttransaction.* FROM tbl_paymenttransaction WHERE TxnID=".$Order_Id;
					  $res = mysql_query($Query);
					  $Total_Price=0;
					  $Sub_Total=0;
					  $discount=0;
					  $charges=0;
					  while ($rec=mysql_fetch_array($res)) {
						$itemPrice=$rec['Amount']/$rec['quantity'];
						if($rec['charges']>0)
							$charges=$rec['charges'];
						if($rec['Discount']>0)
							$discount=$rec['Discount'];
						$Total_Price=$rec['TotalAmount'];
						$Sub_Total=$rec['Amount'];
							
							if($typeID==45) {
							$resvenue = @mysql_fetch_array(mysql_query("select tbl_parkinglocations.Park_Address AS Title from tbl_parking INNER JOIN tbl_parkinglocations ON tbl_parkinglocations.Park_ID=tbl_parking.P_Location where P_ID=".$Cart_ServiceID)); }
						else if($typeID==64) {
							$resvenue = @mysql_fetch_array(mysql_query("select tbl_activities.Act_Title AS Title,Act_Cat_ID,tbl_activitycompany.C_CompanyName from tbl_activities INNER JOIN tbl_activitycompany ON tbl_activitycompany.C_ID=tbl_activities.Act_CompanyID where Act_ID=".$Cart_ServiceID)); }
						else if($typeID==70) {
							$resvenue = @mysql_fetch_array(mysql_query("select M_MovieName AS Title,tbl_movietheater.MT_TheaterName from tbl_movies INNER JOIN tbl_movietheater ON tbl_movietheater.MT_ID=tbl_movies.M_Theater where Act_ID=".$Cart_ServiceID)); }
						else if($typeID==71) {
							$resvenue = @mysql_fetch_array(mysql_query("Select tevoevents.eventName AS Title,tevoevents.eventName from tbl_tickets INNER JOIN tevoevents ON tevoevents.eventId=tbl_tickets.Ticket_Event_IDwhere Ticket_ID=".$Cart_ServiceID)); }	
							//$Title=$resvenue['Title'];
							$message.=' <tr>
							<td height="25" scope="col" align="left"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">&nbsp;&nbsp;'.trim($Title[0],",").'</font></td>';
							if($typeID==64) {
							
							$Ticket_Types=explode(",",$rec['Ticket_Type']);
							$Ticket_Quanties=explode(",",$rec['Ticket_Quantity']);
							$message.='<td scope="col" align="left">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">';
							$pp=0;
							$message.='<table width="100%">';
							foreach($Ticket_Types as $u=>$v)
							{
								$Quan = @mysql_fetch_array(mysql_query("SELECT Act_Label,Act_Price FROM tbl_activities_price WHERE P_ID=".$v));
								$message.='<tr><td height="25"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">'.$Quan['Act_Label'].'</font></td></tr>';
								
								$pp++;
							}
							$message.='</table></td>';
							$message.='<td scope="col" align="left"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">';
							$pp=0;
							$message.='<table width="100%">';
							foreach($Ticket_Types as $u=>$v)
							{
								$Quan = @mysql_fetch_array(mysql_query("SELECT Act_Label,Act_Price FROM tbl_activities_price WHERE P_ID=".$v));
								$message.='<tr><td height="25"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">$'.number_format($Quan['Act_Price'],2).'</font></td></tr>';
								$pp++;
							}
							$message.='</table></td>';
							$message.='<td scope="col" align="left"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;" align="center">';
							$pp=0;
							$message.='<table width="100%">';
							foreach($Ticket_Types as $u=>$v)
							{
								$Quan = @mysql_fetch_array(mysql_query("SELECT Act_Label,Act_Price FROM tbl_activities_price WHERE P_ID=".$v));
								$message.='<tr><td height="25"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">'.$Ticket_Quanties[$pp].'</font></td></tr>';
								$pp++;
							}
							$message.='</table></td>';
							}
							else if($typeID==70) {
							$Ticket_Types=explode(",",$rec['Ticket_Type']);
							$Ticket_Quanties=explode(",",$rec['Ticket_Quantity']);
							$message.='<td scope="col" align="left">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">';
							$pp=0;
							$message.='<table width="100%">';
							foreach($Ticket_Types as $u=>$v)
							{
								$Quan = @mysql_fetch_array(mysql_query("SELECT MST_TicketType,MST_TicketPrice FROM tbl_movietickets WHERE MT_ID=".$v));
								$message.='<tr><td height="25"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">'.$Quan['MST_TicketType'].'($'.number_format($Quan['MST_TicketPrice'],2).')</font></td></tr>';
								
								$pp++;
							}
							$message.='</table></td>';
							/*$message.='<td scope="col" align="left"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">';
							$pp=0;
							$message.='<table width="100%">';
							foreach($Ticket_Types as $u=>$v)
							{
								$Quan = @mysql_fetch_array(mysql_query("SELECT MST_TicketType,MST_TicketPrice FROM tbl_movietickets WHERE MT_ID=".$v));
								$message.='<tr><td height="25"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">$'.number_format($Quan['MST_TicketPrice'],2).'</font></td></tr>';
								$pp++;
							}
							$message.='</table></td>';*/
							$message.='<td scope="col" align="left"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;" align="center">';
							$pp=0;
							$message.='<table width="100%">';
							foreach($Ticket_Types as $u=>$v)
							{
								$Quan = @mysql_fetch_array(mysql_query("SELECT MST_TicketType,MST_TicketPrice FROM tbl_movietickets WHERE MT_ID=".$v));
								$message.='<tr><td height="25"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">'.$Quan['MST_TicketType'].'('.$Ticket_Quanties[$pp].')</font></td></tr>';
								$pp++;
							}
							$message.='</table></td>';
							}
							else {
							$message.='<td scope="col" align="left"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">$ '.number_format($itemPrice,2).'</font></td>
							<td scope="col" align="left"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;" align="center" align="center">'.$rec['quantity'].'</font></td>';
							}
							
							if($typeID==64) {
							$message.='<td scope="col" align="left"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;" align="center">';
							$pp=0;
							$message.='<table width="100%">';
							foreach($Ticket_Types as $u=>$v)
							{
								$Quan = @mysql_fetch_array(mysql_query("SELECT Act_Label,Act_Price FROM tbl_activities_price WHERE P_ID=".$v));
								$message.='<tr><td height="19" scope="col" align="right" style="padding-right:10px;"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;" align="center">&nbsp;&nbsp;$'.number_format($Ticket_Quanties[$pp]*$Quan['Act_Price'],2).'</font></td></tr>';
								$pp++;
							}
							$message.='</table></td>';
							}
							else
							{
							$message.='<td height="19" scope="col" align="right" style="padding-right:10px;"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;" align="center">&nbsp;&nbsp; $'.number_format($rec['Amount'],2).'</font></td>';
							}
						  $message.='</tr>';
						 }
						 $message.='<tr><td height="5" colspan="5" bgcolor="#cccccc"></td></tr>
						<tr>
						<td height="25" colspan="5" style="padding-right:10px" align="right" scope="col"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;"><b>Subtotal: $'.number_format($Sub_Total,2).'</b></font></td>
					  </tr><tr>
						<td height="25" colspan="5" style="padding-right:10px" align="right" scope="col"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;"><b>Charges: $'.number_format($charges,2).'</b></font></td>
					  </tr>';
					  if($discount>0)
					  $message.='<tr>
						<td height="25" colspan="5" style="padding-right:10px" align="right" scope="col"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;"><b>Discount: $'.number_format($discount,2).'</b></font></td>
					  </tr>';
					$message.='<tr><td height="25" colspan="5" align="right" style="padding-right:10px;" scope="col"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;"><b>Total Amount:  '.number_format($Total_Price,2).'</b></font></td>
					  </tr><tr>
						<td height="19" scope="col">&nbsp;</td>
					  </tr>';
					  if($rec1['PaymentDesc']<>'')
					  {
						  $message.='<tr>
							<td colspan="4" height="25" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">&nbsp;&nbsp;<br>Special Instructions:</b>'.$rec1['PaymentDesc'].'</font></td>
						  </tr>';
						}
						// Points Calculation
						/*if($rec1['Activity_ID']>0)
						{
						$SubCat_ID1=GetPlaySubcat($rec1['Activity_ID'],$typeID);
						if($SubCat_ID1=='86' && $typeID=='64')
						{
							$PointPercentage=GetSubcatPointPercentage($SubCat_ID1);
						}
						else
						{
							$PointPercentage=GetPointPercentage($typeID);
						}
							$CheckoutPoints=($Total_Price*$PointPercentage)/100;
						}
						else
						{
							$PointPercentage=GetPointPercentage($typeID);
							$CheckoutPoints=($Total_Price*$PointPercentage)/100;
						}
					$message.='<tr>
						<td colspan="4" height="25" scope="col" align="right">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">You have earned $'.round($CheckoutPoints,2).' <img src="'.$Host_Path.'images/img_buck.png"border="0" alt="bucks" title="bucks"> toward future purchase.</font></td>
					  </tr>';*/
						$message.='<tr><td height="10" scope="col"></td></tr><tr><td height="10" scope="col"><a href="'.$Host_Path.'orderdetails.php?TxnID='.$Order_Id.'&typ='.$Cart_Type.'" class="quicklinks" target="_BLANK"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;text-decoration:underline">More details</font></a></td></tr><tr><td height="10" scope="col"></td></tr></table>
								 </td>
					  </tr>
					  <tr><td height="10" scope="col"></td></tr>
					</table></td>
				  </tr>
				</table>';
				
				GLOBAL $adminmail;
				$headers = "From: ".$adminmail."\r\n" .
				   'Reply-To: '.$adminmail."\r\n" .
						   'X-Mailer: PHP/' . phpversion();
				$headers .= 'MIME-Version: 1.0' . "\r\n";
				$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
				include ("template/email-template.php"); 
			$template1=str_replace('DETAILS',$message."",$template);
			$message=$template1;
			$subject="You received an order through WAY.com!";
			// Hide below Line in Live
			//$tomail1="jebaraj.h@gmail.com";
			mail($tomail1, $subject, $message, $headers);
			//Store confirmation mail to database
			$my_file = 'emails/'.$T_ID.$Order_Id.'.html';
			$my_file1 = $T_ID.$Order_Id.'.html';
			$handle = fopen($my_file, 'w');
			fwrite($handle, $message);			
			
		// Send a copy to Binu, Jeba & Support
		mail("binu.girija@way.com", $subject, $message, $headers); 
		mail("support@way.com", $subject, $message, $headers); 
		//mail("jebaraj.h@gmail.com", $subject, $message, $headers); 
		if($Cart_Type=='Tickets')
			mail($office_email, $subject, $message, $headers); 
		
			
			// Email to Customer
			$to= fetch_customers_email($User_ID);	
			$toname= fetch_customers_name($User_ID);
			$fromname= fetch_customers_name($User_ID);
			$fromphone= fetch_customer_phone($User_ID);
			$tomail1=$to;
			 $message='<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
				  <tr>
					<td  valign="top" scope="col"><table width="100%" border="0" cellspacing="0" cellpadding="0">
					
					  <tr>
						<td valign="top" scope="col"><table width="94%" border="0" align="center" cellpadding="0" cellspacing="0">
						  <tr>
							<td height="19" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:18px; font-weight:normal; color:#4e4e4e;">Hi '.$toname.', &nbsp;&nbsp;</font></td>
						  </tr>
						 <tr>
						<td height="10" scope="col"></td>
					  </tr>';
					 if($rec1['Activity_ID']>0)
					 {
					 $message.='<tr>
						<td height="19" scope="col" colspan="5">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:18px; font-weight:normal; color:#4e4e4e;">&nbsp;&nbsp;Thank you for your order. The following details is your purchase summary.</font></td>
					  </tr>';
					 }
					 else {
					 $message.='<tr>
						<td height="19" scope="col" colspan="5">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:18px; font-weight:normal; color:#4e4e4e;">&nbsp;&nbsp;Your Order details:</font></td>
					  </tr>';
					  }
					   if($rec1['DeliveryMethod']=='Electronic Ticket') {
					   $message.='<tr>
						<td height="10" scope="col"></td>
					  </tr>
					  <tr>
						<td height="19" scope="col" colspan="5" align="justify" style="line-height:25px;"><div style="padding-left:10px;"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:bold; color:#0b9fd8;">This is NOT an eTicket or the Redemption Information and You may NOT use this order confirmation details as a Ticket or  Voucher for redemption at the Venue.</font></div></td>
					  </tr><tr><td height="4" scope="col"></td></tr><tr>
						<td height="19" scope="col" colspan="5" align="justify" style="line-height:25px;"><div style="padding-left:10px;"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:bold; color:#0b9fd8;">Please look for your eTicket or the Redemption Information in a separate email that should arrive shortly at the email address provided at checkout.</font></div></td>
					  </tr>';
						}
					  $message.='<tr>
						<td height="19" scope="col">&nbsp;</td>
					  </tr>
					  <tr>
							<td height="30" width="30%" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">Order No</font><td colspan="3" align="left"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">:&nbsp;'.$T_ID.$Order_Id.'</font></td></td>
						  </tr>	
							<tr>
							<td height="30" width="30%" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">Customer Name</font></td><td align="left" colspan="3"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">:&nbsp;'.$fromname.'</font></td>
						  </tr>
						  <tr>
							<td height="30" width="30%" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">Customer Email</font></td><td align="left" colspan="3"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">:&nbsp;'.$fromemail.'</font></td>
						  </tr>
						  <tr>
							<td height="30" width="30%" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">Customer Phone</font></td><td align="left" colspan="3"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">:&nbsp;'.$fromphone.'</font></td>
						  </tr>
						  <tr>
							<td height="30" width="30%" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">Booked Date</font></td><td align="left" colspan="3"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">:&nbsp;'.$TxnDate.'</font></td>
						  </tr>';
						  if($rec1['Activity_ID']>0)
						{
						$message.='<tr>
						<td height="30" width="30%" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">Booked for</font></td><td align="left" colspan="3"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">:&nbsp;';
						if($rec1['from_date']=='0000-00-00 00:00:00' || $rec1['from_date']=='1970-01-01 00:00:00' || $rec1['from_date']=='' || date("F j, Y",strtotime($rec1['from_date']))=='December 31, 1969')
						$message.='Open';
						else 	
						$message.=date("F j, Y",strtotime($rec1['from_date']));
						$message.='</font></td>
						</tr>';
							if($rec1['Show_Time']<>'')
							{
								$message.='<tr>
								<td height="30" width="30%" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">Show Time</font></td><td align="left" colspan="3"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">:&nbsp;'.$rec1['Show_Time'].'</font></td>
								</tr>';
							}
							if($rec1['Movie_Name']<>'')
							{
								$message.='<tr>
								<td height="30" width="30%" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">Movie Name</font></td><td align="left" colspan="3"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">:&nbsp;'.$rec1['Movie_Name'].'</font></td>
								</tr>';
							}
							if($rec1['Club_Number']<>'')
							{
								$message.='<tr>
								<td height="30" width="30%" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">Regal or AMC
or Club Number</font></td><td align="left" colspan="3"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">:&nbsp;'.$rec1['Club_Number'].'</font></td>
								</tr>';
							}
						}
						  if($rec1['Parking_ID']>0)
						  {
							$message.='<tr>
							<td height="30" width="30%" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">Expected Arrival Date and Time</font></td><td align="left" colspan="3"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">:&nbsp;'.$from_date.'</font></td>
						  </tr>
						  <tr>
							<td height="30" width="30%" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">Expected Departure Date and Time</font></td><td align="left" colspan="3"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">:&nbsp;'.$to_date.'</font></td>
						  </tr>
						  <tr>
							<td height="30" width="30%" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">Parking Venue</font></td><td align="left" colspan="3"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">:&nbsp;'.$parkres['Park_Address'].',<br>&nbsp;&nbsp;'.$parkres['Park_City'].', '.$parkres['Park_State'].' '.$parkres['Park_Zip'].'</font></td>
						  </tr>
						   <tr>
							<td height="30" width="30%" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">Parking Type</font></td><td align="left" colspan="3"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">:&nbsp;'.$parkres['P_Lot_Type'].'</font></td>
						  </tr>
						   <tr>
							<td height="30" width="30%" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">Pricing Type</font></td><td align="left" colspan="3"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">:&nbsp;'.ucwords($parkres['P_Pricingtype']).'</font></td>
						  </tr>
						  <tr>
							<td height="30" width="30%" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">Rate</font></td><td align="left" colspan="3"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">:&nbsp;';
							if(strtolower($parkres['P_Pricingtype'])=='daily')
								$price=number_format($parkres['P_Dailyprice'],2)." Daily";
							else if(strtolower($parkres['P_Pricingtype'])=='weekly')
								$price=number_format($parkres['P_Weeklyprice'],2)." Weekly";
							else if(strtolower($parkres['P_Pricingtype'])=='monthly')
								$price=number_format($parkres['P_Monthlyprice'],2)." Monthly";
							else if(strtolower($rec['P_Pricingtype'])=='hourly')
								$price=number_format($parkres['P_FAmt'],2)." Hourly";
							else if(strtolower($parkres['P_Pricingtype'])=='minute')
								$price=number_format($parkres['P_FAmt'],2)." Minute";
							else if(strtolower($parkres['P_Pricingtype'])=='event')
								$price=number_format($parkres['Event_price'],2)." Event";
							$message.=$price.'</font></td>
						  </tr>
						   <tr>
							<td height="30" width="30%" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">Qty</font></td><td align="left" colspan="3"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">:&nbsp;'.$rec1['quantity'].'</font></td>
						  </tr>';
						  }
						  else if($rec1['Activity_ID']>0)
						  {
							$message.='<tr>
							<td height="30" width="30%" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">Activity Name</font></td><td align="left" colspan="3"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">:&nbsp;<a href="'.$Host_Path.'play/activitydetail.php?id='.$rec1['Activity_ID'].'">'.$activitiesres['Act_Title'].'</a></font></td>
						  </tr>';
						  $message.='<tr>
							<td height="30" width="30%" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">Address</font></td><td align="left" colspan="3"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">:&nbsp;'.$activitiesres['C_CompanyName'].'<br>&nbsp;&nbsp;'.$activitiesres['C_Address'].'<br>&nbsp;&nbsp;'.$activitiesres['C_City'].','.$activitiesres['C_State'].' '.$activitiesres['C_Zipcode'].'</font></td>
						  </tr>';
						  }
						   else if($rec1['Ticket_ID']>0)
						  {
							$message.='<tr>
							<td height="30" width="30%" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">Event Date</font></td><td align="left" colspan="3"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">:&nbsp;'.$from_date1.'</font></td>
						  </tr>
						  <tr>
							<td height="30" width="30%" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">Event Venue</font></td><td align="left" colspan="3"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">:&nbsp;'.$rec1['Ticket_EventVenue'].'</font></td>
						  </tr>
						  <tr>
							<td height="30" width="30%" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">Event Section</font></td><td align="left" colspan="3"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">:&nbsp;'.$rec1['Ticket_Section'].'</font></td>
						  </tr>
						  <tr>
							<td height="30" width="30%" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">Event Row</font></td><td align="left" colspan="3"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">:&nbsp;'.$rec1['Ticket_Row'].'</font></td>
						  </tr>
						  ';
						  }
						  else if($rec1['Movie_ID']>0)
						  {
							$message.='<tr>
							<td height="30" width="30%" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">Movie Name</font></td><td align="left" colspan="3"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">:&nbsp;'.$movieres['M_MovieName'].'</font></td>
						  </tr>
						  <tr>
							<td height="30" width="30%" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">Theater</font></td><td align="left" colspan="3"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">:&nbsp;'.$movieres['MT_TheaterName'].'</font></td>
						  </tr>
						  <tr>
							<td height="30" width="30%" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">Address</font></td><td align="left" colspan="3"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">:&nbsp;'.$movieres['MT_Location'].'<br>'.$movieres['MT_City'].','.$movieres['MT_State'].' '.$movieres['MT_Zip'].'</font></td>
						  </tr>
						  <tr>
							<td height="30" width="30%" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">Contact Name</font></td><td align="left" colspan="3"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">:&nbsp;'.$movieres['MT_ContactName'].'</font></td>
						  </tr>
						  <tr>
							<td height="30" width="30%" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">Contact Phone</font></td><td align="left" colspan="3"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">:&nbsp;'.$movieres['MT_ContactPhone'].'</font></td>
						  </tr>
						  <tr>
							<td height="30" width="30%" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">Show Time</font></td><td align="left" colspan="3"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">:&nbsp;'.$from_date1.'</font></td>
						  </tr>';
						  }
						  else
						  {
						  $message.='<tr>
							<td height="30" width="30%" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">Book For Date</font></td><td align="left" colspan="3"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">:&nbsp;'.$from_date.'</font></td>
						  </tr>';
						  }
						 $message.='<tr>
							<td height="30" width="30%" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">Delivery Type</font></td><td align="left" colspan="3"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">:&nbsp;';
							if($rec1['DeliveryMethod']=='Electronic Confirmation Code')
						$message.='Electronic Ticket <a href="#"><img src="'.$Host_Path.'images/info1.png" width="18"  align="top" border="0" alt="Seller Will Instantly Process Your Order and Send a Voucher Electronically" title="Seller Will Instantly Process Your Order and Send a Voucher Electronically"/></a>';
						else if($rec1['DeliveryMethod']=='Electronic Ticket')
						$message.='Electronic Ticket <a href="#"><img src="'.$Host_Path.'images/info1.png" width="18"  align="top" border="0" alt="Seller Will Manually Process Your Order and Send a Voucher Electronically." title="Seller Will Manually Process Your Order and Send a Voucher Electronically."/></a>';
						else
						$message.=$rec1['DeliveryMethod'];
							$message.='</font></td>
						  </tr>';
						   if($rec1['DeliveryMethod']=='Electronic Ticket' && $rec1['DeliveryEmail']<>'')
						   $message.='<tr>
							<td height="35" width="25%" scope="col">&nbsp;<span style="font-family:din-mediumregular, Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#000;">Delivery Email</span></td><td align="left" colspan="3"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">:&nbsp;&nbsp;&nbsp;'.$rec1['DeliveryEmail'].'</span></td>
						  </tr>';
						  if($rec1['DeliveryMethod']=='Mail') {
							$message.='<tr>
							<td height="30" width="30%" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">Delivery Address</font></td><td align="left" colspan="3"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">:&nbsp;';
							$Loc=explode(",",$rec1['DeliveryAddress']);
							$Strlen=strlen($rec1['DeliveryAddress']);
							$cnt=count($Loc);
							$Strlen1=strlen($Loc[$cnt-2]);
							$Strlen2=strlen($Loc[$cnt-1]);
							$Strdiff=$Strlen-($Strlen1+$Strlen2);
							$Loc=ucwords(strtolower(trim($Loc[$cnt-2]))).", ".strtoupper(trim($Loc[$cnt-1]));
							$Street=substr($rec1['DeliveryAddress'], 0,$Strdiff);
							$Street=trim($Street);
							$Street = substr($Street, 0, strlen($Street)-1);
							$message.=$Street.'<br>&nbsp;&nbsp;'.$Loc;
							$message.='</font></td>
						  </tr>';
						} 
						$message.='<tr>
						<td height="30" width="30%" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">Way.com Confirmation code</font></td><td align="left" colspan="3"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">:&nbsp;'.$rec1['code'].'</font></td>
					  </tr>';	
						  					  
						$message.='<tr>
						<td height="19" scope="col">&nbsp;</td>
					  </tr><tr><td height="5" colspan="5" bgcolor="#cccccc"></td></tr> <tr>
							<td height="25" align="left" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:bold; color:#4e4e4e;">&nbsp;&nbsp;Item Name</font></td>';
							if($typeID==64) {
							$message.='<td align="left" scope="col"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:bold; color:#4e4e4e;">Ticket Type</font></td>';
							}
							$message.='<td height="25" align="left" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:bold; color:#4e4e4e;">Price</font></td>
							<td height="25" align="left" scope="col"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:bold; color:#4e4e4e;"> Quantity</font></td>
							<td height="25" align="right" scope="col" style="padding-right:10px"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:bold; color:#4e4e4e;"> Total</font></td>
						  </tr>	
							<tr><td height="5" colspan="5" bgcolor="#cccccc"></td></tr>						  
						   ';	
				 $Query="SELECT tbl_paymenttransaction.* FROM tbl_paymenttransaction WHERE TxnID=".$Order_Id;
					  $res = mysql_query($Query);
					  $Total_Price=0;
					  $discount=0;
					  $Sub_Total=0;
					  $charges=0;
					  while ($rec=mysql_fetch_array($res)) {
							$itemPrice=$rec['Amount']/$rec['quantity'];
							$Total_Price=$rec['TotalAmount'];
							if($rec['charges']>0)
								$charges=$rec['charges'];
							if($rec['Discount']>0)
								$discount=$rec['Discount'];
							$Sub_Total=$rec['Amount'];
							
							if($typeID==45) {
							$resvenue = @mysql_fetch_array(mysql_query("select tbl_parkinglocations.Park_Address AS Title from tbl_parking INNER JOIN tbl_parkinglocations ON tbl_parkinglocations.Park_ID=tbl_parking.P_Location where P_ID=".$Cart_ServiceID)); }
						else if($typeID==64) {
							$resvenue = @mysql_fetch_array(mysql_query("select Act_Title AS Title,Act_Cat_ID,tbl_activitycompany.C_CompanyName from tbl_activities INNER JOIN tbl_activitycompany ON tbl_activitycompany.C_ID=tbl_activities.Act_CompanyID where Act_ID=".$Cart_ServiceID)); }
						else if($typeID==70) {
							$resvenue = @mysql_fetch_array(mysql_query("select M_MovieName AS Title,tbl_movietheater.MT_TheaterName from tbl_movies INNER JOIN tbl_movietheater ON tbl_movietheater.MT_ID=tbl_movies.M_Theater where Act_ID=".$Cart_ServiceID)); }
						else if($typeID==71) {
							$resvenue = @mysql_fetch_array(mysql_query("Select tevoevents.eventName AS Title,tevoevents.eventName from tbl_tickets INNER JOIN tevoevents ON tevoevents.eventId=tbl_tickets.Ticket_Event_ID where Ticket_ID=".$Cart_ServiceID)); }	
						//$Title=$resvenue['Title'];
							$Title=trim($Title[0],",");
							$message.=' <tr>
							<td height="25" scope="col" align="left">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">&nbsp;&nbsp;'.$Title.'</font></td>';
							if($typeID==64) {
							
							$Ticket_Types=explode(",",$rec['Ticket_Type']);
							$Ticket_Quanties=explode(",",$rec['Ticket_Quantity']);
							$message.='<td height="25" scope="col" align="left">';
							$pp=0;
							$message.='<table width="100%">';
							foreach($Ticket_Types as $u=>$v)
							{
								$Quan = @mysql_fetch_array(mysql_query("SELECT Act_Label,Act_Price FROM tbl_activities_price WHERE P_ID=".$v));
								$message.='<tr><td height="25"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">'.$Quan['Act_Label'].'</font></td></tr>';
								$pp++;
							}
							$message.='</table></td>';
							$message.='<td height="25" scope="col" align="left"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">';
							$message.='<table width="100%">';
							$pp=0;
							foreach($Ticket_Types as $u=>$v)
							{
								$Quan = @mysql_fetch_array(mysql_query("SELECT Act_Label,Act_Price FROM tbl_activities_price WHERE P_ID=".$v));
								$message.='<tr><td height="25"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">$'.number_format($Quan['Act_Price'],2).'</font></td></tr>';
								$pp++;
							}
							$message.='</table></td>
							<td height="25" scope="col" align="left"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;" align="center">';
							$message.='<table width="100%">';
							$pp=0;
							foreach($Ticket_Types as $u=>$v)
							{
								$Quan = @mysql_fetch_array(mysql_query("SELECT Act_Label,Act_Price FROM tbl_activities_price WHERE P_ID=".$v));
								$message.='<tr><td height="25"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">'.$Ticket_Quanties[$pp].'</font></td></tr>';
								$pp++;
							}
							
							$message.='</table></td>';
							}
							else if($typeID==70) {
							$Ticket_Types=explode(",",$rec['Ticket_Type']);
							$Ticket_Quanties=explode(",",$rec['Ticket_Quantity']);
							$message.='<td scope="col" align="left">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">';
							$pp=0;
							$message.='<table width="100%">';
							foreach($Ticket_Types as $u=>$v)
							{
								$Quan = @mysql_fetch_array(mysql_query("SELECT MST_TicketType,MST_TicketPrice FROM tbl_movietickets WHERE MT_ID=".$v));
								$message.='<tr><td height="25"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">'.$Quan['MST_TicketType'].'</font></td></tr>';
								
								$pp++;
							}
							$message.='</table></td>';
							$message.='<td scope="col" align="left"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">';
							$pp=0;
							$message.='<table width="100%">';
							foreach($Ticket_Types as $u=>$v)
							{
								$Quan = @mysql_fetch_array(mysql_query("SELECT MST_TicketType,MST_TicketPrice FROM tbl_movietickets WHERE MT_ID=".$v));
								$message.='<tr><td height="25"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">$'.number_format($Quan['MST_TicketPrice'],2).'</font></td></tr>';
								$pp++;
							}
							$message.='</table></td>';
							$message.='<td scope="col" align="left"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;" align="center">';
							$pp=0;
							$message.='<table width="100%">';
							foreach($Ticket_Types as $u=>$v)
							{
								$Quan = @mysql_fetch_array(mysql_query("SELECT MST_TicketType,MST_TicketPrice FROM tbl_movietickets WHERE MT_ID=".$v));
								$message.='<tr><td height="25"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">'.$Ticket_Quanties[$pp].'</font></td></tr>';
								$pp++;
							}
							$message.='</table></td>';
							}
							else {
							$message.='<td height="25" scope="col" align="left"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">$ '.number_format($itemPrice,2).'</font></td>
							<td height="25" scope="col" align="left"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;" align="center" align="center">'.$rec['quantity'].'</font></td>';
							}
							if($typeID==64) {
							$message.='<td scope="col" align="left"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;" align="center">';
							$pp=0;
							$message.='<table width="100%">';
							foreach($Ticket_Types as $u=>$v)
							{
								$Quan = @mysql_fetch_array(mysql_query("SELECT Act_Label,Act_Price FROM tbl_activities_price WHERE P_ID=".$v));
								$message.='<tr><td height="19" scope="col" align="right" style="padding-right:10px;"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;" align="center">&nbsp;&nbsp;$'.number_format($Ticket_Quanties[$pp]*$Quan['Act_Price'],2).'</font></td></tr>';
								$pp++;
							}
							$message.='</table></td>';
							}
							else
							{
							$message.='<td height="25" scope="col" align="right" style="padding-right:10px"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;" align="center">$'.number_format($Sub_Total,2).'</font></td>';
						}	
						  $message.='</tr>';
						 }
						 $message.='<tr><td height="5" colspan="5" bgcolor="#cccccc"></td></tr>
						<tr>
						<td height="25" colspan="5" style="padding-right:10px" align="right" scope="col"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;"><b>Subtotal: $'.number_format($Sub_Total,2).'</b></font></td>
					  </tr><tr>
						<td height="25" colspan="5" style="padding-right:10px" align="right" scope="col"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;"><b>Charges: $'.number_format($charges,2).'</b></font></td>
					  </tr>';
					   if($discount>0)
					  $message.='<tr>
						<td height="25" colspan="5" style="padding-right:10px" align="right" scope="col"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;"><b>Discount: $'.number_format($discount,2).'</b></font></td>
					  </tr>';
					  $message.='<tr>
						<td height="25" colspan="5" style="padding-right:10px" align="right" scope="col"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;"><b>Total Amount:  '.number_format($Total_Price,2).'</b></font></td>
					  </tr><tr><td height="10" scope="col"></td></tr>';
						if($rec1['PaymentDesc']<>'') {
						  $message.='<tr>
							<td height="25" colspan="5" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">&nbsp;&nbsp;Special Instructions:'.$rec1['PaymentDesc'].'</font></td>
						  </tr>';
						  }
						  // Points Calculation
						if($rec1['Activity_ID']>0)
						{
						$SubCat_ID1=GetPlaySubcat($rec1['Activity_ID'],$typeID);
						if($SubCat_ID1=='86' && $typeID=='64')
						{
							$PointPercentage=GetSubcatPointPercentage($SubCat_ID1);
						}
						else
						{
							$PointPercentage=GetPointPercentage($typeID);
						}
							$CheckoutPoints=($Total_Price*$PointPercentage)/100;
						}
						else
						{
							$PointPercentage=GetPointPercentage($typeID);
							$CheckoutPoints=($Total_Price*$PointPercentage)/100;
						}
					$message.='<tr>
						<td colspan="4" height="25" scope="col" align="right">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">You have earned $'.round($CheckoutPoints,2).' <img src="'.$Host_Path.'images/img_buck.png"border="0" alt="bucks" title="bucks"> toward future purchase.</font></td>
					  </tr>';
						  $message.='<tr>
						<td height="25" scope="col">&nbsp;</td>
					  </tr>';
				
						$message.='</table>
								 </td>
					  </tr>
					  <tr><td height="10" scope="col"></td></tr>
					</table></td>
				  </tr>
				</table>';
				
				GLOBAL $adminmail;
				$headers = "From: ".$adminmail."\r\n" .
				   'Reply-To: '.$adminmail."\r\n" .
						   'X-Mailer: PHP/' . phpversion();
				$headers .= 'MIME-Version: 1.0' . "\r\n";
				$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
				include ($Base_Path."template/email-template.php"); 
			$template1=str_replace('DETAILS',$message."",$template);
			$message=$template1;
			$subject="Thanks for ordering your tickets through WAY.com!";
			// Hide below Line in Live
			//$tomail1="jebaraj.h@gmail.com";
			mail($tomail1, $subject, $message, $headers);
			//mail("jebaraj.h@gmail.com", $subject, $message, $headers); 
			if($rec1['DeliveryEmail']<>'' && $rec1['DeliveryEmail']<>$tomail1)
				mail($rec1['DeliveryEmail'], $subject, $message, $headers);
			if($Additional_Email<>'')
				mail($Additional_Email, $subject, $message, $headers);
		} 
	
	}
	
	// Parking Confirmation Mail
	function insertParkingmessage($Buyer_Name,$Cart_Type,$Owner_ID,$Cart_ServiceID,$Order_Id,$UsrID)
	{
	if($_SESSION['User_ID']=='')
		$User_ID=$UsrID;
	else
		$User_ID=$_SESSION['User_ID'];
	$reply_mail="support@way.com";
	$headers = "From: ".$reply_mail."\r\n" .
			   'Reply-To: '.$reply_mail."\r\n" .
			   'X-Mailer: PHP/' . phpversion();
	$headers .= 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	
	GLOBAL $Base_Path,$Host_Path;
	$typeID=45;
	$T_ID="PA";
	// Owner details
	$sql1="select firstname,lastname,street,city,state,email_add from tbl_registeration where id=".$Owner_ID;
	$exe1=mysql_query($sql1);
	$res1=mysql_fetch_array($exe1);
	$Seller_Name=$res1['firstname']." ".$res1['lastname'];
	$Seller_Email=$res1['email_add'];
	// Buyer details
	$sql2="select firstname,lastname,street,city,state,email_add,contact_principle,home_phone,mobile_phone,work_phone,other_phone from tbl_registeration where id=".$User_ID;
	$exe2=mysql_query($sql2);
	$res2=mysql_fetch_array($exe2);
	$Customer_Name=$res2['firstname']." ".$res2['lastname'];
	$Customer_Email=$res2['email_add'];
	if($res2['home_phone']<>'')
		$Customer_Phone=$res2['home_phone'];
	else if($res2['mobile_phone']<>'')
		$Customer_Phone=$res2['mobile_phone'];
	else if($res2['work_phone']<>'')
		$Customer_Phone=$res2['work_phone'];
		
	if($res2['contact_principle']=='Home')
		$Customer_Phone=$res2['home_phone'];
	else if($res2['contact_principle']=='Mobile')
		$Customer_Phone=$res2['mobile_phone'];
	else if($res2['contact_principle']=='Work')
		$Customer_Phone=$res2['work_phone'];

	$Title= GetServiceTitle($Cart_ServiceID,$typeID);	
			
	//$subject="Re:".$Cart_Type." - ".$Title[0];
	
	// Store Inbox Message for Buyer
	// removed
	
	// Store Inbox Message for Seller
	$Query="SELECT * FROM tbl_paymenttransaction WHERE TxnID=".$Order_Id;
		$res = mysql_query($Query);
		$rec1 = mysql_fetch_array($res);
		$to= fetch_customers_email($Owner_ID);	
		$fromemail= fetch_customers_email($User_ID);
		$toname= fetch_customers_name($Owner_ID);
		$fromname= fetch_customers_name($User_ID);
		$fromphone= fetch_customer_phone($User_ID);
		
		$TxnDate= date("F j, Y, g:i a",strtotime($rec1['TxnDate']));
		
		if($rec1['from_date']=='0000-00-00 00:00:00' || $rec1['from_date']=='1970-01-01 00:00:00' || $rec1['from_date']=='' || date("F j, Y",strtotime($rec1['from_date']))=='December 31, 1969')
			$from_date='Open';
		else
			$from_date= date("F j, Y ",strtotime($rec1['from_date']));
		$from_date1= date("F j, Y g:i a",strtotime($rec1['from_date']));
		$to_date= date("F j, Y g:i a",strtotime($rec1['to_date']));
		$tomail1=$to;
		
		// Get Parking Details
		if($rec1['Parking_ID']>0)
		{
			$sql="SELECT tbl_parkinglocations.Park_Address,tbl_parkinglocations.Park_City,tbl_parkinglocations.Park_State,tbl_parkinglocations.Park_Zip,P_Lot_Type,tbl_parkinglocations.Additional_Email FROM tbl_parking INNER JOIN tbl_parkinglocations ON tbl_parkinglocations.Park_ID=tbl_parking.P_Location
			WHERE tbl_parking.P_ID=".$rec1['Parking_ID'];
			$parkexe=mysql_query($sql);
			$parkres=mysql_fetch_array($parkexe);
			$Additional_Email=$parkres['Additional_Email'];
			
		}
	
	$re=1;
	if($re==1)
	{	
		
		$Query="SELECT * FROM tbl_paymenttransaction WHERE TxnID=".$Order_Id;
		$res = mysql_query($Query);
		$rec1 = mysql_fetch_array($res);
		$to= fetch_customers_email($Owner_ID);	
		$toname= fetch_customers_name($Owner_ID);
		$fromname= fetch_customers_name($User_ID);
		$fromphone= fetch_customer_phone($User_ID);
		$TxnDate= date("F j, Y, g:i a",strtotime($rec1['TxnDate']));
		
		if($rec1['from_date']=='0000-00-00 00:00:00' || $rec1['from_date']=='1970-01-01 00:00:00' || $rec1['from_date']=='')
			$from_date='Open';
		else
			$from_date= date("F j, Y g:i a",strtotime($rec1['from_date']));
		$from_date1= date("F j, Y g:i a",strtotime($rec1['from_date']));
		$to_date= date("F j, Y g:i a",strtotime($rec1['to_date']));
		$tomail1=$to;
		
		// Get Parking Details
		if($rec1['Parking_ID']>0)
		{
			$sql="SELECT tbl_parkinglocations.NetPark_Code,tbl_parkinglocations.Park_Name,tbl_parkinglocations.Park_Address,tbl_parkinglocations.Park_City,tbl_parkinglocations.Park_State,tbl_parkinglocations.Park_Zip,tbl_parkinglocations.P_Instructions,tbl_parkinglocations.Park_Howtofind,tbl_parkinglocations.Park_Locdesc,tbl_parkinglocations.Park_SpecialInstructions,P_Lot_Type,tbl_parking.*,tbl_parkinglocations.Park_Phone FROM tbl_parking INNER JOIN tbl_parkinglocations ON tbl_parkinglocations.Park_ID=tbl_parking.P_Location
			WHERE tbl_parking.P_ID=".$rec1['Parking_ID'];
			$parkexe=mysql_query($sql);
			$parkres=mysql_fetch_array($parkexe);
			
		}
		// Net Park Reservation
		if($rec1['Parking_ID']>0 && $parkres['NetPark_Code']>0)
		{
			include("netpark-reservation.php");
		}
		$message='<style type="text/css">

	@font-face {
		font-family: \'din-mediumregular\';
		src: url(\'https://www.bi.way.com/fonts/dinm____-webfont.eot\');
		src: url(\'https://www.bi.way.com/fonts/dinm____-webfont.eot?#iefix\') format(\'embedded-opentype\'),
			 url(\'https://www.bi.way.com/fonts/dinm____-webfont.woff\') format(\'woff\'),
			 url(\'https://www.bi.way.com/fonts/dinm____-webfont.ttf\') format(\'truetype\'),
			 url(\'https://www.bi.way.com/fonts/dinm____-webfont.svg#din-mediumregular\') format(\'svg\');
		font-weight: normal;
		font-style: normal;
	
	}
	@font-face {
		font-family: \'UniversalDoomsdayBold\';
		src: url(\'https://www.bi.way.com/fonts/universal_doomsday_bold.eot\');
		src: url(\'https://www.bi.way.com/fonts/universal_doomsday_bold.eot\') format(\'embedded-opentype\'),
			 url(\'https://www.bi.way.com/fonts/universal_doomsday_bold.woff\') format(\'woff\'),
			 url(\'https://www.bi.way.com/fonts/universal_doomsday_bold.ttf\') format(\'truetype\'),
			 url(\'https://www.bi.way.com/fonts/fonts/universal_doomsday_bold.svg#UniversalDoomsdayBold\') format(\'svg\');
	}
	
	
	@font-face {
		font-family: \'din-boldregular\';
		src: url(\'https://www.bi.way.com/fonts/dinb____-webfont.eot\');
		src: url(\'https://www.bi.way.com/fonts/dinb____-webfont.eot?#iefix\') format(\'embedded-opentype\'),
			 url(\'https://www.bi.way.com/fonts/dinb____-webfont.woff\') format(\'woff\'),
			 url(\'https://www.bi.way.com/fonts/dinb____-webfont.ttf\') format(\'truetype\'),
			 url(\'https://www.bi.way.com/fonts/dinb____-webfont.svg#din-boldregular\') format(\'svg\');
		font-weight: normal;
		font-style: normal;
	
	}

</style>
	<div style="width: 841px; height: auto; margin: 0 auto;">
        <div style="background: #000; border-radius: 3px; -moz-border-radius: 3px; -webkit-border-radius: 3px;">
        
        	<div>
            <table width="841" cellpadding="0" cellspacing="0"> 
						<tr>
						<td width="30" align="left" style="padding-top:10px;padding-left:10px;padding-bottom:10px;padding-right:10px;">
            	<img src="https://www.bi.way.com/images/icon_hdg.png" />
                </td><td align="left" style="font: bold 18px din-mediumregular, Arial, Helvetica, sans-serif; color: #fff; text-transform: uppercase; ">
               Your Parking Reservation</td></tr>
			   </table>
            </div>
        
        </div>
        
       
        
        <div style="margin: 14px 0; width: 841px; height: auto; border-top: 1px solid #cfcfcf; border-bottom: 1px solid #cfcfcf; background: #fbfbfb;">
        
				<div>
				
				<table width="841" cellpadding="0" cellspacing="0"> 
						<tr>
						<td align="left" style="padding-top:10px;"><img style="float: left; position: relative; top: 10px;" src="https://www.bi.way.com/images/logoblue.png" /></td>
						<td align="right" style="padding-right:20px;padding-top:10px;">
							<p><span style="font: normal 18px din-mediumregular, Arial, Helvetica, sans-serif; color: #4d4d4f; float: right;  padding-right: 20px;">Reservation ID:<font style="font: normal 18px din-mediumregular, Arial, Helvetica, sans-serif; color: #02779e;"> PA'.$Order_Id.'</font></span></p></td>
						</tr>
						</table>
						
				</div>
				
        		<div style="clear: both"></div>
        </div>
        
      
        
        <div style="margin: 13px 0;">
        
        	<div style="width: 841px;">
            
            	<table width="841" border="0" cellspacing="0" cellpadding="0">
                
                  <tr>
                  
                    <td width="409" scope="col" valign="top">
                    
               		 <table width="409" border="0" cellspacing="0" cellpadding="0" style="font: normal 14px din-mediumregular, Arial, Helvetica, sans-serif; color: #000;">
                        
						 <tr>
                          
                            <td height="41" scope="col"><div align="left" style=" border-bottom: 1px solid #cfcfcf; padding: 14px;">Reserved By:</div></td>
                            
                            
                            <td scope="col"><div align="left" style=" border-bottom: 1px solid #cfcfcf;padding: 14px 0;">'.$fromname.'</div></td>
                            
                         </tr>
						 
						<tr>
                          
                            <td height="98" scope="col" valign="top"><div align="left" style="position: relative; top: 0px; padding: 14px;">Parking Lot:</div></td>
                            
                            
                            <td scope="col" valign="top"><div align="left" style="line-height: 22px;padding-top:10px;" valign="top" >';
							if($parkres['Park_Name']<>'')
								$message.=$parkres['Park_Name'].",<br>";
							$Park_Address=explode(",",$parkres['Park_Address']);
							$pacnt=count($Park_Address);
							$pai=1;
							foreach($Park_Address as $pa=>$pad)
							{
								if($pad<>'') {
								if($pacnt==$pai)
								$message.=$pad.".";
								else
								$message.=$pad.",<br>";
								}
								$pai++;
							}
							if($parkres['Park_Phone']<>'')
								$message.="<br>".$parkres['Park_Phone'];
								
							$message.='&nbsp;&nbsp;</div></td>
                            
                 		</tr>
				
                          
                         <tr >
                          
                            <td height="41" scope="col"><div align="left" style="border-bottom: 1px solid #cfcfcf; border-top: 1px solid #cfcfcf; padding: 14px;">View Map:</div></td>
                            
                            
                            <td scope="col"><div align="left" style=" border-bottom: 1px solid #cfcfcf; border-top: 1px solid #cfcfcf; padding: 14px 0;"><a href="https://www.bi.way.com/parkings/park_detail.php?id='.$rec1['Parking_ID'].'&module=map" target="_BLANK">Click here</a></div></td>
                            
                         </tr>
                         <tr><td height="10"></td></tr> 
                          
                          
                        </table>
                    
                    </td>
                    
                    <td width="20" scope="col">&nbsp;</td>
                    
                    <td width="412" scope="col" valign="top"><table width="412" border="0" cellspacing="0" cellpadding="0" style="font: normal 14px din-mediumregular, Arial, Helvetica, sans-serif; color: #000;">
                    
                      <tr>
                      
                        <td width="153" height="40" scope="col"><div align="left" style=" padding: 14px;">Arrival:</div></td>
                        
                        <td width="259" scope="col"><div align="left">'.$from_date.'</div></td>
                        
                      </tr>
					  
                      
                      <tr >
                      
                        <td height="48" scope="col"><div align="left" style=" border-bottom: 1px solid #cfcfcf; border-top: 1px solid #cfcfcf; padding: 14px;">Return:</div></td>
                        
                        <td scope="col"><div align="left" style=" border-bottom: 1px solid #cfcfcf; border-top: 1px solid #cfcfcf; padding: 14px 0;">'.$to_date.'</div></td>
                        
                      </tr>
                      
                      
                      <tr>
                      
                        <td height="39" scope="col"><div align="left" style=" padding: 14px; border-bottom: 1px solid #cfcfcf; ">Total Duration:</div></td>
                        
                        <td scope="col"><div align="left" style=" border-bottom: 1px solid #cfcfcf;padding: 14px 0;">';
						$date1 = $from_date; 

						$date2 = $to_date; 

						$diff = abs(strtotime($date2) - strtotime($date1)); 

						$years   = floor($diff / (365*60*60*24)); 
						$months  = floor(($diff - $years * 365*60*60*24) / (30*60*60*24)); 
						$days    = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));

						$hours   = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24)/ (60*60)); 

						$minuts  = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24 - $hours*60*60)/ 60); 

						$seconds = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24 - $hours*60*60 - $minuts*60)); 
						if($years>0)
							$diff=$years.' years, '.$months.' months, '.$days.' days, '.$hours.' hours, '.$minuts.' minutes';
						else if($months>0)
							$diff=$months.' months, '.$days.' days, '.$hours.' hours, '.$minuts.' minutes';
						else if($days>0)
							$diff=$days.' days, '.$hours.' hours, '.$minuts.' minutes';
						else if($hours>0)
							$diff=$hours.' hours, '.$minuts.' minutes';
						else if($minuts>0)
							$diff=$minuts.' minutes';
						
						$message.=$diff.'</div></td>
                        
                      </tr>
                      
                    </table>
                    
                    </td>
                    
                  </tr>
                  
                </table>
                
          </div>
            <div style="clear: both"></div>
          <div >
            
            	<div style="position: relative; top: -7px; font: normal 14px din-mediumregular, Arial, Helvetica, sans-serif; color: #fff; text-transform: uppercase;">
                
                	<table width="841" border="0" cellspacing="0" cellpadding="0">
                            
                          <tr style="background: #5e5f61;">
                          
                            <td scope="col" width="841" colspan="4"><div align="left" style="font: normal 14px din-mediumregular, Arial, Helvetica, sans-serif; color: #fff; text-transform: uppercase; padding: 15px 10px 10px 12px;height:20px;">Payment Information</div></td>
                            
                          
                      </tr>
                          
                          <tr><td height="10"></td></tr>
                          
                          <tr>
                          
                            <td scope="col" width="464" style=" border-bottom: 1px solid #cfcfcf; padding-bottom: 20px; padding-left: 12px;"><div align="left" style="position: relative; top: 11px;font: normal 24px din-mediumregular, Arial, Helvetica, sans-serif; color: #000; text-transform: none;">Pre-paid Deposit</div></td>
                            
                            <td scope="col" width="118" style=" border-bottom: 1px solid #cfcfcf; padding-bottom: 10px;"><div align="left"></div></td>
                            
                            <td scope="col" width="96" style=" border-bottom: 1px solid #cfcfcf; padding-bottom: 10px;"><div align="left"></div></td>
                            
                            <td scope="col" width="134" style=" border-bottom: 1px solid #cfcfcf; padding-right: 20px;"><div align="right" style="font: normal 24px din-mediumregular, Arial, Helvetica, sans-serif; color: #000; text-transform: none;">$'.number_format($rec1['Amount'],2).'</div></td>
                      </tr>
                          
                          
                          <tr>
                          
                            <td scope="col" width="464" style=" border-bottom: 1px solid #cfcfcf; padding-bottom: 20px;padding-top: 20px;padding-left: 12px;"><div align="left" style="position: relative; top: 11px;font: normal 24px din-mediumregular, Arial, Helvetica, sans-serif; color: #000; text-transform: none;">Taxes and Fees</div></td>
                            
                            <td scope="col" width="118" style=" border-bottom: 1px solid #cfcfcf; padding-right: 10px;"><div align="left"></div></td>
                            
                            <td scope="col" width="96" style=" border-bottom: 1px solid #cfcfcf; padding-right: 10px;"><div align="left"></div></td>
                            
                            <td scope="col" width="134" style=" border-bottom: 1px solid #cfcfcf; padding-right:20px;"><div align="right" style="font: normal 24px din-mediumregular, Arial, Helvetica, sans-serif; color: #000; text-transform: none;">$'.number_format($rec1['charges'],2).'</div></td>
                      </tr>';
					  if($rec1['Overnight_Fee']>0) {
					  $message.='<tr>
                          
                            <td scope="col" width="464" style=" border-bottom: 1px solid #cfcfcf; padding-bottom: 20px;padding-top: 20px;padding-left: 12px;"><div align="left" style="position: relative; top: 11px;font: normal 24px din-mediumregular, Arial, Helvetica, sans-serif; color: #000; text-transform: none;">Overnight Charges</div></td>
                            
                            <td scope="col" width="118" style=" border-bottom: 1px solid #cfcfcf; padding-right: 10px;"><div align="left"></div></td>
                            
                            <td scope="col" width="96" style=" border-bottom: 1px solid #cfcfcf; padding-right: 10px;"><div align="left"></div></td>
                            
                            <td scope="col" width="134" style=" border-bottom: 1px solid #cfcfcf; padding-right:20px;"><div align="right" style="font: normal 24px din-mediumregular, Arial, Helvetica, sans-serif; color: #000; text-transform: none;">$'.number_format($rec1['Overnight_Fee'],2).'</div></td>
                      </tr>';
                       }   
                          
                    $message.='<tr>
                          
                            <td scope="col" width="464" style=" border-bottom: 1px solid #cfcfcf; padding-bottom: 20px;padding-top: 20px;padding-left: 12px;"><div align="left" style="position: relative; top: 11px;font: normal 24px din-mediumregular, Arial, Helvetica, sans-serif; color: #000; text-transform: none;">Estimated Total</div></td>
                            
                            <td scope="col" width="118" style=" border-bottom: 1px solid #cfcfcf; padding-bottom: 10px;"><div align="left"></div></td>
                            
                            <td scope="col" width="96" style=" border-bottom: 1px solid #cfcfcf; padding-bottom: 10px;"><div align="left"></div></td>
                            
                            <td scope="col" width="134" style=" border-bottom: 1px solid #cfcfcf; padding-right: 20px;"><div align="right" style="font: normal 24px din-mediumregular, Arial, Helvetica, sans-serif; color: #000; text-transform: none;">$'.number_format($rec1['TotalAmount'],2).'</div></td>
                      </tr>         
                          
                          <tr style="background: #fbfbfb;">
                          
                            <td scope="col" width="464" style=" border-bottom: 1px solid #cfcfcf; padding-bottom: 20px; padding-left: 12px;"><div align="left" style="position: relative; top: 11px; font: normal 24px din-mediumregular, Arial, Helvetica, sans-serif; color: #000; text-transform: none;"</div></td>
                            
                            <td scope="col" width="118" style=" border-bottom: 1px solid #cfcfcf; padding-bottom: 10px; font: normal 24px din-mediumregular, Arial, Helvetica, sans-serif; color: #000; text-transform: none; padding-top: 13px;"><div align="left">Total Paid:</div></td>
                            
                            <td scope="col" width="96" style=" border-bottom: 1px solid #cfcfcf; padding-bottom: 10px;"><div align="left"></div></td>
                            
                            <td scope="col" width="134" style=" border-bottom: 1px solid #cfcfcf; padding-right: 20px; font: normal 24px din-mediumregular, Arial, Helvetica, sans-serif; color: #000; padding-top: 13px;"><div align="right"><strong>$'.number_format($rec1['TotalAmount'],2).'</strong></div></td>
                      </tr>
                          
                          <tr>
                          
                            <td colspan="4" style=" padding-bottom: 40px;" scope="col" valign="top"><table width="828" border="0" cellspacing="0" cellpadding="0">
                            
                              <tr>
                              
                                <td width="476" height="151" scope="col" valign="top">
                                
                           	    <table width="841" border="0" align="left" cellpadding="0" cellspacing="0">
                                    
									<tr><td height="30"></td></tr>';
                                     $message.='<tr><td width="841" height="25" scope="col"><div align="left" style="font: normal 14px din-mediumregular, Arial, Helvetica, sans-serif; color: #000;padding-bottom: 10px;"><strong>Parking Type: '.$parkres['P_Lot_Type'].'</strong> <br /><br /><strong>Reservation Receipt:</strong> 
                                        <span style="line-height: 31px; text-transform: none;">This is your Parking Reservation Receipt.  Please present it to ';
										if($parkres['Park_Name']<>'')
											$message.=$parkres['Park_Name'];
										else
											$message.=$parkres['Park_Address'];
										$message.=' for validation.</span><br>
										<span style="font: normal 14px din-mediumregular, Arial, Helvetica, sans-serif; color: #000; margin: 6px 0 1px 0; padding-bottom: 10px;text-transform: none;line-height:25px;">Your total price is calculated based on your chosen departure and return dates and times. Your final price will be determined at the time of your return based on your actual length of stay. This rate cannot be combined with any other discounts or offers.<br>Please make sure to remove your personal belongings.  Do not leave your valuables in your vehicle in plain sight.  While security measures are in place to prevent any theft, this facility or way.com is not liable for any lost or stolen items.</span></div></td>
                                  </tr>';
								  
									$message.='<tr>
                                      <td width="841" height="45" scope="col"><div align="left" style="font: normal 18px din-mediumregular, Arial, Helvetica, sans-serif; color: #000; margin: 6px 0 10px 0;  padding-bottom: 10px;"><strong>Parking Information</strong>
                                       </div></td></tr>';
									 $message.='<tr>
                                      <td width="841" height="45" scope="col"><div align="left" style="font: normal 14px din-mediumregular, Arial, Helvetica, sans-serif; color: #000; margin: 6px 0 1px 0; padding-bottom: 10px;text-transform: none;line-height:25px;">Your price quote covers your entire length of stay based on your chosen departure and return dates and times. Your actual price may be higher or lower, based on the actual length of time your vehicle is parked at  ';
										if($parkres['Park_Name']<>'')
											$message.=$parkres['Park_Name'];
										else
											$message.=$parkres['Park_Address'];
										$message.=' . Your reservation rate may not be combined with any additions coupons, special offers, regular parking rates or other promotion when you exit the parking lot. Special discounts will not apply.</td></tr>'; 
									
									 
									if($parkres['P_Instructions']<>'')  
										$message.='<tr>
                                      <td width="841" height="45" scope="col"><div align="left" style="font: normal 14px din-mediumregular, Arial, Helvetica, sans-serif; color: #000; margin: 6px 0 10px 0;  padding-bottom: 10px;"><strong>Parking Instructions:</strong> <br />
                                        <span style="line-height: 31px; text-transform: none;">'.$parkres['P_Instructions'].'</span></div></td></tr>';
									if($parkres['Park_Howtofind']<>'')  
									 $message.='<tr>
                                      <td width="841" height="45" scope="col"><div align="left" style="font: normal 14px din-mediumregular, Arial, Helvetica, sans-serif; color: #000; margin: 6px 0 10px 0; padding-bottom: 10px;"><strong>How To Find It (Directions):</strong> <br />
                                        <span style="line-height: 31px; text-transform: none;">'.$parkres['Park_Howtofind'].'</span></div></td></tr>';
									  
									if($parkres['Park_Locdesc']<>'') 
										$message.='<tr>
                                      <td width="841" height="45" scope="col"><div align="left" style="font: normal 14px din-mediumregular, Arial, Helvetica, sans-serif; color: #000; margin: 6px 0 1px 0; padding-bottom: 10px;"><strong>Parking Location Description:</strong> <br />
                                        <span style="line-height: 31px; text-transform: none;">'.$parkres['Park_Locdesc'].'</span></div></td></tr>';
									if($parkres['Park_SpecialInstructions']<>'') 
										$message.='<tr>
                                      <td width="841" height="45" scope="col"><div align="left" style="font: normal 14px din-mediumregular, Arial, Helvetica, sans-serif; color: #000; margin: 6px 0 1px 0; padding-bottom: 10px;"><strong>Special Instructions:</strong> <br />
                                        <span style="line-height: 31px; text-transform: none;">'.$parkres['Park_SpecialInstructions'].'</span></div></td></tr>';
									$message.='<tr>
                                       <td width="841" height="2" scope="col"><div align="left" style="font: normal 14px din-mediumregular, Arial, Helvetica, sans-serif; color: #000; margin: 6px 0 10px 0; border-bottom: 1px solid #cfcfcf; padding-bottom: 10px;"></td></tr>
                                </table></td>
                                
                               <tr>
                                      
                                        <td>
                                        
                                        	<div style="font: normal 13px din-mediumregular, Arial, Helvetica, sans-serif; color: #000; text-transform: none; line-height: 22px;text-align:center;">Questions? Please call Way.com at 408-598-3338 immediately or send an email to support@way.com.</div> 
										</td>
										
                               </tr>
                          </tr>
                          
                          <div style="clear: both;"></div>
                    </table>
              </div>
             <div style="padding-top:30px;"></div>
            <div align="center" margin: 0 auto;">
                
                <img src="https://www.bi.way.com/images/slogan.png" /><br/><img style="padding: 14px 0 44px 0;" src="https://www.bi.way.com/images/footer.png" />
                
            </div>  
			<div style="font: normal 13px UniversalDoomsdayBold, Arial, Helvetica, sans-serif; color: #000; text-transform: none; line-height: 22px;text-align:center;">It\'s your life, way makes it simpler.</div> 
			
                
          </div>
         
            
        
        </div>
        
       
        
        
        <div style="clear: both"></div>
        
    
    </div>';

		GLOBAL $adminmail;
		$headers = "From: ".$adminmail."\r\n" .
		   'Reply-To: '.$adminmail."\r\n" .
				   'X-Mailer: PHP/' . phpversion();
		$headers .= 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		
		$subject="You received an order through WAY.com!";
		// Hide below Line in Live
		//$tomail1="jebaraj.h@gmail.com";
		mail($tomail1, $subject, $message, $headers);
		//Store confirmation mail to database
		/*$my_file = 'emails/'.$T_ID.$Order_Id.'.html';
		$my_file1 = $T_ID.$Order_Id.'.html';
		$handle = fopen($my_file, 'w');
		fwrite($handle, $message);	*/		
		
		// Send a copy to Binu, Jeba & Support
		mail("binu.girija@way.com", $subject, $message, $headers); 
		mail("support@way.com", $subject, $message, $headers); 
		//mail("jebaraj.h@gmail.com", $subject, $message, $headers); 
	
		
		// Email to Customer
		$to= fetch_customers_email($User_ID);	
		$toname= fetch_customers_name($User_ID);
		$fromname= fetch_customers_name($User_ID);
		$fromphone= fetch_customer_phone($User_ID);
		$tomail1=$to;
		$message='<style type="text/css">

	@font-face {
		font-family: \'din-mediumregular\';
		src: url(\'https://www.bi.way.com/fonts/dinm____-webfont.eot\');
		src: url(\'https://www.bi.way.com/fonts/dinm____-webfont.eot?#iefix\') format(\'embedded-opentype\'),
			 url(\'https://www.bi.way.com/fonts/dinm____-webfont.woff\') format(\'woff\'),
			 url(\'https://www.bi.way.com/fonts/dinm____-webfont.ttf\') format(\'truetype\'),
			 url(\'https://www.bi.way.com/fonts/dinm____-webfont.svg#din-mediumregular\') format(\'svg\');
		font-weight: normal;
		font-style: normal;
	
	}
	@font-face {
		font-family: \'UniversalDoomsdayBold\';
		src: url(\'https://www.bi.way.com/fonts/universal_doomsday_bold.eot\');
		src: url(\'https://www.bi.way.com/fonts/universal_doomsday_bold.eot\') format(\'embedded-opentype\'),
			 url(\'https://www.bi.way.com/fonts/universal_doomsday_bold.woff\') format(\'woff\'),
			 url(\'https://www.bi.way.com/fonts/universal_doomsday_bold.ttf\') format(\'truetype\'),
			 url(\'https://www.bi.way.com/fonts/fonts/universal_doomsday_bold.svg#UniversalDoomsdayBold\') format(\'svg\');
	}
	
	
	@font-face {
		font-family: \'din-boldregular\';
		src: url(\'https://www.bi.way.com/fonts/dinb____-webfont.eot\');
		src: url(\'https://www.bi.way.com/fonts/dinb____-webfont.eot?#iefix\') format(\'embedded-opentype\'),
			 url(\'https://www.bi.way.com/fonts/dinb____-webfont.woff\') format(\'woff\'),
			 url(\'https://www.bi.way.com/fonts/dinb____-webfont.ttf\') format(\'truetype\'),
			 url(\'https://www.bi.way.com/fonts/dinb____-webfont.svg#din-boldregular\') format(\'svg\');
		font-weight: normal;
		font-style: normal;
	
	}

</style>
	<div style="width: 841px; height: auto; margin: 0 auto;">
        <div style="background: #000; border-radius: 3px; -moz-border-radius: 3px; -webkit-border-radius: 3px;">
        
        	<div>
            <table width="841" cellpadding="0" cellspacing="0"> 
						<tr>
						<td width="30" align="left" style="padding-top:10px;padding-left:10px;padding-bottom:10px;padding-right:10px;">
            	<img src="https://www.bi.way.com/images/icon_hdg.png" />
                </td><td align="left" style="font: bold 18px din-mediumregular, Arial, Helvetica, sans-serif; color: #fff; text-transform: uppercase; ">
               Your Parking Reservation</td></tr>
			   </table>
            </div>
        
        </div>
        
       
        
        <div style="margin: 14px 0; width: 841px; height: auto; border-top: 1px solid #cfcfcf; border-bottom: 1px solid #cfcfcf; background: #fbfbfb;">
        
				<div>
				
				<table width="841" cellpadding="0" cellspacing="0"> 
						<tr>
						<td align="left" style="padding-top:10px;"><img style="float: left; position: relative; top: 10px;" src="https://www.bi.way.com/images/logoblue.png" /></td>
						<td align="right" style="padding-right:20px;padding-top:10px;">
							<p><span style="font: normal 18px din-mediumregular, Arial, Helvetica, sans-serif; color: #4d4d4f; float: right;  padding-right: 20px;">Reservation ID:<font style="font: normal 18px din-mediumregular, Arial, Helvetica, sans-serif; color: #02779e;"> PA'.$Order_Id.'</font></span></p></td>
						</tr>
						</table>
						
				</div>
				
        		<div style="clear: both"></div>
        </div>
        
      
        
        <div style="margin: 13px 0;">
        
        	<div style="width: 841px;">
            
            	<table width="841" border="0" cellspacing="0" cellpadding="0">
                
                  <tr>
                  
                    <td width="409" scope="col" valign="top">
                    
               		 <table width="409" border="0" cellspacing="0" cellpadding="0" style="font: normal 14px din-mediumregular, Arial, Helvetica, sans-serif; color: #000;">
                        
						 <tr>
                          
                            <td height="41" scope="col"><div align="left" style=" border-bottom: 1px solid #cfcfcf; padding: 14px;">Reserved By:</div></td>
                            
                            
                            <td scope="col"><div align="left" style=" border-bottom: 1px solid #cfcfcf;padding: 14px 0;">'.$fromname.'</div></td>
                            
                         </tr>
						 
						<tr>
                          
                            <td height="98" scope="col" valign="top"><div align="left" style="position: relative; top: 0px; padding: 14px;">Parking Lot:</div></td>
                            
                            
                            <td scope="col" valign="top"><div align="left" style="line-height: 22px;padding-top:10px;" valign="top" >';
							if($parkres['Park_Name']<>'')
								$message.=$parkres['Park_Name'].",<br>";
							$Park_Address=explode(",",$parkres['Park_Address']);
							$pacnt=count($Park_Address);
							$pai=1;
							foreach($Park_Address as $pa=>$pad)
							{
								if($pad<>'') {
								if($pacnt==$pai)
								$message.=$pad.".";
								else
								$message.=$pad.",<br>";
								}
								$pai++;
							}
							if($parkres['Park_Phone']<>'')
								$message.="<br>".$parkres['Park_Phone'];
								
							$message.='&nbsp;&nbsp;</div></td>
                            
                 		</tr>
				
                          
                         <tr >
                          
                            <td height="41" scope="col"><div align="left" style="border-bottom: 1px solid #cfcfcf; border-top: 1px solid #cfcfcf; padding: 14px;">View Map:</div></td>
                            
                            
                            <td scope="col"><div align="left" style=" border-bottom: 1px solid #cfcfcf; border-top: 1px solid #cfcfcf; padding: 14px 0;"><a href="https://www.bi.way.com/parkings/park_detail.php?id='.$rec1['Parking_ID'].'&module=map" target="_BLANK">Click here</a></div></td>
                            
                         </tr>
                         <tr><td height="10"></td></tr> 
                          
                          
                        </table>
                    
                    </td>
                    
                    <td width="20" scope="col">&nbsp;</td>
                    
                    <td width="412" scope="col" valign="top"><table width="412" border="0" cellspacing="0" cellpadding="0" style="font: normal 14px din-mediumregular, Arial, Helvetica, sans-serif; color: #000;">
                    
                      <tr>
                      
                        <td width="153" height="40" scope="col"><div align="left" style=" padding: 14px;">Arrival:</div></td>
                        
                        <td width="259" scope="col"><div align="left">'.$from_date.'</div></td>
                        
                      </tr>
					  
                      
                      <tr >
                      
                        <td height="48" scope="col"><div align="left" style=" border-bottom: 1px solid #cfcfcf; border-top: 1px solid #cfcfcf; padding: 14px;">Return:</div></td>
                        
                        <td scope="col"><div align="left" style=" border-bottom: 1px solid #cfcfcf; border-top: 1px solid #cfcfcf; padding: 14px 0;">'.$to_date.'</div></td>
                        
                      </tr>
                      
                      
                      <tr>
                      
                        <td height="39" scope="col"><div align="left" style=" padding: 14px; border-bottom: 1px solid #cfcfcf; ">Total Duration:</div></td>
                        
                        <td scope="col"><div align="left" style=" border-bottom: 1px solid #cfcfcf;padding: 14px 0;">';
						$date1 = $from_date; 

						$date2 = $to_date; 

						$diff = abs(strtotime($date2) - strtotime($date1)); 

						$years   = floor($diff / (365*60*60*24)); 
						$months  = floor(($diff - $years * 365*60*60*24) / (30*60*60*24)); 
						$days    = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));

						$hours   = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24)/ (60*60)); 

						$minuts  = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24 - $hours*60*60)/ 60); 

						$seconds = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24 - $hours*60*60 - $minuts*60)); 
						//if(date("g:i a",strtotime($date1))==date("g:i a",strtotime($date2)))
							//$minuts++;
						if($years>0)
							$diff=$years.' years, '.$months.' months, '.$days.' days, '.$hours.' hours, '.$minuts.' minutes';
						else if($months>0)
							$diff=$months.' months, '.$days.' days, '.$hours.' hours, '.$minuts.' minutes';
						else if($days>0)
							$diff=$days.' days, '.$hours.' hours, '.$minuts.' minutes';
						else if($hours>0)
							$diff=$hours.' hours, '.$minuts.' minutes';
						else if($minuts>0)
							$diff=$minuts.' minutes';
						
						$message.=$diff.'</div></td>
                        
                      </tr>
                      
                    </table>
                    
                    </td>
                    
                  </tr>
                  
                </table>
                
          </div>
            <div style="clear: both"></div>
          <div >
            
            	<div style="position: relative; top: -7px; font: normal 14px din-mediumregular, Arial, Helvetica, sans-serif; color: #fff; text-transform: uppercase;">
                
                	<table width="841" border="0" cellspacing="0" cellpadding="0">
                            
                          <tr style="background: #5e5f61;">
                          
                            <td scope="col" width="841" colspan="4"><div align="left" style="font: normal 14px din-mediumregular, Arial, Helvetica, sans-serif; color: #fff; text-transform: uppercase; padding: 15px 10px 10px 12px;height:20px;">Payment Information</div></td>
                            
                          
                      </tr>
                          
                          <tr><td height="10"></td></tr>
                          
                          <tr>
                          
                            <td scope="col" width="464" style=" border-bottom: 1px solid #cfcfcf; padding-bottom: 20px; padding-left: 12px;"><div align="left" style="position: relative; top: 11px;font: normal 24px din-mediumregular, Arial, Helvetica, sans-serif; color: #000; text-transform: none;">Pre-paid Deposit</div></td>
                            
                            <td scope="col" width="118" style=" border-bottom: 1px solid #cfcfcf; padding-bottom: 10px;"><div align="left"></div></td>
                            
                            <td scope="col" width="96" style=" border-bottom: 1px solid #cfcfcf; padding-bottom: 10px;"><div align="left"></div></td>
                            
                            <td scope="col" width="134" style=" border-bottom: 1px solid #cfcfcf; padding-right: 20px;"><div align="right" style="font: normal 24px din-mediumregular, Arial, Helvetica, sans-serif; color: #000; text-transform: none;">$'.number_format($rec1['Amount'],2).'</div></td>
                      </tr>';
					  if($rec1['Overnight_Fee']>0) {
					  $message.='<tr>
                          
                            <td scope="col" width="464" style=" border-bottom: 1px solid #cfcfcf; padding-bottom: 20px;padding-top: 20px;padding-left: 12px;"><div align="left" style="position: relative; top: 11px;font: normal 24px din-mediumregular, Arial, Helvetica, sans-serif; color: #000; text-transform: none;">Overnight Charges</div></td>
                            
                            <td scope="col" width="118" style=" border-bottom: 1px solid #cfcfcf; padding-right: 10px;"><div align="left"></div></td>
                            
                            <td scope="col" width="96" style=" border-bottom: 1px solid #cfcfcf; padding-right: 10px;"><div align="left"></div></td>
                            
                            <td scope="col" width="134" style=" border-bottom: 1px solid #cfcfcf; padding-right:20px;"><div align="right" style="font: normal 24px din-mediumregular, Arial, Helvetica, sans-serif; color: #000; text-transform: none;">$'.number_format($rec1['Overnight_Fee'],2).'</div></td>
                      </tr>';
                       }   
                          
                    $message.='<tr>
                          
                            <td scope="col" width="464" style=" border-bottom: 1px solid #cfcfcf; padding-bottom: 20px;padding-top: 20px;padding-left: 12px;"><div align="left" style="position: relative; top: 11px;font: normal 24px din-mediumregular, Arial, Helvetica, sans-serif; color: #000; text-transform: none;">Taxes and Fees</div></td>
                            
                            <td scope="col" width="118" style=" border-bottom: 1px solid #cfcfcf; padding-right: 10px;"><div align="left"></div></td>
                            
                            <td scope="col" width="96" style=" border-bottom: 1px solid #cfcfcf; padding-right: 10px;"><div align="left"></div></td>
                            
                            <td scope="col" width="134" style=" border-bottom: 1px solid #cfcfcf; padding-right:20px;"><div align="right" style="font: normal 24px din-mediumregular, Arial, Helvetica, sans-serif; color: #000; text-transform: none;">$'.number_format($rec1['charges'],2).'</div></td>
                      </tr>
                          
                          
                          <tr>
                          
                            <td scope="col" width="464" style=" border-bottom: 1px solid #cfcfcf; padding-bottom: 20px;padding-top: 20px;padding-left: 12px;"><div align="left" style="position: relative; top: 11px;font: normal 24px din-mediumregular, Arial, Helvetica, sans-serif; color: #000; text-transform: none;">Estimated Total</div></td>
                            
                            <td scope="col" width="118" style=" border-bottom: 1px solid #cfcfcf; padding-bottom: 10px;"><div align="left"></div></td>
                            
                            <td scope="col" width="96" style=" border-bottom: 1px solid #cfcfcf; padding-bottom: 10px;"><div align="left"></div></td>
                            
                            <td scope="col" width="134" style=" border-bottom: 1px solid #cfcfcf; padding-right: 20px;"><div align="right" style="font: normal 24px din-mediumregular, Arial, Helvetica, sans-serif; color: #000; text-transform: none;">$'.number_format($rec1['TotalAmount'],2).'</div></td>
                      </tr>         
                          
                          <tr style="background: #fbfbfb;">
                          
                            <td scope="col" width="464" style=" border-bottom: 1px solid #cfcfcf; padding-bottom: 20px; padding-left: 12px;"><div align="left" style="position: relative; top: 11px; font: normal 24px din-mediumregular, Arial, Helvetica, sans-serif; color: #000; text-transform: none;"</div></td>
                            
                            <td scope="col" width="118" style=" border-bottom: 1px solid #cfcfcf; padding-bottom: 10px; font: normal 24px din-mediumregular, Arial, Helvetica, sans-serif; color: #000; text-transform: none; padding-top: 13px;"><div align="left">Total Paid:</div></td>
                            
                            <td scope="col" width="96" style=" border-bottom: 1px solid #cfcfcf; padding-bottom: 10px;"><div align="left"></div></td>
                            
                            <td scope="col" width="134" style=" border-bottom: 1px solid #cfcfcf; padding-right: 20px; font: normal 24px din-mediumregular, Arial, Helvetica, sans-serif; color: #000; padding-top: 13px;"><div align="right"><strong>$'.number_format($rec1['TotalAmount'],2).'</strong></div></td>
                      </tr>
                          
                          <tr>
                          
                            <td colspan="4" style=" padding-bottom: 40px;" scope="col" valign="top"><table width="828" border="0" cellspacing="0" cellpadding="0">
                            
                              <tr>
                              
                                <td width="476" height="151" scope="col" valign="top">
                                
                           	    <table width="841" border="0" align="left" cellpadding="0" cellspacing="0">
                                    
									<tr><td height="30"></td></tr>';
                                     $message.='<tr><td width="841" height="25" scope="col"><div align="left" style="font: normal 14px din-mediumregular, Arial, Helvetica, sans-serif; color: #000;padding-bottom: 10px;"><strong>Parking Type: '.$parkres['P_Lot_Type'].'</strong> <br /><br /><strong>Reservation Receipt:</strong> 
                                        <span style="line-height: 31px; text-transform: none;">This is your Parking Reservation Receipt.  Please present it to ';
										if($parkres['Park_Name']<>'')
											$message.=$parkres['Park_Name'];
										else
											$message.=$parkres['Park_Address'];
										$message.=' for validation.</span><br>
										<span style="font: normal 14px din-mediumregular, Arial, Helvetica, sans-serif; color: #000; margin: 6px 0 1px 0; padding-bottom: 10px;text-transform: none;line-height:25px;">Your total price is calculated based on your chosen departure and return dates and times. Your final price will be determined at the time of your return based on your actual length of stay. This rate cannot be combined with any other discounts or offers.<br>Please make sure to remove your personal belongings.  Do not leave your valuables in your vehicle in plain sight.  While security measures are in place to prevent any theft, this facility or way.com is not liable for any lost or stolen items.</span></div></td>
                                  </tr>';
								  
									$message.='<tr>
                                      <td width="841" height="45" scope="col"><div align="left" style="font: normal 18px din-mediumregular, Arial, Helvetica, sans-serif; color: #000; margin: 6px 0 10px 0;  padding-bottom: 10px;"><strong>Parking Information</strong>
                                       </div></td></tr>';
									 $message.='<tr>
                                      <td width="841" height="45" scope="col"><div align="left" style="font: normal 14px din-mediumregular, Arial, Helvetica, sans-serif; color: #000; margin: 6px 0 1px 0; padding-bottom: 10px;text-transform: none;line-height:25px;">Your price quote covers your entire length of stay based on your chosen departure and return dates and times. Your actual price may be higher or lower, based on the actual length of time your vehicle is parked at  ';
										if($parkres['Park_Name']<>'')
											$message.=$parkres['Park_Name'];
										else
											$message.=$parkres['Park_Address'];
										$message.=' . Your reservation rate may not be combined with any additions coupons, special offers, regular parking rates or other promotion when you exit the parking lot. Special discounts will not apply.</td></tr>'; 
									
									 
									if($parkres['P_Instructions']<>'')  
										$message.='<tr>
                                      <td width="841" height="45" scope="col"><div align="left" style="font: normal 14px din-mediumregular, Arial, Helvetica, sans-serif; color: #000; margin: 6px 0 10px 0;  padding-bottom: 10px;"><strong>Parking Instructions:</strong> <br />
                                        <span style="line-height: 31px; text-transform: none;">'.$parkres['P_Instructions'].'</span></div></td></tr>';
									if($parkres['Park_Howtofind']<>'')  
									 $message.='<tr>
                                      <td width="841" height="45" scope="col"><div align="left" style="font: normal 14px din-mediumregular, Arial, Helvetica, sans-serif; color: #000; margin: 6px 0 10px 0; padding-bottom: 10px;"><strong>How To Find It (Directions):</strong> <br />
                                        <span style="line-height: 31px; text-transform: none;">'.$parkres['Park_Howtofind'].'</span></div></td></tr>';
									  
									if($parkres['Park_Locdesc']<>'') 
										$message.='<tr>
                                      <td width="841" height="45" scope="col"><div align="left" style="font: normal 14px din-mediumregular, Arial, Helvetica, sans-serif; color: #000; margin: 6px 0 1px 0; padding-bottom: 10px;"><strong>Parking Location Description:</strong> <br />
                                        <span style="line-height: 31px; text-transform: none;">'.$parkres['Park_Locdesc'].'</span></div></td></tr>';
									if($parkres['Park_SpecialInstructions']<>'') 
										$message.='<tr>
                                      <td width="841" height="45" scope="col"><div align="left" style="font: normal 14px din-mediumregular, Arial, Helvetica, sans-serif; color: #000; margin: 6px 0 1px 0; padding-bottom: 10px;"><strong>Special Instructions:</strong> <br />
                                        <span style="line-height: 31px; text-transform: none;">'.$parkres['Park_SpecialInstructions'].'</span></div></td></tr>';
									$message.='<tr>
                                       <td width="841" height="2" scope="col"><div align="left" style="font: normal 14px din-mediumregular, Arial, Helvetica, sans-serif; color: #000; margin: 6px 0 10px 0; border-bottom: 1px solid #cfcfcf; padding-bottom: 10px;"></td></tr>
                                </table></td>
                                
                               <tr>
                                      
                                        <td>
                                        
                                        	<div style="font: normal 13px din-mediumregular, Arial, Helvetica, sans-serif; color: #000; text-transform: none; line-height: 22px;text-align:center;">Questions? Please call Way.com at 408-598-3338 immediately or send an email to support@way.com.</div> 
										</td>
										
                               </tr>
                          </tr>
                          
                          <div style="clear: both;"></div>
                    </table>
              </div>
             <div style="padding-top:30px;"></div>
            <div align="center" margin: 0 auto;">
                
                <img src="https://www.bi.way.com/images/slogan.png" /><br/><img style="padding: 14px 0 44px 0;" src="https://www.bi.way.com/images/footer.png" />
                
            </div>  
			<div style="font: normal 13px UniversalDoomsdayBold, Arial, Helvetica, sans-serif; color: #000; text-transform: none; line-height: 22px;text-align:center;">It\'s your life, way makes it simpler.</div> 
			
                
          </div>
         
            
        
        </div>
        
       
        
        
        <div style="clear: both"></div>
        
    
    </div>';
			
		GLOBAL $adminmail;
		$headers = "From: ".$adminmail."\r\n" .
		   'Reply-To: '.$adminmail."\r\n" .
				   'X-Mailer: PHP/' . phpversion();
		$headers .= 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		
		$subject="Thanks for ordering your Parking through WAY.com!";
		// Hide below Line in Live
		//$tomail1="jebaraj.h@gmail.com";
		mail($tomail1, $subject, $message, $headers);
		if($rec1['DeliveryEmail']<>'' && $rec1['DeliveryEmail']<>$tomail1)
			mail($rec1['DeliveryEmail'], $subject, $message, $headers);
		//mail("jebaraj.h@gmail.com", $subject, $message, $headers); 
		if($Additional_Email<>'')
			mail($Additional_Email, $subject, $message, $headers);
	} 

}
	// Dine Confirmation Mail
	function insertmessageDine($Merchant_ID,$Res_Owner_ID,$Items,$Order_Id,$orderdate,$ordertime,$orderAmount,$orderfor,$deliveryFee,$discount)
	{
		GLOBAL $Host_Path;
		if($orderfor=='TakeOut')
			$orderfor='Pickup';
		else
			$orderfor=$orderfor;
		$Cart_Type="Dine";
		if($_SESSION["User_ID"]<>'')
			$User_ID=$_SESSION["User_ID"];
		else
			$User_ID=$_SESSION["GUEST"];
		$pay_type=$_REQUEST['pay_type'];
		// Order details
		$sql="select * from orders where id=".$Order_Id;
		$exe=mysql_query($sql);
		$Orderres=mysql_fetch_array($exe);
		$DeliveryAddress=$Orderres['DeliveryAddress'];
		
		// Merchant details
		$sql="select id,Res_UserID,merchantName,contactName,faxNumber,email,tax,deliveryFee,contactAddress,crossStreet,postalCode,city,state,telephone,gsmNumber from merchant where id=".$Merchant_ID;
		$exe=mysql_query($sql);
		$res=mysql_fetch_array($exe);
		$merchantName=$res['merchantName'];
		$contactName=$res['contactName'];
		$merchantAddress=$res['contactAddress'];
		$merchantCity=$res['city'];
		$merchantState=$res['state'];
		$merchantZip=$res['postalCode'];
		$merchantPhone=$res['telephone'];
		if($merchantPhone=='')
			$merchantPhone=$res['gsmNumber'];
		if($merchantPhone=='')
			$merchantPhone="-";
		$merchantEmail=$res['email'];
		if($merchantEmail=="")
			$merchantEmail=fetch_customers_email($res['Res_UserID']);
		$faxNumber=$res['faxNumber'];
		$Tax=$res['tax'];
		if($orderfor=='TakeOut' || $orderfor=='Pickup')
			$Delivery_Fee=0;
		else
			$Delivery_Fee=$res['deliveryFee'];
		// Owner details
		$sql1="select firstname,lastname,street,city,state,email_add from tbl_registeration where id=".$Res_Owner_ID;
		$exe1=mysql_query($sql1);
		$res1=mysql_fetch_array($exe1);
		
		// Buyer details
		$sql2="select firstname,lastname,street,city,state,email_add,contact_principle,home_phone,mobile_phone,work_phone,other_phone from tbl_registeration where id=".$_SESSION["User_ID"];
		$exe2=mysql_query($sql2);
		$res2=mysql_fetch_array($exe2);
		$Customer_Name=$res2['firstname']." ".$res2['lastname'];
		$Customer_Email=$res2['email_add'];
		if($res2['home_phone']<>'')
			$Customer_Phone=$res2['home_phone'];
		else if($res2['mobile_phone']<>'')
			$Customer_Phone=$res2['mobile_phone'];
		else if($res2['work_phone']<>'')
			$Customer_Phone=$res2['work_phone'];
			
		if($res2['contact_principle']=='Home')
			$Customer_Phone=$res2['home_phone'];
		else if($res2['contact_principle']=='Mobile')
			$Customer_Phone=$res2['mobile_phone'];
		else if($res2['contact_principle']=='Work')
			$Customer_Phone=$res2['work_phone'];
			
		GLOBAL $Time_Zone;
		$currenttime = strtotime(date("F j, Y, g:i a"));
		$Res_Timezone=getTimeZone($Merchant_ID);
		$diff = $Res_Timezone - $Time_Zone;               
		$currenttime += ($diff * 60 * 60);
		$Customer_OrderTime=date("M j, g:i a",$currenttime);
		//$Customer_OrderTime=date("M j, g:i a");
		$Customer_pickupTime=date("M j, g:i a",strtotime($orderdate." ".$ordertime));
		
		$subject=$res2['firstname']." Ordered in ".$res['merchantName'];
		// Store Inbox Message for Buyer
		$Msg_Body='<table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
		<tr><td align="left">
		<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
		<tr><td align="left" width="15%">
		<img src="'.$Host_Path.'images/img_logo_small1.png" border="0" alt="Way" title="Way"></td>
		<td style="padding-left:15px;padding-top:5px;" valign="top">
		<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
		<tr><td align="left" height="20">
		<font style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold; color:#4e4e4e;">Way.com sent this message to '.$Customer_Name.' ('.$Customer_Email.').</font></td></tr>
		<tr><td align="left" height="20"><font style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#4e4e4e;">Your registered full name and email id is included to show this message was originated from Way.com.</font></td></tr>
		</table>
		</td></tr>
		</table>
		</td></tr>
		<tr><td height="20"></td></tr>
		<tr bgcolor="#FFED97"><td height="30" align="left" style="padding-left:10px;"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:bold; color:#4e4e4e;">Thanks for your order!</font></td></tr>
		<tr><td>
		<table width="98%" border="0" align="center" cellpadding="0" cellspacing="0">
		<tr><td height="25" align="left"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:bold; color:#4e4e4e;">Hi '.$Customer_Name.',</font></td></tr>
		<tr><td height="10"></td></tr>
		<tr><td height="25" align="left"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:bold; color:#4e4e4e;">Thank you for your order. You can visit My Way any time to view your <a href="'.$Host_Path.'dine/order-details.php?id='.$Merchant_ID.'&TxnID='.$Order_Id.'&act=tran" class="quicklinks" target="_BLANK">order details</a>.</font></td></tr>
		<tr><td height="10"></td></tr>
		
		<tr><td>
		<table width="98%" border="0" align="center" cellpadding="0" cellspacing="0">
		<tr><td width="50%" valign="top">
		<table width="98%" border="0" align="center" cellpadding="0" cellspacing="0">
		<tr><td height="25" align="left"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:bold; color:#4e4e4e;"><a href="'.$Host_Path.'dine/order-details.php?id='.$Merchant_ID.'&TxnID='.$Order_Id.'&act=tran" class="content14" target="_BLANK">Order Details:</a></font></td></tr>
		<tr><td height="25" align="left"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;"><b>Receipt Sent To:</b></font></td></tr>
		<tr><td height="25" align="left"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">'.$Customer_Email.'</font></td></tr>
		<tr><td height="5"></td></tr>
		</table>
		</td><td width="50%" valign="top">
		<table width="98%" border="0" align="center" cellpadding="0" cellspacing="0">
		<tr><td height="25" align="left"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:bold; color:#4e4e4e;">Payment details</font></td></tr>
		<tr><td height="25" align="left"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">'.$pay_type.'</font></td></tr>
		<tr><td height="25" align="left"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;"><b>Restaurant Name:</b> '.$merchantName.'</font></td></tr>
		</table>
		</td></tr>
		</table>
		</td></tr>
		</table>
		</td></tr>
		</table>
		';
		//echo $Msg_Body;
		//exit;
		$sql="Insert into tbl_message(Msg_FromID,Service_ID,Service_Type,Msg_ToID,Msg_Subject,Msg_Body,Msg_LastViewed_In,Msg_LastViewed_Out,Msg_Approved,Msg_PostedDate) 
		 VALUES 
		 ('".$Res_Owner_ID."','".$Merchant_ID."','".$Cart_Type."','".$_SESSION[User_ID]."','".mysql_real_escape_string($subject)."','".mysql_real_escape_string($Msg_Body)."','".N."','".N."',1,Now())"; 
		 $re=mysql_query($sql);
		// Store Inbox Message for Seller
		$subject="Congratulations, you have received an order!";
		$Msg_Body='<table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
		<tr><td align="left">
		<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
		<tr><td align="left" width="15%">
		<img src="'.$Host_Path.'images/img_logo_small1.png" border="0" alt="Way" title="Way"></td>
		<td style="padding-left:15px;padding-top:5px;" valign="top">
		
		</td></tr>
		</table>
		</td></tr>
		<tr><td height="20"></td></tr>
		<tr bgcolor="#FFED97"><td height="30" align="left" style="padding-left:10px;"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:bold; color:#4e4e4e;">Thanks for your order!</font></td></tr>
		<tr><td>
		<table width="98%" border="0" align="center" cellpadding="0" cellspacing="0">
		<tr><td height="10"></td></tr>
		<tr><td height="25" align="left"><font style="font-family:Arial, Helvetica, sans-serif; font-size:16px; font-weight:bold; color:#4e4e4e;">Congratulations, you have received an order!</font></td></tr>
		<tr><td height="5"></td></tr>
		<tr><td height="25" align="left"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:bold; color:#4e4e4e;">Hi '.$merchantName.',</font></td></tr>
		<tr><td height="5"></td></tr>
		<tr><td height="25" align="left"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">You did it! You have received a new order. You can visit My Way any time to view the <a href="'.$Host_Path.'dine/dine.php" class="quicklinks" target="_BLANK">order details</a>.</font></td></tr>
		<tr><td height="5"></td></tr>
		<tr><td height="25" align="left"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:bold; color:#4e4e4e;">Order details:</font></td></tr>
		<tr><td height="5"></td></tr>';
		// SHOW ORDER DETAILS HERE
		$Msg_Body.='<tr><td>
		<table width="100%" border="0">
		<tr>
			<td height="30" width="30%" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">Order No</font></td><td align="left"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">:&nbsp;DI'.$Order_Id.'</font></td>
		  </tr>	
		  <tr>
			<td height="30" width="30%" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">Restaurant</font></td><td align="left"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">:&nbsp;'.$merchantName.'</font></td>
		  </tr>
			<tr>
			<td height="30" width="30%" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">Customer Name</font></td><td align="left"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">:&nbsp;'.$Customer_Name.'</font></td>
		  </tr>
		  <tr>
			<td height="30" width="30%" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">Customer Phone</font></td><td align="left"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">:&nbsp;'.$Customer_Phone.'</font></td>
		  </tr>
		  <tr>
			<td height="30" width="30%" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">Order Placed At</font></td><td align="left"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">:&nbsp;'.$Customer_OrderTime.'</font></td>
		  </tr>
		  <tr>
			<td height="30" width="30%" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">Order Type</font></td><td align="left"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">:&nbsp;'.$orderfor.'</font></td>
		  </tr>
		  <tr>
			<td height="30" width="30%" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">'.$orderfor.' Time</font></td><td align="left"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">:&nbsp;'.$Customer_pickupTime.'</font></td>
		  </tr>';
		  if($orderfor=='Delivery') {
		  $Msg_Body.='<tr>
			<td height="30" width="30%" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">Delivery Address</font></td><td align="left" colspan="4"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">:&nbsp;';
			$Loc=explode(",",$DeliveryAddress);
			$Strlen=strlen($DeliveryAddress);
			$cnt=count($Loc);
			$Strlen1=strlen($Loc[$cnt-2]);
			$Strlen2=strlen($Loc[$cnt-1]);
			$Strdiff=$Strlen-($Strlen1+$Strlen2);
			$Loc=ucwords(strtolower(trim($Loc[$cnt-2]))).", ".strtoupper(trim($Loc[$cnt-1]));
			$Street=substr($DeliveryAddress, 0,$Strdiff);
			$Street=trim($Street);
			$Street = substr($Street, 0, strlen($Street)-1);
			$Msg_Body.=$Street.'<br>&nbsp;&nbsp;'.$Loc;
			$Msg_Body.='</font></td>
		  </tr>';
		  
		  }
		$Msg_Body.='</table>
		</td></tr>
		<tr><td height="5"></td></tr>
		 <tr><td height="25" align="left"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:bold; color:#4e4e4e;">Email reference id: [#'.$Order_Id.'#]</font></td></tr>
		 <tr><td height="5"></td></tr>
		 <tr><td height="25" align="left"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">Please don\'t remove this number. Way.com customer support may ask you for this number, if you should need assistance.</font></td></tr>
		 <tr><td height="5"></td></tr>
		 <tr><td height="25" align="left"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">Way sent this email to '.$merchantName.' ('.$merchantEmail.') about your account registered on www.bi.way.com.</font></td></tr>
		 <tr><td height="25" align="left"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">Way.com will periodically send you required emails about the site and your transactions. Visit our Privacy Policy and User Agreement if you have any questions.</font></td></tr>
		 <tr><td height="25" align="left"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">Copyright © 2012 Way.com Inc. All Rights Reserved. Designated trademarks and brands are the property of their respective owners. Way.com and the Way.com logo are trademarks of Way.com Inc. Way.com Inc. is located at 830 Stewart Dr, Sunnyvale, CA 94085.</font></td></tr>
		<tr><td height="5"></td></tr>
		</table>
		</td></tr>
		</table>
		</td></tr>
		</table>
		';
		
		$sql="Insert into tbl_message(Msg_FromID,Service_ID,Service_Type,Msg_ToID,Msg_Subject,Msg_Body,Msg_LastViewed_In,Msg_LastViewed_Out,Msg_Approved,Msg_PostedDate) 
		 VALUES 
		 ('".$_SESSION[User_ID]."','".$Merchant_ID."','".$Cart_Type."','".$Res_Owner_ID."','".mysql_real_escape_string($subject)."','".mysql_real_escape_string($Msg_Body)."','".N."','".N."',1,Now())"; 
		
		$re=mysql_query($sql);
		
		$re=1;
		if($re==1)
		{	
			// Send Mail to Restaurant
			$to= $res['email'];	
			$toname= ucwords($res['contactName']);	
			
			$tomail1=$to;
			
			 $message='<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
				  <tr>
					<td  valign="top" scope="col"><table width="100%" border="0" cellspacing="0" cellpadding="0">
					
					  <tr>
						<td valign="top" scope="col"><table width="94%" border="0" align="center" cellpadding="0" cellspacing="0">
						  <tr>
							<td height="19" colspan="4" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">Hi '.$toname.' &nbsp;&nbsp;</font></td>
						  </tr>
						  <tr><td height="10" scope="col"></td></tr>
						  <tr>
							<td height="19" colspan="4" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:16px; font-weight:normal; color:#4e4e4e;">&nbsp;&nbsp;You received an order!</font></td>
						  </tr>	
							<tr><td height="10" scope="col"></td></tr>
							<tr><td colspan="4"><table width="100%" cellpadding="0" cellspacing="0">
						  <tr>
							<td height="30" width="30%" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">Order No</font><td align="left"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">:&nbsp;DI'.$Order_Id.'</font></td></td>
						  </tr>	
						  <tr>
							<td height="30" width="30%" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">Restaurant</font></td><td align="left"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">:&nbsp;'.$merchantName.'<br>&nbsp;&nbsp;'.$merchantCity.', '.$merchantState.'&nbsp;'.$merchantZip.'<br>&nbsp;&nbsp;Phone:'.$merchantPhone.'</font></td>
						  </tr>
							<tr>
							<td height="30" width="30%" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">Customer Name</font></td><td align="left"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">:&nbsp;'.$Customer_Name.'</font></td>
						  </tr>
						  <tr>
							<td height="30" width="30%" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">Customer Phone</font></td><td align="left"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">:&nbsp;'.$Customer_Phone.'</font></td>
						  </tr>
						   <tr>
							<td height="30" width="30%" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">Order Type</font></td><td align="left"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">:&nbsp;'.$orderfor.'</font></td>
						  </tr>
						  <tr>
							<td height="30" width="30%" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">Order Placed At</font></td><td align="left"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">:&nbsp;'.$Customer_OrderTime.'</font></td>
						  </tr>
						  <tr>
							<td height="30" width="30%" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">'.$orderfor.' Time</font></td><td align="left"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">:&nbsp;'.$Customer_pickupTime.'</font></td>
						  </tr>
						  <tr>
							<td height="30" width="30%" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">Mode of Payment</font></td><td align="left"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">:&nbsp;';
							if($_REQUEST['pay_type']=='CreditCard')
								$message.='Credit Card';
							else if($_REQUEST['pay_type']=='Cash')
								$message.="Cash on ".$orderfor." <br>&nbsp;(Customers may receive a confirmatory call from the restaurant.)";
							else
								$message.=$_REQUEST['pay_type'];
							$message.='</font></td></tr>';
						  if($orderfor=='Delivery') {
						  $message.='<tr>
							<td height="30" width="30%" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">Delivery Address</font></td><td align="left" colspan="4"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">:&nbsp;';
							$Loc=explode(",",$DeliveryAddress);
							$Strlen=strlen($DeliveryAddress);
							$cnt=count($Loc);
							$Strlen1=strlen($Loc[$cnt-2]);
							$Strlen2=strlen($Loc[$cnt-1]);
							$Strdiff=$Strlen-($Strlen1+$Strlen2);
							$Loc=ucwords(strtolower(trim($Loc[$cnt-2]))).", ".strtoupper(trim($Loc[$cnt-1]));
							$Street=substr($DeliveryAddress, 0,$Strdiff);
							$Street=trim($Street);
							$Street = substr($Street, 0, strlen($Street)-1);
							$message.=$Street.'<br>&nbsp;&nbsp;'.$Loc;
							$message.='</font></td>
						  </tr>';
						  
						  }
						  $message.='</table></td></tr>						 
						 					  
						   <tr>
						<td height="19" scope="col">&nbsp;</td>
					  </tr>';
					$message.='<tr><td height="5" colspan="4" bgcolor="#cccccc"></td></tr> <tr>
							<td height="19" width="55%" scope="col" align="left">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:bold; color:#4e4e4e;">&nbsp;&nbsp;Item Name</font></td>
							<td height="19" width="15%" align="right" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:bold; color:#4e4e4e;">&nbsp;&nbsp; QTY</font></td>
							<td height="19" width="15%" align="right" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:bold; color:#4e4e4e;">&nbsp;&nbsp;Price</font></td>
							<td height="19" width="15%" align="right" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:bold; color:#4e4e4e;">&nbsp;&nbsp;Total</font></td>
						  </tr>	
							<tr><td height="5" colspan="4" bgcolor="#cccccc"></td></tr> 
						   <tr>
						<td height="5" scope="col">&nbsp;</td>
					  </tr>';
					  $Query="SELECT tbl_cart.*,items.*,menus.menuName FROM tbl_cart INNER JOIN items ON items.id=tbl_cart.Cart_ServiceID INNER JOIN menus ON menus.id=items.menuID WHERE Cart_Type='Dine' AND Sess_ID='".session_id()."'";
					  $res = mysql_query($Query);
					  $Total_Price=0;
					  while ($rec=mysql_fetch_array($res)) {
							$itemPrice=$rec['Amount'];
							$Total_Price=$Total_Price+$itemPrice;
							$message.=' <tr>
							<td height="19" scope="col" valign="top"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">'.$rec['menuName'].' - '.$rec['itemName'];
							if($rec['Size']<>'' && $rec['Size']<>'NORMAL' && $rec['Size']<>'normal' && $rec['Size']<>'Normal' && $rec['Size']<>'1')
								$message.=" (".$rec['Size'].")";
							// Sub Item only for display
							$Query221="SELECT * from tbl_cartsubitems where Cart_ID=".$rec['Cart_ID'];
							$res221 = mysql_query($Query221);
							$SNum=mysql_num_rows($res221);
							$sub_price=0;
							if($SNum>0)
								$message.='<br><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#000000;">';
							$cnt=1;
							$priceval=0;
							$price_index=0;
							while ($result221=mysql_fetch_array($res221)) {
								//$sub_price=$SubGdetails[1];
								$price_index=$result221['price_index'];
								$SubGdetails=SubItemdetails($result221['subgroup_id']);
								$message.=$SubGdetails[0];
								if($result221['subgroup_value']<>'')
									$message.=' - '.$result221['subgroup_value'];
								if($result221['subgroup_x2']==1)
									$message.=' (2X)';
								if($price_index>0)
								{
									$subprices=explode(",",$SubGdetails[1]);
									$priceval=$subprices[$price_index-1];
								}
								else
									$priceval=$SubGdetails[1];
								if($result221['subgroup_value']=='Left Side')
									$priceval=$priceval/2;
								else if($result221['subgroup_value']=='Right Side')
									$priceval=$priceval/2;
								if($result221['subgroup_x2']==1)
									$priceval=$priceval*2;
								if($result221['Free_Toppings']==1) {
									$priceval=0;
								}
								$sub_price=$sub_price+$priceval;
								if($priceval>0)
									$message.=' ($'.number_format($priceval,2).')';
								
								if($cnt<>$SNum)
									$message.=', ';
								$cnt++;
							}
							$message.='</font>';
							$sub_price=$sub_price*$rec['Cart_Quantity'];
							
							// End Sub item
							$message.='</font></td>
							<td height="19" scope="col" align="right" valign="top">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">&nbsp;&nbsp; '.$rec['Cart_Quantity'].'</font></td>
							<td height="19" scope="col" align="right" valign="top">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">&nbsp;&nbsp;$ '.number_format($itemPrice,2).'</font></td>
							<td height="19" scope="col" align="right" valign="top">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">&nbsp;&nbsp;$ '.number_format((($itemPrice*$rec['Cart_Quantity'])+$sub_price),2).'</font></td>
						  </tr>';
								// Insert Sub Items
							$Query21="SELECT * from tbl_cartsubitems where Cart_ID=".$rec['Cart_ID'];
							$res21 = mysql_query($Query21);
							$SNum=mysql_num_rows($res21);
							while ($result1=mysql_fetch_array($res21)) {
								$price_index=$result1['price_index'];
								$SubGdetails=SubItemdetails($result1['subgroup_id']);
								if($price_index>0)
								{
									$subprices=explode(",",$SubGdetails[1]);
									$priceval=$subprices[$price_index-1];
								}
								else
									$priceval=$SubGdetails[1];
								if($result1['subgroup_value']=='Left Side')
									$priceval=$priceval/2;
								else if($result1['subgroup_value']=='Right Side')
									$priceval=$priceval/2;
								if($result1['subgroup_x2']==1)
									$priceval=$priceval*2;
								if($result1['Free_Toppings']<>1) {
									$Total_Price=$Total_Price+$priceval;
									}
								
							}
					  if($rec['Notes']<>'')
					  $message.=' <tr>
							<td height="19" scope="col" valign="top"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;"><b>Instructions:</b> '.$rec['Notes'].'</td></tr>';
						 $message.='<tr>
						<td height="19" scope="col">&nbsp;</td>
					  </tr>';
					  }
					  
						$tax_total = sprintf("%.2f",($_REQUEST['orderSubTotal']*$Tax)/100);
						$order_total = $tax_total+$Total_Price+$Delivery_Fee;
						$message.='<tr><td height="5" colspan="4" bgcolor="#cccccc"></td></tr><tr><td height="2" colspan="4" bgcolor="#cccccc"></td></tr>';
						if($tax_total>0)
						$message.='<tr><td height="30" colspan="4" align="right">
						<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">Tax: <b>$'.number_format($tax_total,2).'</b></font></td></tr>';
						/*if($discount>0) {
					  $message.='<tr>
						<td height="19" colspan="4" align="right" scope="col" style="padding-right:5px;"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#cc0d08;" >&nbsp;Discount : $'. number_format($discount,2).'</font></td>
					  </tr><tr><td height="5" scope="col"></td></tr>';
					  }*/
						if($Delivery_Fee>0)
						$message.='<tr><td height="30" colspan="4" align="right">
						<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">Delivery Fee: <b>$'.number_format($Delivery_Fee,2).'</b></font></td></tr>';
						$message.='<tr>
							<td height="19"  colspan="4" scope="col" align="right">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">&nbsp;&nbsp;Total: <b>$'.number_format(($_REQUEST['orderDineAmount']+$discount),2).'</b></font></td>
						  </tr><tr><td height="5" colspan="4" bgcolor="#cccccc"></td></tr><tr><td height="10" scope="col"></td></tr>';
						  if($_REQUEST['Instructions']<>'Enter special instructions for your order here:' && $_REQUEST['Instructions']<>'')
						$message.=' <tr>
							<td height="19" scope="col" valign="top"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;"><b>Special Instructions:</b> '.$_REQUEST['Instructions'].'</td></tr>';
						/*$PointPercentage=GetPointPercentage('45');
						$CheckoutPoints=($_REQUEST['orderDineAmount']*$PointPercentage)/100;
						$message.='<tr>
						<td colspan="4" height="25" scope="col" align="right">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">You have earned $'.round($CheckoutPoints,2).' <img src="'.$Host_Path.'images/img_buck.png"border="0" alt="bucks" title="bucks"> toward future purchase.</font></td>
					  </tr>';*/
						  $message.='<tr><td height="10" scope="col"></td></tr><tr>
							<td height="19" colspan="2" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">&nbsp;&nbsp;<a href="'.$Host_Path.'dine/orderdetails.php?TxnID='.$Order_Id.'"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;text-decoration:underline">See more details.</a> </font></font></td>
						  </tr>	</table>
								 </td>
					  </tr>
					  <tr><td height="10" scope="col"></td></tr>
					</table></td>
				  </tr>
				</table>';
		//echo "<br>".$message;
		//echo $tomail1;
		
	// SEND FAX
		$faxmessage='<table width="720" border="0" align="center" cellpadding="0" cellspacing="0">
				  <tr>
					<td  valign="top" scope="col"><table width="100%" border="0" cellspacing="0" cellpadding="0">
					  <tr>
						<td valign="top" scope="col"><table width="98%" border="0" align="center" cellpadding="0" cellspacing="0">
						   <tr>
							<td height="19" colspan="4" align="left" scope="col">&nbsp;<font style="font-family:Lucida; font-size:36px; font-weight:bold; color:#000000;">    Attention '.$contactName.',</font></td>
						  </tr>	
						   <tr>
							<td height="19" colspan="4" align="center" scope="col">&nbsp;<font style="font-family:Lucida; font-size:36px; font-weight:bold; color:#000000;">****  Online Order  ****</font></td>
						  </tr>	
						  <tr><td height="5" scope="col"></td></tr>
						   <tr>
							<td height="19" colspan="4" align="center" scope="col">&nbsp;<font style="font-family:Lucida; font-size:33px; font-weight:bold; color:#000000;">for '.$merchantName.'  from WAY.com!</font></td>
						  </tr>
						  <tr><td height="10" scope="col"></td></tr>
						  <tr><td colspan="4"><table width="100%" cellpadding="0" cellspacing="0">
						  <tr><td >
						  <table width="100%" border="0" cellspacing="0" cellpadding="0">
						  <tr>
						  <td colspan="2" align="right" valign="top" style="padding-right:20px;"><font style="font-family:Lucida; font-size:33px; font-weight:bold; color:#000000;">Order No:DI'.$Order_Id.'</font></td>
						  </tr>
						  <tr>
							<td height="30" width="50%" scope="col">&nbsp;<font style="font-family:Lucida; font-size:33px; font-weight:bold; color:#000000;">Customer Name</font></td><td align="left" width="50%"><font style="font-family:Lucida; font-size:33px; font-weight:bold; color:#000000;">:&nbsp;'.$Customer_Name.'</font></td>
						  </tr>
						  <tr>
							<td height="30" width="50%" scope="col">&nbsp;<font style="font-family:Lucida; font-size:33px; font-weight:bold; color:#000000;">Customer Phone</font></td><td align="left"><font style="font-family:Arial, Helvetica, sans-serif; font-size:33px; font-weight:bold; color:#000000;">:&nbsp;'.$Customer_Phone.'</font></td>
						  </tr>
						  <tr>
							<td height="30" width="50%" scope="col">&nbsp;<font style="font-family:Lucida; font-size:33px; font-weight:bold; color:#000000;">Order Time</font></td><td align="left"><font style="font-family:Lucida; font-size:33px; font-weight:bold; color:#000000;">:&nbsp;'.$Customer_OrderTime.'</font></td>
						  </tr>
						  <tr>
							<td height="30" width="50%" scope="col">&nbsp;<font style="font-family:Lucida; font-size:33px; font-weight:bold; color:#000000;">'.$orderfor.' Time</font></td><td align="left"><font style="font-family:Lucida; font-size:33px; font-weight:bold; color:#000000;">:&nbsp;'.$Customer_pickupTime.'</font></td>
						  </tr>
						  <tr>
							<td height="30" width="30%" scope="col">&nbsp;<font style="font-family:Lucida; font-size:33px; font-weight:bold; color:#000000;">Mode of Payment</font></td><td align="left"><font style="font-family:Lucida; font-size:33px; font-weight:bold; color:#000000;">:&nbsp;';
							if($_REQUEST['pay_type']=='CreditCard')
								$faxmessage.='Credit Card';
							else if($_REQUEST['pay_type']=='Cash')
								$faxmessage.="Cash on ".$orderfor." <br>&nbsp;(Customers may receive a confirmatory call from the restaurant.)";
							else
								$faxmessage.=$_REQUEST['pay_type'];
							$faxmessage.='</font></td></tr>';
						  if($_REQUEST['pay_type']=='Cash')
				{
				$faxmessage.=' <tr><td height="10"></td></tr>
				  <tr>
					<td height="30" width="30%" scope="col">&nbsp;<font style="font-family:Lucida; font-size:33px; font-weight:bold; color:#000000;">Payment Status</font></td><td align="left"><font style="font-family:Lucida; font-size:33px; font-weight:bold; color:#000000;">:&nbsp;Cash on '.$orderfor.'</font></td></tr><tr>
					<td height="30" width="30%" scope="col">&nbsp;<font style="font-family:Lucida; font-size:33px; font-weight:bold; color:#000000;">Amount to Pay</font></td><td align="left"><font style="font-family:Lucida; font-size:33px; font-weight:bold; color:#000000;">:&nbsp;$'.number_format(($Orderres['orderAmount']+$Orderres['discount']),2).'</font></td>
				  </tr>';
				}
				else
				{
				$faxmessage.=' <tr><td height="10"></td></tr>
				  <tr>
					<td height="30" width="30%" scope="col">&nbsp;<font style="font-family:Lucida; font-size:33px; font-weight:bold; color:#000000;">Payment Status</font></td><td align="left"><font style="font-family:Lucida; font-size:33px; font-weight:bold; color:#000000;">:&nbsp;Paid</font></td>
				  </tr><tr>
					<td height="30" width="30%" scope="col">&nbsp;<font style="font-family:Lucida; font-size:33px; font-weight:bold; color:#000000;">Amount Paid</font></td><td align="left"><font style="font-family:Lucida; font-size:33px; font-weight:bold; color:#000000;">:&nbsp;$'.number_format(($Orderres['orderAmount']+$Orderres['discount']),2).'</font></td>
				  </tr>
				  ';
				  }
				 
						  $faxmessage.='<tr>
							<td height="30" width="30%" scope="col">&nbsp;<font style="font-family:Lucida; font-size:33px; font-weight:bold; color:#000000;">Restaurant Address</font></td><td align="left"><font style="font-family:Lucida; font-size:33px; font-weight:bold; color:#000000;">:&nbsp;'.$merchantName.'<br>&nbsp;&nbsp;'.$merchantCity.', '.$merchantState.'&nbsp;'.$merchantZip.'<br>&nbsp;&nbsp;Phone:'.$merchantPhone.'</font></td>
						  </tr>';
						   if($orderfor=='Delivery') {
						   $faxmessage.='<tr>
							<td height="30" width="50%" scope="col">&nbsp;<font style="font-family:Lucida; font-size:33px; font-weight:bold; color:#000000;">Delivery Address</font></td><td align="left"><font style="font-family:Lucida; font-size:33px; font-weight:bold; color:#000000;">:&nbsp;';
							$Loc=explode(",",$DeliveryAddress);
							$Strlen=strlen($DeliveryAddress);
							$cnt=count($Loc);
							$Strlen1=strlen($Loc[$cnt-2]);
							$Strlen2=strlen($Loc[$cnt-1]);
							$Strdiff=$Strlen-($Strlen1+$Strlen2);
							$Loc=ucwords(strtolower(trim($Loc[$cnt-2]))).", ".strtoupper(trim($Loc[$cnt-1]));
							$Street=substr($DeliveryAddress, 0,$Strdiff);
							$Street=trim($Street);
							$Street = substr($Street, 0, strlen($Street)-1);
							$faxmessage.=$Street.',&nbsp;&nbsp;'.$Loc;
							$faxmessage.='</font></td>
						  </tr>';
						  }
						   $faxmessage.='</table>
					</td></tr>	</table></td></tr>						  
						   <tr>
						<td height="19" scope="col">&nbsp;</td>
					  </tr>';
					$faxmessage.='<tr><td colspan="4" scope="col">
					<table width="100%" cellpadding="0" cellspacing="0" rules="all" style="border:4px solid #e9e9e9;">
						   <tr>
							<td height="30" width="55%" scope="col" align="center">&nbsp;<font style="font-family:Lucida; font-size:33px; font-weight:bold; color:#000000;">&nbsp;&nbsp;Item</font></td>
							<td height="25" align="right" style="padding-right:20px;" width="15%" scope="col">&nbsp;<font style="font-family:Lucida; font-size:33px; font-weight:bold; color:#000000;">&nbsp;&nbsp; Quantity</font></td>
							<td height="19" align="right" style="padding-right:20px;" width="15%">&nbsp;<font style="font-family:Lucida; font-size:33px; font-weight:bold; color:#000000;">&nbsp;&nbsp;Amount</font></td>
							<td height="19" align="right" style="padding-right:20px;" width="15%">&nbsp;<font style="font-family:Lucida; font-size:33px; font-weight:bold; color:#000000;">&nbsp;&nbsp; Total</font></td>
						  </tr><tr><td height="4"></td></tr><tr ><td height="2" colspan="4" bgcolor="#e9e9e9"></td></tr><tr><td height="4"></td></tr>';
					  $Query="SELECT tbl_cart.*,items.*,menus.menuName FROM tbl_cart INNER JOIN items ON items.id=tbl_cart.Cart_ServiceID INNER JOIN menus ON menus.id=items.menuID WHERE Cart_Type='Dine' AND Sess_ID='".session_id()."'";
					  $res = mysql_query($Query);
					  $Total_Price=0;
					  $Total_Qty=0;
					  
					  while ($rec=mysql_fetch_array($res)) {
							$itemPrice=$rec['Amount'];
							$Total_Price=$Total_Price+$itemPrice*$rec['Cart_Quantity'];
							$Total_Qty=$Total_Qty+$rec['Cart_Quantity'];
							$faxmessage.='<tr>
							<td height="30" scope="col">&nbsp;<font style="font-family:Lucida; font-size:33px; font-weight:bold; color:#000000;">&nbsp;&nbsp;'.$rec['menuName'].' - '.$rec['itemName'];
							if($rec['Size']<>'' && $rec['Size']<>'NORMAL' && $rec['Size']<>'Normal' && $rec['Size']<>'normal' && $rec['Size']<>'1')
								$faxmessage.=" (".$rec['Size'].")";
							
							// Sub Item only for display
							$Query221="SELECT * from tbl_cartsubitems where Cart_ID=".$rec['Cart_ID'];
							$res221 = mysql_query($Query221);
							$SNum=mysql_num_rows($res221);
							$sub_price=0;
							if($SNum>0)
								$faxmessage.='<br>&nbsp;&nbsp;&nbsp;<font style="font-family:Lucida; font-size:30px; font-weight:bold; color:#000000;">';
							$cnt=1;
							while ($result221=mysql_fetch_array($res221)) {
								$SubGdetails=SubItemdetails($result221['subgroup_id']);
								$faxmessage.=$SubGdetails[0];
								if($result221['subgroup_value']<>'')
									$faxmessage.=' - '.$result221['subgroup_value'];
								if($result221['subgroup_x2']==1)
									$faxmessage.=' (2X)';
								$priceval=$SubGdetails[1];
								if($result221['subgroup_value']=='Left Side')
									$priceval=$priceval/2;
								else if($result221['subgroup_value']=='Right Side')
									$priceval=$priceval/2;
								if($result221['subgroup_x2']==1)
									$priceval=$priceval*2;
								if($result221['Free_Toppings']==1) {
									$priceval=0;
								}
								$sub_price=$sub_price+$priceval;
								if($priceval>0)
									$faxmessage.=' ($'.number_format($priceval,2).')';
								if($cnt<>$SNum)
									$faxmessage.=', ';
								$cnt++;
							}
							$faxmessage.='</font>';
							$sub_price=$sub_price*$rec['Cart_Quantity'];
							$Total_Price=$Total_Price+$sub_price;
							// End Sub item	
							if($rec['Notes']<>'')
					  $faxmessage.='<br><font style="font-family:Lucida; font-size:33px; font-weight:bold; color:#000000;"><b>&nbsp;&nbsp;Instructions:</b> '.ucwords($rec['Notes']);
							$faxmessage.='</font></td>
							<td height="19" align="right" style="padding-right:20px;" scope="col">&nbsp;<font style="font-family:Lucida; font-size:33px; font-weight:bold; color:#000000;">&nbsp;&nbsp; '.$rec['Cart_Quantity'].'</font></td>
							<td height="19" align="right" style="padding-right:20px;" scope="col">&nbsp;<font style="font-family:Lucida; font-size:33px; font-weight:bold; color:#000000;">&nbsp;&nbsp;$ '.number_format($itemPrice,2).'</font></td>
							<td height="19" align="right" scope="col">&nbsp;<font style="font-family:Lucida; font-size:33px; font-weight:bold; color:#000000;">$'.number_format((($itemPrice*$rec['Cart_Quantity'])+$sub_price),2).'</font>&nbsp;</td>
						  </tr><tr><td height="10"></td></tr><tr ><td height="2" colspan="4" bgcolor="#e9e9e9"></td></tr><tr><td height="10"></td></tr>';
					  }
				$tax_total = sprintf("%.2f",($_REQUEST['orderSubTotal']*$Tax)/100);
				$order_total = $tax_total+$Total_Price+$Delivery_Fee;	 
				$faxmessage.='<tr>
					<td colspan="4"><table width="100%" cellpadding="0" cellspacing="0"><tr><td width="70%" height="30" colspan="2" align="center"><font style="font-family:Lucida; font-size:33px; font-weight:bold; color:#000000;">&nbsp;&nbsp;Total Quantity: '.$Total_Qty.'</font></td>
					<td height="19" colspan="2" width="30%" align="right" scope="col">
					<table width="95%" align="left" cellpadding="0" cellspacing="0">
					<tr><td align="right">
					<font style="font-family:Lucida; font-size:33px; font-weight:bold; color:#000000;">Sub Total: <b>$'.number_format($Total_Price,2).'</b></font></td></tr>';
					if($tax_total>0)
					$faxmessage.='<tr><td align="right">
					<font style="font-family:Lucida; font-size:33px; font-weight:bold; color:#000000;">Tax: <b>$'.number_format($tax_total,2).'</b></font></td></tr>';
					if($Delivery_Fee>0)
					$faxmessage.='<tr><td align="right">
					<font style="font-family:Lucida; font-size:33px; font-weight:bold; color:#000000;">Delivery Fee: <b>$'.number_format($Delivery_Fee,2).'</b></font></td></tr>';
					/*if($discount>0) {
					  $faxmessage.='<tr>
						<td height="19" colspan="4" align="right" scope="col" style="padding-right:5px;"><font style="font-family:Lucida; font-size:33px; font-weight:bold; color:#cc0d08;" >&nbsp;Discount : $'. number_format($discount,2).'</font></td>
					  </tr><tr><td height="5" scope="col"></td></tr>';
					  }*/
					 // if($discount>0)
						//$order_total=$order_total+$discount;
					$faxmessage.='<tr><td align="right">
					<font style="font-family:Lucida; font-size:33px; font-weight:bold; color:#000000;">Total : <b>$'.number_format($order_total,2).'</b></font></td></tr>
					</table></td>
					</tr>
					</table>
					</td>
				  </tr>
				 </table>
				 </td></tr>';
				if($_REQUEST['Instructions']<>'Enter special instructions for your order here:' && $_REQUEST['Instructions']<>'')
				$faxmessage.=' <tr><td height="10"></td></tr><tr>
					<td height="19" scope="col" valign="top"><font style="font-family:Lucida; font-size:33px; font-weight:bold; color:#000000;"><b>Special Instructions:</b> '.$_REQUEST['Instructions'].'</td></tr>';
				
				  $faxmessage.='<tr><td height="10" scope="col"></td></tr><tr>
					<td height="19" colspan="4" align="center" scope="col">&nbsp;<font style="font-family:Lucida; font-size:33px; font-weight:bold; color:#000000;">&nbsp;&nbsp;Questions? Please call the customer directly or call
WAY.com at 408-598-3338 immediately.</font></td>
				  </tr>	</table>
				</td>
			  </tr>
			  <tr><td height="10" scope="col"></td></tr>
			</table></td>
		  </tr>
		</table>';
		//echo "<br>FAX:".$faxmessage;
		// New Fax Message Template
		

		GLOBAL $adminmail;
		$headers = "From: ".$adminmail."\r\n" .
		   'Reply-To: '.$adminmail."\r\n" .
				   'X-Mailer: PHP/' . phpversion();
		$headers .= 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		
		include_once ("template/email-template.php"); 
		
		$adminmessage=$message;
		$template1=str_replace('DETAILS',$message."",$template);
		$message=$template1;
		
		$subject="You received an order through WAY.com!";
		//$tomail="jebaraj.h@gmail.com";
		mail($tomail1, $subject, $message, $headers); 
		
		//Store confirmation mail to database
		$my_file = 'emails/DI'.$Order_Id.'.html';
		$my_file1 = 'DI'.$Order_Id.'.html';
		$handle1 = fopen($my_file, 'w');
		fwrite($handle1, $message);
		
		// Fax using EMAIL with Provider Ringcentral
		$my_file = 'dine/fax/D-8885194198-'.$Order_Id.'.html';
		$my_file1 = 'D-8885194198-'.$Order_Id.'.html';
		$handle = fopen($my_file, 'w');
		$sst=fwrite($handle, $faxmessage);
			
		/*$my_path = $_SERVER['DOCUMENT_ROOT']."/dine/fax/";
		$my_name = "way";
		$my_mail = "wayfax@way.com";
		$my_replyto = "wayfax@way.com";
		$my_subject = "Way FAX";
		$my_message = "FAX";*/
		if($faxNumber!='')
		{
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
		//$status=mail_attachmentnew($my_file1, $my_path, $faxNumber."@rcfax.com", $my_mail, $my_name, $my_replyto, $my_subject, $my_message);
		
		
		// Send fax Using FAXAGE
		
		$file = "https://www.bi.way.com/".$my_file;

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL,"https://www.faxage.com/httpsfax.php");
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS,"username=binugirija&company=29014&password=Sapconf18!&callerid=8887818138&faxno=$faxNumber&recipname=WAY&operation=sendfax&faxurls[0]=$file");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

		$server_output = curl_exec ($ch);
		curl_close ($ch);
		}
		// Store in DB
		//$sql="INSERT INTO tbl_fax(Fax_ID,Fax_From,Fax_To,Fax_Restaurant,Fax_User,Fax_Content,Fax_Status,Fax_Created) VALUES('','888-781-8138','".$faxNumber1."','".$Merchant_ID."',".$User_ID.",'".$my_file1."',".$status.",Now())";
		$sql="INSERT INTO tbl_fax(Fax_ID,Fax_From,Fax_To,Fax_Restaurant,Fax_User,Fax_Content,Fax_Status,Fax_Created) VALUES('','888-781-8138','".$faxNumber1."','".$Merchant_ID."',".$User_ID.",'".$my_file1."',1,Now())";
		mysql_query($sql);
		
		// Send a copy to Binu, Support
		
		$faxlink='<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0"><tr><td  valign="top" scope="col" align="right"><a href="https://www.bi.way.com/dine/fax/'.$my_file1.'">View Fax</a>&nbsp;&nbsp;</td></tr><tr><td>';
		$message=$faxlink.$adminmessage.'</td></tr></table>';
		$template1=str_replace('DETAILS',$message."",$template);
		$message=$template1;
		
		mail("binu.girija@way.com", $subject, $message, $headers); 
		
		mail("support@way.com", $subject, $message, $headers); 
		//mail("jebaraj.h@gmail.com", $subject, $message, $headers); 
		
		// New Code
		/*$attach = "'@/".$my_path.$my_file1."'";  // the @ sign is required
		$attach = '@E:/Apache/htdocs/demo/dine/faxnew.txt;filename=faxnew.txt';
		
		$form_data = array('Username'=>'8885194198','Password'=>'181818','Recipient'=>'8887818138','Coverpage'=>'None','Resolution'=>'Low','Attachment'=>$attach);

		$curl = curl_init('https://service.ringcentral.com/faxapi.asp');
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

		curl_setopt($curl, CURLOPT_VERBOSE, 1);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $form_data);

		
		curl_setopt($curl, CURLOPT_HEADER, 1);

		$headers = array('Content-Type: form-data', 'Accept-Charset: UTF-8');
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		$result = curl_exec($curl);
		error_log('result: '.$result);
		curl_close($ch);
		*/
		// New Code
		
		/*$attach = '@/E:/Apache/htdocs/voucher.pdf';
		$form_data = array('Username'=>'8885194198','Password'=>'181818','Recipient'=>'8887818138','Coverpage'=>'None','Resolution'=>'Low','Attachment'=>$attach);
		
		
		$curl = curl_init('http://service.ringcentral.com/faxapi.asp');
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

		curl_setopt($curl, CURLOPT_VERBOSE, 1);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $form_data);

		
		curl_setopt($curl, CURLOPT_HEADER, 1);

		$headers = array('Content-Type: form-data', 'Accept-Charset: UTF-8');
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		$result = curl_exec($curl);
		error_log('result: '.$result);
		curl_close($ch);
		
		exit;
		*/
		// END FAX
		
			// Send Mail to Buyer
			$to= $res2['email_add'];	
			GetRestaurantname($Merchant_ID);
			$toname= ucwords($res2['firstname']);	
			$tomail1=$to;
			$subject="Thanks for ordering your food through WAY.com!";
			
			 $message='<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
				  <tr>
					<td  valign="top" scope="col"><table width="100%" border="0" cellspacing="0" cellpadding="0">
					
					  <tr>
						<td valign="top" scope="col"><table width="94%" border="0" align="center" cellpadding="0" cellspacing="0">
						  <tr>
							<td height="30" colspan="4" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">Hi '.$toname.' &nbsp;&nbsp;</font></td>
						  </tr>
						  <tr>
							<td height="30" colspan="4" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">Thank you for order to '.GetRestaurantname($Merchant_ID).'. Here are your order details:</font></td>
						  </tr>
						  <tr><td height="10" scope="col"></td></tr>
							<tr><td colspan="4"><table width="100%" cellpadding="0" cellspacing="0">
						  <tr>
							<td height="30" width="30%" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">Order No</font><td align="left"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">:&nbsp;DI'.$Order_Id.'</font></td></td>
						  </tr>	
						  <tr>
							<td height="30" width="30%" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">Restaurant</font></td><td align="left"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">:&nbsp;'.$merchantName.'<br>&nbsp;&nbsp;'.$merchantCity.', '.$merchantState.'&nbsp;'.$merchantZip.'<br>&nbsp;&nbsp;Phone:'.$merchantPhone.'</font></td>
						  </tr>
						  <tr>
							<td height="30" width="30%" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">Customer Name</font></td><td align="left"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">:&nbsp;'.$Customer_Name.'</font></td>
						  </tr>
						  <tr>
							<td height="30" width="30%" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">Order Type</font></td><td align="left"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">:&nbsp;'.$orderfor.'</font></td>
						  </tr>
						  <tr>
							<td height="30" width="30%" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">Restaurant Address</font></td><td align="left"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">:&nbsp;'.$merchantAddress.'<br>&nbsp;&nbsp;'.$merchantCity.', '.$merchantState.'&nbsp;'.$merchantZip.'</font></td>
						  </tr>
						  <tr>
							<td height="30" width="30%" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">Order Placed At</font></td><td align="left"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">:&nbsp;'.$Customer_OrderTime.'</font></td>
						  </tr>
						  <tr>
							<td height="30" width="30%" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">'.$orderfor.' Time</font></td><td align="left"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">:&nbsp;'.$Customer_pickupTime.'</font></td>
						  </tr><tr>
							<td height="30" width="30%" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">Mode of Payment</font></td><td align="left"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">:&nbsp;';
							if($_REQUEST['pay_type']=='CreditCard')
								$message.='Credit Card';
							else if($_REQUEST['pay_type']=='Cash')
								$message.="Cash on ".$orderfor." <br>&nbsp;(Customers may receive a confirmatory call from the restaurant.)";
							else
								$message.=$_REQUEST['pay_type'];
							$message.='</font></td></tr>';
						  if($orderfor=='Delivery') {
						  $message.='<tr>
							<td height="30" width="30%" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">Delivery Address</font></td><td align="left" colspan="4"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">:&nbsp;';
							$Loc=explode(",",$DeliveryAddress);
							$Strlen=strlen($DeliveryAddress);
							$cnt=count($Loc);
							$Strlen1=strlen($Loc[$cnt-2]);
							$Strlen2=strlen($Loc[$cnt-1]);
							$Strdiff=$Strlen-($Strlen1+$Strlen2);
							$Loc=ucwords(strtolower(trim($Loc[$cnt-2]))).", ".strtoupper(trim($Loc[$cnt-1]));
							$Street=substr($DeliveryAddress, 0,$Strdiff);
							$Street=trim($Street);
							$Street = substr($Street, 0, strlen($Street)-1);
							$message.=$Street.'<br>&nbsp;&nbsp;'.$Loc;
							$message.='</font></td>
						  </tr>';
						  
						  }
						  $message.='</table></td></tr>
						<tr>
						<td height="19" scope="col">&nbsp;</td>
					  </tr><tr>
							<td height="19" colspan="4" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">&nbsp;&nbsp;Your Order Details:</font></td>
						  </tr>						  
						   <tr>
						<td height="19" scope="col">&nbsp;</td>
					  </tr>';
					$message.='<tr><td height="2" colspan="4" bgcolor="#cccccc"></td></tr> <tr>
							<td height="30" width="55%" scope="col" align="left">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:bold; color:#4e4e4e;">&nbsp;&nbsp;Item Name</font></td>
							<td height="19" width="15%" align="right" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:bold; color:#4e4e4e;">&nbsp;&nbsp; Quantity</font></td>
							<td height="19"  width="15%"align="right" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:bold; color:#4e4e4e;">&nbsp;&nbsp;Price</font></td>
							<td height="19" width="15%" align="right" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:bold; color:#4e4e4e;">&nbsp;&nbsp;Amount</font></td>
						  </tr>	
							<tr><td height="2" colspan="4" bgcolor="#cccccc"></td></tr>						  
						   <tr>
						<td height="19" scope="col">&nbsp;</td>
					  </tr>';
					  $Query="SELECT tbl_cart.*,items.*,menus.menuName FROM tbl_cart INNER JOIN items ON items.id=tbl_cart.Cart_ServiceID INNER JOIN menus ON menus.id=items.menuID WHERE Cart_Type='Dine' AND Sess_ID='".session_id()."'";
					  $res = mysql_query($Query);
					  while ($rec=mysql_fetch_array($res)) {
							$itemPrice=$rec['Amount'];
							$message.=' <tr>
							<td height="19" scope="col"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">'.$rec['menuName'].' - '.$rec['itemName'];
							if($rec['Size']<>'' && $rec['Size']<>'NORMAL' && $rec['Size']<>'normal' && $rec['Size']<>'Normal' && $rec['Size']<>'1')
								$message.=" (".$rec['Size'].")";
							// Sub Item only for display
							$Query221="SELECT * from tbl_cartsubitems where Cart_ID=".$rec['Cart_ID'];
							$res221 = mysql_query($Query221);
							$SNum=mysql_num_rows($res221);
							$sub_price=0;
							if($SNum>0)
								$message.='<br><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#000000;">';
							$cnt=1;
							while ($result221=mysql_fetch_array($res221)) {
								$SubGdetails=SubItemdetails($result221['subgroup_id']);
								$price_index=$result221['price_index'];
								$message.=$SubGdetails[0];
								if($result221['subgroup_value']<>'')
									$message.=' - '.$result221['subgroup_value'];
								if($result221['subgroup_x2']==1)
									$message.=' (2X)';
								if($price_index>0)
								{
									$subprices=explode(",",$SubGdetails[1]);
									$priceval=$subprices[$price_index-1];
								}
								else
									$priceval=$SubGdetails[1];
								if($result221['subgroup_value']=='Left Side')
									$priceval=$priceval/2;
								else if($result221['subgroup_value']=='Right Side')
									$priceval=$priceval/2;
								if($result221['subgroup_x2']==1)
									$priceval=$priceval*2;
								if($result221['Free_Toppings']==1) {
									$priceval=0;
								}
								$sub_price=$sub_price+$priceval;
								if($priceval>0)
									$message.=' ($'.number_format($priceval,2).')';
								if($cnt<>$SNum)
									$message.=', ';
								$cnt++;
							}
							$sub_price=$sub_price*$rec['Cart_Quantity'];
							// End Sub item		
							$message.='</font></td>
							<td height="19" scope="col" align="right">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">&nbsp;&nbsp; '.$rec['Cart_Quantity'].'</font></td>
							<td height="19" scope="col" align="right">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">&nbsp;&nbsp;$ '.number_format($itemPrice,2).'</font></td>
							<td height="19" scope="col" align="right">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">&nbsp;&nbsp;$ '.number_format((($itemPrice*$rec['Cart_Quantity'])+$sub_price),2).'</font></td>
						  </tr>';	
							// Insert Sub Items
							$Query21="SELECT * from tbl_cartsubitems where Cart_ID=".$rec['Cart_ID'];
							$res21 = mysql_query($Query21);
							$SNum=mysql_num_rows($res21);
							while ($result1=mysql_fetch_array($res21)) {
								$SubGdetails=SubItemdetails($result1['subgroup_id']);
								$price_index=$result1['price_index'];
								if($price_index>0)
								{
									$subprices=explode(",",$SubGdetails[1]);
									$priceval=$subprices[$price_index-1];
								}
								else
									$priceval=$SubGdetails[1];
								if($result1['subgroup_value']=='Left Side')
									$priceval=$priceval/2;
								else if($result1['subgroup_value']=='Right Side')
									$priceval=$priceval/2;
								if($result1['subgroup_x2']==1)
									$priceval=$priceval*2;
								if($result1['Free_Toppings']<>1) {
									$Total_Price=$Total_Price+$priceval;
									}
							}						  
					  if($rec['Notes']<>'')
					  $message.=' <tr>
							<td height="19" scope="col" valign="top"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;"><b>Instructions:</b> '.$rec['Notes'].'</td></tr>';
						 $message.='<tr>
						<td height="19" scope="col">&nbsp;</td>
					  </tr>';
					  $message.='<tr>
						<td height="19" scope="col">&nbsp;</td>
					  </tr>
					  ';
					  }
					  $tax_total = sprintf("%.2f",($_REQUEST['orderSubTotal']*$Tax)/100);
				$order_total = $tax_total+$Total_Price+$Delivery_Fee;
					   $message.='<tr><td height="2" colspan="4" bgcolor="#cccccc"></td></tr><tr><td height="5" scope="col"></td></tr>';
					   if($deliveryFee>0)
					   {
					   $message.='<tr>
						<td height="19" colspan="4" align="right" scope="col" style="padding-right:5px;"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#cc0d08;" >&nbsp;Delivery Charge : $'. number_format($deliveryFee,2).'</font></td>
					  </tr><tr><td height="5" scope="col"></td></tr>';
					  }
					  if($tax_total>0) {
					  $message.='<tr>
						<td height="19" colspan="4" align="right" scope="col" style="padding-right:5px;"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#cc0d08;" >&nbsp;Tax : $'. number_format($tax_total,2).'</font></td>
					  </tr><tr><td height="5" scope="col"></td></tr>';
					  }
					  if($discount>0) {
					  $message.='<tr>
						<td height="19" colspan="4" align="right" scope="col" style="padding-right:5px;"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#cc0d08;" >&nbsp;Discount : $'. number_format($discount,2).'</font></td>
					  </tr><tr><td height="5" scope="col"></td></tr>';
					  }
					  
					   $message.='<tr>
						<td height="19" colspan="4" align="right" scope="col" style="padding-right:5px;"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:bold; color:#4e4e4e;" >&nbsp;Total : $'. number_format($_REQUEST['orderDineAmount'],2).'</font></td>
					  </tr>
					   <tr>
						<td height="19" scope="col">&nbsp;</td>
					  </tr><tr><td height="2" colspan="4" bgcolor="#cccccc"></td></tr><tr><td height="10" scope="col"></td></tr>';
						if($_REQUEST['Instructions']<>'Enter special instructions for your order here:' && $_REQUEST['Instructions']<>'')
				$message.=' <tr><td height="10"></td></tr><tr>
					<td height="19" scope="col" valign="top"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;"><b>Special Instructions:</b> '.$_REQUEST['Instructions'].'</td></tr>';
				 $PointPercentage=GetPointPercentage('45');
						$CheckoutPoints=($_REQUEST['orderDineAmount']*$PointPercentage)/100;
						$message.='<tr>
						<td colspan="4" height="25" scope="col" align="right">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;">You have earned $'.round($CheckoutPoints,2).' <img src="'.$Host_Path.'images/img_buck.png"border="0" alt="bucks" title="bucks"> toward future purchase.</font></td>
					  </tr>';
				 $message.=' <tr><td height="10"></td></tr>';
						$message.='<tr>
							<td height="19" colspan="2" scope="col">&nbsp;&nbsp;&nbsp;<a href="'.$Host_Path.'signin_join.php?act=login"><font style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:normal; color:#4e4e4e;text-decoration:underline">Please Check your account for more details.</a></font></td>
						  </tr></table>
						</td>
					  </tr>
					  <tr><td height="10" scope="col"></td></tr>
					</table></td>
				  </tr>
				</table>';
				
				GLOBAL $adminmail;
				$headers = "From: ".$adminmail."\r\n" .
				   'Reply-To: '.$adminmail."\r\n" .
						   'X-Mailer: PHP/' . phpversion();
				$headers .= 'MIME-Version: 1.0' . "\r\n";
				$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
				include_once ("template/email-template.php"); 
			$template1=str_replace('DETAILS',$message."",$template);
			$message=$template1;
			
			//$tomail1="jebaraj.h@gmail.com";
			mail($tomail1, $subject, $message, $headers); 
		} 
	}
}
?>