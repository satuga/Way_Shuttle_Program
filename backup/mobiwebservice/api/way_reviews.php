<?php
include 'config.php';
include("../common/config.php");
include("../include/functions.php"); 
include "../dine/dineclass.php";
$Dine_Obj	=	new Dine();
//$Records=$Dine_Obj->view_ServiceReviews();
if(isset($_REQUEST['User_ID']) && isset($_REQUEST['id'])){
$Transactions=GetTotalRestaurantTransactions($_REQUEST['User_ID'],$_REQUEST['id']);
$bsql="SELECT AVG(txt0) AS RATING FROM tbl_reviews WHERE R_Type='Dine' AND list_id =".$_REQUEST['id'];
$bexe=mysqli_query($con,$bsql);
$bnum=mysqli_num_rows($bexe);
$bres=mysqli_fetch_array($bexe);
$overall_rating=round($bres['RATING']);
$rsql="SELECT * FROM tbl_reviewlabel WHERE La_Status=1 AND La_Cat=72 LIMIT 6";
$rexe=mysqli_query($con,$rsql);
$rnum=mysqli_num_rows($rexe);
		if($rnum>0)
		{
			$ki=1;
			while($rres=mysqli_fetch_array($rexe))
			{
				$asql="SELECT AVG(txt".$ki.") AS RATING FROM tbl_reviews WHERE R_Type='Dine' AND list_id =".$_REQUEST['id'];
				$aexe=mysqli_query($con,$asql);
				$anum=mysqli_num_rows($aexe);
				$ares=mysqli_fetch_array($aexe);
			    $name_rating[]= array("name"=>$rres["La_Name"],"rating"=>round($ares['RATING']));
				$ki++;
			}
        }
        else{
            $name_rating=array();
        }
					
	$Reviews=$Dine_Obj->View_DineReviews();
	if($Reviews[1]!=0)
	{
	foreach($Reviews[0] as $k=>$v) {
	if($v['status']==1){
	$stat="Active"; $st=0; }
	else {
	$stat="InActive"; $st=1; }

		
					$sql="SELECT * FROM tbl_registeration WHERE id=".$v['user_id'];
					$exe=mysqli_query($con,$sql);
					$res=mysqli_fetch_array($exe);
					$path="admin/upload/users/";
					$pos = strpos($res['logo'], "http://");
					if($res['display_name']<>'')
						$CName=$res['display_name'];
					else if($res['firstname']<>'')
						$CName=$res['firstname'];
					else
						$CName="Guest";
		
						if ($pos !== false) {
						$f=$res['logo'];
						}
						else
						{
							if($res['logo']!='') 
								$f=$path.$res['logo'];
							else
								$f='admin/upload/users/img_defaultphoto.gif';
							$thumb_image= HOSTPATH."imageresize.php?maxsize=75&source=".$f."'";
						 }
						
		if($v['Review']=='')
			$review_text="-";
		else
			$review_text=$v['Review'];
            $date=date("F j, Y",strtotime($v['Date_Created']));
			$review_id=$v['Review_ID'];
        $review[]=array("name"=>$CName,"image"=>$thumb_image,"review_text"=>$review_text,"review_id"=>$review_id,"date"=>$date);           
	   }
	}
    else{
        $review=array();
    }
    
    $content=array("status"=>1,"message"=>"success","review"=>$review,"overall_rating"=>$overall_rating,"other_rating"=>$name_rating);
    echo json_encode($content);
    exit;
}
else{
    $content=array("status"=>0,"message"=>PARAMETER_MSG);
    echo json_encode($content);
    exit;
}    
	?>

