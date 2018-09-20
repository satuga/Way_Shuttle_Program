<?php
include ('config.php');
include ('function.php');

$query="SELECT id,checksum_register,email_add FROM tbl_registeration WHERE checksum_register='".sanitize($_REQUEST['checksum_register'])."' AND id=".sanitize($_REQUEST['id']); 
$res=mysql_query($query);
$num=mysql_num_rows($res);
	
if($num>0)
{
	$qry="UPDATE tbl_registeration SET status=1 WHERE id=".sanitize($_REQUEST['id']); 
	mysql_query($qry);
	$act=1;
	?>
	<script>alert("Account has been activated successfully"); window.location="http://way.com";</script>
	<?php 
}
else
{
	$act=0;
}
?>
