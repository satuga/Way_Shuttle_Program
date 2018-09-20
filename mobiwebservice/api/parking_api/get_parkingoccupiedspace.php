<?php
		error_reporting(0);
		header('Content-Type: application/json');
		include ('../config.php');
		include ('../function.php');
		$data = json_decode(file_get_contents('php://input'), TRUE);
		$pe_id = isset($_REQUEST['pe_id'])? sanitize($_REQUEST['pe_id']):'';
		$fdate = isset($_REQUEST['fromdate'])? sanitize($_REQUEST['fromdate']):'';
		$tdate = isset($_REQUEST['todate'])? sanitize($_REQUEST['todate']):'';
		/*$pe_id =isset($data['data']['pe_id'])? sanitize($data['data']['pe_id']):'';
		$fdate = sanitize($data['data']['fromdate']);
		$tdate= sanitize($data['data']['todate']); 
		$$park_price_type = sanitize($data['data']['park_price_type']); */
		if($pe_id == '')
		{
			$content = array("status" => "0","response" => ERROR, "data" => PARAMETER_MSG);
			echo json_encode($content);
			exit;
		}
		else
		{
			$fdate=  $fdate!=''? $fdate : date("Y-m-d g:i A");
		    $tdate= $tdate!=''? $tdate : date('Y-m-d g:i A');
			$parkingData = parkingOccupiedData($pe_id, $fdate, $tdate, 'daily', '');
			$temp = array();
			$lot = 0; 
			
			if (!empty($parkingData)) {				
					foreach ($parkingData as $records) {					
							 $date = date('Y-m-d',strtotime($records['CheckinDate']));
                             $temp[$date][] = $records;		
							 					 
							 $all = $temp;
					} 
			}
			if(!empty($all)){
				$content = array("status" => "1","data" => $all);
				echo $json = json_encode($content);
				exit;
			}else{
				$content = array("response" => ERROR, "message" => 'No Records Found');
				echo json_encode($content);
				exit;
			}
		}
?>
