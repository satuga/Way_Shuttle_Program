<?php
include("config.php");
include("function.php");

$device_id = sanitize($_REQUEST['device_id']);
$user_id = sanitize($_REQUEST['user_id']);
$cart = array();
if($device_id != '' && $user_id != '')
{
	$Query="SELECT * from tbl_cart where Sess_ID='".$device_id."' AND Cart_Type!='Dine' and Cart_UserID ='".$user_id."'";
	$res = mysqli_query($con,$Query);
	$cart_count = mysqli_num_rows($res);
	while($info = mysqli_fetch_array($res))
	{
		$records[] = $info;
	}

	if(count($records) > 0)
	{
		foreach($records as $k=>$v)
		{

			if($v['Payat_Lot']>0)
				$Payat_Lot=$v['Payat_Lot'];
			else
				$Payat_Lot=0;
			if($v['Payment_Type']=='partial')
				$Payment_Type=$v['Payment_Type'];
			else
				$Payment_Type='full';

			$get_loc = @mysqli_fetch_array(mysqli_query($con,"select tbl_parkinglocations.Park_Address AS Title,tbl_parkinglocations.Park_Name from tbl_parking INNER JOIN tbl_parkinglocations ON tbl_parkinglocations.Park_ID=tbl_parking.P_Location where P_ID=".$v['Cart_ServiceID']));

			$cart[$k]['title'] = $get_loc['Title'];
			$cart[$k]['park_name'] = $get_loc['Park_Name'];
			$cart[$k]['cart_quantity'] = $v['Cart_Quantity'];
			$cart[$k]['amount'] = "$".number_format($v['Amount'],2);

			//-----get point percentage-----------//
			$cat = '45';
			$Point = @mysqli_fetch_array(mysqli_query($con,"SELECT Point_Percentage FROM tbl_categories WHERE Cat_ID=".$cat));
			$PointPercentage = $Point['Point_Percentage'];
			$Points1=($v['TotalAmount']*$PointPercentage)/100;
			$Points=$Points+$Points1;

			$cart[$k]['points'] = $Points1;

			//----------getting the parking Fee--------//
			$fees = GetParkingfee($v['Cart_ServiceID']);

			$cart[$k]['fees'] = array();
			if(count($fees) > 0 )
			{
				foreach($fees[0] as $pp=>$qq)
				{
					if($qq['format']=='%')
						$Setting_Charge=$v['Amount']*$qq['Setting_Value']/100;
					else
						$Setting_Charge=$qq['Setting_Value'];

					if($qq['format']=='%')
					{
						$cart[$k]['fees'][$qq['Setting_Name']] = "$".number_format($Setting_Charge,2);
					}
					else
					{
						$cart[$k]['fees'][$qq['Setting_Name']] = '$'.number_format($Setting_Charge,2);
					}

				}
			}

			//--------------overnight fee----------//
			if($v['Overnight_Fee']>0)
			{
				$cart[$k]['overnight_fee'] = '$'.number_format($v['Overnight_Fee'],2);
			}

		}

		//-----------getting the total amount of the cart----------//
		$total = @mysqli_fetch_array(mysqli_query($con,"select SUM(TotalAmount) AS TOTAL from tbl_cart where Sess_ID='".$device_id."' and Cart_UserID ='".$user_id."'"));


		//---------if partial payment-----------//
		if($Payment_Type=='partial')
		{
			$pay_now = "$".number_format((($total['TOTAL'])-$Payat_Lot),2);
			$balance_due = "$".number_format(($Payat_Lot),2);
		}
		else
		{
			$pay_now = '';
			$balance_due = '';
		}

		$way_amount = number_format(round($Points,2),2);

		$content=array("status"=>1,"data"=>$cart,"total" => $total['TOTAL'],"pay_now" => $pay_now,"balance_due" => $balance_due,"way_buck" => $way_amount);
		echo json_encode($content);
		exit;

	}
	else
	{
		$content=array("status"=>0,"message"=>"No records Found");
		echo json_encode($content);
		exit;
	}
}
else
{
	$content=array("status"=>0,"message"=>PARAMETER_MSG);
    echo json_encode($content);
    exit;
}
?>
