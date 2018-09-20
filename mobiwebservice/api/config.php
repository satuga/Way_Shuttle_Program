<?php
ini_set('display_errors', '0');
include('constants.php');
//$c1=mysqli_connect(DB_SERVER,DB_USERNAME,DB_PASSWORD) or die("can not connect to database");
//mysqli_select_db($c1,DB_DATABASE)or die("can not select database");
$EncryptKey=ENCRYPTKEY;
GLOBAL $con;
//echo DB_SERVER." ".DB_USERNAME." ".DB_PASSWORD." ".DB_DATABASE; die;
$con=mysqli_connect(DB_SERVER,DB_USERNAME,DB_PASSWORD,DB_DATABASE) or die("can not connect to database");


function chk_parameter($parameter,$count)
{
    $parameter_arr=explode(':',$parameter);
    if(Count($parameter_arr)<$count)
    {
     //echo "arry".Count($parameter_arr);  
        return "0";
        exit;
    }
    for($i=0;$i<$count;$i++)
    {
        if(empty($parameter_arr[$i]))
        {
            return "0";
              exit;
        }
    }
    return "1";
}

?>