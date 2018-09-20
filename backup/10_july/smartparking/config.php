<?php
//error_reporting(0);
if ($_SERVER['SERVER_PORT']!=443)
{
//$url = "https://www.". str_replace('www.','',$_SERVER['SERVER_NAME']). ":443".$_SERVER['REQUEST_URI'];
//header("Location: $url");
}

// Database Login
$DBSERVER="localhost";
$DBUSER="root";
$DBPWD="Sapconf18";
$DBDATABASE="way_livedbfortest";


db_connect();

$Site_Title="Way.com";
$path="admin/upload/users/";
if($_SERVER['SERVER_PORT']==443)
{
$Base_Path = $_SERVER['DOCUMENT_ROOT']."/";
$Root_Path = $_SERVER['HTTP_HOST']."/";
$Host_Path = "http://".$_SERVER['HTTP_HOST']."/";
$Host_PathLIVE= "http://".$_SERVER['HTTP_HOST']."/";
$Host_Admin_Path = "http://".$_SERVER['HTTP_HOST']."/waypanel/";
$Admin_Base_Path = $_SERVER['DOCUMENT_ROOT']."/waypanel/";
}
else
{
$Base_Path = $_SERVER['DOCUMENT_ROOT']."/";
$Root_Path = $_SERVER['HTTP_HOST']."/";
$Host_Path = "http://".$_SERVER['HTTP_HOST']."/";
$Host_PathLIVE= "http://".$_SERVER['HTTP_HOST']."/";
$Host_Admin_Path = "http://".$_SERVER['HTTP_HOST']."/waypanel/";
$Admin_Base_Path = $_SERVER['DOCUMENT_ROOT']."/waypanel/";
}

$PHP_SELF=$_SERVER['PHP_SELF'];
$Folder="/";
$network_url='way.com';
$Time_Zone			= "-8.0";

// Twilio settings
$version = "2010-04-01";
$sid = "ACf663e8c6d936bd580735fa4c8beb8081";
$token = "ef675cfeb75316cbc49d4cb88abc474d";
$phonenumber = "4087365761";
$fromSMSNumber = "4087365761";

$EncryptKey="Archu@19!@14!";
// Google Map API Settings
$Map_API_KEY="AIzaSyDhwgA-2PprPvZBEF2BvZX9qG2pzCIn2e8";
$Map_API="ABQIAAAAyizmwBcajGaC_qcNJvVBQRTyRwIAU8UGfeJZL2Ig9l_2M8J71hS9YSoZ5mn1Sd0q4L3J2ENS6F2OWw";


// PNF Integration settings
//Production
//$wsdl="http://192.168.168.24:8001/WayPNFIntegration/WayProxy?wsdl";
$wsdl="http://192.168.168.24:8003/WayPNFIntegration/WayProdProxy?WSDL";
$AgentSine=100761;
$TrackingNumber='0070108';
$SharedKey = 'R5x!t1B3';



//Demo
/*$wsdl="http://192.168.168.24:8001/WayPNFIntegration/WayProxy?wsdl";
$AgentSine=100759;
$TrackingNumber='0070108';
$SharedKey = 'AP48FNAR';*/

// End  PNF Integration settings

########## Google Settings.. Client ID, Client Secret from https://cloud.google.com/console #############
$google_client_id 		= '926285547778-7tl6u93e7v89hhf4d30rcl2lss8k8mek.apps.googleusercontent.com';
$google_client_secret 	= 'wXJH9PB6JUAgBTrQppdLPzvd';
$google_redirect_url 	= 'http://www.bi.way.com/login.php'; //path to your script
$google_developer_key 	= 'AIzaSyC4qAOVVDqiYcRQpdBhr2rpWbKQoFb7uWM';


$Img_RackSpace_Path = 'http://ade6d0af6103c17f31fb-beda012522e6252bd703e87e8e1d419c.r88.cf5.rackcdn.com/admin/upload/users/';

function db_connect() {

	 GLOBAL $DBSERVER,$DBUSER,$DBPWD,$DBDATABASE;
	 $c1=mysqli_connect($DBSERVER,$DBUSER,$DBPWD) or die("can not connect to database");
	 mysqli_select_db($c1,$DBDATABASE) or die("can not select database");
  	 return $c1;
	 }

/*Start code for mysqli added gopalanmani 05-01-2015*/
GLOBAL $con;
$con=mysqli_connect($DBSERVER,$DBUSER,$DBPWD,$DBDATABASE);

function ExecudeQuery($Query)
{
	GLOBAL $DBSERVER,$DBUSER,$DBPWD,$DBDATABASE;
	try
	{

	  $dbh = new PDO("mysql:host=".$DBSERVER.";dbname=".$DBDATABASE, $DBUSER, $DBPWD);
			$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$stmt = $dbh->prepare($Query);
			$stmt->execute();

			$result = $stmt->fetchAll();

			$stmt= null;
			$dbh = null;

	   return $result;
	}
	catch(PDOException  $e)
	{
		echo $e->getMessage();
	}

}

/*Start code for mysqli added gopalanmani 05-01-2015*/

////////////////  BID CONTROL  /////////////////////////
$sql="Select bid from tbl_control";
$rec=mysqli_fetch_array(mysqli_query($con,$sql));
$Bid_Control= $rec[0];
///////////////////////////////////////////////PAYMENT STYLE MODE///////////////////////////////////////////

function formatInIndianStyle($num,$case) {
	if($case !=""){
		$Splt = -3;
	}else{
		$Splt = -5;
	}
	$pos = strpos((string)$num, ".");
	if ($pos === false) { $decimalpart="00";}
	else
	{
		$decimalpart= substr($num, $pos+1, 2); $num = substr($num,0,$pos);
	}

	if(strlen($num)>3 & strlen($num) <= 12)
	{

		$last3digits = substr($num, $Splt );
		$numexceptlastdigits = substr($num, 0, $Splt );
		$formatted = makecomma($numexceptlastdigits);
		$stringtoreturn = $formatted.",".$last3digits.".".$decimalpart ;
	}
	elseif(strlen($num)<=3)
	{
		$stringtoreturn = $num.".".$decimalpart;
	}
	elseif(strlen($num)>12)
	{
		$stringtoreturn = number_format($num, 2);
	}
	if(substr($stringtoreturn,0,2)=="-,"){$stringtoreturn = "-".substr($stringtoreturn,2 );}
	$stringtoreturn=str_replace(".00","",$stringtoreturn);
	if($case !=""){
		$stringtoreturn = $stringtoreturn.".00";
	}else{
		$stringtoreturn = $stringtoreturn;
	}

	return $stringtoreturn;
}

function makecomma($input)	{
	if(strlen($input)<=2)
	{ return $input; }
	$length=substr($input,0,strlen($input)-2);
	$formatted_input = makecomma($length).",".substr($input,-2);
	return $formatted_input;
}

function control()
{
	$sql="Select results_per_page from tbl_control";
	$rec=mysqli_fetch_array(mysqli_query($con,$sql));
	return $rec[0];
}
$sqlcnt="Select results_per_page,reservation_email,IsBlockMovieInstantTicket,IsBlockMovieTicket from tbl_control";
$reccnt=mysqli_fetch_array(mysqli_query($con,$sqlcnt));
$page=$reccnt['results_per_page'];
$CONFIRMATION_MAIL=$reccnt['reservation_email'];
$BLOCK_INSTANTMOVIE = $reccnt['IsBlockMovieInstantTicket'];
$BLOCK_MOVIE = $reccnt['IsBlockMovieTicket'];
$Domain_Name	=	'www.bi.way.com';
$DOMAIN			=	'way.com';
$DOMAINNAME		=	'way';
$Site_Name		=	'way.com';
$reply_mail		=	'support@way.com';
$adminmail		=	'support@way.com';
$contactmail	=	'support@way.com';
$Domaincom		=	'www.bi.way.com';
$SUBDOMAIN		=	'way.com';
if(	preg_match( "/parkings/", $_SERVER["PHP_SELF"]) || preg_match( "/playmovie/", $_SERVER["PHP_SELF"]) || preg_match( "/play/", $_SERVER["PHP_SELF"]) || preg_match( "/social/", $_SERVER["PHP_SELF"]) || preg_match( "/dine/", $_SERVER["PHP_SELF"]) || preg_match( "/help/", $_SERVER["PHP_SELF"]) || preg_match( "/tickets/", $_SERVER["PHP_SELF"]))
{
	$Path = "../";
	$Path1 = "../";
} else
{
	$Path = "";
	$Path1 = "";
}
?>
