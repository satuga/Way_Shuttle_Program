<?php 
require_once('config.php');
GLOBAL $con;

/* get current and current+14 day record from tbl_parkingweekdayavailability */
$GetParkingrecords = "SELECT * FROM tbl_parkingweekdayavailability AS tpw INNER JOIN tbl_parking AS tpp ON tpp.P_ID = tpw.P_ID WHERE DATE( tpw.P_fromDate ) 
BETWEEN CURDATE( ) AND CURDATE( ) + INTERVAL 14 DAY AND tpw.PA_No_Spaces !=  '0' AND tpw.occupiedSpaces IS NOT NULL AND tpw.occupiedSpaces !=  '0'
AND tpp.Is_DynamicPrice =  '1' ORDER BY tpw.PA_ID DESC";
$GetParkingrecord_query = mysqli_query($con, $GetParkingrecords) or die("Error");
$GetParkingrecord_count = mysqli_num_rows($GetParkingrecord_query);       
	if (!empty($GetParkingrecord_count)){   
            $CURDATE = date('Y-m-d');
	        while ($GetParkingrecord = mysqli_fetch_array($GetParkingrecord_query)) {            
                    $pa_id = $GetParkingrecord['P_ID'];
                    $parking_space = $GetParkingrecord['PA_No_Spaces']; 
	                $occupiedSpaces = $GetParkingrecord['occupiedSpaces'];
	                $PA_P_Dailyprice = $GetParkingrecord['PA_P_Dailyprice'];  
			        /* get percentage from tbl_parkingweekdayavailability */    
		            $percentage = (($occupiedSpaces*100)/$parking_space); 

                    /* check utilization threshold and price on tbl_dynamic_price_rule */
		            $dynamic_detail = "SELECT * FROM tbl_dynamic_price_rule WHERE P_ID = '".$pa_id."' and util_threshold <= '".$percentage."' order by util_threshold desc limit 1";
	                $dynamic_detail_query = mysqli_query($con, $dynamic_detail) or die("Error");
	                $dynamic_detail_count = mysqli_num_rows($dynamic_detail_query);
		                if (!empty($dynamic_detail_count)){   

			                while ($dynamic_detail_data = mysqli_fetch_array($dynamic_detail_query)){
	                        $util_threshold = $dynamic_detail_data['util_threshold'];  
						    $par_id = $GetParkingrecord['PA_ID']; 
						    $old_price = $GetParkingrecord['PA_P_Dailyprice'];
						    //$new_price = floor(($old_price*$dynamic_detail_data['Price']) * 100)/100;
                            
                            /* calculate new price */
                            $increment_price = $dynamic_detail_data['price_increment'];
						    $new_price = floor(((($old_price*$increment_price)/100)+$old_price)*100)/100;


	                            /* check one time trigger and update price login on tbl_dynamic_price_log */
							    $check_dynamic_detail = "SELECT * FROM tbl_dynamic_price_log WHERE PA_ID = '".$par_id."'";
						        $check_dynamic_detail_query = mysqli_query($con, $check_dynamic_detail) or die("Error");
						        $rowCount11 = mysqli_num_rows($check_dynamic_detail_query);
		                             
					                    if(!empty($rowCount11)){	  
		                                $check_dynamic_detail_data=mysqli_fetch_assoc($check_dynamic_detail_query);


						                   	if($check_dynamic_detail_data["dynamic_Percentage"] < $util_threshold){
						                   	       
						                   	       /* if thresold match and Parking Id get update value in tbl_dynamic_price_log table */
				                                   $check_dynamic_data_update = "update tbl_dynamic_price_log set dynamic_Percentage = '".$util_threshold."',Increment_Price = '".$increment_price."',P_ID = '".$pa_id."', Date = '".$GetParkingrecord['P_fromDate']."',new_price = '".$new_price."',old_price = '".$old_price."',C_Date='".$CURDATE."' where PA_ID = '".$par_id."'";
				                                    $update_dynamic_data = mysqli_query($con, $check_dynamic_data_update) or die("Error");

	                                                /* Update New price on tbl_parkingweekdayavailability */
				                                    $thresold_Price_Update = "update tbl_parkingweekdayavailability set PA_P_Dailyprice = '".$new_price."' where PA_ID = '".$par_id."'";
				                                    $thresold_Update = mysqli_query($con, $thresold_Price_Update) or die("Error");

		                                            /* Keep change price log */
				                                    $action="Parking Prices updated ".$old_price." to ".$new_price." for ".date('d-M-Y',strtotime($GetParkingrecord['P_fromDate']))."";  
												    $Parking_log="INSERT INTO tbl_parkinglog(PL_UserID,PL_AdminID,PL_ParkingID,PL_ActionType,PL_Description,			PL_PriceOrSpaceUpdatedFor, PL_OldPrice,PL_NewPrice,PL_Created,PL_IPAddress,PL_IsEmailSent) VALUES(0,0,'".$pa_id."','".$action."','','".$GetParkingrecord['P_fromDate']."','".$old_price."','".$new_price."',now(),'".$_SERVER['REMOTE_ADDR']."','N')";
												    $Parking_log_update = mysqli_query($con, $Parking_log) or die("Error");
			                                }

					                    }else{ 
					                   	           /* if thresold match and Parking Id not get than insert value in tbl_dynamic_price_log table */
				                                   $check_dynamic_data_insert = "INSERT INTO tbl_dynamic_price_log(`PA_ID`, `dynamic_Percentage`, `Increment_Price`, `Date`,`P_ID`,`new_price`,`old_price`,`C_Date`) VALUES ('".$par_id."', '".$util_threshold."', '".$increment_price."','".$GetParkingrecord['P_fromDate']."','".$pa_id."','".$new_price."','".$old_price."','".$CURDATE."')";
				                                   $check_dynamic_insert = mysqli_query($con, $check_dynamic_data_insert) or die("Error");

				                                   /* Update New price on tbl_parkingweekdayavailability */
				                                   $thresold_Price_Update = "update tbl_parkingweekdayavailability set PA_P_Dailyprice = '".$new_price."' where PA_ID = '".$par_id."'";
				                                   $thresold_Update = mysqli_query($con, $thresold_Price_Update) or die("Error");

                                                    /* Keep change price log */
				                                    $action="Parking Prices updated ".$old_price." to ".$new_price." for ".date('d-M-Y',strtotime($GetParkingrecord['P_fromDate']))."";
												    $Parking_log="INSERT INTO tbl_parkinglog(PL_UserID,PL_AdminID,PL_ParkingID,PL_ActionType,PL_Description,			PL_PriceOrSpaceUpdatedFor, PL_OldPrice,PL_NewPrice,PL_Created,PL_IPAddress,PL_IsEmailSent) VALUES(0,0,'".$pa_id."','".$action."','','".$GetParkingrecord['P_fromDate']."','".$old_price."','".$new_price."',now(),'".$_SERVER['REMOTE_ADDR']."','N')";
												    $Parking_log_update = mysqli_query($con, $Parking_log) or die("Error");
					                   } 					
									
	                    }
            } 
	   }   
	}
?>