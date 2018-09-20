<?php
include 'config.php';
include("../common/config.php");
include("../include/functions.php"); 
include "../dine/dineclass.php";
if(isset($_REQUEST['id']) && isset($_REQUEST['Review']) && isset($_REQUEST['User_ID'])){
		$id		=$_REQUEST['id'];
		$txt1=0;$txt2=0;$txt3=0;$txt4=0;$txt5=0;$txt6=0;
		$txt1	=$_REQUEST['rating1'];
		$txt2	=$_REQUEST['rating2'];
		$txt3	=$_REQUEST['rating3'];
		$txt4	=$_REQUEST['rating4'];
		$txt5	=$_REQUEST['rating5'];
		$txt6	=$_REQUEST['rating6'];
		if($txt1=='')
			$txt1=0;
		if($txt2=='')
			$txt2=0;
		if($txt3=='')
			$txt3=0;
		if($txt4=='')
			$txt4=0;
		if($txt5=='')
			$txt5=0;
		if($txt6=='')
			$txt6=0;
            
		// Total & Average Calculation
		$Total=$txt1+$txt2+$txt3+$txt4+$txt5+$txt6;
		$TT=0;
		if($txt1>0) {
			$TT++; }
		if($txt2>0) {
			$TT++; }
		if($txt3>0) {
			$TT++; }
		if($txt4>0) {
			$TT++; }
		if($txt5>0) {
			$TT++; }
		if($txt6>0) {
			$TT++; }
		$Average=round($Total/$TT);
		// End Total & Average Calculation
		
		$Review ='<p class="content14" style="margin:4px; padding:4px;">'.preg_replace("~[\r\n]+~", '</p><p class="content14" style="margin:4px; padding:4px;">', $_REQUEST['Review']).'</p>';
		$Review		=		mysql_real_escape_string($Review);
		$R_Type="Dine";
		 //$sql = "INSERT into tbl_reviews(Review_ID,user_id,list_id,Dir_ID,R_Type,txt0_poor,txt0_average,txt0_good,txt0_vgood,txt0_excellence,txt1_poor,txt1_average,txt1_good,txt1_vgood,txt1_excellence,txt2_poor,txt2_average,txt2_good,txt2_vgood,txt2_excellence,txt3_poor,txt3_average,txt3_good,txt3_vgood,txt3_excellence,txt4_poor,txt4_average,txt4_good,txt4_vgood,txt4_excellence,txt5_poor,txt5_average,txt5_good,txt5_vgood,txt5_excellence,Review,status,Date_Created) VALUES('','".$_SESSION['User_ID']."','".$id."','".$dir_id."','".$R_Type."','".$txt0_poor."','".$txt0_avg."','".$txt0_good."','".$txt0_vgood."','".$txt0_excellence."','".$txt1_poor."','".$txt1_avg."','".$txt1_good."','".$txt1_vgood."','".$txt1_excellence."','".$txt2_poor."','".$txt2_avg."','".$txt2_good."','".$txt2_vgood."','".$txt2_excellence."','".$txt3_poor."','".$txt3_avg."','".$txt3_good."','".$txt3_vgood."','".$txt3_excellence."','".$txt4_poor."','".$txt4_avg."','".$txt4_good."','".$txt4_vgood."','".$txt4_excellence."','".$txt5_poor."','".$txt5_avg."','".$txt5_good."','".$txt5_vgood."','".$txt5_excellence."','".$Review."',1, now())";
		$sql = "INSERT into tbl_reviews(user_id,list_id,R_Type,Owner_ID,txt0,txt1,txt2,txt3,txt4,txt5,Review,Average,status,Date_Created) VALUES('".$_REQUEST['User_ID']."','".$id."','".$R_Type."','".$_REQUEST['Owner_ID']."','".$txt1."','".$txt2."','".$txt3."','".$txt4."','".$txt5."','".$txt6."','".$Review."','".$Average."',1, now())";
		
		$rec=mysql_query($sql); 
		if($id<>'')
		{
			// Update Sell Table for Average Reviews
			$sql="SELECT total_reviews,average_reviews FROM merchant WHERE id=".$id;
			$rec=@mysql_query($sql);
			$res=@mysql_fetch_array($rec);
			$average_reviews=$res['average_reviews'];
			$total_reviews=$res['total_reviews'];
			$total_reviews=$total_reviews+1;
			if($average_reviews>0)
				$average_review=ceil(($average_reviews+$Average)/2);
			else
				$average_review=$Average;
			$sql="UPDATE merchant SET average_reviews=".$average_review.",total_reviews=".$total_reviews." WHERE id=".$id;
			
			mysql_query($sql);
		}
        $content=array("status"=>1,"message"=>"success");
        echo json_encode($content);
        exit;
}
else{
    $content=array("status"=>0,"message"=>PARAMETER_MSG);
    echo json_encode($content);
    exit; 
}        

?>