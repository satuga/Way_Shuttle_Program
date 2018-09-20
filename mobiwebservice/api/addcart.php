<?php
// echo "<pre>";print_r($_REQUEST); die;
header('Content-Type: application/json');
error_reporting(0);
    include ('config.php');
    //include '../common/config.php';
    include ('function.php');
    include "../dine/dineclassbk.php";
	$Cart_Type= sanitize($_REQUEST['Cart_Type']);
  $user_id=sanitize($_REQUEST['User_ID']) !='' ? sanitize($_REQUEST['User_ID']):'';
  $device_id=$user_id!='' ? '': sanitize($_REQUEST['device_id']);

if(isset($Cart_Type) && strtolower($Cart_Type) == 'dine'){
	GLOBAL $con;
	$Dine_Obj	=	new Dine();
	if($user_id!=''){
          $cartRecord = mysqli_query($con,"select Owner_Restaurant from tbl_cart where Cart_Type='Dine' AND Cart_UserID ='".$user_id."'");
        }else{
          $cartRecord = mysqli_query($con,"select Owner_Restaurant from tbl_cart where Cart_Type='Dine' AND Sess_ID='".$device_id."' ");
        }
		//echo mysqli_num_rows($cartRecord);
		if(mysqli_num_rows($cartRecord)>0)
		while($rec = mysqli_fetch_assoc($cartRecord))
		{
			if($rec['Owner_Restaurant']!=sanitize($_REQUEST['Restaurant']))
			{
				//$output = array("status" => 0, "message"=> "There are already items in your cart from a different restaurant. Do you want to cancel that?");
			/*	$output = array("status" => 0,"already_exists"=>1, "message"=> "Looks like you already have food from another restaurant in your cart. Would you like to clear your existing food items and start a new dinning order?");
				echo json_encode($output);
				exit;*/
        mysqli_query($con,"DELETE from tbl_cart where Cart_Type='Dine' AND Owner_Restaurant ='".$rec['Owner_Restaurant']."'");
			}
		}

	$sizevalue=explode(":",sanitize($_REQUEST['sizeval']));
	$subgroup_ids=sanitize($_REQUEST['subgroup_ids']);
	$subitems_values=sanitize($_REQUEST['subitems']);

  if(isset($_REQUEST['Item']) && !empty($_REQUEST['Item']))
  {
    $sq="SELECT * FROM items where id=".sanitize($_REQUEST['Item']);
     $ex=mysqli_query($con,$sq);
    $re=mysqli_num_rows($ex);
    if($re<=0)
    {
      $output = array("status" => 0, "message"=> "Items does not exist");
      echo json_encode($output);
      exit;
    }

  }
  else {
    $output = array("status" => 0, "message"=> "Please select Items");
    echo json_encode($output);
    exit;
  }

	if(sanitize($_REQUEST['Item'])<>'')
		$Free_Toppings=$Dine_Obj->GetFree_Toppings(sanitize($_REQUEST['Item']));
	$tabindex=0;
	$sq="SELECT menus.* FROM items INNER JOIN menus on menus.id=items.menuID WHERE items.id=".sanitize($_REQUEST['Item']);
	$ex=mysqli_query($con,$sq);
	$re=mysqli_fetch_array($ex);
	if($re['menuSize1']==$sizevalue[0])
		$tabindex=1;
	else if($re['menuSize2']==$sizevalue[0])
		$tabindex=2;
	else if($re['menuSize3']==$sizevalue[0])
		$tabindex=3;
	else if($re['menuSize4']==$sizevalue[0])
		$tabindex=4;
	else if($re['menuSize5']==$sizevalue[0])
		$tabindex=5;
	else if($re['menuSize6']==$sizevalue[0])
		$tabindex=6;
	else if($re['menuSize7']==$sizevalue[0])
		$tabindex=7;
	else if($re['menuSize8']==$sizevalue[0])
		$tabindex=8;
	else if($re['menuSize9']==$sizevalue[0])
		$tabindex=9;
	else if($re['menuSize10']==$sizevalue[0])
		$tabindex=10;

	$subgroup_ids=explode(",",$subgroup_ids);
	$subgroup_ids=array_unique($subgroup_ids);
	$fromDate1="0000-00-00 00:00:00";


	if(sanitize($_REQUEST['Cart_ID'])>0)
	{
    $query="UPDATE tbl_cart SET Notes='".mysqli_real_escape_string($con,(sanitize($_REQUEST['notes'])))."',Cart_Quantity='".sanitize($_REQUEST['quantity'])."',Size='".mysqli_real_escape_string($con,($sizevalue[0]))."',crust='".sanitize($_REQUEST['crust'])."',Amount='".$sizevalue[1]."',TotalAmount='".(sanitize($_REQUEST['quantity'])*$sizevalue[1])."' WHERE Cart_ID=".mysqli_real_escape_string($con,(sanitize($_REQUEST['Cart_ID'])));
		//print_r($query); exit;
		$res = mysqli_query($con,$query);
		$Cart_ID=sanitize($_REQUEST['Cart_ID']);
		$sql="DELETE FROM tbl_cartsubitems WHERE Cart_ID=".$Cart_ID;
		mysqli_query($con,$sql);

		if(sanitize($_REQUEST['cus'])==1)
		{
		if(sanitize($_REQUEST['Multiple_Toppings'])==1)
		{
		   // Multi Toppings Pizza selection
		   $FT=0;
		   for($i=0;$i<sanitize($_REQUEST['Topping_Count']);$i++)
		   {
			if(sanitize($_REQUEST['subitems'.$i])<>'')
			{
				$subitems_value1=explode(":",sanitize($_REQUEST['subitems'.$i]));
				$subitemsX2_value1=sanitize($_REQUEST['subitemsX2'.$i]);
				$exist=$subitemsX2_value1;
				if($Free_Toppings>0 && $FT<$Free_Toppings) {
					$S_Free_Toppings=1;
					if($exist==1 && $subitems_value1[1]=='Whole')
					$FT++;
				}
				else
					$S_Free_Toppings=0;
				if($exist<>1)
					$exist=0;
				if(COUNT($subitems_value1)>1)
					$sql="INSERT INTO tbl_cartsubitems(Cart_ID,subgroup_id,subgroup_value,subgroup_x2,Free_Toppings,price_index) VALUES(".$Cart_ID.",".mysqli_real_escape_string($con,($subitems_value1[0])).",'".mysqli_real_escape_string($con,($subitems_value1[1]))."',".$exist.",".$S_Free_Toppings.",".$tabindex.")";
				else
					$sql="INSERT INTO tbl_cartsubitems(Cart_ID,subgroup_id,subgroup_x2,Free_Toppings,price_index) VALUES(".$Cart_ID.",".mysqli_real_escape_string($con,($subitems_value)).",".$exist.",".$S_Free_Toppings.",".$S_Free_Toppings.",".$tabindex.")";
				mysqli_query($con,$sql);
			}
			$FT++;
		   }
		}
		else
		{
		$pi=0;
		$FT=0;
		foreach($subgroup_ids as $subgroup)
		{
			if($Free_Toppings>0 && $FT<$Free_Toppings) {
				$S_Free_Toppings=1;
				if($exist==1 && $subitems[1]=='Whole')
				$FT++;
			}
			else
				$S_Free_Toppings=0;
			$CLeft=sanitize($_REQUEST['R'.$subgroup.'_comboleft']);

			$CX2 = implode(",",sanitize($_REQUEST['R'.$subgroup.'_comboX2']));
			$CX2= explode(",",$CX2);
			$CX2 = implode(":",sanitize($_REQUEST['R'.$subgroup.'_comboX2']));
			$CX2= explode(":",$CX2);
			$exist=0;
			if(count($CLeft)>0)
			{
				$subitems= explode(":",$CLeft);
				if(COUNT($subitems)>1)
					$exist=in_array($subitems[0],$CX2);
				else
					$exist=in_array($subitem,$CX2);
				if($exist<>1)
					$exist=0;
				if(COUNT($subitems)>1)
					$sql="INSERT INTO tbl_cartsubitems(Cart_ID,subgroup_id,subgroup_value,subgroup_x2,Free_Toppings,price_index) VALUES(".$Cart_ID.",".mysqli_real_escape_string($con,($subitems[0])).",'".mysqli_real_escape_string($con,($subitems[1]))."',".$exist.",".$S_Free_Toppings.",".$tabindex.")";
				else
					$sql="INSERT INTO tbl_cartsubitems(Cart_ID,subgroup_id,subgroup_x2,Free_Toppings,price_index) VALUES(".$Cart_ID.",".mysqli_real_escape_string($con,($subitem)).",".$exist.",".$S_Free_Toppings.",".$tabindex.")";
				mysqli_query($con,$sql);
			}
			$CWhole=sanitize($_REQUEST['R'.$subgroup.'_combowhole']);
			if(count($CWhole)>0)
			{
				$subitems=explode(":",$CWhole);
				if(COUNT($subitems)>1)
					$exist=in_array($subitems[0],$CX2);
				else
					$exist=in_array($subitem,$CX2);
				if($exist<>1)
					$exist=0;
				if(COUNT($subitems)>1)
					$sql="INSERT INTO tbl_cartsubitems(Cart_ID,subgroup_id,subgroup_value,subgroup_x2,Free_Toppings,price_index) VALUES(".$Cart_ID.",".mysqli_real_escape_string($con,($subitems[0])).",'".mysqli_real_escape_string($con,($subitems[1]))."',".$exist.",".$S_Free_Toppings.",".$tabindex.")";
				else
					$sql="INSERT INTO tbl_cartsubitems(Cart_ID,subgroup_id,subgroup_x2,Free_Toppings,price_index) VALUES(".$Cart_ID.",".mysqli_real_escape_string($con,($subitem)).",".$exist.",".$S_Free_Toppings.",".$tabindex.")";
				mysqli_query($con,$sql);
			}
			$CRight=sanitize($_REQUEST['R'.$subgroup.'_comboright']);
			if(count($CRight)>0)
			{
				$subitems=explode(":",$CRight);
				if(COUNT($subitems)>1)
					$exist=in_array($subitems[0],$CX2);
				else
					$exist=in_array($subitem,$CX2);
				if($exist<>1)
					$exist=0;
				if(COUNT($subitems)>1)
					$sql="INSERT INTO tbl_cartsubitems(Cart_ID,subgroup_id,subgroup_value,subgroup_x2,Free_Toppings,price_index) VALUES(".$Cart_ID.",".mysqli_real_escape_string($con,($subitems[0])).",'".mysqli_real_escape_string($con,($subitems[1]))."',".$exist.",".$S_Free_Toppings.",".$tabindex.")";
				else
					$sql="INSERT INTO tbl_cartsubitems(Cart_ID,subgroup_id,subgroup_x2,Free_Toppings,price_index) VALUES(".$Cart_ID.",".mysqli_real_escape_string($con,($subitem)).",".$exist.",".$S_Free_Toppings.",".$tabindex.")";
				mysqli_query($con,$sql);
			}
			$pi++;
			$FT++;
		}
		}
		}
		else
		{
			$pi=0;
			$FT=0;
			foreach($subitems_values as $subitems_value)
			{
				$subitems_value1=explode(":",$subitems_value);
				if($Free_Toppings>0 && $FT<$Free_Toppings) {
					$S_Free_Toppings=1;
					if($exist==1 && $subitems_value1[1]=='Whole')
					$FT++;
				}
				else
					$S_Free_Toppings=0;
				if(COUNT($subitems_value1)>1)
					$sql="INSERT INTO tbl_cartsubitems(Cart_ID,subgroup_id,subgroup_value,Free_Toppings,price_index) VALUES(".$Cart_ID.",".mysqli_real_escape_string($con,($subitems_value1[1])).",'".mysqli_real_escape_string($con,($subitems_value1[1]))."',".$S_Free_Toppings.",".$tabindex.")";
				else
					$sql="INSERT INTO tbl_cartsubitems(Cart_ID,subgroup_id,Free_Toppings,price_index) VALUES(".$Cart_ID.",".mysqli_real_escape_string($con,($subitems_value)).",".$S_Free_Toppings.",".$tabindex.")";
				mysqli_query($con,$sql);
				$pi++;
				$FT++;
			}
		}
	}
	else
	{

		$query="INSERT INTO  tbl_cart(Sess_ID,Cart_UserID,Cart_ServiceID,Owner_ID,Dinning_type,Owner_Restaurant,crust,Notes,Cart_Quantity,Cart_Type,Size,Amount,charges,TotalAmount,from_date,Ticket_Type,Ticket_Quantity,Cart_Created)VALUES('".$device_id."','".$user_id."','".mysqli_real_escape_string($con,(sanitize($_REQUEST['Item'])))."','".sanitize($_REQUEST['Owner'])."','".sanitize($_REQUEST['type'])."','".sanitize($_REQUEST['Restaurant'])."','".sanitize($_REQUEST['crust'])."','".mysqli_real_escape_string($con,(sanitize($_REQUEST['notes'])))."','".mysqli_real_escape_string($con,(sanitize($_REQUEST['quantity'])))."','Dine','".mysqli_real_escape_string($con,($sizevalue[0]))."','".mysqli_real_escape_string($con,($sizevalue[1]))."','".mysqli_real_escape_string($con,($extfees))."','".mysqli_real_escape_string($con,((sanitize($_REQUEST['quantity'])*$sizevalue[1])))."','".mysqli_real_escape_string($con,($fromDate1))."','".mysqli_real_escape_string($con,($Ticket_Type))."','".mysqli_real_escape_string($con,($Ticket_Quantity))."',Now());";
		// print_r($query); exit;
		$res = mysqli_query($con,$query);

		$Cart_ID=mysqli_insert_id($con);

		//print_r($Cart_ID); exit;
		if(sanitize($_REQUEST['cus'])==1)
		{
		if(sanitize($_REQUEST['Multiple_Toppings'])==1)
		{
		   // Multi Toppings Pizza selection
		   $FT=0;
		   for($i=0;$i<=sanitize($_REQUEST['Topping_Count']);$i++)
		   {
			if(sanitize($_REQUEST['subitems'.$i])<>'')
			{
				$subitems_value1=explode(":",sanitize($_REQUEST['subitems'.$i]));
				$subitemsX2_value1=sanitize($_REQUEST['subitemsX2'.$i]);
				$exist=$subitemsX2_value1;
				if($Free_Toppings>0 && $FT<$Free_Toppings) {
					$S_Free_Toppings=1;
					if($exist==1 && $subitems_value1[1]=='Whole')
					$FT++;
				}
				else
					$S_Free_Toppings=0;
				if($exist<>1)
					$exist=0;
				if(COUNT($subitems_value1)>1)
					$sql="INSERT INTO tbl_cartsubitems(Cart_ID,subgroup_id,subgroup_value,subgroup_x2,Free_Toppings,price_index) VALUES(".$Cart_ID.",".mysqli_real_escape_string($con,($subitems_value1[0])).",'".mysqli_real_escape_string($con,($subitems_value1[1]))."','".$exist."',".$S_Free_Toppings.",".$tabindex.")";
				else
					$sql="INSERT INTO tbl_cartsubitems(Cart_ID,subgroup_id,subgroup_x2,Free_Toppings,price_index) VALUES(".$Cart_ID.",".mysqli_real_escape_string($con,($subitems_value)).",'".$exist."',".$S_Free_Toppings.",".$tabindex.")";
				mysqli_query($con,$sql);
			}
			$FT++;
		   }
		}
		else
		{
		$exist=0;
		$pi=0;
		$FT=0;
		foreach($subgroup_ids as $subgroup)
		{

			$CX2 = implode(",",sanitize($_REQUEST['R'.$subgroup.'_comboX2']));
			$CX2=explode(",",$CX2);
			$CX2 = implode(":",sanitize($_REQUEST['R'.$subgroup.'_comboX2']));
			$CX2=explode(":",$CX2);
			$exist=0;
			$CLeft=sanitize($_REQUEST['R'.$subgroup.'_comboleft']);
			if(count($CLeft)>0)
			{
				$subitems=explode(":",$CLeft);
				if(COUNT($subitems)>1)
					$exist=@in_array($subitems[0],$CX2);
				else
					$exist=in_array($subitem,$CX2);
				if($Free_Toppings>0 && $FT<$Free_Toppings) {
				$S_Free_Toppings=1;
				if($exist==1 && $subitems[1]=='Whole')
				$FT++;
				}
				else
					$S_Free_Toppings=0;
				if($exist<>1)
					$exist=0;
				if(COUNT($subitems)>1)
					$sql="INSERT INTO tbl_cartsubitems(Cart_ID,subgroup_id,subgroup_value,subgroup_x2,Free_Toppings,price_index) VALUES(".$Cart_ID.",".mysqli_real_escape_string($con,($subitems[0])).",'".mysqli_real_escape_string($con,($subitems[1]))."',".$exist.",".$S_Free_Toppings.",".$tabindex.")";
				else
					$sql="INSERT INTO tbl_cartsubitems(Cart_ID,subgroup_id,subgroup_x2,Free_Toppings,price_index) VALUES(".$Cart_ID.",".mysqli_real_escape_string($con,($subitem)).",".$exist.",".$S_Free_Toppings.",".$tabindex.")";
				mysqli_query($con,$sql);
			}
			$CWhole=sanitize($_REQUEST['R'.$subgroup.'_combowhole']);
			if(count($CWhole)>0)
			{
				$subitems=explode(":",$CWhole);
				if(COUNT($subitems)>1)
					$exist=in_array($subitems[0],$CX2);
				else
					$exist=in_array($subitem,$CX2);
				if($Free_Toppings>0 && $FT<$Free_Toppings) {
				$S_Free_Toppings=1;
				if($exist==1 && $subitems[1]=='Whole')
				$FT++;
				}
				else
					$S_Free_Toppings=0;
				if($exist<>1)
					$exist=0;
				if(COUNT($subitems)>1)
					$sql="INSERT INTO tbl_cartsubitems(Cart_ID,subgroup_id,subgroup_value,subgroup_x2,Free_Toppings,price_index) VALUES(".$Cart_ID.",".mysqli_real_escape_string($con,($subitems[0])).",'".mysqli_real_escape_string($con,($subitems[1]))."',".$exist.",".$S_Free_Toppings.",".$tabindex.")";
				else
					$sql="INSERT INTO tbl_cartsubitems(Cart_ID,subgroup_id,subgroup_x2,Free_Toppings,price_index) VALUES(".$Cart_ID.",".mysqli_real_escape_string($con,($subitem)).",".$exist.",".$S_Free_Toppings.",".$tabindex.")";
				mysqli_query($con,$sql);
			}
			$CRight=sanitize($_REQUEST['R'.$subgroup.'_comboright']);
			if(count($CRight)>0)
			{
				$subitems=explode(":",$CRight);
				if(COUNT($subitems)>1)
					$exist=in_array($subitems[0],$CX2);
				else
					$exist=in_array($subitem,$CX2);
				if($Free_Toppings>0 && $FT<$Free_Toppings) {
				$S_Free_Toppings=1;
				if($exist==1 && $subitems[1]=='Whole')
				$FT++;
				}
				else
					$S_Free_Toppings=0;
				if($exist<>1)
					$exist=0;
				if(COUNT($subitems)>1)
					$sql="INSERT INTO tbl_cartsubitems(Cart_ID,subgroup_id,subgroup_value,subgroup_x2,Free_Toppings,price_index) VALUES(".$Cart_ID.",".mysqli_real_escape_string($con,($subitems[0])).",'".mysqli_real_escape_string($con,($subitems[1]))."',".$exist.",".$S_Free_Toppings.",".$tabindex.")";
				else
					$sql="INSERT INTO tbl_cartsubitems(Cart_ID,subgroup_id,subgroup_x2,Free_Toppings,price_index) VALUES(".$Cart_ID.",".mysqli_real_escape_string($con,($subitem)).",".$exist.",".$S_Free_Toppings.",".$tabindex.")";
				mysqli_query($con,$sql);
			}
			$pi++;
			$FT++;
		}
		}
		}
		else
		{
			$pi=0;
			$FT=0;
			if($subitems_values!='')
			{
				foreach($subitems_values as $subitems_value)
				{
					$subitems_value1=explode(":",$subitems_value);
					if($Free_Toppings>0 && $FT<$Free_Toppings) {
						$S_Free_Toppings=1;
						if($exist==1 && $subitems_value1[1]=='Whole')
						$FT++;
					}
					else
						$S_Free_Toppings=0;
					if(COUNT($subitems_value1)>1)
						$sql="INSERT INTO tbl_cartsubitems(Cart_ID,subgroup_id,subgroup_value,Free_Toppings,price_index) VALUES(".$Cart_ID.",".mysqli_real_escape_string($con,($subitems_value1[0])).",'".mysqli_real_escape_string($con,($subitems_value1[1]))."',".$S_Free_Toppings.",".$tabindex.")";
					else
						$sql="INSERT INTO tbl_cartsubitems(Cart_ID,subgroup_id,Free_Toppings,price_index) VALUES(".$Cart_ID.",".mysqli_real_escape_string($con,($subitems_value)).",".$S_Free_Toppings.",".$tabindex.")";

					mysqli_query($con,$sql);
					$pi++;
					$FT++;
				}
			}
		}
    /* ------------------ Get total cart items before login --------------------------*/

    if(sanitize($_REQUEST['User_ID']) !=""){
       $sql_count="select count(*) as total_cart from tbl_cart where Cart_Type in('Dine','Parking') and Cart_UserID='".sanitize($_REQUEST['User_ID'])."'";
    }else{
      $sql_count="select count(*) as total_cart from tbl_cart where Cart_Type in('Dine','Parking') and Sess_ID='".$device_id."'";
    }
     $query_count=mysqli_query($con,$sql_count);
     if (mysqli_num_rows($query_count))
     {
      $counts= mysqli_fetch_array($query_count);
      $total_cart=$counts['total_cart'];
     }
    if($Cart_ID !='' )
    {
      $output = array("status" => 1, "cart_id" => $Cart_ID,'total_cart'=>$total_cart);
      echo json_encode($output);
      exit;
    }
	}
}
else if(isset($Cart_Type) && $Cart_Type == 'parking'){
	 GLOBAL $con;
      $parking_id = sanitize($_REQUEST['id']);
      $fromdate = sanitize($_REQUEST['fromDate']);
      //$fromdate = '04/22/2015 5:02 am';
      $todate = sanitize($_REQUEST['toDate']);
      //$todate = '04/30/2015 5:02 am';
      $user_id = sanitize($_REQUEST['User_ID']);
      $owner = sanitize($_REQUEST['owner_id']);
      $pe_id = sanitize($_REQUEST['pe_id']);
      $pricing_type = sanitize($_REQUEST['pricing_type']);

      if($todate != '' && $fromdate != '' && $parking_id != '')
      {
      	// get the parking data from the database
      	$sqll="select *,tbl_parkinglocations.*,tbl_parking.* from tbl_parking
      	INNER JOIN tbl_parkinglocations ON tbl_parkinglocations.Park_ID=tbl_parking.P_Location
      	where P_ID=".$parking_id;
      	$exe=mysqli_query($con,$sqll);
      	$res=mysqli_fetch_array($exe);

      	$fdate=strtotime($fromdate);
      	$tdate=strtotime($todate);
      	$Park_AirportVenue=$res['Park_AirportVenue'];
      	$Payment_Type=$res['Payment_Type'];
      	$Payment_Collection=$res['Payment_Collection'];
      	$Payment_Collection_Amt=$res['Payment_Collection_Amt'];
      	$Payat_Lot=0;
        $min_reservation= $res['min_reservation']>0 ? $res['min_reservation'] : 0;
        $max_reservation= $res['max_reservation']>0 ? $res['max_reservation'] : 0;
      	if($Park_AirportVenue<>1)
      		$Park_AirportVenue=0;


      	if($fdate>=$tdate)
      	{
          // echo "Invalid Date";
          $output = array("status" => 0, "message" => 'Invalid Date');
          echo json_encode($output);
      		exit;
      	}

      	if($res['P_Pricingtype']=='daily')
      	{
      		// Check Net Park Availability
      		//include("netpark-availability.php");
      		$start = $fdate;
      		$end = $tdate;
      		$start=date("Y-m-d",$fdate);
      		$end=date("Y-m-d",$tdate);
      		$num=1;
      		$diff=0;
      		$error="";
      		$totalprice=0;
      		$diff = abs($tdate - $fdate);

      		$years   = floor($diff / (365*60*60*24));
      		$months  = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
      		$days    = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
      		$hours   = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24)/ (60*60));
      		$minuts  = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24 - $hours*60*60)/ 60);
      		if($years>0)
      		{
      			$days=$days+($years*364);

      			$days--;
      			$days=$days;
      		}
      		if($months>0)
      		{
      			$days=$days+($months*30);

      			$days--;
      			$days=$days;
      		}

      		if($hours>0 || $minuts>0)
      			$days++;

      		$diff=$days;
			$_REQUEST['quantity']=$diff;
      $closed=0;
      		if($min_reservation> 0 && $min_reservation>$diff)
      		{
      			$closed=1;
      			// echo $min_reservation."-day minimum stay required.";
            $output = array("status" => 0, "message" => $min_reservation."-day minimum stay required.");
            echo json_encode($output);
            exit;
      		}
          if($max_reservation> 0 && $max_reservation<$diff)
      		{
      			$closed=1;
      		//	echo $max_reservation."-day maximum stay allowed.";
            $output = array("status" => 0, "message" => $max_reservation."-day minimum stay required.");
            echo json_encode($output);
            exit;
      		}
      		$ik=1;

      		$closeddates="";
      		$PayatLotdaysAmt=0;

      		while ($ik<=$days)
      		{
      			$cl=0;

      			if($res['P_Daily_Price_Type']=='Week')
      			{
      				$week=date('l', strtotime($start));
      				//$sqlw="SELECT P_".$week."_Price AS PRICE,P_".$week."_Space AS SPACES FROM tbl_parking WHERE P_ID=".sanitize($_REQUEST['id']);

      				$sqlw="SELECT PA_P_Dailyprice AS PRICE,PA_No_Spaces AS SPACES,PA_Updated FROM tbl_parkingweekdayavailability WHERE P_fromDate='".$start."' AND P_ID=".$parking_id;
      				$resw = mysqli_fetch_array(mysqli_query($con,$sqlw));
      				$totalprice=$totalprice+$resw['PRICE'];
      				if($Payment_Type=='partial' && $Payment_Collection=='days' && $ik<=$Payment_Collection_Amt)
      					$PayatLotdaysAmt+=$resw['PRICE'];

      				//Check for Inventory
      				$sql1="SELECT COUNT(TxnID) AS CNT FROM tbl_paymenttransaction WHERE ('".$start."' BETWEEN date_format(from_date, '%Y-%m-%d') AND date_format(to_date, '%Y-%m-%d')) AND TxnDate>='".$resw['PA_Updated']."' AND Parking_ID='".$parking_id."' AND OrderStatus!='Cancelled'";
      				$exe1=mysqli_query($con,$sql1);
      				$res1=mysqli_fetch_array($exe1);

      				if($res1['CNT']>=$resw['SPACES'])
      				{
      					$closed=1;
      					if($cl==0)
      						$closeddates.=", ".date("F j, Y",strtotime($start));
      				}
      			}
      			else
      			{
      				$sql="SELECT Park_ID,PA_P_Dailyprice,PA_No_Spaces,PA_Created FROM tbl_parkingavailability WHERE ('".$start."' BETWEEN P_fromDate AND PA_toDate) AND P_ID='".$parking_id."'";

      				$exe=mysqli_query($con,$sql);
      				$res=mysqli_fetch_array($exe);
      				$num=mysqli_num_rows($exe);
      				$totalprice=$totalprice+$res['PA_P_Dailyprice'];
      				if($num<1){
      					$closed=1;
      					$cl=1;
      					$closeddates.=", ".date("F j, Y",strtotime($start));
      				}
      				//Check for Inventory
      				$sql1="SELECT COUNT(TxnID) AS CNT FROM tbl_paymenttransaction WHERE ('".$start."' BETWEEN date_format(from_date, '%Y-%m-%d') AND date_format(to_date, '%Y-%m-%d')) AND Parking_ID='".$parking_id."' AND TxnDate>='".$res['PA_Created']."' AND OrderStatus!='Cancelled'";
      				$exe1=mysqli_query($con,$sql1);
      				$res1=mysqli_fetch_array($exe1);
      				if($res1['CNT']>=$res['PA_No_Spaces'])
      				{
      					$closed=1;
      					if($cl==0)
      						$closeddates.=", ".date("F j, Y",strtotime($start));
      				}
      			}

      			$start = date ("Y-m-d", strtotime("+1 day", strtotime($start)));
      			//$diff++;
      			$ik++;
      		}

      		if($closed==1)
      		{
      			// echo 'One of the days you have selected('.trim($closeddates,',').') is sold out and you may need to select another time or a different parking lot.';
            $err='One of the days you have selected('.trim($closeddates,',').') is sold out and you may need to select another time or a different parking lot.';
            $output = array("status" => 0, "message" => $err);
            echo json_encode($output);
      			exit;
      		}
      		if($error=='Closed')
      		{
      			// echo "Closed";
            $err= "Closed";
            $output = array("status" => 0, "message" => $err);
            echo json_encode($output);
      			exit;
      		}
      	}

      	//echo "Total Price = ".$totalprice;
      	//echo "<br />";

      	if($res['P_Pricingtype']=='weekly')
      	{
      		$price=number_format($res['P_Weeklyprice'],2);
      		$pr="Week";
      		$fdate=strtotime($fromdate);
      		$tdate=strtotime($todate);
      		$diff = ceil(($tdate - $fdate)/ ((3600*24)*7));
			$_REQUEST['quantity'] = $diff; // set diff as a quantity - bindra shah - 2-june-2016
      		$totalprice=$price*$diff;
      	}
      	else if($res['P_Pricingtype']=='event')
      	{
      		$price=number_format($res['Event_price'],2);
      		$pr="Event";
      		$fdate=strtotime($fromdate);
      		$tdate=strtotime($todate);
      		//$diff = ceil(($tdate - $fdate)/ 3600/24);
      		$diff=1;
			$_REQUEST['quantity'] = $diff; // add total hours as a quantity - bindra shah - 2-june-2016
      		$totalprice=$price*$diff;
      	}
      	else if($res['P_Pricingtype']=='special')
      	{
      		$price=number_format($res['Event_price'],2);
      		$pr="Special";
      		$fdate=strtotime($fromdate);
      		$tdate=strtotime($todate);
      		$diff = ceil(($tdate - $fdate)/ 3600/24);
			$_REQUEST['quantity'] = $diff; // add total hours as a quantity - bindra shah - 2-june-2016
      		$totalprice=$price*$diff;
      	}
      	else if($res['P_Pricingtype']=='monthly')
      	{
      		$price=number_format($res['P_Monthlyprice'],2);
      		$pr="Month";
      		$fdate=strtotime($fromdate);
      		$tdate=strtotime($todate);
      		$diff = ceil(($tdate - $fdate)/ ((3600*24)*31));
			$_REQUEST['quantity'] = $diff; // add total hours as a quantity - bindra shah - 2-june-2016
      		$totalprice=$price*$diff;
      	}
      	else if($res['P_Pricingtype']=='hourly')
      	{
      		$Overnight_Fee=0;

      		//Check Lot opened for selected arrival date
      		$weekfday=date("l",$fdate);

      		if($weekfday=='Monday')
      		{
      			$Park_timing=$res['P_OP_Monday'];
      			$Park_Closed=$res['P_OP_Monday_Closed'];
      		}
      		else if($weekfday=='Tuesday')
      		{
      			$Park_timing=$res['P_OP_Tuesday'];
      			$Park_Closed=$res['P_OP_Tuesday_Closed'];
      		}
      		else if($weekfday=='Wednesday')
      		{
      			$Park_timing=$res['P_OP_Wednesday'];
      			$Park_Closed=$res['P_OP_Wednesday_Closed'];
      		}
      		else if($weekfday=='Thursday')
      		{
      			$Park_timing=$res['P_OP_Thursday'];
      			$Park_Closed=$res['P_OP_Thursday_Closed'];
      		}
      		else if($weekfday=='Friday')
      		{
      			$Park_timing=$res['P_OP_Friday'];
      			$Park_Closed=$res['P_OP_Friday_Closed'];
      		}
      		else if($weekfday=='Saturday')
      		{
      			$Park_timing=$res['P_OP_Saturday'];
      			$Park_Closed=$res['P_OP_Saturday_Closed'];
      		}
      		else if($weekfday=='Sunday')
      		{
      			$Park_timing=$res['P_OP_Sunday'];
      			$Park_Closed=$res['P_OP_Sunday_Closed'];
      		}

      		if($Park_Closed==1 || $Park_timing=='closed')
      		{
      			// echo 'Parking lot is closed during the time you have selected.';
            $err= 'Parking lot is closed during the time you have selected.';
            $output = array("status" => 0, "message" => $err);
            echo json_encode($output);
      			exit;
      		}
      		else if($Park_timing=='24hr')
      		{
      			echo '';
      		}
      		else
      		{
      			$Park_timings=explode("-",$Park_timing);

      			//echo "<br>From Time:".$Current_Time=date("g:i A",$fdate);
      			$Current_Date=date("Y-m-d",$fdate);
      			$today_timestamp=$fdate;
      			//echo "<br>open Time:".$Park_timings[0];
      			$start_timestamp=strtotime($Current_Date.' '.$Park_timings[0]);
      			//echo "<br>Close Time:".$Park_timings[1];
      			if(strtotime($Park_timings[1])<strtotime($Park_timings[0]))
      				$Current_Date = date("Y-m-d",strtotime("+1 day", strtotime($Current_Date)));
      			$end_timestamp=strtotime($Current_Date.' '.$Park_timings[1]);
      			if(($today_timestamp >= $start_timestamp) && ($today_timestamp <= $end_timestamp))
      			{

      			}
      			else
      			{
      				//Check for Over Night Parking
      				if($res['P_Overnight']==1)
      				{
      					// Calculate Over Night Rate
      					$Overnight_Fee=$Overnight_Fee+$res['P_Overnight_Fee'];
      				}
      				else
      				{
      					// echo 'Overnight parking is not permitted in this location';
                $err= 'Overnight parking is not permitted in this location';
                $output = array("status" => 0, "message" => $err);
                echo json_encode($output);
      					//echo 'Parking Closed on Selected Arrival Time';
      					exit;
      				}
      			}
      		}

      		//Check Lot opened for selected departs date
      		$weektday=date("l",$tdate);

      		if($weektday=='Monday')
      		{
      			$Park_timing=$res['P_OP_Monday'];
      			$Park_Closed=$res['P_OP_Monday_Closed'];
      		}
      		else if($weektday=='Tuesday')
      		{
      			$Park_timing=$res['P_OP_Tuesday'];
      			$Park_Closed=$res['P_OP_Tuesday_Closed'];
      		}
      		else if($weektday=='Wednesday')
      		{
      			$Park_timing=$res['P_OP_Wednesday'];
      			$Park_Closed=$res['P_OP_Wednesday_Closed'];
      		}
      		else if($weektday=='Thursday')
      		{
      			$Park_timing=$res['P_OP_Thursday'];
      			$Park_Closed=$res['P_OP_Thursday_Closed'];
      		}
      		else if($weektday=='Friday')
      		{
      			$Park_timing=$res['P_OP_Friday'];
      			$Park_Closed=$res['P_OP_Friday_Closed'];
      		}
      		else if($weektday=='Saturday')
      		{
      			$Park_timing=$res['P_OP_Saturday'];
      			$Park_Closed=$res['P_OP_Saturday_Closed'];
      		}
      		else if($weektday=='Sunday')
      		{
      			$Park_timing=$res['P_OP_Sunday'];
      			$Park_Closed=$res['P_OP_Sunday_Closed'];
      		}
      		if($Park_Closed==1 || $Park_timing=='closed')
      		{
      			// echo 'Parking lot is closed during the time you have selected.';
            $err= 'Parking lot is closed during the time you have selected.';
            $output = array("status" => 0, "message" => $err);
            echo json_encode($output);
      			exit;
      		}
      		else if($Park_timing=='24hr')
      		{
      			echo '';
      		}
      		else
      		{
      			$Park_timings=explode("-",$Park_timing);

      			//echo "<br>From Time:".$Current_Time=date("g:i A",$tdate);
      			$Current_Date=date("Y-m-d",$tdate);
      			$today_timestamp=$tdate;
      			//echo "<br>open Time:".$Park_timings[0];
      			$start_timestamp=strtotime($Current_Date.' '.$Park_timings[0]);
      			//echo "<br>Close Time:".$Park_timings[1];
      			if(strtotime($Park_timings[1])<strtotime($Park_timings[0]))
      				$Current_Date = date("Y-m-d",strtotime("+1 day", strtotime($Current_Date)));
      			$end_timestamp=strtotime($Current_Date.' '.$Park_timings[1]);

      			if(($today_timestamp >= $start_timestamp) && ($today_timestamp <= $end_timestamp))
      			{

      			}
      			else
      			{
      				//Check for Over Night Parking
      				if($res['P_Overnight']==1)
      				{
      					// Calculate Over Night Rate
      					$Overnight_Fee=$Overnight_Fee+$res['P_Overnight_Fee'];
      				}
      				else
      				{
      					// echo 'Overnight parking is not permitted in this location';
                $err= 'Overnight parking is not permitted in this location';
                $output = array("status" => 0, "message" => $err);
                echo json_encode($output);
      					//echo 'Parking Closed on Selected Arrival Time';
      					exit;
      				}
      			}
      		}


      		$price=number_format($res['P_FAmt'],2);
      		//$totalprice=$price;
      		$pr="Hourly";
      		$fdate=strtotime($fromdate);
      		$tdate=strtotime($todate);
      		$diff = $tdate - $fdate;
      		$d1 = ceil(($diff)/ 60/60/24);
      		$diff = ceil(($diff - $dl*60*60*24)/60/60);
			$_REQUEST['quantity'] = $diff; // add total hours as a quantity - bindra shah - 2-june-2016
      		if($res['P_MaxMinEnable']==1 && $res['P_MaxAmt']>0)
      		{
      			$P_MaxMin=$res['P_MaxMin'];
      			if($P_MaxMin<1)
      				$P_MaxMin=24;
      			$Total_Hours=$diff;
      			$Avg_Hours=$Total_Hours/$P_MaxMin;
      			if($Avg_Hours>=1)
      			{
      				$otherprice=floor($Avg_Hours)*$res['P_MaxAmt'];
      				$Remaining_Hours=$Total_Hours%$P_MaxMin;
      				$Remaining_Price=$Remaining_Hours*$price;
      				if($Remaining_Price>$res['P_MaxAmt'])
      					$Remaining_Price=$res['P_MaxAmt'];
      				$totalprice=$otherprice+$Remaining_Price;
      			}
      			else
      			{
      				$otherprice=$Total_Hours*$price;
      				if($otherprice>$res['P_MaxAmt'])
      					$otherprice=$res['P_MaxAmt'];
      				$totalprice=$otherprice;
      			}
      			//$totalprice=$Avg_Hours;
      		}
      		else
      		{
      			//$totalprice=$price*$diff;
				$totalprice=getNewMainHourPrice($price, $diff);
      		}


      	}
      	else if($res['P_Pricingtype']=='minute')
      	{
      		$otherprice=0;
      		$price=number_format($res['P_FAmt'],2);
      		$totalprice=$price;
      		$pr="Hourly";
      		$fdate=strtotime($fromdate);
      		$tdate=strtotime($todate);

      		$diff=round(abs($tdate - $fdate) / 60,2);


      		if($diff>$res['P_FMIN'])
      		{
      			if($res['P_MaxMinEnable']==1)
      			{
      				$diff1=$diff-$res['P_FMIN'];
      				if($diff1>=$res['P_MaxMin'])
      				{
      					$othermin=floor($diff1/$res['P_MaxMin']);
      					$otherprice=$othermin*$res['P_MaxAmt'];
      					$othermin1=$diff1%$res['P_MaxMin'];
      					if($othermin1>0)
      					{
      						$othermin1=ceil($othermin1/$res['P_IncMin']);
      						$otherprice1=$othermin1*$res['P_IncAmt'];
      						$otherprice=$otherprice+$otherprice1;
      					}
      				}
      				else
      				{
      					$diff1=ceil($diff1/$res['P_IncMin']);
      					$otherprice=$diff1*$res['P_IncAmt'];
      				}
      			}
      			else
      			{
      				if($res['P_IncMin']==0)
      					$P_IncMin=$res['P_FMIN'];
      				else
      					$P_IncMin=$res['P_IncMin'];
      				if($res['P_IncAmt']==0)
      					$P_IncAmt=$res['P_FAmt'];
      				else
      					$P_IncAmt=$res['P_IncAmt'];
      				$diff1=$diff-$res['P_FMIN'];
      				$diff1=ceil($diff1/$P_IncMin);
      				$otherprice=$diff1*$P_IncAmt;
      			}
      		}
      		$diff=$diff/$res['P_FMIN'];
			$_REQUEST['quantity'] = $diff; // add total hours as a quantity - bindra shah - 2-june-2016
      		$totalprice=$totalprice+$otherprice;
      	}



      	$extfees=0;
      	$Way_Fee=0;
      	// Admin External Fee based on Individual Listing

      	if($Park_AirportVenue==1)
      	{
      		$csql="SELECT * FROM tbl_categories_settings WHERE Cat_ID=45 AND Parking_ID=".$parking_id;
      		$cexe=mysqli_query($con,$csql);

      		$ext_setting=0;
      		while($cres=mysqli_fetch_array($cexe))
      		{
      			$ext_setting=1;
      			if($cres['format']=='%')
      			{
      				$per_amt=$totalprice*$cres['Setting_Value']/100;
      				$extfees=$extfees+$per_amt;
      			}
      			else
      			{
      				$per_amt=$cres['Setting_Value'];
      				$extfees=$extfees+$cres['Setting_Value'];
      			}
      		}
      		$Way_Fee=$extfees;
      	}

      	//echo "Way Fees =".$Way_Fee;
      	//echo "<br />";

      	// Admin External Fees
      	if($Park_AirportVenue==1)
      	{
      		if($ext_setting==0)
      		{
      			$csql="SELECT * FROM tbl_categories_settings WHERE Setting_Value>0 AND Cat_ID=45 AND Parking_ID=0";
      			$cexe=mysqli_query($con,$csql);
      			while($cres=mysqli_fetch_array($cexe))
      			{
      			//echo "Ddd";
      				if($cres['format']=='%')
      				{
      					$per_amt=$totalprice*$cres['Setting_Value']/100;
      					$extfees=$extfees+$per_amt;
      					$Way_Fee=$Way_Fee+$per_amt;
      				}
      				else
      				{
      					$per_amt=$cres['Setting_Value'];
      					$extfees=$extfees+$cres['Setting_Value'];
      					$Way_Fee=$Way_Fee+$cres['Setting_Value'];
      				}
      			}

      		}
      	}



      	// Get Additional Charges
      	$csql="SELECT * FROM tbl_sell_fee WHERE Parking_ID=".mysqli_real_escape_string($con,($parking_id));
      	$cexe=mysqli_query($con,$csql);

      	while($cres=mysqli_fetch_array($cexe))
      	{
      		if($cres['format']=='%')
      		{
      			$per_amt=round($totalprice*$cres['Setting_Value']/100,2);
      			$extfees=$extfees+$per_amt;
      		}
      		else
      		{
      			$per_amt=$cres['Setting_Value'];
      			$extfees=$extfees+$cres['Setting_Value'];
      		}
      	}

      	//echo $extfees;
      	//echo "<br />";
      	//echo $totalprice;
      	//echo "<br />";
      	$totalpriceval=$totalprice+$extfees+$Overnight_Fee;
	       //echo $Payment_Collection;
      	$fromDate1=date("Y-m-d H:i:s",strtotime($fromdate));
      	$toDate1=date("Y-m-d H:i:s",strtotime($todate));



      	/*$sql="DELETE FROM tbl_cart WHERE Cart_Type='Parking' AND Cart_ServiceID=".$parking_id." AND Sess_ID='".$device_id."'";
      	mysqli_query($con,$sql);*/

      	// Pay at Lot calculation
      	if($Payment_Type=='partial' && $Payment_Collection=='fixed' && $Payment_Collection_Amt>0)
      		$Payat_Lot=$Payment_Collection_Amt;
      	else if($Payment_Type=='partial' && $Payment_Collection=='percent' && $Payment_Collection_Amt>0)
      		$Payat_Lot=($totalprice*$Payment_Collection_Amt/100)+$Way_Fee;
      	else if($Payment_Type=='partial' && $Payment_Collection=='percentwithcharges' && $Payment_Collection_Amt>0)
      		$Payat_Lot=(($totalpriceval-$Way_Fee)*$Payment_Collection_Amt/100)+$Way_Fee;
      	else if($Payment_Type=='partial' && $Payment_Collection=='percentwithwaycharges' && $Payment_Collection_Amt>0)
      		$Payat_Lot=(($totalpriceval-$Way_Fee)*$Payment_Collection_Amt/100)+$Way_Fee;
      	else if($Payment_Type=='partial' && $Payment_Collection=='days' && $Payment_Collection_Amt>0)
      		$Payat_Lot=$PayatLotdaysAmt;

      	if($Payment_Type=='partial')
      	{
      		$Payat_Lot=$totalpriceval-$Payat_Lot;
      		if($Payat_Lot<0)
      		{
      			$Payat_Lot=0;
      		}
      	}

      	$query="INSERT INTO tbl_cart(Sess_ID,Cart_UserID,Cart_ServiceID,Owner_ID,Cart_Quantity,Cart_Type,Amount,charges,Overnight_Fee,TotalAmount,Payat_Lot,Payment_Type,Parking_type,from_date,to_date,Cart_Created,NetPark_rate,NetPark_daily_rate,Way_Fee,Parking_Event_PE_ID)
      		  VALUES('".$device_id."','".$user_id."','".$parking_id."','".$owner."','".mysqli_real_escape_string($con,sanitize($_REQUEST['quantity']))."',
            'Parking','".$totalprice."',
            '".$extfees."','".$Overnight_Fee."','".round($totalpriceval,2)."',
            '".$Payat_Lot."','".$Payment_Type."','".$pricing_type."','".$fromDate1."',
            '".$toDate1."',Now(),'".$NetPark_rate."','".$NetPark_daily_rate."','".$Way_Fee."','".$pe_id."');";

          	$res = mysqli_query($con,$query);
          	$Cart_ID=mysqli_insert_id($con);

			/* ------------------ Get total cart items before login --------------------------*/

              if(sanitize($_REQUEST['User_ID'] )!=""){
          	     $sql_count="select count(*) as total_cart from tbl_cart where Cart_Type in('Dine','Parking') and  Cart_UserID='".sanitize($_REQUEST['User_ID'])."'";
          		}else{
          			$sql_count="select count(*) as total_cart from tbl_cart where Cart_Type in('Dine','Parking') and  Sess_ID='".$device_id."'";
          		}
               $query_count=mysqli_query($con,$sql_count);
               if (mysqli_num_rows($query_count))
               {
                $counts= mysqli_fetch_array($query_count);
                $total_cart=$counts['total_cart'];
               }
            	if($Cart_ID !='' && $total_cart>0 )
            	{
					$output = array("status" => 1, "cart_id" => $Cart_ID,'total_cart'=>$total_cart);
					echo json_encode($output);
					exit;
            	}
				else
				{
					$output = array("status" => 0, "message"=> "No records found");
					echo json_encode($output);
					exit;
				}
          }
          else
          {
          	  $output = array("status" => 0, "error" => 'Please Pass Required Parameters');
              echo json_encode($output);
              exit;
          }
}else{
  $output = array("status" => 0, "message"=> "Please select type");
  echo json_encode($output);
  exit;
}
?>
