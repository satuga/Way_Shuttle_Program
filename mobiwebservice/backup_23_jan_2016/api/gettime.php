<?php
include ('config.php');
//include '../common/config.php';
include ('function.php');
include "../dine/dineclassbk.php";
$objdine = new Dine();
$id = $_REQUEST['id'];
//***getting days
$today = $selected= strtotime(date("Y-m-d"));
$oneday = 24 * 3600;

for($i = $today;$i<= $today + 4*$oneday ; $i += $oneday)
{
	if(strtotime(date("Y-m-d"))==$i)
		$label="Today";
	else if(strtotime(date('Y-m-d', strtotime('tomorrow')))==$i)
		$label="Tomorrow";
	else
		$label=ucfirst(strtolower(date( "l", $i)));
	if($selected=='')
		$selected=$i;
	$weekday=idate("w",$i);
	$closed=$objdine->Restaurant_Open($_REQUEST['id'],$weekday);
	if($closed=='No')
	{	
            $date[]=array("label"=>$label,"date"=>date("Y-m-d", $i));
		
	}
}
//***getting times
//GLOBAL $Time_Zone;
$currenttime = strtotime(date("F j, Y, g:i a"));
$ordertime=$currenttime;
$selectedtime=strtotime($currenttime);	

$order_times=array();
$weekday=idate("w",$selected);
$Open_Times=$objdine->Restaurant_OpenHours($_REQUEST['id'],$weekday,$_REQUEST['orderfor']);
$Open_Time=strtotime($Open_Times[0]);
$End_Time=strtotime($Open_Times[1]);
$timer=date("g:i A",strtotime('0 minutes',$Open_Time));

if($Open_Time<>'' && $End_Time<>'')
{
if($Open_Time<$End_Time)
{
while($Open_Time<$End_Time)
{
	$timer=date("g:i A",strtotime('15 minutes',$Open_Time));
	$Open_Time=date(strtotime('15 minutes',$Open_Time));
	if($selected==$today) {
	if($Open_Time>$currenttime)
	{
	if($selectedtime==strtotime('15 minutes',$Open_Time))
	//$order_times.= '<option value="'.date("H:i:s",strtotime('0 minutes',$Open_Time)).'" selected>'.$timer.'</option>';
    $order_times[]=array("timer"=>$timer,"time"=>date("H:i:s",strtotime('0 minutes',$Open_Time)));
	else
	//$order_times.= '<option value="'.date("H:i:s",strtotime('0 minutes',$Open_Time)).'">'.$timer.'</option>';
    $order_times[]=array("timer"=>$timer,"time"=>date("H:i:s",strtotime('0 minutes',$Open_Time)));
	}
	}
	else
	{
	if($selectedtime==strtotime('15 minutes',$Open_Time))
	//$order_times.= '<option value="'.date("H:i:s",strtotime('0 minutes',$Open_Time)).'" selected>'.$timer.'</option>';
    $order_times[]=array("timer"=>$timer,"time"=>date("H:i:s",strtotime('0 minutes',$Open_Time)));
	else
	//$order_times.= '<option value="'.date("H:i:s",strtotime('0 minutes',$Open_Time)).'">'.$timer.'</option>';
    $order_times[]=array("timer"=>$timer,"time"=>date("H:i:s",strtotime('0 minutes',$Open_Time)));
	}
}
}
else
{
	// Times comes in  2 ranges
	//if($Open_Time>$End_Time)
	//First Range
	$End_Time=strtotime($Open_Times[1]);
	$Open_Time=strtotime("12:00 AM");
	if($selected<>$today)
	//$order_times.= '<option value="'.date("H:i:s",strtotime('12:00 AM')).'">12:00 AM</option>';
     $order_times[]=array("timer"=>"12:00 AM","time"=>date("H:i:s",strtotime('12:00 AM')));
	while($Open_Time<$End_Time)
	{
		$timer=date("g:i A",strtotime('15 minutes',$Open_Time));
		$Open_Time=date(strtotime('15 minutes',$Open_Time));
		//echo "<br>selected:".date("Y-m-d",$selected);
	//echo "<br>today:".date("Y-m-d",$today);
		if($selected==$today) {
		if($Open_Time>$currenttime)
		{
		if($selectedtime==strtotime('15 minutes',$Open_Time))
		//$order_times.= '<option value="'.date("H:i:s",strtotime('0 minutes',$Open_Time)).'" selected>'.$timer.'</option>';
        $order_times[]=array("timer"=>$timer,"time"=>date("H:i:s",strtotime('0 minutes',$Open_Time)));
		else
		//$order_times.= '<option value="'.date("H:i:s",strtotime('0 minutes',$Open_Time)).'">'.$timer.'</option>';
        $order_times[]=array("timer"=>$timer,"time"=>date("H:i:s",strtotime('0 minutes',$Open_Time)));
		}
		}
		else
		{
		if($selectedtime==strtotime('15 minutes',$Open_Time))
		//$order_times.= '<option value="'.date("H:i:s",strtotime('0 minutes',$Open_Time)).'" selected>'.$timer.'</option>';
        $order_times[]=array("timer"=>$timer,"time"=>date("H:i:s",strtotime('0 minutes',$Open_Time)));
		else
		//$order_times.= '<option value="'.date("H:i:s",strtotime('0 minutes',$Open_Time)).'">'.$timer.'</option>';
        $order_times[]=array("timer"=>$timer,"time"=>date("H:i:s",strtotime('0 minutes',$Open_Time)));
		}
	}
	// End First Range
	// Second Range
	$Open_Time=strtotime($Open_Times[0]);
	$End_Time=strtotime("11:45 PM");
	//echo "<br>OT:".date("H:i:s",$Open_Time);
	//echo "<br>ET:".date("H:i:s",$End_Time);
	if($selected==$today) {
	if($Open_Time>$currenttime) {
		if($selectedtime==strtotime('0 minutes',$Open_Time))
		//$order_times.= '<option value="'.date("H:i:s",strtotime('0 minutes',$Open_Time)).'" selected>'.$timer.'</option>';
        $order_times[]=array("timer"=>$timer,"time"=>date("H:i:s",strtotime('0 minutes',$Open_Time)));
		//else
		//$order_times.= '<option value="'.date("H:i:s",strtotime('0 minutes',$Open_Time)).'">'.$timer.'222222</option>';
	}
	}
	else
	{
		if($selectedtime==strtotime('0 minutes',$Open_Time))
		//$order_times.= '<option value="'.date("H:i:s",strtotime('0 minutes',$Open_Time)).'" selected>'.$timer.'</option>';
        $order_times[]=array("timer"=>$timer,"time"=>date("H:i:s",strtotime('0 minutes',$Open_Time)));
		else
		//$order_times.= '<option value="'.date("H:i:s",strtotime('0 minutes',$Open_Time)).'">'.$timer.'</option>';
        $order_times[]=array("timer"=>$timer,"time"=>date("H:i:s",strtotime('0 minutes',$Open_Time)));
	}
	while($Open_Time<$End_Time)
	{
		$timer=date("g:i A",strtotime('15 minutes',$Open_Time));
		$Open_Time=date(strtotime('15 minutes',$Open_Time));
		if($selected==$today) {
		if($Open_Time>$currenttime)
		{
		if($selectedtime==strtotime('15 minutes',$Open_Time))
		//$order_times.= '<option value="'.date("H:i:s",strtotime('0 minutes',$Open_Time)).'" selected>'.$timer.'</option>';
        $order_times[]=array("timer"=>$timer,"time"=>date("H:i:s",strtotime('0 minutes',$Open_Time)));
		else
		//$order_times.= '<option value="'.date("H:i:s",strtotime('0 minutes',$Open_Time)).'">'.$timer.'</option>';
        $order_times[]=array("timer"=>$timer,"time"=>date("H:i:s",strtotime('0 minutes',$Open_Time)));
		}
		}
		else
		{
		if($selectedtime==strtotime('15 minutes',$Open_Time))
		//$order_times.= '<option value="'.date("H:i:s",strtotime('0 minutes',$Open_Time)).'" selected>'.$timer.'</option>';
        $order_times[]=array("timer"=>$timer,"time"=>date("H:i:s",strtotime('0 minutes',$Open_Time)));
		else
		//$order_times.= '<option value="'.date("H:i:s",strtotime('0 minutes',$Open_Time)).'">'.$timer.'</option>';
        $order_times[]=array("timer"=>$timer,"time"=>date("H:i:s",strtotime('0 minutes',$Open_Time)));
		}
	}
	
	/*if(date("H:i:s",strtotime("11:45 PM"))==date("H:i:s",$End_Time))
	{
		$order_times.= '<option value="'.date("H:i:s",strtotime('15 minutes',$End_Time)).'">12:00 AM</option>';
	}*/
	// End Second range
	
// Times comes in  2 ranges
}
// Time other
$Open_Time=strtotime($Open_Times[2]);
$End_Time=strtotime($Open_Times[3]);
$timer=date("g:i A",strtotime('0 minutes',$Open_Time));
if($Open_Time<>'' && $End_Time<>'')
{
if($selected==$today) {
if($Open_Time>$currenttime)
{
	if($selectedtime==strtotime('0 minutes',$Open_Time))
	//$order_times.= '<option value="'.date("H:i:s",strtotime('0 minutes',$Open_Time)).'" selected>'.$timer.'</option>';
    $order_times[]=array("timer"=>$timer,"time"=>date("H:i:s",strtotime('0 minutes',$Open_Time)));
	else
	//$order_times.= '<option value="'.date("H:i:s",strtotime('0 minutes',$Open_Time)).'">'.$timer.'</option>';
    $order_times[]=array("timer"=>$timer,"time"=>date("H:i:s",strtotime('0 minutes',$Open_Time)));
}
}
else
{
	if($selectedtime==strtotime('0 minutes',$Open_Time) && $ordertime<>'00:00:00')
	//$order_times.= '<option value="'.date("H:i:s",strtotime('0 minutes',$Open_Time)).'" selected>'.$timer.'</option>';
    $order_times[]=array("timer"=>$timer,"time"=>date("H:i:s",strtotime('0 minutes',$Open_Time)));
	else if($timer<>'12:00 AM')
	//$order_times.= '<option value="'.date("H:i:s",strtotime('0 minutes',$Open_Time)).'">'.$timer.'</option>';
    $order_times[]=array("timer"=>$timer,"time"=>date("H:i:s",strtotime('0 minutes',$Open_Time)));
}
while($Open_Time<$End_Time)
{
	$timer=date("g:i A",strtotime('15 minutes',$Open_Time));
	$Open_Time=date(strtotime('15 minutes',$Open_Time));
	if($selected==$today) {
	if($Open_Time>$currenttime)
	{
	if($selectedtime==strtotime('15 minutes',$Open_Time))
		//$order_times.= '<option value="'.date("H:i:s",strtotime('0 minutes',$Open_Time)).'" selected>'.$timer.'</option>';
        $order_times[]=array("timer"=>$timer,"time"=>date("H:i:s",strtotime('0 minutes',$Open_Time)));
	else
		//$order_times.= '<option value="'.date("H:i:s",strtotime('0 minutes',$Open_Time)).'">'.$timer.'</option>';
        $order_times[]=array("timer"=>$timer,"time"=>date("H:i:s",strtotime('0 minutes',$Open_Time)));
	}
	}
	else
	{
		if($selectedtime==strtotime('15 minutes',$Open_Time))
		//$order_times.= '<option value="'.date("H:i:s",strtotime('0 minutes',$Open_Time)).'" selected>'.$timer.'</option>';
        $order_times[]=array("timer"=>$timer,"time"=>date("H:i:s",strtotime('0 minutes',$Open_Time)));
		else
		//$order_times.= '<option value="'.date("H:i:s",strtotime('0 minutes',$Open_Time)).'">'.$timer.'</option>';
        $order_times[]=array("timer"=>$timer,"time"=>date("H:i:s",strtotime('0 minutes',$Open_Time)));
	}
}
}
}
else
{
	$order_times="";
}
$row['date']=$date;
$row['order_times']=$order_times;
echo json_encode($row);
exit;
?>