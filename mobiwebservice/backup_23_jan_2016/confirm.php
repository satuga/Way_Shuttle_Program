<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN"
   "http://www.w3.org/TR/html4/frameset.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml">
   <!--<html xmlns="http://www.w3.org/1999/xhtml"
      xmlns:fb="http://www.facebook.com/2008/fbml">-->
<head>
<meta http-equiv="innerformcontent-Type" innerformcontent="text/html; charset=iso-8859-1" />
<META HTTP-EQUIV="Expires" CONTENT="Tue, 01 Jan 1980 1:00:00 GMT">
<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
<meta http-equiv="X-UA-Compatible" content="IE=100" >
<TITLE>Confirm Password</TITLE>
<link rel="shortcut icon" href="images/favicon.ico">
<META NAME="Keywords" CONTENT="Find and book or order Online  - Parking Tickets, Food Delivery, Tours, Movies, Shows, Events in Chicago, San Francisco, Boston, New York, Philly, DC, LA, San Diego, Portland, Seattle and Denver. ">
<META NAME="Description" CONTENT="Reserve airport parking space, book parking space, airport parking, discount airport parking, long term airport parking, cheap airport parking, online parking space booking, order food online, Movie ticket booking, Shows and Events ticket booking, order food online for delivery,  online event tickets">
<?php
//session_start();
include("api/config.php");
//include("include/header.php");
include("api/function.php");
$query="SELECT id,checksum_register,email_add FROM tbl_registeration WHERE checksum_register='".sanitize($_REQUEST['checksum_register'])."' AND id=".sanitize($_REQUEST['id']); 
$res=mysql_query($query);
$num=mysql_num_rows($res);
		
if($num>0)
{
	$qry="UPDATE tbl_registeration SET status=1 WHERE id=".sanitize($_REQUEST['id']); 
	mysql_query($qry);
	$act=1;
}
else
{
	$act=0;
}
?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td  valign="top" scope="col">					
		<?php 
		if($act==1)
		{
		?>
		<table border=0 cellpadding=0 cellspacing=0 width=88% bgcolor=#ffffff align=center>
			<tr>
				<td height=40></td>
			</tr>
			<tr>
				<td valign=top>
					<table border=0 cellpadding=0 cellspacing=0 width=60% align=center >
						<tr height=50><td class=headtext align=center style=padding-left:10px;><b>Your account has been activated successfully</b></td></tr>
						<tr><td class=normaltext align=center style=padding-left:10px;><a href='https://www.bi.way.com/login' class=pagelink>Click Here to login</a> </td></tr>
					</table>
				</td>
			</tr>
		</table>
		<?php
		}
		else
		{
		?>
		<table border=0 cellpadding=0 cellspacing=0 width=88% bgcolor=#ffffff align=center>
			<tr>
				<td height=50></td>
			</tr>
			<tr>
				<td valign=top>
				<table border=0 cellpadding=0 cellspacing=0 width=60% align=center>
					<tr height=50><td class=headtext align=center style=padding-left:10px;><b>Invalid Checksum value.</b></td></tr>
						
				</table>
				</td>
			</tr>
		</table>
		<?php
		}
		?>
		</td>
	</tr>
</table>																	</table>
<?php //include("include/footer.php");?>