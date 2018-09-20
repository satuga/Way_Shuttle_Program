<?php 
include ('config.php');
include ('function.php');

$data = json_decode(file_get_contents('php://input'), TRUE);
$promocode = $data['data']['promocode'];

if($promocode != '')
{
	$promo = "select * from tbl_promocode where PMC_PromoCode = '".$promocode."' and PMC_IsActive = '1'";
	$promo_query = mysql_query($promo);
	$rows = mysql_num_rows($promo_query);
	if($rows > 0)
	{
		while($rowss = mysql_fetch_assoc($promo_query))
		{
			$records[] = $rowss;
		}
		
		
		function replacer(& $item, $key) {
    		if ($item === null) {
       		$item = '';
    		}
		}

	
		array_walk_recursive($records, 'replacer');
		unset($records[0]['PMC_IsActive']);
		$output=array("status"=>"1","data"=>$records);
		echo json_encode($output);
		exit;
	}
	else
	{
		$output=array("status"=>"0","data"=>"Promo Code Not Available");
		echo json_encode($output);exit;
	}
}
else 
{
	$output=array("status"=>"0","data"=>"Please add correct data");
	echo json_encode($output);exit;
}


?>