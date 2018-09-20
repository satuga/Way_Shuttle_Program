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
<TITLE>Change Password</TITLE>
<link rel="shortcut icon" href="images/favicon.ico">
<META NAME="Keywords" CONTENT="Find and book or order Online  - Parking Tickets, Food Delivery, Tours, Movies, Shows, Events in Chicago, San Francisco, Boston, New York, Philly, DC, LA, San Diego, Portland, Seattle and Denver. ">
<META NAME="Description" CONTENT="Reserve airport parking space, book parking space, airport parking, discount airport parking, long term airport parking, cheap airport parking, online parking space booking, order food online, Movie ticket booking, Shows and Events ticket booking, order food online for delivery,  online event tickets">
<?php
include("config.php");
//include("include/header.php");
include("function.php");
//include("class/register_class.php");
//$req = new register_class();
$EncryptKey = ENCRYPTKEY;


if(isset($_POST['Pass_submit']))
{
	$pass=$_POST['mypassword'];
	// Check checksum value
	if($_REQUEST['checksum'] != '')
	{
		$query="SELECT id,checksum FROM tbl_registeration WHERE checksum='".sanitize($_REQUEST['checksum'])."'";
		$res=mysql_query($query);
		$rec=mysql_fetch_array($res);
		$id=$rec['id'];

		// Update Password
		if($id>0)
		{
			$qry="UPDATE tbl_registeration SET encrypt_password=AES_ENCRYPT('".$pass."','".$EncryptKey."'),checksum='' WHERE id=".$id;
			mysql_query($qry);
			echo "<script language='javascript'>alert('Password changed successfully');location.href=('https://www.bi.way.com/login');</script>";		
		}
		else
		{
			echo "<script language='javascript'>location.href=('changepassword?act=Report&err=checksum');</script>";
		}
	}
	else
	{
		echo "<script language='javascript'>location.href=('http://bi.way.com/responsive/mobiwebservice/api/change_password.php?Report=Error');</script>";
	}

}
?>
<script language="javascript" type="text/javascript" src="http://bi.way.com/responsive/mobiwebservice/api/jscripts/validation.js"></script>


<div id="main_content_inner" style="width:550px; margin:0px auto; margin-top:30px; padding:15px; border:1px solid #ccc;">
<form name="forgetpassword" method="POST" action="change_password.php" onSubmit="return valid('forgetpassword');">

	<div class="middleAreaStart">
		<div class="loginHdgArea">
			<div class="loginHdgAreaInner">
				<div class="loginHdg">
					<div class="padding"></div>
					<p class="loginHdgTxt">We all forget no worries</p>
					<p class="loginHdgTxt01">Change Your Password</p>
				</div>
			</div>
		</div>

		<div class="clear"></div>

		<div class="loginTxtArea" style="height:200px">
			<div class="loginTxtAreaInner">
				<div class="loginTxtAreaInnerLeftFullwidth">
				<?php
					// Check checksum value
					$query="SELECT id,checksum FROM tbl_registeration WHERE checksum='".$_REQUEST['checksum']."'";
					$res=mysql_query($query);
					$num=mysql_num_rows($res);
					if($num>0)
					{
						//echo '<div class="h6grey5">Please enter your new password twice to ensure it is correct, then press the "Submit" button below.<br><br></div>';
					}
					if ($_GET['Report']=="Error") {
						echo '<div align="center" class="h2_redbold">Something Went Wrong</div>';
					}
					if($_GET['act']=="Report") {
						?>
						<div>
						<table border='0' cellpadding='0' cellspacing='0' width='100%'>
							<tr>
								<td style='padding-left:10px;'>
									<table border='0' cellpadding='0' cellspacing='0' width='90%' align='left'>
										<tr><td colspan='2' class='welcomemessage' style='padding-top:10px;'>Change Password</td></tr>
										<tr><td height='70'></td></tr>
										<tr><td>
										<table border='0' cellpadding='0' cellspacing='0' width='100%'>
										<tr><td class='normaltext' align='center' style='padding-left:10px;'>
										Your password has been successfully changed. </td></tr>
										<tr><td height='10'></td></tr>
										</table>
										</td></tr>
									</table>
								</td>
							</tr>
							<!-- <tr><td height="10"></td></tr>
							<tr><td class="section_break"></td></tr>
							<tr><td height="10"></td></tr>
							<tr><td colspan='2' class='welcomemessage' style='padding-top:10px;padding-left:10px;'>What's Next</td></tr>
							<tr><td height="20"></td></tr>
							<tr><td colspan='2' height="40" class='welcomemessagesmall' style='padding-top:10px;padding-left:10px;'><a href="index.php" class='welcomemessagesmall'>Sell Your Service</a></td></tr>
							<tr><td colspan='2' height="40" class='welcomemessagesmall' style='padding-top:10px;padding-left:10px;'><a href="search.php" class='welcomemessagesmall'>Search For Service</a></td></tr>
							<tr><td colspan='2' height="40" class='welcomemessagesmall' style='padding-top:10px;padding-left:10px;'><a href="dashboard.php" class='welcomemessagesmall'>Go to My Way</a></td></tr>-->
						</table>
						</div>
						<?php
					}
				?>

				</div>

				<div class="loginTxtAreaInnerLeft">
					<?php
					if($num>0)
					{ ?>
					<div>

						<input name="mypassword" type="password" maxlength="15" id="req_Password" class="loginPwdBoxNew" value="" placeholder="Password"/>
						<div class="padding2"><br><br></div>
						<input name="MED_conpass" type="password" maxlength="15" id="req_Password" class="loginPwdBoxNew" value="" placeholder="Confirm Password"/>
						<div class="padding2"></div>
						<input type="hidden" name="id" value="<?=$_REQUEST['id']?>">
						<input type="hidden" name="checksum" value="<?=$_REQUEST['checksum']?>">
						<input type='hidden' name="act" value=<?=$_GET['act'];?>>
						<input type="hidden" name="MED_username" value="<?=$_SESSION['User_Name']?>">
						<input type="hidden" name="Pass_submit" value="change">
					</div>
					<div class="padding1"></div>


					<div>



						<div style="float:left; width: 248px; position: relative; top: 20px; right: 2px;">

							<div>
							<input type="hidden" name="submit" value="submit">
							<input type="hidden" name="login1" value="SIGN IN" class="button5">
							<input type="image" name="login1" src="https://www.bi.way.com/images/btn_submitNew.png" value="Submit" title="Submit"></div>

						</div>

					</div>
					<?php }
					else
					{
						//echo '<div align="center" class="h2_redbold"><br><br><br>Invalid Check Sum value.</div>';
					}
					?>



				</div>


				<div class="loginTxtAreaInnerRight">

					<div>



						<div class="padding"></div>

						<div>

						</div>

					</div>

				</div>


			</div>

		</div>

	</div>

	<!-- middle area end -->
	</form>
	</div>
	<div class="clear"></div>
<?php //include("include/footer.php");?>
