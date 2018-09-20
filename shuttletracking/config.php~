<?php 
//error_reporting(0);
if ($_SERVER['SERVER_PORT']!=443)
{
//$url = "https://www.". str_replace('www.','',$_SERVER['SERVER_NAME']). ":443".$_SERVER['REQUEST_URI'];
//header("Location: $url");
}

// Database Login
//$DBSERVER="192.168.168.37";
//$DBUSER="band";
//$DBPWD="WayDB@Conf@Web08132016!";
//$DBDATABASE="way_livedb";
$DBSERVER="localhost";
$DBUSER="root";
$DBPWD="ln";
$DBDATABASE="waybi";
db_connect();
define("ENCRYPTKEY","Archu@19!@14!");
$Site_Title="Way.com";
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

$geoFancingRange = '100';

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

function removeNull($retArr)
{   
        foreach ($retArr as $key => $value) {
            if(is_array($value)){
                $retArr[$key] = removeNull($value);
            } else {
                if (is_null($value)) {
                     $retArr[$key] = "";
                }
            }
        }
        return $retArr;
}

function jsonResponse($array){
    $array = array_map('removeNull', $array);
    echo json_encode($array);
    exit;
} 
?>
