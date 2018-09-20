<?php
ini_set('display_errors', '0');
include('constants.php');
mysql_connect(DB_SERVER,DB_USERNAME,DB_PASSWORD) or die("can not connect to database");
mysql_select_db(DB_DATABASE)or die("can not select database");

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