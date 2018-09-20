 <?php
    include ('config.php');
    //include '../common/config.php';
	//include("../include/functions.php");
	include("function.php");
    if(isset($_REQUEST['toDate']) && isset($_REQUEST['id']))
    {
    	$dw = date('l', strtotime($_REQUEST['toDate']));
    	$ctime=date('g:i:A');
    	$sql="SELECT * FROM tbl_tablebooking_settings WHERE merchant=".$_REQUEST['id'];
    	$exe=mysql_query($sql);
    	$num=mysql_num_rows($exe);
    	
    	//date_default_timezone_set('GMT+8');//Set New York timezone
    	$today = date("m/d/Y");
    	if($num>0)
    	{
	    	$res=mysql_fetch_array($exe);
	    	$TBS_Breakfast_Start=$res['TBS_Breakfast_'.$dw.'_Start'];
	    	$TBS_Breakfast_End=$res['TBS_Breakfast_'.$dw.'_End'];
	    	
	    	$TBS_BreakTime=$res['TBS_Breakfast'];
	    	if($TBS_BreakTime<1)
	    		$TBS_BreakTime=45;
	    	$TBS_Breakfast_Start1=strtotime($TBS_Breakfast_Start);
	    	$TBS_Breakfast_End1=strtotime($TBS_Breakfast_End);
	    	if($_REQUEST['toDate']==$today && $TBS_Breakfast_Start1<strtotime(date('H:i:s')))
	    	{
	    		$seconds = strtotime(date('H:i:s'));
	    		$rounded = ceil($seconds / (5* 60)) * (5 * 60);
	    		$TBS_Breakfast_Start1=$rounded;
	    	}
	    	if($TBS_Breakfast_Start1==$TBS_Breakfast_End1){
	     	      $content=array("status"=>1,"message"=>"Restaurant Closed");
	              echo json_encode($content);
	              exit;
	          }
	    	else
	    	{
	    
	       	$duration='+'.$TBS_BreakTime.' minutes';
	    	while($TBS_Breakfast_Start1<$TBS_Breakfast_End1)
	    	{
	    		$interval_timestamp = $TBS_Breakfast_Start1;
	    		$Ctiming=$interval_timestamp;
	    		$Ctiming1=date('g:i a',$interval_timestamp);
	    		
	    		$TBS_Breakfast_Start1 += 60*$TBS_BreakTime;
	    		//echo '<option value="'.$Ctiming1.'" >'.$Ctiming1.'</option>';
	            $timings[]=array("timestamp"=>$Ctiming,"time"=>$Ctiming1);
	           	}
	            $content=array("status"=>1,"message"=>"success","timings"=>$timings);
	            echo json_encode($content);
	            exit;
	         } 
      	} 
      	else 
      	{ 
      	
    	$wn = date('N', strtotime($_REQUEST['toDate']));
    	
    	if($wn==7)
    		$wn=0;
    	$sql="SELECT * FROM merchant_hours WHERE weekDay=".$wn." AND merchantID=".$_REQUEST['id'];
    	$exe=mysql_query($sql);
    	$res=mysql_fetch_array($exe);
    	$num=mysql_num_rows($exe);
    	if($res['closed']=='Yes')
    	{
    		$Restaurant_Closed=1;
    	} 
    	else if($res['24hours']=='Yes')
    	{
    		$Restaurant_Closed=0;
    		$TBS_Breakfast_Start="00:00:00";
    		$TBS_Breakfast_End="23:59:59";
    	
    		$TBS_BreakTime=45;
    		$TBS_Breakfast_Start1=strtotime($TBS_Breakfast_Start);
    		$TBS_Breakfast_End1=strtotime($TBS_Breakfast_End);
    		if($_REQUEST['toDate']==$today && $TBS_Breakfast_Start1<strtotime(date('H:i:s')))
    		{
    			$seconds = strtotime(date('H:i:s'));
    			$rounded = ceil($seconds / ($TBS_BreakTime* 60)) * ($TBS_BreakTime * 60);
    			$TBS_Breakfast_Start1=$rounded;
    		}
    	}
    	else 
    	{
    		$Restaurant_Closed=0;
    		$TBS_Breakfast_Start=$res['startTime'];
    		$TBS_Breakfast_End=$res['endTime'];
    		
    		$TBS_Breakfast_StartOther=$res['startTimeOther'];
    		$TBS_Breakfast_EndOther=$res['endTimeOther'];
    
    	
    		$TBS_BreakTime=45;
    		$TBS_Breakfast_StartOther1=strtotime($TBS_Breakfast_StartOther);
    		$TBS_Breakfast_EndOther1=strtotime($TBS_Breakfast_EndOther);
    		
    		$TBS_Breakfast_Start1=strtotime($TBS_Breakfast_Start);
    		$TBS_Breakfast_End1=strtotime($TBS_Breakfast_End);
    		
    		if($_REQUEST['toDate']==$today && $TBS_Breakfast_Start1<strtotime(date('H:i:s')))
    		{
    			$seconds = strtotime(date('H:i:s'));
    			$rounded = ceil($seconds / (5* 60)) * (5 * 60);
    			$TBS_Breakfast_Start1=$rounded;
    		}
    		if($_REQUEST['toDate']==$today && $TBS_Breakfast_StartOther1<strtotime(date('H:i:s')))
    		{
    			$seconds = strtotime(date('H:i:s'));
    			$rounded = ceil($seconds / (5* 60)) * (5 * 60);
    			$TBS_Breakfast_EndOther1=$rounded;
    		}
    	}
    	if($Restaurant_Closed==1)
    	{
    		$content=array("status"=>1,"message"=>"Restaurant Closed");
            echo json_encode($content);
            exit;
    	}
    	else
    	{
    	   $duration='+'.$TBS_BreakTime.' minutes';
    		while($TBS_Breakfast_Start1<$TBS_Breakfast_End1)
    		{
    			$interval_timestamp = $TBS_Breakfast_Start1;
    			$Ctiming=$interval_timestamp;
    			$Ctiming1=date('g:i a',$interval_timestamp);
    			
    			$TBS_Breakfast_Start1 += 60*$TBS_BreakTime;
    			//echo '<option value="'.$Ctiming1.'" >'.$Ctiming1.'</option>';
                $timings[]=array("timestamp"=>$Ctiming,"time"=>$Ctiming1);
    		}
    		if($TBS_Breakfast_StartOther<>$TBS_Breakfast_EndOther)
    		{
    		while($TBS_Breakfast_StartOther1<$TBS_Breakfast_EndOther1)
    		{
    			$interval_timestamp = $TBS_Breakfast_StartOther1;
    			$Ctiming=$interval_timestamp;
    			$Ctiming1=date('g:i a',$interval_timestamp);
    			
    			$TBS_Breakfast_StartOther1 += 60*$TBS_BreakTime;
    			//echo '<option value="'.$Ctiming1.'" >'.$Ctiming1.'</option>';
                $timings[]=array("timestamp"=>$Ctiming,"time"=>$Ctiming1);
    		}
    		}
    		    /*----------- Deeshit -----------*/
	    		if(empty($timings))
	    		{
		            $content=array("status"=>"0","message"=>"No timing found for this restaurant");
		            echo json_encode($content);
		            exit;
	    		}
	    		else
	    		{
	    			$content=array("status"=>"1","message"=>"success","timings"=>$timings);
		            echo json_encode($content);
		            exit;
	    		}
    	}
      }
  }
  else{
    $content=array("status"=>0,"message"=>PARAMETER_MSG);
    echo json_encode($content);
    exit;
  }
  ?>
