<?php
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once('config.php');
$get_parking_id_query = "select PL_ParkingID,PL_ID from tbl_parkinglog where PL_IsEmailSent = 'N' and ( PL_OldSpace IS NOT NULL or PL_NewSpace IS NOT NULL or PL_OldPrice IS NOT NULL or PL_NewPrice IS NOT NULL or PL_ParkingStatus IS NOT NULL )group by PL_ParkingID";

//$get_parking_id_query = "select PL_ParkingID from tbl_parkinglog where PL_Created >= DATE_SUB(NOW(),INTERVAL 1 HOUR) AND PL_IsEmailSent = 'N' group by PL_ParkingID";
$get_parking_id_sql = mysqli_query($con, $get_parking_id_query);

//print_r($get_parking_id_query);exit;


while ($get_parking_id_row = mysqli_fetch_assoc($get_parking_id_sql)) {
    $one_park_detail_query = "select tbl_parking.P_ID,Park_ID,Park_Name,Park_Address,P_Status from tbl_parkinglocations left join tbl_parking on tbl_parking.P_Location =  tbl_parkinglocations.Park_ID where tbl_parking.P_ID = '".$get_parking_id_row['PL_ParkingID']."'" ;
    $one_park_detail = mysqli_query($con, $one_park_detail_query);
    $row_one_park_detail = mysqli_fetch_assoc($one_park_detail);
	
	
    // print_r($one_park_detail_query);exit;
    if ($row_one_park_detail) {
		//$park_log_detail_query = "select PL_ActionType,PL_PriceOrSpaceUpdatedFor,PL_OldPrice,PL_NewPrice,PL_OldSpace,PL_NewSpace from tbl_parkinglog where PL_ParkingID = '".$row_one_park_detail['P_ID']."' and PL_Created >= DATE_SUB(NOW(),INTERVAL 1 HOUR) and PL_IsEmailSent = 'N' and PL_OldSpace <> '' and PL_NewSpace <> ''order by PL_Created desc";
		
        $park_log_detail_query = "select PL_ActionType,PL_PriceOrSpaceUpdatedFor,PL_OldPrice,PL_NewPrice,PL_OldSpace,PL_NewSpace from tbl_parkinglog where PL_ParkingID = '".$row_one_park_detail['P_ID']."' and PL_IsEmailSent = 'N' and PL_OldSpace IS NOT NULL and PL_NewSpace IS NOT NULL order by PL_Created desc";
        $log_sql_park = mysqli_query($con, $park_log_detail_query);
        // $row_log_park = mysqli_fetch_assos($log_sql_park);
        // echo "<pre>";
        // print_r($log_sql_park);exit;
        $num_of_row_for_space = mysqli_num_rows($log_sql_park);
		//$row_one_park_detail = mysqli_fetch_all($log_sql_park);
		//print_r($row_one_park_detail);exit;
    // );

    }
    if ($row_one_park_detail) {
        //$park_get_old_price_query = "select PL_ActionType,PL_PriceOrSpaceUpdatedFor,PL_OldPrice,PL_NewPrice,PL_OldSpace,PL_NewSpace from tbl_parkinglog where PL_ParkingID = '".$row_one_park_detail['P_ID']."' and PL_Created >= DATE_SUB(NOW(),INTERVAL 1 HOUR) and PL_IsEmailSent = 'N' and PL_OldPrice <> '' and PL_NewPrice <> ''order by PL_Created desc";
		
		 $park_get_old_price_query = "select PL_ActionType,PL_PriceOrSpaceUpdatedFor,PL_OldPrice,PL_NewPrice,PL_OldSpace,PL_NewSpace from tbl_parkinglog where PL_ParkingID = '".$row_one_park_detail['P_ID']."' and PL_IsEmailSent = 'N' and PL_OldPrice IS NOT NULL and PL_NewPrice IS NOT NULL order by PL_Created desc";
		 
        $log_get_old_price_sql_park = mysqli_query($con, $park_get_old_price_query);
        // $row_get_old_price_log_park = mysqli_fetch_assos($log_sql_park);
        // print_r($park_get_old_price_query);exit;
        $num_of_row_for_price = mysqli_num_rows($log_get_old_price_sql_park);


    }

    if ($row_one_park_detail) {
        //$get_parking_status_chnage_query = "select PL_ActionType,PL_PriceOrSpaceUpdatedFor,PL_OldPrice,PL_NewPrice,PL_OldSpace,PL_NewSpace,PL_ParkingStatus,PL_Created from tbl_parkinglog where PL_ParkingID = '".$row_one_park_detail['P_ID']."' and PL_Created >= DATE_SUB(NOW(),INTERVAL 1 HOUR) and PL_IsEmailSent = 'N' and  PL_ParkingStatus <> '' order by PL_Created desc";
		
		  $get_parking_status_chnage_query = "select PL_ActionType,PL_PriceOrSpaceUpdatedFor,PL_OldPrice,PL_NewPrice,PL_OldSpace,PL_NewSpace,PL_ParkingStatus,PL_Created from tbl_parkinglog where PL_ParkingID = '".$row_one_park_detail['P_ID']."' and PL_IsEmailSent = 'N' and  PL_ParkingStatusIS NOT NULL  order by PL_Created desc";
        $get_parking_status_chnage_sql = mysqli_query($con, $get_parking_status_chnage_query);
        // $row_get_old_price_log_park = mysqli_fetch_assos($log_sql_park);
        // print_r($get_parking_status_chnage_query);exit;
        $get_parking_status_chnage_count = mysqli_num_rows($get_parking_status_chnage_sql);


    }

if($row_one_park_detail['P_Status'] == '0'){
    $p_status= 'In-Active';
}else{
    $p_status ='Active';
}
// Seller Confimation mail details
$message='	<style type="text/css">
@font-face {
font-family: \'UniversalDoomsdayBold\';
src: url(\''.$Host_Path.'fonts/universal_doomsday_bold.eot\');
src: url(\''.$Host_Path.'fonts/universal_doomsday_bold.eot\') format(\'embedded-opentype\'),
 url(\''.$Host_Path.'fonts/universal_doomsday_bold.woff\') format(\'woff\'),
 url(\''.$Host_Path.'fonts/universal_doomsday_bold.ttf\') format(\'truetype\'),
 url(\''.$Host_Path.'fonts/universal_doomsday_bold.svg#UniversalDoomsdayBold\') format(\'svg\');
}
@font-face {
font-family: \'din-mediumregular\';
src: url(\''.$Host_Path.'fonts/dinm____-webfont.eot\');
src: url(\''.$Host_Path.'fonts/dinm____-webfont.eot?#iefix\') format(\'embedded-opentype\'),
 url(\''.$Host_Path.'fonts/dinm____-webfont.woff\') format(\'woff\'),
 url(\''.$Host_Path.'fonts/dinm____-webfont.ttf\') format(\'truetype\'),
 url(\''.$Host_Path.'fonts/dinm____-webfont.svg#din-mediumregular\') format(\'svg\');
font-weight: normal;
font-style: normal;

}
</style>
<table cellpadding="0" cellspacing="0" border="0" width="780" style="border: 1px solid #ccc;">
<tr>
<td>
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background: #454645; height: 60px; width: 100%;">
        <tr><td style="float:center;text-align:center;font: bold 38px din-mediumregular; color: #fff; text-transform: none; text-shadow: 1px 1px 18px #000;padding-top:10px;"><img src="'.$Host_Path.'images/logored.png" /></td></tr>
        <tr><td style="float:center;text-align:center;font: bold 18px din-mediumregular; color: #fff; text-transform: none; text-shadow: 1px 1px 18px #000;">When you go out, go all out in style.</td></tr>
        <tr><td style="padding-top:15px;padding-bottom:10px;align:center;">
        <table width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
        <td width="20%" align="center" ><a href="'.$Host_Path.'" style="float:center;text-align:center;font: normal 18px din-mediumregular; color: #f15852;text-decoration:none;"><span style="float:center;text-align:center;font: normal 18px din-mediumregular; color: #f15852;text-decoration:none;">DINING</span></a></td>
        <td width="20%" align="center"><a href="'.$Host_Path.'movies" style="float:center;text-align:center;font: normal 18px din-mediumregular; color: #f15852;text-decoration:none;"><span style="float:center;text-align:center;font: normal 18px din-mediumregular; color: #f15852;text-decoration:none;">MOVIES</span></a></td>
        <td width="20%" align="center"><a href="'.$Host_Path.'parking" style="float:center;text-align:center;font: normal 18px din-mediumregular; color: #f15852;text-decoration:none;"><span style="float:center;text-align:center;font: normal 18px din-mediumregular; color: #f15852;text-decoration:none;">PARKING</span></a></td>
        <td width="20%" align="center"><a href="'.$Host_Path.'events" style="float:center;text-align:center;font: normal 18px din-mediumregular; color: #f15852;text-decoration:none;"><span style="float:center;text-align:center;font: normal 18px din-mediumregular; color: #f15852;text-decoration:none;">EVENT TICKETS</span></a></td>
        <td width="20%" align="center"><a href="'.$Host_Path.'activities" style="float:center;text-align:center;font: normal 18px din-mediumregular; color: #f15852;text-decoration:none;"><span style="float:center;text-align:center;font: normal 18px din-mediumregular; color: #f15852;text-decoration:none;">ACTIVITIES</span></a></td>
        </tr>
        </table>
        </td></tr>
    </table>
</td>
</tr>

<tr>
<td style="padding: 30px 20px;">
</td>
</tr>
<tr>
<td>
    <table cellpadding="0" cellspacing="0" border="0" style="border: 1px solid #ccc;  font: normal 14px din-mediumregular, Arial, Helvetica, sans-serif; color: #353535; width:98%; margin:0 auto; ">
        <tr>
            <td width="200" style="border-right: 1px solid #ccc; border-bottom: 1px solid #ccc; padding: 10px;"><strong>Lot Name</strong></td>
            <td  style="padding: 10px; border-bottom: 1px solid #ccc;">'.$row_one_park_detail['Park_Name'].'<br/>'.$row_one_park_detail['Park_Address'].'</td>

        </tr>
        <tr>
            <td width="200" style="border-right: 1px solid #ccc; padding: 10px; border-bottom: 1px solid #ccc;"><strong>Lot ID</strong></td>
            <td  style="padding: 10px; border-bottom: 1px solid #ccc;">'.$row_one_park_detail['P_ID'].'</td>

        </tr>
        <tr>
            <td width="200" style="border-right: 1px solid #ccc; border-bottom: 1px solid #ccc; padding: 10px;"><strong>No of Spaces changes</strong></td>';
            if($num_of_row_for_space > 0){
            $message .='<td  style="padding: 5px; border-bottom: 1px solid #ccc;">
            <table cellpadding="5" cellspacing="0" border="0" style="font: normal 14px din-mediumregular, Arial, Helvetica, sans-serif; color: #353535; width:100%; margin:0 auto; ">
                    <thead style="text-align:left; padding:0px 10px; ">
                        <tr>
                        <th>Dates: </th>
                        <th>Old Space:</th>
                        <th>New Space:</th>
                        <th style="width:150px;">Action:</th>
                        </tr>
                    </thead>
                    <tbody>';
                        while($row_log_park = mysqli_fetch_assoc($log_sql_park)){
							
                            if (!empty($row_log_park)) {
                            if(($row_log_park['PL_OldSpace'] != '')  && ($row_log_park['PL_NewSpace'] != '')){
                                $message .='
                                <tr>
                                    <td>'.date('d-m-Y', strtotime($row_log_park['PL_PriceOrSpaceUpdatedFor'])).'
                                    </td><td>'.$row_log_park['PL_OldSpace'].'</td>
                                    <td>'.$row_log_park['PL_NewSpace'].'</td>
                                    <td style="width:150px;">'.$row_log_park['PL_ActionType'].'</td>
                                </tr>
                                ';
                            }
                        }

                    }
                    $message .='</tbody>
                </table></td>';
            }else{
                $message .='<td  style="padding: 10px; border-bottom: 1px solid #ccc;">No changes</td>';
            }
        $message .='</tr>
        <tr>
            <td width="200" style="border-right: 1px solid #ccc; border-bottom: 1px solid #ccc; padding: 10px;"><strong>Price Changes</strong></td>';
            if($num_of_row_for_price > 0){

            $message .='<td  style="padding: 5px; border-bottom: 1px solid #ccc;"><table cellpadding="5" cellspacing="0" border="0" style="font: normal 14px din-mediumregular, Arial, Helvetica, sans-serif; color: #353535; width:100%; margin:0 auto; ">
                    <thead style="text-align:left; padding:0px 10px; ">
                        <tr>
                        <th>Dates: </th>
                        <th>Old Value:</th>
                        <th>New Value:</th>
                        <th style="width:150px;">Action:</th>
                        </tr>
                    </thead>
                    <tbody>';
                        while($row_get_old_price_log_park = mysqli_fetch_assoc($log_get_old_price_sql_park)){
                            if (!empty($row_get_old_price_log_park)) {
                            if(($row_get_old_price_log_park['PL_OldPrice'] != '')  && ($row_get_old_price_log_park['PL_NewPrice'] != '')){
                                $message .='
                                <tr>
                                    <td>'.date('d-m-Y', strtotime($row_get_old_price_log_park['PL_PriceOrSpaceUpdatedFor'])).'
                                    </td><td>'.$row_get_old_price_log_park['PL_OldPrice'].'</td>
                                    <td>'.$row_get_old_price_log_park['PL_NewPrice'].'</td>
                                    <td style="width:150px;">'.$row_get_old_price_log_park['PL_ActionType'].'</td>

                                </tr>
                                ';
                            }
                        }

                    }
            $message .='</tbody>
                </table></td>';
            }else{
                $message .='<td  style="padding:10px; border-bottom: 1px solid #ccc;">No changes</td>';
            }
            $message .='</tr>
            <tr>
                <td width="200" style="border-right: 1px solid #ccc; border-bottom: 1px solid #ccc; padding: 10px;"><strong>Parking Status Log</strong></td>';
                if($get_parking_status_chnage_count > 0){

                $message .='<td  style="padding: 5px; border-bottom: 1px solid #ccc;"><table cellpadding="5" cellspacing="0" border="0" style="font: normal 14px din-mediumregular, Arial, Helvetica, sans-serif; color: #353535; width:100%; margin:0 auto; ">
                        <thead style="text-align:left; padding:0px 10px; ">
                            <tr>
                            <th>Action:</th>
                            <th>Action on:</th>
                            </tr>
                        </thead>
                        <tbody>';
                            while($get_parking_status_chnage_Data = mysqli_fetch_assoc($get_parking_status_chnage_sql)){
                                if (!empty($get_parking_status_chnage_Data)) {
                                if($get_parking_status_chnage_Data['PL_ParkingStatus'] != ''){
                                    $message .='
                                    <tr>
                                        <td>'.$get_parking_status_chnage_Data['PL_ActionType'].'</td>
                                        <td>'.date('d-M-Y H:i:s', strtotime($get_parking_status_chnage_Data['PL_Created'])).'
                                        </td>
                                    </tr>
                                    ';
                                }
                            }

                        }
                $message .='</tbody>
                    </table></td>';
                }else{
                    $message .='<td  style="padding: 10px; border-bottom: 1px solid #ccc;">No changes</td>';
                }
                $message .='</tr>
        <tr>
            <td width="200" style="border-right: 1px solid #ccc; border-bottom: 1px solid #ccc; padding: 10px;"><strong>Current Status</strong></td>
            <td  style="padding: 10px; border-bottom: 1px solid #ccc;">'.$p_status.'</td>
        </tr>
    </table>
</td>
</tr>

</tr><tr><td height="25">&nbsp;</td></tr><tr>
<td style="padding: 10px 20px;"><p style="font: bold 14px din-mediumregular, Arial, Helvetica, sans-serif; color: #343434; line-height: 25px; margin: 0;">Questions? Please call Way.com at 408-598-3338 immediately or send an email to support@way.com.</p></td>
</tr>
<tr><td>
<div style="padding-top:30px;"></div>
   <div align="center" margin: 0 auto;">

       <img src="'.$Host_Path.'images/slogan.png" /><br/><img style="padding: 14px 0 44px 0;" src="'.$Host_Path.'images/footerblack.png" />

   </div>
<div style="font: normal 13px UniversalDoomsdayBold, Arial, Helvetica, sans-serif; color: #000; text-transform: none; line-height: 22px;text-align:center;">It\'s your life, way makes it simpler.</div>
</td></tr>
</table>';

$email_from = "support@way.com"; // Who the email is from
  $headers = "From: ".$email_from."\r\n" .
  'Reply-To: '.$email_from."\r\n" .
  'X-Mailer: PHP/' . phpversion();
  $headers .= 'MIME-Version: 1.0' . "\r\n";
  $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
  $subject = $row_one_park_detail['Park_Name']." update alert.";
	$mailto = "mohinder.singh@way.com,rodrigo.aronas@way.com,srisakthi.manohar@way.com;arun.krishnan@way.com;nishanth.reddy@way.com;binu.girija@way.com;jr.anciano@way.com;bhumi.bhutani@way.com;Brandon.nader@gmail.com";
    //$mailto = "jeba.raj@way.com";
    //$mailto = "mukund.bhut@letsnurture.com, mukund.letsnurture@gmail.com";

if(mail($mailto, $subject, $message, $headers)){
    //$update_email_status_in_log_tbl= "UPDATE tbl_parkinglog SET PL_IsEmailSent ='Y' WHERE PL_ParkingID=".$get_parking_id_row['PL_ParkingID']." AND PL_Created >= DATE_SUB(NOW(),INTERVAL 1 HOUR)";
	$update_email_status_in_log_tbl= "UPDATE tbl_parkinglog SET PL_IsEmailSent ='Y' WHERE PL_ParkingID='".$get_parking_id_row['PL_ParkingID']."'";
    $one_park_detail = mysqli_query($con, $update_email_status_in_log_tbl);
       echo "mail sent <br/>";
  }else{
       echo "mail not sent<br/>";
  }
}
echo "Done";
